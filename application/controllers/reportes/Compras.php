<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Compras extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$sucursales = $this->db->query("select *from public.sucursales where estado=1")->result_array();
			$this->load->view("reportes/compras/index",compact("sucursales"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function ver_grafico(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]); $categorias = array(); $totales = array();

			if ($this->request->codsucursal==0) {
				$sucursales = $this->db->query("select *from public.sucursales where estado=1")->result_array();
				foreach ($sucursales as $key => $value) {
					$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codkardex<>0 and fechamovimiento>='".$this->request->fechadesde."' and fechamovimiento<='".$this->request->fechahasta."' and tipomovimiento=2 and estado=".(int)$this->request->estado)->result_array();
					$categorias[] = $value["descripcion"]; $totales[] = (double)$total[0]["importe"];
				}
			}else{
				if ($this->request->codcaja==0) {
					$cajas = $this->db->query("select *from caja.cajas where codsucursal=".$this->request->codsucursal." and estado=1")->result_array();
					foreach ($cajas as $key => $value) {
						$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codkardex<>0 and fechamovimiento>='".$this->request->fechadesde."' and fechamovimiento<='".$this->request->fechahasta."' and tipomovimiento=2 and codcaja=".$value["codcaja"]." and estado=".(int)$this->request->estado)->result_array();
						$categorias[] = $value["descripcion"]; $totales[] = (double)$total[0]["importe"];
					}
				}else{
					$desde = explode("-", $this->request->fechadesde); $hasta = explode("-", $this->request->fechahasta);

					if ( ($hasta[0] - $desde[0])!=0 ) {
						$year = $hasta[0] - $desde[0] + 1; $y_inicio = $desde[0]; $f_inicio = $this->request->fechadesde;
						for ($i=0; $i < $year ; $i++) { 
							$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codkardex<>0 and TO_CHAR(fechamovimiento,'YYYY')='".$y_inicio."' and tipomovimiento=2 and codcaja=".$this->request->codcaja." and estado=".(int)$this->request->estado)->result_array();

							$categorias[$i] = "Año-".$y_inicio; $totales[$i] = (double)$total[0]["importe"];
							$y_inicio = $y_inicio + 1; $f_inicio = date("Y-m-d",strtotime($f_inicio."+ 1 year")); 
						}
					}else{
						if ( ($hasta[1] - $desde[1]!=0 ) ) {
							$meses = $hasta[1] - $desde[1] + 1; $m_inicio = $desde[1]; $f_inicio = $this->request->fechadesde;
							for ($i=0; $i < $meses ; $i++) { 
								$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codkardex<>0 and TO_CHAR(fechamovimiento,'YYYY-MM')='".$desde[0]."-".$m_inicio."' and tipomovimiento=2 and codcaja=".$this->request->codcaja." and estado=".(int)$this->request->estado)->result_array();

								$categorias[$i] = "Mes-".$m_inicio; $totales[$i] = (double)$total[0]["importe"];
								$m_inicio = $m_inicio + 1; $f_inicio = date("Y-m-d",strtotime($f_inicio."+ 1 month")); 
							}
						}else{
							$dias = $hasta[2] - $desde[2] + 1; $d_inicio = $desde[2]; $f_inicio = $this->request->fechadesde;
							for ($i=0; $i < $dias ; $i++) { 
								$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codkardex<>0 and fechamovimiento='".$f_inicio."' and tipomovimiento=2 and codcaja=".$this->request->codcaja." and estado=".(int)$this->request->estado)->result_array();

								$categorias[$i] = "Dia-".$d_inicio; $totales[$i] = (double)$total[0]["importe"];
								$d_inicio = $d_inicio + 1; $f_inicio = date("Y-m-d",strtotime($f_inicio."+ 1 days")); 
							}
						}
					}
				}
			}

			$data["categorias"] = $categorias; $data["totales"] = $totales;
			echo json_encode($data);
		}
	}

	// REPORTES PDF DE COMPRAS //

	function pdf_cabecera($titulo, $subtitulo){
		$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:15%">';
					$html .='<img src="'.base_url().'public/img/'.$_SESSION['netix_logo'].'" height="50">';
				$html .= '</th>';
				$html .= '<th style="width:55%">';
					$html .= '<h3>'.$_SESSION["netix_empresa"].'</h3>';
					$html .= '<h4>'.$titulo.'</h4>';
				$html .= '</th>';
				$html .= '<th style="width:30%">';
					$html .= '<h3>'.$_SESSION["netix_sucursal"].'</h3>';
					$html .= '<h4>COMPRAS REALIZADAS</h4>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table> <hr>';

		$html .= '<h4 align="center">'.$subtitulo.'</h4> <hr> <h6></h6>';
		return $html;
	}

	function pdf_imprimir($html,$titulo,$descarga){
		$this->load->library('Pdf');
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('WEB NETIX');
        $pdf->SetTitle($titulo);
        $pdf->SetSubject('WEB NETIX');

        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setPrintHeader(false);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage("A");
        $pdf->writeHTML($html, true, 0, true, 0);

        $nombre_archivo = utf8_decode($descarga);
        ob_end_clean();
        $pdf->Output($nombre_archivo, 'I');
	}

	function pdf_compras(){
		if (isset($_SESSION["netix_codusuario"])) {
			if ($_GET["datos"]) {
				$this->request = json_decode($_GET["datos"]);

				$estilo = "border-top:1px solid #D5D8DC; border-left:1px solid #D5D8DC; border-right:1px solid #D5D8DC;";
				$html = $this->pdf_cabecera("REPORTE DE COMPRAS","REPORTE GENERAL DE COMPRAS (".$this->request->fechadesde." HASTA ".$this->request->fechahasta.")");

				if ($this->request->codsucursal==0) {
					$sucursales = $this->db->query("select *from public.sucursales where estado=1")->result_array();
				}else{
					$sucursales = $this->db->query("select *from public.sucursales where codsucursal=".$this->request->codsucursal)->result_array();
				}

				foreach ($sucursales as $key => $value) {
					$html .= '<h4 align="center">SUCURSAL: '.$value["descripcion"].'</h4>';

					$lista = $this->db->query("select personas.documento,personas.razonsocial,personas.nombrecomercial,kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago,kardex.nrocomprobante,kardex.fechakardex,round(kardex.importe,2) as importe,kardex.estado,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.codmovimientotipo=2 and kardex.codsucursal=".$value["codsucursal"]." and kardex.fechakardex>='".$this->request->fechadesde."' and kardex.fechakardex<='".$this->request->fechahasta."' and kardex.estado=".(int)$this->request->estado)->result_array();

					$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:9px;">';
						$html .= '<tr>';
							$html .= '<th style="'.$estilo.' width:12%;"> <b>DOCUMENTO</b> </th>';
							$html .= '<th style="'.$estilo.' width:30%;"> <b>RAZON SOCIAL</b> </th>';
							$html .= '<th style="'.$estilo.' width:15%;"> <b>FECHA</b> </th>';
							$html .= '<th style="'.$estilo.' width:18%;"> <b>TIPO</b> </th>';
							$html .= '<th style="'.$estilo.' width:15%;"> <b>COMPROBANTE</b> </th>';
							$html .= '<th style="'.$estilo.' width:10%;"> <b>IMPORTE</b> </th>';
						$html .= '</tr>';
						foreach ($lista as $key => $value) {
							$html .= '<tr>';
								$html .= '<th style="'.$estilo.'"> '.$value["documento"].'</th>';
								$html .= '<th style="'.$estilo.'"> '.$value["razonsocial"].' </th>';
								$html .= '<th style="'.$estilo.'"> '.$value["fechakardex"].' </th>';
								$html .= '<th style="'.$estilo.'"> '.$value["tipo"].' </th>';
								$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante"].'-'.$value["nrocomprobante"].' </th>';
								$html .= '<th style="'.$estilo.'"> '.$value["importe"].' </th>';
							$html .= '</tr>';
						}
					$html .= '</table>';
				}

				$this->pdf_imprimir($html,"REPORTE DE COMPRAS","compras.pdf");
			}
		}
	}
}