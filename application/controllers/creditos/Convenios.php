<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Convenios extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {

				$empresas_convenio = $this->db->query("select distinct(creditos.codpersona_convenio) as codpersona, personas.razonsocial from kardex.creditos as creditos inner join public.personas as personas on (creditos.codpersona_convenio=personas.codpersona) where creditos.estado=1 and creditos.codpersona_convenio > 0 order by creditos.codpersona_convenio asc")->result_array();

				$this->load->view("creditos/convenios/index", compact("empresas_convenio"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function lista(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$limit = 10; $offset = $this->request->pagina * $limit - $limit;

			$filtro = "creditos.codpersona_convenio > 0 and ";
			if ($this->request->codpersona != 0) {
				$filtro = "creditos.codpersona_convenio = ".$this->request->codpersona." and ";
			}
			$lista = $this->db->query("select personas.razonsocial,personas.documento,creditos.codsucursal,creditos.codcredito,creditos.codkardex, creditos.fechacredito, creditos.fechavencimiento, round(creditos.importe,2) as importe, round(creditos.interes,2) as interes, round(creditos.saldo,2) as saldo, creditos.tipo, pconvenio.razonsocial as convenio from kardex.creditos as creditos inner join public.personas as personas on (creditos.codpersona=personas.codpersona) inner join public.personas as pconvenio on (creditos.codpersona_convenio=pconvenio.codpersona) where ".$filtro." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and creditos.estado=1 order by creditos.codcredito desc offset ".$offset." limit ".$limit)->result_array();
			foreach ($lista as $key => $value) {
				$kardex = $this->db->query("select seriecomprobante,nrocomprobante from kardex.kardex where codkardex=".(int)$value["codkardex"])->result_array();
				if (count($kardex)>0) {
					$lista[$key]["comprobante"] = $kardex[0]["seriecomprobante"]."-".$kardex[0]["nrocomprobante"];
				}else{
					$lista[$key]["comprobante"] = "SIN - DOCUMENTO";
				}
			}

			$total = $this->db->query("select count(*) as total from kardex.creditos as creditos inner join public.personas as personas on (creditos.codpersona=personas.codpersona) inner join public.personas as pconvenio on (creditos.codpersona_convenio=pconvenio.codpersona) where ".$filtro."  (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and creditos.estado=1")->result_array();

			$paginas = floor($total[0]["total"] / $limit);
			if ( ($total[0]["total"] % $limit)!=0 ) {
				$paginas = $paginas + 1;
			}

			$paginacion = array();
			$paginacion["total"] = $total[0]["total"];
			$paginacion["actual"] = $this->request->pagina;
			$paginacion["ultima"] = $paginas;
			$paginacion["desde"] = $offset;
			$paginacion["hasta"] = $offset + $limit;

			echo json_encode(array("lista" => $lista,"paginacion" => $paginacion));
		}else{
			$this->load->view("netix/404");
		}
	}

	public function pdf_resumen($codpersona_convenio){
		if (isset($codpersona_convenio)) {
			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header_titulo("CREDITOS POR CONVENIO RESUMEN");

			$persona = $this->db->query("select *from public.personas where codpersona=".$codpersona_convenio)->result_array();
			$pdf->SetFont('Arial','B',9); $pdf->SetFillColor(230,230,230);
	        $pdf->Cell(0,7,utf8_decode("RESPONSABLE: ".$persona[0]["razonsocial"]),1,1,'L',True); $pdf->Ln();

			$columnas = array("N°","RUC / DNI","RAZON SOCIAL","S/ TOTAL","S/ SALDO");
			$w = array(10,20,110,25,25); $pdf->pdf_tabla_head($columnas,$w,8);

			$lista = $this->db->query("select personas.codpersona, personas.razonsocial, personas.documento, sum(creditos.total) as total, sum(creditos.saldo) as saldo from kardex.creditos as creditos inner join public.personas as personas on (creditos.codpersona=personas.codpersona) where creditos.codpersona_convenio = ".$codpersona_convenio." and creditos.estado=1 group by personas.codpersona, personas.razonsocial, personas.documento")->result_array();

			$pdf->SetWidths(array(10,20,110,25,25)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',8);
			$item = 0; $total = 0; $saldo = 0;
			foreach ($lista as $key => $value) {
				$item = $item + 1; $total = $total + $value["total"]; $saldo = $saldo + $value["saldo"];

				$datos = array($item);
				array_push($datos,$value["documento"]);
				array_push($datos,$value["razonsocial"]);
				array_push($datos,number_format($value["total"],2));
				array_push($datos,number_format($value["saldo"],2));
                $pdf->Row($datos);
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(140,5,"TOTALES",1,0,'R');
		    $pdf->Cell(25,5,number_format($total,2),1,"R");
		    $pdf->Cell(25,5,number_format($saldo,2),1,"R");

			$pdf->SetTitle("Creditos por Convenio Resumen"); $pdf->Output();
		}
	}

	public function pdf_detallado($codpersona_convenio){
		if (isset($codpersona_convenio)) {
			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header_titulo("CREDITOS POR CONVENIO DETALLE");

			$persona = $this->db->query("select *from public.personas where codpersona=".$codpersona_convenio)->result_array();
			$pdf->SetFont('Arial','B',9); $pdf->SetFillColor(230,230,230);
	        $pdf->Cell(0,7,utf8_decode("RESPONSABLE: ".$persona[0]["razonsocial"]),1,1,'L',True); $pdf->Ln();

			$columnas = array("FECHA","CONCEPTO","COMPROBANTE","REFERENCIA","TOTAL","SALDO");
			$w = array(15,20,25,90,20,20); $pdf->pdf_tabla_head($columnas,$w,8);

			$lista = $this->db->query("select personas.codpersona, personas.razonsocial, personas.documento, sum(creditos.total) as total, sum(creditos.saldo) as saldo from kardex.creditos as creditos inner join public.personas as personas on (creditos.codpersona=personas.codpersona) where creditos.codpersona_convenio = ".$codpersona_convenio." and creditos.estado=1 and creditos.tipo=1 group by personas.codpersona, personas.razonsocial, personas.documento")->result_array();

			$item = 0; $total = 0; $saldo = 0;
			foreach ($lista as $key => $value) {
				$pdf->SetWidths(array(150,20,20)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','B',8);
				$total = $total + $value["total"]; $saldo = $saldo + $value["saldo"];

				$datos = array("SOCIO: ".$value["documento"]." ".$value["razonsocial"]);
				array_push($datos,number_format($value["total"],2));
				array_push($datos,number_format($value["saldo"],2));
                $pdf->Row($datos);

                $detalle = $this->db->query("select c.fechacredito as fecha, round(c.importe,2) as importe,round(c.interes) as interes, round(c.total,2) as total, round(c.total - c.saldo,2) as cobranza, round(c.saldo) as saldo, (select COALESCE(k.seriecomprobante || '-' || k.nrocomprobante,'') from kardex.kardex as k where c.codkardex=k.codkardex) as comprobante, (select COALESCE(string_agg(p.descripcion::text || ' || CANT: ' || round(kd.cantidad,2)::text || ' || P.U: ' || round(kd.preciounitario,2)::text,',') ,c.referencia) from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) where c.codkardex=k.codkardex) as referencia from kardex.creditos as c where c.codpersona_convenio=".$codpersona_convenio." and c.tipo=1 and c.codpersona=".$value["codpersona"]." and c.estado=1")->result_array();

                $pdf->SetWidths(array(15,20,25,90,20,20)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);
				foreach ($detalle as $k => $v) {
					$datos = array($v["fecha"]);
					array_push($datos,"VENTA/CRED");
					array_push($datos,$v["comprobante"]);
					array_push($datos,$v["referencia"]);
					array_push($datos,number_format($v["total"],2));
					array_push($datos,number_format($v["saldo"],2));
	                $pdf->Row($datos);
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(150,5,"TOTALES",1,0,'R');
		    $pdf->Cell(20,5,number_format($total,2),1,"R");
		    $pdf->Cell(20,5,number_format($saldo,2),1,"R");

			$pdf->SetTitle("Creditos por Convenio Detalle"); $pdf->Output();
		}
	}
}