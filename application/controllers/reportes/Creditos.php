<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Creditos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Creditos_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$this->load->view("reportes/creditos/index");
		}else{
			$this->load->view("netix/404");
		}
	}

	public function ver_creditos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->saldos == 0) {
				if($this->request->codpersona == 0){
					$socios = $this->Creditos_model->socios_creditos($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo);
				}else{
					$socios = $this->db->query("select codpersona,razonsocial,documento,direccion,telefono from personas where codpersona=".$this->request->codpersona)->result_array();
				}

				if($this->request->tipo_consulta == 1){
					if ($this->request->mostrar==1) {
						foreach ($socios as $key => $value) {
							$anterior = $this->Creditos_model->estado_cuenta_anterior($this->request->fecha_desde,$this->request->tipo,$value["codpersona"]);
							$movimientos = $this->Creditos_model->estado_cuenta_cliente($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);

							$saldo = $anterior["saldo"]; $abono = 0; $cargo = 0;
							foreach ($movimientos as $k => $v) {
								$saldo = $saldo + $v["cargo"] - $v["abono"];
								$movimientos[$k]["saldo"] = number_format($saldo,2);
								$cargo = $cargo + $v["cargo"]; $abono = $abono + $v["abono"];
							}
							$socios[$key]["anterior"] = number_format($anterior["saldo"],2);
							$socios[$key]["movimientos"] = $movimientos;
							$socios[$key]["cargo"] = number_format($cargo,2);
							$socios[$key]["abono"] = number_format($abono,2);
							$socios[$key]["saldo"] = number_format($anterior["saldo"] + $cargo - $abono,2);
						}
					}else{
						foreach ($socios as $key => $value) {
							$creditos = $this->Creditos_model->estado_cuenta_creditos($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);
							$socios[$key]["creditos"] = $creditos;
						}
					}
				}else{
					foreach ($socios as $key => $value) {
						$anterior = $this->Creditos_model->estado_cuenta_anterior($this->request->fecha_desde,$this->request->tipo,$value["codpersona"]);
						$movimientos = $this->Creditos_model->estado_cuenta_detallado($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);

						$saldo = $anterior["saldo"]; $abono = 0; $cargo = 0;
						foreach ($movimientos as $k => $v) {
							$saldo = $saldo + $v["cargo"] - $v["abono"];
							$movimientos[$k]["saldo"] = number_format($saldo,2);
							$cargo = $cargo + $v["cargo"]; $abono = $abono + $v["abono"];
						}

						$socios[$key]["anterior"] = number_format($anterior["saldo"],2);
						$socios[$key]["movimientos"] = $movimientos;
						$socios[$key]["cargo"] = number_format($cargo,2);
						$socios[$key]["abono"] = number_format($abono,2);
						$socios[$key]["saldo"] = number_format($anterior["saldo"] + $cargo - $abono,2);
					}
				}
			}else{
				if($this->request->codpersona == 0){
					$socios = $this->Creditos_model->socios_saldos($this->request->fecha_saldos,$this->request->tipo);
				}else{
					$socios = $this->db->query("select codpersona,razonsocial,documento,direccion,telefono from personas where codpersona=".$this->request->codpersona)->result_array();
				}

				foreach ($socios as $key => $value) {
					$creditos = $this->Creditos_model->netix_saldos($this->request->fecha_saldos,$this->request->tipo,$value["codpersona"]);

					$importe = 0; $interes = 0; $total = 0; $saldo = 0;
					foreach ($creditos as $k => $v) {
						$hora_i_2 = new DateTime("now"); $hora_s_2 = new DateTime($v["fechavencimiento"]);	
						$intervalo_2 = $hora_i_2->diff($hora_s_2);
						if(date("Y-m-d") < $v["fechavencimiento"]){
							$sum_dias = (int)$intervalo_2->days + 1;
							$color = "green"; $estado = "POR VENCER EN ".$sum_dias." DIA(S)";
						}else{
							$color = "red"; $estado = "VENCIDO HACE ".$intervalo_2->days." DIA(S)";
						}
						$creditos[$k]["color"] = $color;
						$creditos[$k]["estado"] = $estado;

						$importe = $importe + $v["importe"]; 
						$interes = $interes + $v["interes"]; 
						$total = $total + $v["total"]; 
						$saldo = $saldo + $v["saldo"];

						$socios[$key]["importe"] = number_format($importe,2);
						$socios[$key]["interes"] = number_format($interes,2);
						$socios[$key]["total"] = number_format($total,2);
						$socios[$key]["saldo"] = number_format($saldo,2);
					}
					$socios[$key]["creditos"] = $creditos;
				}
			}
			echo json_encode($socios);
		}
	}

	function pdf_creditos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			if ($this->request->tipo==1) {
				$tipo = "CREDITOS POR COBRAR"; $socio = "CLIENTE"; $tipo_texto = "COBRANZA";
			}else{
				$tipo = "CREDITOS POR PAGAR"; $socio = "PROVEEDOR"; $tipo_texto = "PAGO";
			}

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();

			$total_importe = 0; $total_interes = 0; $total_total = 0; $total_saldo = 0;
			if ($this->request->saldos == 0) {
				if($this->request->codpersona == 0){
					$socios = $this->Creditos_model->socios_creditos($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo);
				}else{
					$socios = $this->db->query("select codpersona,razonsocial,documento,direccion,telefono from personas where codpersona=".$this->request->codpersona)->result_array();
				}

				if($this->request->tipo_consulta == 1){
					if ($this->request->mostrar==1) {
						$pdf->pdf_header("ESTADO DE CUENTA - ".$tipo,"ESTADO DE CUENTA POR CLIENTE DE ".$tipo." (DE:".$this->request->fecha_desde." A:".$this->request->fecha_hasta.")");
						$desde = explode("-", $this->request->fecha_desde); $hasta = explode("-", $this->request->fecha_hasta);
						$pdf->Cell(0,5,"REPORTE DESDE EL ".$desde[2]."-".$desde[1]."-".$desde[0]." HASTA EL ".$hasta[2]."-".$hasta[1]."-".$hasta[0],0,"C"); $pdf->ln(7);

						foreach ($socios as $key => $value) {
							$texto = $socio.": ".utf8_decode($value["razonsocial"])." | DIRECCION: ".utf8_decode($value["direccion"]);
							$pdf->SetFont('Arial','B',9);
							$pdf->Cell(190,6,substr($texto,0,95),1); $pdf->Ln();

							$columnas = array("FECHA","COMPROBANTE","DESCRIPCION","CARGO","ABONO","SALDO");
							$w = array(20,25,85,20,20,20); $pdf->pdf_tabla_head($columnas,$w,8);

							$pdf->SetWidths(array(20,25,85,20,20,20));
				            $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);

				            $anterior = $this->Creditos_model->estado_cuenta_anterior($this->request->fecha_desde,$this->request->tipo,$value["codpersona"]);
							$movimientos = $this->Creditos_model->estado_cuenta_cliente($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);

							$pdf->Cell(170,5,"SALDO ANTERIOR HASTA EL ".$desde[2]."-".$desde[1]."-".$desde[0],1,0,'R');
						    $pdf->Cell(20,5,number_format($anterior["saldo"],2),1,"R"); $pdf->ln();

							$saldo = $anterior["saldo"]; $abono = 0; $cargo = 0;
							foreach ($movimientos as $k => $v) {
								$saldo = $saldo + $v["cargo"] - $v["abono"];
								$cargo = $cargo + $v["cargo"]; $abono = $abono + $v["abono"];

								$datos = array($v["fecha"]);
								array_push($datos,utf8_decode($v["comprobante"]));
								array_push($datos,utf8_decode($v["referencia"]));

								array_push($datos,number_format($v["cargo"],2));
								array_push($datos,number_format($v["abono"],2));
								array_push($datos,number_format($saldo,2));
				                $pdf->Row($datos);
							}
							$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

							$pdf->SetFont('Arial','B',8);
							$pdf->Cell(130,5,"TOTALES",1,0,'R');
						    $pdf->Cell(20,5,number_format($cargo,2),1,"R");
						    $pdf->Cell(20,5,number_format($abono,2),1,"R");
						    $pdf->Cell(20,5,number_format($anterior["saldo"] + $cargo - $abono,2),1,"R"); $pdf->Ln(); $pdf->Ln();

						    $total_importe = $total_importe + $anterior["cargo"] + $cargo;
						    $total_total = $total_total + $anterior["abono"] + $abono;
						    $total_saldo = $total_saldo + ($anterior["saldo"] + $cargo - $abono);
						}
					}else{
						$pdf->pdf_header("ESTADO DE CUENTA - ".$tipo,"ESTADO DE CUENTA POR CREDITO DE ".$tipo." (DE:".$this->request->fecha_desde." A:".$this->request->fecha_hasta.")");

						foreach ($socios as $key => $value) {
							$texto = $socio.": ".utf8_decode($value["razonsocial"])." | DIRECCION: ".utf8_decode($value["direccion"]);
							$pdf->SetFont('Arial','B',9);
							$pdf->Cell(190,6,substr($texto,0,95),1); $pdf->Ln();

							$columnas = array("FECHA","COMPROBANTE","DESCRIPCION","IMPORTE","INTERES","TOTAL",$tipo_texto,"SALDO");
							$w = array(20,25,65,15,15,15,20,15); $pdf->pdf_tabla_head($columnas,$w,8);

							$pdf->SetWidths(array(20,25,65,15,15,15,20,15));
				            $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);

							$creditos = $this->Creditos_model->estado_cuenta_creditos($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);

							$importe = 0; $interes = 0; $total = 0; $cobranza = 0; $saldo = 0;
							foreach($creditos as $v){
								$datos = array($v["fecha"]);
								array_push($datos,utf8_decode($v["comprobante"]));
								array_push($datos,utf8_decode($v["referencia"]));

								array_push($datos,number_format($v["importe"],2));
								array_push($datos,number_format($v["interes"],2));
								array_push($datos,number_format($v["total"],2));
								array_push($datos,number_format($v["cobranza"],2));
								array_push($datos,number_format($v["saldo"],2));
				                $pdf->Row($datos);

				                $importe = $importe + $v["importe"]; 
				                $interes = $interes + $v["interes"]; 
				                $total = $total + $v["total"]; 
				                $cobranza = $cobranza + $v["cobranza"]; 
				                $saldo = $saldo + $v["saldo"];
							}
							$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

							$pdf->SetFont('Arial','B',8);
							$pdf->Cell(110,5,"TOTALES",1,0,'R');
						    $pdf->Cell(15,5,number_format($importe,2),1,"R");
						    $pdf->Cell(15,5,number_format($interes,2),1,"R");
						    $pdf->Cell(15,5,number_format($total,2),1,"R");
						    $pdf->Cell(20,5,number_format($cobranza,2),1,"R");
						    $pdf->Cell(15,5,number_format($saldo,2),1,"R"); $pdf->Ln(); $pdf->Ln();

						    $total_importe = $total_importe + $importe;
						    $total_interes = $total_interes + $interes;
						    $total_total = $total_total + $total;
						    $total_saldo = $total_saldo + $saldo;
						}
					}
				}else{
					$pdf->pdf_header("ESTADO DE CUENTA DETALLADO ".$tipo,"ESTADO DE CUENTA POR CREDITO DE ".$tipo." (DE:".$this->request->fecha_desde." A:".$this->request->fecha_hasta.")");
					$desde = explode("-", $this->request->fecha_desde); $hasta = explode("-", $this->request->fecha_hasta);
					$pdf->Cell(0,5,"REPORTE DESDE EL ".$desde[2]."-".$desde[1]."-".$desde[0]." HASTA EL ".$hasta[2]."-".$hasta[1]."-".$hasta[0],0,"C"); $pdf->ln(7);

					foreach ($socios as $key => $value) {
						$texto = $socio.": ".utf8_decode($value["razonsocial"])." | DIRECCION: ".utf8_decode($value["direccion"]);
						$pdf->SetFont('Arial','B',9);
						$pdf->Cell(190,6,substr($texto,0,95),1); $pdf->Ln();

						$columnas = array("FECHA","DESCRIPCION","CANTIDAD","P.UNITARIO","CARGO","ABONO","SALDO");
						$w = array(20,88,17,20,15,15,15); $pdf->pdf_tabla_head($columnas,$w,8);

						$pdf->SetWidths(array(20,88,17,20,15,15,15));
			            $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);

			            $anterior = $this->Creditos_model->estado_cuenta_anterior($this->request->fecha_desde,$this->request->tipo,$value["codpersona"]);
						$movimientos = $this->Creditos_model->estado_cuenta_detallado($this->request->fecha_desde,$this->request->fecha_hasta,$this->request->tipo,$value["codpersona"]);

						$pdf->Cell(175,5,"SALDO ANTERIOR HASTA EL ".$desde[2]."-".$desde[1]."-".$desde[0],1,0,'R');
						$pdf->Cell(15,5,number_format($anterior["saldo"],2),1,"R"); $pdf->ln();

						$saldo = $anterior["saldo"]; $abono = 0; $cargo = 0;
						foreach ($movimientos as $k => $v) {
							$saldo = $saldo + $v["cargo"] - $v["abono"];
							$cargo = $cargo + $v["cargo"]; $abono = $abono + $v["abono"];

							$datos = array($v["fecha"]);
							array_push($datos,utf8_decode($v["referencia"]));

							array_push($datos,number_format($v["cantidad"],2));
							array_push($datos,number_format($v["preciounitario"],2));
							array_push($datos,number_format($v["cargo"],2));
							array_push($datos,number_format($v["abono"],2));
							array_push($datos,number_format($saldo,2));
			                $pdf->Row($datos);
						}
						$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

						$pdf->SetFont('Arial','B',8);
						$pdf->Cell(145,5,"TOTALES",1,0,'R');
					    $pdf->Cell(15,5,number_format($cargo,2),1,"R");
					    $pdf->Cell(15,5,number_format($abono,2),1,"R");
					    $pdf->Cell(15,5,number_format($anterior["saldo"] + $cargo - $abono,2),1,"R"); $pdf->Ln(); $pdf->Ln();

					    $total_importe = $total_importe + $anterior["cargo"] + $cargo;
					    $total_total = $total_total + $anterior["abono"] + $abono;
					    $total_saldo = $total_saldo + ($anterior["saldo"] + $cargo - $abono);
					}
				}
				if ($this->request->mostrar==1) {
					$columnas = array("TOTAL CARGO","TOTAL ABONO","TOTAL SALDO");
					$w = array(130,30,30); $pdf->pdf_tabla_head($columnas,$w,8,"R");
					$columnas = array(number_format($total_importe,2),number_format($total_total,2),number_format($total_saldo,2));
					$pdf->pdf_tabla_head($columnas,$w,8,"R");
				}else{
					$columnas = array("TOTAL IMPORTE","TOTAL INTERES","TOTAL GENERAL","SALDO TOTAL");
					$w = array(100,30,30,30); $pdf->pdf_tabla_head($columnas,$w,8,"R");
					$columnas = array(number_format($total_importe,2),number_format($total_interes,2),number_format($total_total,2),number_format($total_saldo,2));
					$pdf->pdf_tabla_head($columnas,$w,8,"R");
				}
			}else{
				if($this->request->codpersona == 0){
					$socios = $this->Creditos_model->socios_saldos($this->request->fecha_saldos,$this->request->tipo);
				}else{
					$socios = $this->db->query("select codpersona,razonsocial,documento,direccion,telefono from personas where codpersona=".$this->request->codpersona)->result_array();
				}

				$pdf->pdf_header("SALDOS DE ".$tipo." ".$this->request->fecha_saldos,"SALDOS");

				foreach ($socios as $key => $value) {
					$texto = $socio.": ".utf8_decode($value["razonsocial"])." | DIRECCION: ".utf8_decode($value["direccion"]);
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(190,6,substr($texto,0,95),1); $pdf->Ln();

					$columnas = array("COMPROBANTE","FECHA CREDITO","FECHA VENCE","ESTADO","IMPORTE","INTERES","TOTAL","SALDO");
					$w = array(25,25,25,55,15,15,15,15); $pdf->pdf_tabla_head($columnas,$w,8);

					$pdf->SetWidths(array(25,25,25,55,15,15,15,15));
		            $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);

					$creditos = $this->Creditos_model->netix_saldos($this->request->fecha_saldos,$this->request->tipo,$value["codpersona"]);

					$importe = 0; $interes = 0; $total = 0; $saldo = 0;
					foreach ($creditos as $k => $v) {
						$hora_i_2 = new DateTime("now"); $hora_s_2 = new DateTime($v["fechavencimiento"]);	
						$intervalo_2 = $hora_i_2->diff($hora_s_2);
						if(date("Y-m-d") < $v["fechavencimiento"]){
							$sum_dias = (int)$intervalo_2->days + 1;
							$color = "green"; $estado = "POR VENCER EN ".$sum_dias." DIA(S)";
						}else{
							$color = "red"; $estado = "VENCIDO HACE ".$intervalo_2->days." DIA(S)";
						}

						$datos = array($v["seriecomprobante_ref"]."-".$v["nrocomprobante_ref"]);
						array_push($datos,utf8_decode($v["fechacredito"]));
						array_push($datos,utf8_decode($v["fechavencimiento"]));
						array_push($datos,utf8_decode($estado));

						array_push($datos,number_format($v["importe"],2));
						array_push($datos,number_format($v["interes"],2));
						array_push($datos,number_format($v["total"],2));
						array_push($datos,number_format($v["saldo"],2));
		                $pdf->Row($datos);

						$importe = $importe + $v["importe"]; 
						$interes = $interes + $v["interes"]; 
						$total = $total + $v["total"]; 
						$saldo = $saldo + $v["saldo"];
					}
					$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(130,5,"TOTALES",1,0,'R');
				    $pdf->Cell(15,5,number_format($importe,2),1,"R");
				    $pdf->Cell(15,5,number_format($interes,2),1,"R");
				    $pdf->Cell(15,5,number_format($total,2),1,"R");
				    $pdf->Cell(15,5,number_format($saldo,2),1,"R"); $pdf->Ln(); $pdf->Ln();

				    $total_importe = $total_importe + $importe;
				    $total_interes = $total_interes + $interes;
				    $total_total = $total_total + $total;
				    $total_saldo = $total_saldo + $saldo;
				}

				$columnas = array("TOTAL IMPORTE","TOTAL INTERES","TOTAL GENERAL","SALDO TOTAL");
				$w = array(100,30,30,30); $pdf->pdf_tabla_head($columnas,$w,8,"R");
				$columnas = array(number_format($total_importe,2),number_format($total_interes,2),number_format($total_total,2),number_format($total_saldo,2));
				$pdf->pdf_tabla_head($columnas,$w,8,"R");
			}
			
			$pdf->SetTitle("Netix Peru - Reporte Creditos"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function excel_creditos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$this->load->view("reportes/creditos/creditosxls.php",compact("titulo","lineas"));
		}else{
			$this->load->view("netix/404");
		}
	}
}