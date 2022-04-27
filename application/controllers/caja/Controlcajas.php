<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Controlcajas extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {

				$caja = $this->Caja_model->netix_estadocaja();
				if (count($caja) == 0) {
					$saldocaja = $this->Caja_model->netix_saldocaja_general($_SESSION["netix_codcaja"]); 
					$saldobanco = $this->Caja_model->netix_saldobanco_general($_SESSION["netix_codcaja"]); 
					$this->load->view("caja/controlcajas/aperturar", compact("saldocaja","saldobanco"));
				}else{
					$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();
					foreach ($tipopagos as $key => $value) {
						$total = $this->Caja_model->netix_saldotipopago($_SESSION["netix_codcontroldiario"],$value["codtipopago"]);

						$tipopagos[$key]["transacciones"] = $total["transacciones"];
						$tipopagos[$key]["ingresos"] = $total["ingresos"];
						$tipopagos[$key]["egresos"] = $total["egresos"];
					}

					$comprobantes = $this->db->query("select *from caja.comprobantetipos where control=1 and estado=1 order by codcomprobantetipo")->result_array();
					foreach ($comprobantes as $key => $value) {
						$total = $this->Caja_model->netix_saldocomprobantes($_SESSION["netix_codcontroldiario"],$value["codcomprobantetipo"]);

						$comprobantes[$key]["ingresos"] = $total["ingresos"];
						$comprobantes[$key]["egresos"] = $total["egresos"];
					}

					$saldocaja = $this->Caja_model->netix_saldocaja_diario($_SESSION["netix_codcontroldiario"]); 
					$saldobanco = $this->Caja_model->netix_saldobanco_diario($_SESSION["netix_codcontroldiario"]); 

					$this->load->view("caja/controlcajas/index", compact("caja","tipopagos","comprobantes","saldocaja","saldobanco"));
				}
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function netix_graficocaja(){
		if ($this->input->is_ajax_request()) {
			$saldocaja = $this->Caja_model->netix_saldocaja_general($_SESSION["netix_codcaja"]); 
			$saldobanco = $this->Caja_model->netix_saldobanco_general($_SESSION["netix_codcaja"]); 

			$data = array();
			$data["ingresos"] = [(double)$saldocaja["ingresos"],(double)$saldobanco["ingresos"]];
			$data["egresos"] = [(double)$saldocaja["egresos"],(double)$saldobanco["egresos"]];
			echo json_encode($data);
		}
	}

	function netix_aperturar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if (isset($_SESSION["netix_usuario"])) {
				$this->db->trans_begin();

				$caja = $this->db->query("select max(codcontroldiario) as codcontroldiario from caja.controldiario where codcaja=".$_SESSION["netix_codcaja"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and fechaapertura <= '".$this->request->fecha."' and cerrado=0 and estado=1")->result_array();
				if ($caja[0]["codcontroldiario"]=="") {
					$saldoinicialcaja = $this->Caja_model->netix_saldocaja(0);
					$saldoinicialbanco = $this->Caja_model->netix_saldobanco(0);
				}else{
					$saldoinicialcaja = $this->Caja_model->netix_saldocaja($caja[0]["codcontroldiario"]);
					$saldoinicialbanco = $this->Caja_model->netix_saldobanco($caja[0]["codcontroldiario"]);
				}
				$fecha = explode("-", $this->request->fecha);
				$codigodiario = $fecha[2].$fecha[1].$fecha[0];

				$campos = ["codcaja","codusuario","codsucursal","saldoinicialcaja","saldoinicialbanco","fechaapertura","codigodiario","cerrado"];
				$valores = [
					(int)$_SESSION["netix_codcaja"],
					(int)$_SESSION["netix_codusuario"],
					(int)$_SESSION["netix_codsucursal"],
					((double)$saldoinicialcaja["total"] + (double)$saldoinicialcaja["saldoinicial"]),
					((double)$saldoinicialbanco["total"] + (double)$saldoinicialbanco["saldoinicial"]),
					$this->request->fecha, $codigodiario, 1
				];
				$estado = $this->Netix_model->netix_guardar("caja.controldiario", $campos, $valores);

				if ($estado == 1 ) {
					$this->db->trans_commit();
				}else{
					$this->db->trans_rollback(); $estado = 0;
				}
				echo $estado;
			}else{
				echo "e";
			}	
	    }else{
			$this->load->view("inicio/404");
		}
	}

	function netix_cerrar(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->db->trans_begin();

				$saldocaja = $this->Caja_model->netix_saldocaja($_SESSION["netix_codcontroldiario"]);
				$saldobanco = $this->Caja_model->netix_saldobanco($_SESSION["netix_codcontroldiario"]);

				$campos = ["codusuariocierre","fechacierre","saldofinalcaja","totalingresoscaja","totalegresoscaja","saldofinalbanco","totalingresosbanco","totalegresosbanco","cerrado"];
				$valores = [
					(int)$_SESSION["netix_codusuario"],date("Y-m-d"),
					(double)($saldocaja["total"]),
					(double)($saldocaja["ingresos"]),
					(double)($saldocaja["egresos"]),
					(double)($saldobanco["total"]),
					(double)($saldobanco["ingresos"]),
					(double)($saldobanco["egresos"]),0
				];
				$estado = $this->Netix_model->netix_editar("caja.controldiario", $campos, $valores, "codcontroldiario", $_SESSION["netix_codcontroldiario"]);
				if ($estado == 1 ) {
					$this->db->trans_commit();
				}else{
					$this->db->trans_rollback(); $estado = 0;
				}
				echo $estado;
			}else{
				echo "e";
			}
	    }else{
			$this->load->view("inicio/404");
		}
	}

	function netix_almacenes($codsucursal){
		if ($this->input->is_ajax_request()) {
			$almacenes = $this->db->query("select * from almacen.almacenes where codsucursal=".$codsucursal." and estado=1")->result_array();
			echo json_encode($almacenes);
		}else{
			$this->load->view("netix/404");
		}
	}

	function netix_cajas($codsucursal){
		if ($this->input->is_ajax_request()) {
			$cajas = $this->db->query("select * from caja.cajas where codsucursal=".$codsucursal." and estado=1")->result_array();
			echo json_encode($cajas);
		}else{
			$this->load->view("netix/404");
		}
	}

	function netix_seriescaja($codcomprobantetipo){
		if ($this->input->is_ajax_request()) {
			$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();
			$serie = "";
			if (count($series)==1) {
				$serie = $series[0]["seriecomprobante"];
			}
			$data = array();
			$data["series"] = $series;
			$data["serie"] = $serie;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function netix_correlativo($codcomprobantetipo,$seriecomprobante){
		if ($this->input->is_ajax_request()) {
			$comprobante = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and seriecomprobante='".$seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();
			if (count($comprobante)==0) {
				$nrocorrelativo = "00000000";
			}else{
				$nrocorrelativo = (int)($comprobante[0]["nrocorrelativo"]) + 1;
				$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
			}
			echo $nrocorrelativo;
		}
	}

	function netix_reaperturar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$empresa = $this->db->query("select *from public.empresas where claveseguridad='".$this->request->clave."'")->result_array();
			if (count($empresa) == 0) {
				echo "e"; exit();
			}
			if (isset($_SESSION["netix_codcontroldiario"]) && $_SESSION["netix_codcontroldiario"] > 0) {
				$this->db->trans_begin();

				$saldocaja = $this->Caja_model->netix_saldocaja($_SESSION["netix_codcontroldiario"]);
				$saldobanco = $this->Caja_model->netix_saldobanco($_SESSION["netix_codcontroldiario"]);

				$campos = ["codusuariocierre","fechacierre","saldofinalcaja","totalingresoscaja","totalegresoscaja","saldofinalbanco","totalingresosbanco","totalegresosbanco","cerrado"];
				$valores = [
					(int)$_SESSION["netix_codusuario"],date("Y-m-d"),
					(double)($saldocaja["total"]),
					(double)($saldocaja["ingresos"]),
					(double)($saldocaja["egresos"]),
					(double)($saldobanco["total"]),
					(double)($saldobanco["ingresos"]),
					(double)($saldobanco["egresos"]),0
				];
				$estado = $this->Netix_model->netix_editar("caja.controldiario", $campos, $valores, "codcontroldiario", $_SESSION["netix_codcontroldiario"]);
				if ($estado == 1 ) {
					$this->db->trans_commit();
				}else{
					$this->db->trans_rollback(); $estado = 0;
				}
			}

			$reapertura = $this->db->query("select *from caja.controldiario where codcontroldiario=".$this->request->codcontroldiario)->result_array();
			$caja = $this->db->query("select max(codcontroldiario) as codcontroldiario from caja.controldiario where codcaja=".$_SESSION["netix_codcaja"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and fechaapertura < '".$reapertura[0]["fechaapertura"]."' and cerrado=0 and estado=1")->result_array();
			if ($caja[0]["codcontroldiario"]=="") {
				$saldoinicialcaja = $this->Caja_model->netix_saldocaja(0);
				$saldoinicialbanco = $this->Caja_model->netix_saldobanco(0);
			}else{
				$saldoinicialcaja = $this->Caja_model->netix_saldocaja($caja[0]["codcontroldiario"]);
				$saldoinicialbanco = $this->Caja_model->netix_saldobanco($caja[0]["codcontroldiario"]);
			}

			$campos = ["saldoinicialcaja","saldoinicialbanco","cerrado"];
			$valores = [
				((double)$saldoinicialcaja["total"] + (double)$saldoinicialcaja["saldoinicial"]),
				((double)$saldoinicialbanco["total"] + (double)$saldoinicialbanco["saldoinicial"]), 1
			];
			$estado = $this->Netix_model->netix_editar("caja.controldiario", $campos, $valores, "codcontroldiario", $this->request->codcontroldiario);
			
			echo $estado;
		}
	}

	// FUNCIONES DE REPORTES DE CAJA //

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
					$html .= '<h4>'.$_SESSION["netix_caja"].'</h4>';
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
        $pdf->Output($nombre_archivo, 'I');
	}

	function pdf_arqueo($fecha){
		$estilo = "border-top:1px solid #D5D8DC; border-left:1px solid #D5D8DC; border-right:1px solid #D5D8DC;";
		$html = $this->pdf_cabecera("ARQUEO DE CAJA","FECHA DEL ARQUEO DE CAJA (".$fecha.")");

		$sesiones = $this->db->query("select *from caja.controldiario where fechaapertura='".$fecha."' and codcaja=".$_SESSION["netix_codcaja"]." and estado=1 order by codcontroldiario desc")->result_array();
		$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();

		foreach ($sesiones as $key => $value) {
			$html .= '<h3>SESION DE CAJA 0000'.$value["codcontroldiario"].' - USUARIO: '.$_SESSION["netix_usuario"].'</h3>';
			$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:11px;">';
				$html .= '<tr>';
					$html .= '<th style="'.$estilo.' width:30%;"> <b>SALDO INICIAL</b> </th>';
					$html .= '<th style="'.$estilo.' width:20%;"> <b>EN CAJA:</b> </th>';
					$html .= '<th style="'.$estilo.' width:15%;"> <b>S/. '.$value["saldoinicialcaja"].'</b> </th>';
					$html .= '<th style="'.$estilo.' width:15%;"> <b>EN BANCO:</b> </th>';
					$html .= '<th style="'.$estilo.' width:20%;"> <b>S/. '.$value["saldoinicialbanco"].'</b> </th>';
				$html .= '</tr>';

				$html .= '<tr>';
					$html .= '<th style="'.$estilo.' width:30%;"> <b>FORMA DE PAGO</b> </th>';
					$html .= '<th style="'.$estilo.' width:20%;"> <b>TRANSACCIONES</b> </th>';
					$html .= '<th style="'.$estilo.' width:15%;"> <b>INGRESOS</b> </th>';
					$html .= '<th style="'.$estilo.' width:15%;"> <b>EGRESOS</b> </th>';
					$html .= '<th style="'.$estilo.' width:20%;"> <b>S/. TOTAL</b> </th>';
				$html .= '</tr>';

				$saldocaja = 0;
				foreach ($tipopagos as $key => $val) {
					$total = $this->Caja_model->netix_saldotipopago($value["codcontroldiario"],$val["codtipopago"]);
					if ($val["codtipopago"]==1) {
						$saldocaja = $total["ingresos"] - $total["egresos"];
					}
					$html .= '<tr>';
						$html .= '<th style="'.$estilo.'"> '.$val["descripcion"].'</th>';
						$html .= '<th style="'.$estilo.'"> '.$total["transacciones"].' </th>';
						$html .= '<th style="'.$estilo.'"> '.$total["ingresos"].' </th>';
						$html .= '<th style="'.$estilo.'"> '.$total["egresos"].' </th>';
						$html .= '<th style="'.$estilo.'"> S/. '.($total["ingresos"] - $total["egresos"]).' </th>';
					$html .= '</tr>';
				}
			$html .= '</table>';
			$html .= '<h3 align="center">MONTO DE CIERRE DE CAJA EFECTIVO: S/. '.number_format(($saldocaja + $value["saldoinicialcaja"]),2).'</h3> <hr>';
		}

		$this->pdf_imprimir($html,"ARQUEO DE CAJA","arqueo.pdf");
	}

	function pdf_movimientos($desde,$hasta){
		$estilo = "border-top:1px solid #D5D8DC; border-left:1px solid #D5D8DC; border-right:1px solid #D5D8DC;";
		$html = $this->pdf_cabecera("REPORTE DE MOVIMIENTOS DE CAJA","REPORTE DE MOVIMIENTO DESDE (".$desde." HASTA ".$hasta.")");

		$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.fechamovimiento>='".$desde."' and movimientos.fechamovimiento<='".$hasta."' and movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();

		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:9px;">';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:9%;"> <b>FECHA</b> </th>';
				$html .= '<th style="'.$estilo.' width:12%;"> <b>N° RECIBO</b> </th>';
				$html .= '<th style="'.$estilo.' width:22%;"> <b>CONCEPTO CAJA</b> </th>';
				$html .= '<th style="'.$estilo.' width:12%;"> <b>DOC. REF.</b> </th>';
				$html .= '<th style="'.$estilo.' width:25%;"> <b>RAZÓN SOCIAL</b> </th>';
				$html .= '<th style="'.$estilo.' width:8%;"> <b>TIPO</b> </th>';
				$html .= '<th style="'.$estilo.' width:12%;"> <b>S/. IMPORTE</b> </th>';
			$html .= '</tr>';

			foreach ($lista as $value) {
				$html .= '<tr>';
					$html .= '<th style="'.$estilo.'"> '.$value["fechamovimiento"].'</th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante"].'-'.$value["nrocomprobante"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["concepto"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante_ref"].'-'.$value["nrocomprobante_ref"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["razonsocial"].' </th>';
					if ($value["tipomovimiento"]==1) {
						$html .= '<th style="'.$estilo.'"> INGRESO </th>';
					}else{
						$html .= '<th style="'.$estilo.'"> EGRESO </th>';
					}
					$html .= '<th style="'.$estilo.'"> S/. '.$value["importe_r"].' </th>';
				$html .= '</tr>';
			}
		$html .= '</table>';
		
		$this->pdf_imprimir($html,"MOVIMIENTOS DE CAJA","movimientos.pdf");
	}

	function pdf_arqueo_caja($codcontroldiario){
		$estilo = "border-top:1px solid #D5D8DC; border-left:1px solid #D5D8DC; border-right:1px solid #D5D8DC;";

		$sesion = $this->db->query("select *from caja.controldiario where codcontroldiario=".$codcontroldiario)->result_array();
		$html = $this->pdf_cabecera("ARQUEO DE CAJA","CAJA NUMERO 000".$sesion[0]["codcontroldiario"]." - FECHA: ".$sesion[0]["fechaapertura"]);

		$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();
		$caja = $this->db->query("select *from caja.controldiario where codcontroldiario=".$codcontroldiario)->result_array();

		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:11px;">';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>SALDO INICIAL</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>EN CAJA:</b> </th>';
				$html .= '<th style="'.$estilo.' width:15%;"> <b>S/. '.round($caja[0]["saldoinicialcaja"],2).'</b> </th>';
				$html .= '<th style="'.$estilo.' width:15%;"> <b>EN BANCO:</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>S/. '.round($caja[0]["saldoinicialbanco"],2).'</b> </th>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>FORMA DE PAGO</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>TRANSACCIONES</b> </th>';
				$html .= '<th style="'.$estilo.' width:15%;"> <b>INGRESOS</b> </th>';
				$html .= '<th style="'.$estilo.' width:15%;"> <b>EGRESOS</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>S/. TOTAL</b> </th>';
			$html .= '</tr>';

			$transacciones = 0; $ingresos = 0; $egresos = 0; $utilidad = 0; $saldocaja = 0;
			foreach ($tipopagos as $key => $val) {
				$total = $this->Caja_model->netix_saldotipopago($codcontroldiario,$val["codtipopago"]);

				if ($val["codtipopago"]==1) {
					$saldocaja = $total["ingresos"] - $total["egresos"];
				}

				$transacciones = $transacciones + $total["transacciones"];
				$ingresos = $ingresos + $total["ingresos"];
				$egresos = $egresos + $total["egresos"];
				$utilidad = $utilidad + ($total["ingresos"] - $total["egresos"]);

				$html .= '<tr>';
					$html .= '<th style="'.$estilo.'"> <b>'.$val["descripcion"].'</b> </th>';
					$html .= '<th style="'.$estilo.'"> '.$total["transacciones"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$total["ingresos"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$total["egresos"].' </th>';
					$html .= '<th style="'.$estilo.'"> <b>S/. '.($total["ingresos"] - $total["egresos"]).'</b> </th>';
				$html .= '</tr>';
			}
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.'"> <b>TOTALES</b> </th>';
				$html .= '<th style="'.$estilo.'"> '.$transacciones.' </th>';
				$html .= '<th style="'.$estilo.'"> '.$ingresos.' </th>';
				$html .= '<th style="'.$estilo.'"> '.$egresos.' </th>';
				$html .= '<th style="'.$estilo.'"> <b>S/. '.$utilidad.'</b> </th>';
			$html .= '</tr>';
		$html .= '</table>';

		$otros = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex=0 and m.tipomovimiento=1 and m.estado=1")->result_array();

		$html .= '<h6></h6> <table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:11px;">';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:70%;"> <b>OTROS INGRESOS</b> </th>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>'.number_format($otros[0]["importe"],2).'</b> </th>';
			$html .= '</tr>';

			$venta = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex>0 and m.tipomovimiento=1 and m.estado=1")->result_array();
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:70%;"> <b>INGRESOS POR VENTAS</b> </th>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>'.number_format($venta[0]["importe"],2).'</b> </th>';
			$html .= '</tr>';

			$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and m.estado=1")->result_array();
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:70%;"> <b>TOTAL EGRESOS</b> </th>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>'.number_format($egresos[0]["importe"],2).'</b> </th>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:70%;"> <b>SALDO TOTAL</b> </th>';
				$html .= '<th style="'.$estilo.' width:30%;"> <b>'.number_format($otros[0]["importe"] + $venta[0]["importe"] - $egresos[0]["importe"],2).'</b> </th>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<br> <h3 align="center" style="color:red;">TOTAL EN CAJA EFECTIVO (CAJA + SALDO INICIAL): S/. '.number_format( ($saldocaja + $caja[0]["saldoinicialcaja"]),2).' </h3>';

		$html .= '<br> <h4 align="center">OPERACIONES REALIZADAS (CAJA APERTURADA N° 000'.$codcontroldiario.') (FECHA APERTURADA: '.$sesion[0]["fechaapertura"].')</h4> <hr> <h6></h6>';

		$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$codcontroldiario." and movimientos.tipomovimiento=1 and movimientos.condicionpago=1 and movimientos.estado=1 order by movimientos.codmovimiento asc")->result_array();

		$html .= '<h4 align="center">LISTA DE INGRESOS</h4>';
		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:8px;">';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:8%;"> <b>FECHA</b> </th>';
				$html .= '<th style="'.$estilo.' width:11%;"> <b>N° RECIBO</b> </th>';
				$html .= '<th style="'.$estilo.' width:19%;"> <b>CONCEPTO CAJA</b> </th>';
				$html .= '<th style="'.$estilo.' width:12%;"> <b>DOC. REF.</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>RAZÓN SOCIAL</b> </th>';
				$html .= '<th style="'.$estilo.' width:23%;"> <b>REFERENCIA</b> </th>';
				$html .= '<th style="'.$estilo.' width:10%;"> <b>S/. IMPORTE</b> </th>';
			$html .= '</tr>';

			$ingresos = 0;
			foreach ($lista as $value) { $ingresos = $ingresos + $value["importe_r"]; 
				$html .= '<tr>';
					$html .= '<th style="'.$estilo.'"> '.$value["fechamovimiento"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante"].'-'.$value["nrocomprobante"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["concepto"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante_ref"].'-'.$value["nrocomprobante_ref"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["razonsocial"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["referencia"].' </th>';
					$html .= '<th style="'.$estilo.'"> S/. '.$value["importe_r"].' </th>';
				$html .= '</tr>';
			}
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.'" colspan="6">TOTAL INGRESOS</th>';
				$html .= '<th style="'.$estilo.'"> S/. '.number_format($ingresos,2).'</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$codcontroldiario." and movimientos.tipomovimiento=2 and movimientos.condicionpago=1 and movimientos.estado=1 order by movimientos.codmovimiento asc")->result_array();

		$html .= '<br> <h4 align="center">LISTA DE EGRESOS</h4>';
		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #D5D8DC;font-size:8px;">';
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.' width:8%;"> <b>FECHA</b> </th>';
				$html .= '<th style="'.$estilo.' width:11%;"> <b>N° RECIBO</b> </th>';
				$html .= '<th style="'.$estilo.' width:19%;"> <b>CONCEPTO CAJA</b> </th>';
				$html .= '<th style="'.$estilo.' width:12%;"> <b>DOC. REF.</b> </th>';
				$html .= '<th style="'.$estilo.' width:20%;"> <b>RAZÓN SOCIAL</b> </th>';
				$html .= '<th style="'.$estilo.' width:23%;"> <b>REFERENCIA</b> </th>';
				$html .= '<th style="'.$estilo.' width:10%;"> <b>S/. IMPORTE</b> </th>';
			$html .= '</tr>';

			$egresos = 0;
			foreach ($lista as $value) { $egresos = $egresos + $value["importe_r"];
				$html .= '<tr>';
					$html .= '<th style="'.$estilo.'"> '.$value["fechamovimiento"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante"].'-'.$value["nrocomprobante"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["concepto"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["seriecomprobante_ref"].'-'.$value["nrocomprobante_ref"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["razonsocial"].' </th>';
					$html .= '<th style="'.$estilo.'"> '.$value["referencia"].' </th>';
					$html .= '<th style="'.$estilo.'"> S/. '.$value["importe_r"].' </th>';
				$html .= '</tr>';
			}
			$html .= '<tr>';
				$html .= '<th style="'.$estilo.'" colspan="6">TOTAL EGRESOS</th>';
				$html .= '<th style="'.$estilo.'"> S/. '.number_format($egresos,2).'</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$this->pdf_imprimir($html,"ARQUEO DE CAJA","arqueo.pdf");
	}

	function pdf_arqueo_excel($codcontroldiario){
		if ($codcontroldiario) {
			$ingresos = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$codcontroldiario." and movimientos.tipomovimiento=1 and movimientos.condicionpago=1 and movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();
			$egresos = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$codcontroldiario." and movimientos.tipomovimiento=2 and movimientos.condicionpago=1 and movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();
			$this->load->view("caja/controlcajas/excel", compact("ingresos", "egresos"));
		}
	}

	// FUNCIONES EXTRAS DE CAJA //

	function actualizar_caja(){
		$movimientos = $this->db->query("select codmovimiento,importe from caja.movimientos order by codmovimiento")->result_array();
		foreach ($movimientos as $key => $value) {
			$pago = $this->db->query("select importeentregado from caja.movimientosdetalle where codmovimiento=".$value["codmovimiento"])->result_array();

			if (count($pago)>0) {
				$campos = ["importe","vuelto"];
				$valores = [
					(double)($value["importe"]),
					(double)($pago[0]["importeentregado"] - $value["importe"]),
				];
				$estado = $this->Netix_model->netix_editar("caja.movimientosdetalle", $campos, $valores, "codmovimiento", $value["codmovimiento"]);
			}else{
				$estado = 1;
			}
		}
		echo $estado;
	}

	function actualizar_controldiario(){
		$control = $this->db->query("select * from caja.controldiario order by codcontroldiario")->result_array();
		foreach ($control as $key => $value) {
			$codcontroldiario = $value["codcontroldiario"];

			$controlanterior = $this->db->query("select COALESCE(max(codcontroldiario),0) as codcontroldiario from caja.controldiario where codcaja=".$value["codcaja"]." and codcontroldiario<".$value["codcontroldiario"])->result_array();

			$inicial = $this->db->query("select * from caja.controldiario where codcontroldiario=".$controlanterior[0]["codcontroldiario"])->result_array();

			if (count($inicial)>0) {
				$inicialcaja = $inicial[0]["saldoinicialcaja"];
				$inicialbanco = $inicial[0]["saldoinicialbanco"];
				$finalcaja = $inicial[0]["saldofinalcaja"];
				$finalbanco = $inicial[0]["saldofinalbanco"];
			}else{
				$inicialcaja = 0;
				$inicialbanco = 0;
				$finalcaja = 0;
				$finalbanco = 0;
			}

			$ingresos_caja = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();
			$egresos_caja = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

			$ingresos_banco = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago<>1 and md.codtipopago<>2) and m.estado=1")->result_array();
			$egresos_banco = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago<>1 and md.codtipopago<>3) and m.estado=1")->result_array();

			echo 
				"CONTROL DE CAJA: ".$value["codcontroldiario"].
				"<br> SALDO INICIAL CAJA: ".($inicialcaja + $finalcaja).
				"<br> SALDO FINAL CAJA: ".($ingresos_caja[0]["importe"] - $egresos_caja[0]["importe"]).
				"<br> TOTAL INGRESOS CAJA: ".($ingresos_caja[0]["importe"]).
				"<br> TOTAL EGRESOS CAJA: ".($egresos_caja[0]["importe"]).

				"<br> SALDO INICIAL BANCO: ".($inicialbanco + $finalbanco).
				"<br> SALDO FINAL BANCO: ".($ingresos_banco[0]["importe"] - $egresos_banco[0]["importe"]).
				"<br> TOTAL INGRESOS BANCO: ".($ingresos_banco[0]["importe"]).
				"<br> TOTAL EGRESOS BANCO: ".($egresos_banco[0]["importe"]).
				"<br> <br>";

			$campos = ["saldoinicialcaja","saldofinalcaja","totalingresoscaja","totalegresoscaja","saldoinicialbanco","saldofinalbanco","totalingresosbanco","totalegresosbanco"];
			$valores = [
				(double)($inicialcaja + $finalcaja),(double)($ingresos_caja[0]["importe"] - $egresos_caja[0]["importe"]),(double)$ingresos_caja[0]["importe"],(double)$egresos_caja[0]["importe"],
				(double)($inicialbanco + $finalbanco),(double)($ingresos_banco[0]["importe"] - $egresos_banco[0]["importe"]),(double)$ingresos_banco[0]["importe"],(double)$egresos_banco[0]["importe"]
			];
			$estado = $this->Netix_model->netix_editar("caja.controldiario", $campos, $valores, "codcontroldiario", $value["codcontroldiario"]);

		}
	}

	function generar_cajadiario(){
		$cajas = $this->db->query("select codcaja from caja.cajas where codcaja=2 or codcaja=7")->result_array();

		$controles = $this->db->query("select codcontroldiario,codusuario,codcaja,fechaapertura from caja.controldiario where codcaja=2 or codcaja=7")->result_array();

		foreach ($controles as $key => $value) {
			$campos = ["codusuariocierre","fechacierre","cerrado"];
			$valores = [
				(int)$value["codusuario"],$value["fechaapertura"],0
			];
			$estado = $this->Netix_model->netix_editar("caja.controldiario", $campos, $valores, "codcontroldiario", $value["codcontroldiario"]);
		}

		foreach ($cajas as $v) {
			echo "caja ".$v["codcaja"]."<br>";
			$movimientos = $this->db->query("select distinct(fechamovimiento) from caja.movimientos where codcaja=".$v["codcaja"]." order by fechamovimiento")->result_array();
			if (count($movimientos)>1) {
				foreach ($movimientos as $key => $value) {
					echo $value["fechamovimiento"]."<br>";
					$controldiario = $this->db->query("select *from caja.controldiario where fechaapertura='".$value["fechamovimiento"]."' and codcaja=".$v["codcaja"])->result_array();

					if (count($controldiario)==0) {
						$codigo = explode("-",$value["fechamovimiento"]);
						$codigodiario = $codigo[2].$codigo[1].$codigo[0];

						$info = $this->db->query("select *from caja.controldiario where codcaja=".$v["codcaja"]." limit 1")->result_array();

						$campos = ["codcaja","codusuario","codusuariocierre","codsucursal","fechaapertura","fechacierre","codigodiario","cerrado"];
						$valores = [
							(int)$info[0]["codcaja"],
							(int)$info[0]["codusuario"],(int)$info[0]["codusuario"],
							(int)$info[0]["codsucursal"],
							$value["fechamovimiento"],$value["fechamovimiento"],$codigodiario,0
						];
						$codcontroldiario = $this->Netix_model->netix_guardar("caja.controldiario", $campos, $valores,"true");
						$codcaja = $info[0]["codcaja"];
					}else{
						$codcontroldiario = $controldiario[0]["codcontroldiario"];
						$codcaja = $controldiario[0]["codcaja"];
					}

					$data = array("codcontroldiario" => $codcontroldiario);
					$this->db->where("codcaja", $codcaja);
					$this->db->where("fechamovimiento", $value["fechamovimiento"]);
					$estado = $this->db->update("caja.movimientos",$data);
				}
			}
		}
	}

	function actualizar_movimientos(){
		$lista = $this->db->query("select *from caja.movimientos order by codmovimiento asc")->result_array();
		foreach ($lista as $key => $value) {

			if ($value["tipomovimiento"]==1) {
				$codcomprobantetipo = 1; $seriecomprobante = "RI01";
			}else{
				$codcomprobantetipo = 2; $seriecomprobante = "RE01";
			}

			$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and seriecomprobante='".$seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

			$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
			$data = array(
				"nrocorrelativo" => $nrocorrelativo
			);
			$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
			$this->db->where("codcomprobantetipo", $codcomprobantetipo);
			$this->db->where("seriecomprobante", $seriecomprobante);
			$estado = $this->db->update("caja.comprobantes", $data);

			$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);

			$data = array(
				"codcomprobantetipo" => $codcomprobantetipo,
				"seriecomprobante" => $seriecomprobante,
				"nrocomprobante" => $nrocorrelativo
			);
			$this->db->where("codmovimiento", $value["codmovimiento"]);
			$estado = $this->db->update("caja.movimientos",$data);
		}
		echo $estado;
	}

	function actualizar_creditos(){
		$lista = $this->db->query("select *from kardex.creditos")->result_array();
		foreach ($lista as $key => $value) {
			if ($value["tipo"]==1) {
				$tipomovimiento = 2;
			}else{
				$tipomovimiento = 1;
			}

			$data = array(
				"tipomovimiento" => $tipomovimiento
			);
			$this->db->where("codmovimiento", $value["codmovimiento"]);
			$estado = $this->db->update("caja.movimientos",$data);
		}
		echo $estado;
	}

	function actualizar_fechas(){
		$lista = $this->db->query("select kardex.fechakardex,kardex.codkardex from kardex.kardex")->result_array();
		foreach ($lista as $key => $value) {
			$data = array(
				"fechacomprobante" => $value["fechakardex"]
			);
			$this->db->where("codkardex", $value["codkardex"]);
			$estado = $this->db->update("kardex.kardex",$data);
		}
		echo $estado;
	}
}