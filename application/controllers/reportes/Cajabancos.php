<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cajabancos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$almacenes = $this->db->query("select *from almacen.almacenes where estado=1")->result_array();
			$usuarios = $this->db->query("select *from seguridad.usuarios where estado=1")->result_array();

			$this->load->view("reportes/cajabancos/index",compact("almacenes","usuarios"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function caja_detallado(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$consulta = "";
			if ($this->request->codpersona!=0) {
				$consulta = " m.codpersona=".$this->request->codpersona." and ";
			}
			if ($this->request->codusuario!=0) {
				$consulta .= " m.codusuario=".$this->request->codusuario." and ";
			}

			$lista = $this->db->query("select m.*,round(m.importe,2) as importe_r, personas.razonsocial, conceptos.descripcion as concepto from caja.movimientos as m inner join caja.movimientosdetalle as detalle on(m.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(m.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(m.codconcepto=conceptos.codconcepto) where m.fechamovimiento='".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.estado=1 and m.condicionpago=1 order by m.tipomovimiento")->result_array();

			$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();

			$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

			$ingresostotal = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento='".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();

			$egresostotal = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento='".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

			if ($ingresos[0]["importe"]>=$egresos[0]["importe"]) {
				$resultado_i = round($ingresos[0]["importe"] - $egresos[0]["importe"],2); $resultado_e = "";

				$total_i = (double)($resultado_i); $total_e = 0;
			}else{
				$resultado_e = round($egresos[0]["importe"] - $ingresos[0]["importe"],2); $resultado_i = "";
				$total_e = (double)($resultado_e); $total_i = 0;
			}
			$total = round($ingresostotal[0]["importe"] + $total_i,2) - round($egresostotal[0]["importe"] + $total_e,2);

			$data["lista"] = $lista;
			$data["ingresos"] = $resultado_i;
			$data["egresos"] = $resultado_e;
			$data["totalingresos"] = round($ingresostotal[0]["importe"] + $total_i,2);
			$data["totalegresos"] = round($egresostotal[0]["importe"] + $total_e,2);
			$data["total"] = round($total,2);
			echo json_encode($data);
		}
	}

	function reporte_movimientos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->caja==true && $this->request->banco==true) {
				$consulta = "";
			}else{
				if ($this->request->caja==false && $this->request->banco==false) {
					$consulta = "";
				}else{
					if ($this->request->caja==true) {
						$consulta = " detalle.codtipopago=1 and ";
					}
					if ($this->request->banco==true) {
						$consulta = " detalle.codtipopago<>1 and ";
					}
				}
			}

			if ($this->request->codpersona!=0) {
				$consulta = $consulta." movimientos.codpersona=".$this->request->codpersona." and";
			}
			if ($this->request->codusuario!=0) {
				$consulta = $consulta." movimientos.codusuario=".$this->request->codusuario." and";
			}

			$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();

			$ingresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=1 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();
			$egresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=2 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();

			if ($this->request->caja==false && $this->request->banco==false) {
				$lista = [];
			}
			$data["lista"] = $lista;
			$data["ingresos"] = $ingresos[0]["importe"];
			$data["egresos"] = $egresos[0]["importe"];
			$data["total"] = (double)round($ingresos[0]["importe"] - $egresos[0]["importe"],2);
			echo json_encode($data);
		}
	}

	function pdf_caja(){
		if (isset($_SESSION["netix_codusuario"])) {
			if ($_GET["datos"]) {
				$this->request = json_decode($_GET["datos"]);
				if ($this->request->reporte==1) {

					$consulta = "";
					if ($this->request->codpersona!=0) {
						$consulta = " m.codpersona=".$this->request->codpersona." and ";
					}
					if ($this->request->codusuario!=0) {
						$consulta = " m.codusuario=".$this->request->codusuario." and ";
					}
					$titulo = "REPORTE DE CAJA DETALLADO";

					$lista = $this->db->query("select m.*,round(m.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as m inner join caja.movimientosdetalle as detalle on(m.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(m.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(m.codconcepto=conceptos.codconcepto) where m.fechamovimiento='".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.estado=1 and m.condicionpago=1 order by m.tipomovimiento")->result_array();

					$ingreso = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();

					$egreso = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();
				}else{
					$titulo ="REPORTE DE MOVIMIENTOS DE CAJA";

					if ($this->request->caja==true && $this->request->banco==true) {
						$consulta = "";
					}else{
						if ($this->request->caja==false && $this->request->banco==false) {
							$consulta = "";
						}else{
							if ($this->request->caja==true) {
								$consulta = " detalle.codtipopago=1 and ";
							}
							if ($this->request->banco==true) {
								$consulta = " detalle.codtipopago<>1 and ";
							}
						}
					}

					if ($this->request->codpersona!=0) {
						$consulta = $consulta." movimientos.codpersona=".$this->request->codpersona." and";
					}

					$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();

					$ingresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=1 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();
					$egresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=2 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();

					if ($this->request->caja==false && $this->request->banco==false) {
						$lista = [];
					}
				}

				$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage('L','A4',0);
				$pdf->pdf_header($titulo,"");

				if ($this->request->reporte==1) {
					$columnas = array("N°","N° RECIBO","CONCEPTO","DOC.REFEREN","RAZON SOCIAL","REFERENCIA","INGRESOS","EGRESOS");
					$w = array(10,20,35,30,70,65,20,20); $pdf->pdf_tabla_head($columnas,$w,9);

					$item = 0; $ingresos = 0; $egresos = 0;

					if ($ingreso[0]["importe"]>=$egreso[0]["importe"]) {
						$resultado_i = round($ingreso[0]["importe"] - $egreso[0]["importe"],2); $resultado_e = "";
						$ingresos = $ingresos + $resultado_i;
					}else{
						$resultado_e = round($egreso[0]["importe"] - $ingreso[0]["importe"],2); $resultado_i = "";
						$egresos = $egresos + $resultado_e;
					}

					$pdf->Cell(230,5,"SALDO ANTERIOR",1,0,'R');
				    $pdf->Cell(20,5,$resultado_i,1,"R");
				    $pdf->Cell(20,5,$resultado_e,1,"R"); $pdf->Ln();

				    $pdf->SetWidths($w);
		            $pdf->SetLineHeight(5);
					$pdf->SetFont('Arial','',7);

					foreach ($lista as $key => $value) { 
						$item = $item + 1;

						$datos = array("0".$item);
						array_push($datos,$value["seriecomprobante"]."-".$value["nrocomprobante"]);
						array_push($datos,utf8_decode($value["concepto"]));
						array_push($datos,$value["seriecomprobante_ref"]."-".$value["nrocomprobante_ref"]);
						array_push($datos,utf8_decode($value["razonsocial"]));
						array_push($datos,utf8_decode($value["referencia"]));
						if ($value["tipomovimiento"]==1) {
							array_push($datos,number_format($value["importe_r"],2));
							array_push($datos,""); $ingresos = $ingresos + $value["importe_r"];
						}else{
							array_push($datos,""); $egresos = $egresos + $value["importe_r"];
							array_push($datos,number_format($value["importe_r"],2));
						}
						
		                $pdf->Row($datos);
					}
					$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(230,5,"TOTALES",1,0,'R');
				    $pdf->Cell(20,5,number_format($ingresos,2),1,"R");
				    $pdf->Cell(20,5,number_format($egresos,2),1,"R"); $pdf->Ln();

					$pdf->Cell(270,5,"SALDO (INGRESOS - EGRESOS): ".number_format($ingresos - $egresos,2),1,0,'R');
				}else{
					$columnas = array("FECHA","N° RECIBO","CONCEPTO","DOC.REFER","RAZON SOCIAL","REFERENCIA","INGRESOS","EGRESOS");
					$w = array(15,20,40,22,70,70,20,20); $pdf->pdf_tabla_head($columnas,$w,9);

					$ingresos = 0; $egresos = 0;

					$pdf->SetWidths($w);
		            $pdf->SetLineHeight(5);
					$pdf->SetFont('Arial','',7);

					foreach ($lista as $key => $value) {
						$datos = array($value["fechamovimiento"]);
						array_push($datos,$value["seriecomprobante"]."-".$value["nrocomprobante"]);
						array_push($datos,utf8_decode($value["concepto"]));
						array_push($datos,$value["seriecomprobante_ref"]."-".$value["nrocomprobante_ref"]);
						array_push($datos,utf8_decode($value["razonsocial"]));
						array_push($datos,utf8_decode($value["referencia"]));
						if ($value["tipomovimiento"]==1) {
							array_push($datos,number_format($value["importe_r"],2));
							array_push($datos,""); $ingresos = $ingresos + $value["importe_r"];
						}else{
							array_push($datos,""); $egresos = $egresos + $value["importe_r"];
							array_push($datos,number_format($value["importe_r"],2));
						}
		                $pdf->Row($datos);
					}
					$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(237,5,"TOTALES",1,0,'R');
				    $pdf->Cell(20,5,number_format($ingresos,2),1,"R");
				    $pdf->Cell(20,5,number_format($egresos,2),1,"R"); $pdf->Ln();

					$pdf->Cell(277,5,"SALDO (INGRESOS - EGRESOS): ".number_format($ingresos - $egresos,2),1,0,'R');
				}

				$pdf->SetTitle("Netix Peru - Reporte de Caja"); $pdf->Output();
			}
		}
	}

	function excel_caja(){
		if (isset($_SESSION["netix_codusuario"])) {
			if ($_GET["datos"]) {
				$this->request = json_decode($_GET["datos"]);

				if ($this->request->reporte==1) {
					$consulta = "";
					if ($this->request->codpersona!=0) {
						$consulta = " m.codpersona=".$this->request->codpersona." and ";
					}
					if ($this->request->codusuario!=0) {
						$consulta = " m.codusuario=".$this->request->codusuario." and ";
					}
					$titulo = "REPORTE DE CAJA DETALLADO";

					$lista = $this->db->query("select m.*,round(m.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as m inner join caja.movimientosdetalle as detalle on(m.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(m.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(m.codconcepto=conceptos.codconcepto) where m.fechamovimiento='".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.estado=1 and m.condicionpago=1 order by m.tipomovimiento")->result_array();

					$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();

					$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.fechamovimiento<'".$this->request->fecha_detallado."' and ".$consulta." m.codcaja=".$_SESSION["netix_codcaja"]." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();
				}else{
					if ($this->request->caja==true && $this->request->banco==true) {
						$consulta = "";
					}else{
						if ($this->request->caja==false && $this->request->banco==false) {
							$consulta = "";
						}else{
							if ($this->request->caja==true) {
								$consulta = " detalle.codtipopago=1 and ";
							}
							if ($this->request->banco==true) {
								$consulta = " detalle.codtipopago<>1 and ";
							}
						}
					}

					if ($this->request->codpersona!=0) {
						$consulta = $consulta." movimientos.codpersona=".$this->request->codpersona." and";
					}
					$titulo ="REPORTE DE MOVIMIENTOS DE CAJA";

					$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1 order by movimientos.codmovimiento desc")->result_array();

					$ingresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=1 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();
					$egresos = $this->db->query("select round(COALESCE(sum(movimientos.importe),0),2) as importe from caja.movimientos as movimientos inner join caja.movimientosdetalle as detalle on(movimientos.codmovimiento=detalle.codmovimiento) inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.tipomovimiento=2 and movimientos.fechamovimiento>='".$this->request->fecha_desde."' and movimientos.fechamovimiento<='".$this->request->fecha_hasta."' and movimientos.codcaja=".$_SESSION["netix_codcaja"]." and ".$consulta." movimientos.estado=1")->result_array();

					if ($this->request->caja==false && $this->request->banco==false) {
						$lista = [];
					}
				}
				$reporte = $this->request->reporte;

				$this->load->view("reportes/cajabancos/excel",compact("reporte","titulo","lista","ingresos","egresos"));
			}
		}
	}
}