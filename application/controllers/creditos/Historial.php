<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Historial extends CI_Controller {

	public function imprimir_recibo($formato, $codmovimiento, $tipo){
		if (isset($_SESSION["netix_usuario"])) {
			$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
			$movimiento = $this->db->query("select movimientos.*, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codmovimiento=".$codmovimiento)->result_array();

			// $this->db->query("select round(COALESCE(sum(importe),0),2) as cobrado from kardex.cuotaspagos where codcredito=".$value["codcredito"]." and nrocuota=".$value["nrocuota"]." and estado=1")->result_array();

			$saldo = $this->db->query("select coalesce(sum(saldo),2) as saldo from kardex.creditos where codpersona=".$movimiento[0]["codpersona"]." and estado=1")->result_array();

			$this->load->library("Number"); $number = new Number();
            $tot_total = (String)(number_format($movimiento[0]["importe"],2,".","")); $imptotaltexto = explode(".", $tot_total);
            $det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

            $texto_importe = strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES";

			if ($formato=="ticket") {
				$html = "<div style='width:300px;text-aling:center;'>";
					$html .= "<h3 align='center'>".$empresa[0]["nombrecomercial"]."</h3> ";
					$html .= "<h5 align='center'> RECIBO DE INGRESO CAJA ".$movimiento[0]["seriecomprobante"]." - ".$movimiento[0]["nrocomprobante"]."</h5> ";
					$html .= "<h5 align='center' style='margin:5px'>SALDO ANTERIOR: S/. ".number_format($saldo[0]["saldo"] + $movimiento[0]["importe"],2)."</h5>";
					$html .= "<h5 align='center' style='margin:5px'>IMPORTE: S/. ".number_format($movimiento[0]["importe"],2)."</h5>";
					$html .= "<h5 align='center' style='margin:5px'>SALDO FINAL: S/. ".number_format($saldo[0]["saldo"],2)."</h5>";

					$html .= "<p align='center' style='margin:5px;'> Recibí de ".$movimiento[0]["razonsocial"]."</p> ";
					$html .= "<p align='center' style='margin:5px;'> La cantidad de ".$texto_importe."</p> ";
					$html .= "<p align='center' style='margin:5px;'> Por concepto de ".$movimiento[0]["concepto"]."</p> ";
					$html .= "<p align='center' style='margin:5px;'> Ref: ".$movimiento[0]["seriecomprobante_ref"]." - ".$movimiento[0]["nrocomprobante_ref"]."</p> ";

					$html .= "<p> Fecha ".date("d / m / Y", strtotime($movimiento[0]["fechamovimiento"]))."</p> ";

					$html .= "<h5 style='margin:5px;'>____________________________________________</h5>";
					$html .= "<h5 style='margin:5px;'>NOMBRE:</h5>";
					$html .= "<h5 style='margin:5px;'>D.N.I. </h5>";
				$html .= "</div>";

				echo $html;
			}else{
				$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();

				$pdf->SetFont('Arial', 'B', 12);
				$pdf->MultiCell(0,5,utf8_decode($empresa[0]["nombrecomercial"]),0,"C",false); $pdf->Ln(5);

				$pdf->SetFont('Arial', 'B', 11);
				$pdf->MultiCell(0,5,"RECIBO DE INGRESO CAJA ".$movimiento[0]["seriecomprobante"]." - ".$movimiento[0]["nrocomprobante"],0,"L",false);
				$pdf->MultiCell(0,5,"SALDO ANTERIOR: S/. ".number_format($saldo[0]["saldo"]+$movimiento[0]["importe"],2),0,"L",false);
				$pdf->MultiCell(0,5,"IMPORTE: S/. ".number_format($movimiento[0]["importe"],2),0,"L",false);
				$pdf->MultiCell(0,5,"SALDO FINAL: S/. ".number_format($saldo[0]["saldo"],2),0,"L",false); $pdf->Ln(5);

				$pdf->SetFont('Arial', '', 10);
				$pdf->MultiCell(0,5,utf8_decode("Recibí de ".$movimiento[0]["razonsocial"]),0,"L",false);
				$pdf->MultiCell(0,5,"La cantidad de ".$texto_importe,0,"L",false);
				$pdf->MultiCell(0,5,utf8_decode("Por concepto de ".$movimiento[0]["concepto"]),0,"L",false);
				$pdf->MultiCell(0,5,"Ref: ".$movimiento[0]["seriecomprobante_ref"]." - ".$movimiento[0]["nrocomprobante_ref"],0,"L",false); $pdf->Ln(5);

				$pdf->MultiCell(0,5,"Fecha ".date("d / m / Y", strtotime($movimiento[0]["fechamovimiento"])),0,"L",false); $pdf->Ln(5);

				$pdf->MultiCell(0,5,"_________________________________________________",0,"L",false);
				$pdf->MultiCell(0,5,"NOMBRE:",0,"L",false);
				$pdf->MultiCell(0,5,"DNI:",0,"L",false);

			    $pdf->SetTitle(utf8_decode("Netix Perú - Recibo Pago")); $pdf->Output();
			}
		}else{
			$this->load->view("netix/404");
		}
	}
}