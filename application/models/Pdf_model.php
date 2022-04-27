<?php

class Pdf_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function pdf_comprobante($tipo,$codkardex){
		$estilo = "border:1px solid #4E4E4E;"; $color = "color:#4E4E4E";

		$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
		$logo = "default.png";
		if ($empresa[0]["foto"]!="") {
			$logo = $empresa[0]["foto"];
		}

		$comprobante = $this->db->query("select k.seriecomprobante,k.nrocomprobante,k.fechacomprobante,k.subtotal,k.porcdescuento, k.descuentoglobal, k.porcigv, k.igv,(select COALESCE(sum(importe),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select COALESCE(sum(importe),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select COALESCE(sum(importe),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select COALESCE(sum(importe),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito, k.importe, k.codmotivonota, k.codcomprobantetipo_ref, k.seriecomprobante_ref, k.nrocomprobante_ref, ks.nombre_xml, dt.oficial as coddocumento, p.documento, k.cliente,k.direccion from kardex.kardex as k inner join sunat.kardexsunat as ks on(k.codkardex=ks.codkardex) inner join public.personas as p on(k.codpersona=p.codpersona) inner join public.documentotipos as dt on(p.coddocumentotipo=dt.coddocumentotipo) where k.codkardex=".$codkardex)->result_array();
        $detalle = $this->db->query("select kd.*,p.descripcion as producto,u.oficial as unidad from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codkardex." and kd.estado=1 order by kd.item asc")->result_array();

		$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:60%">';
					if ($logo=="default.png") {
						$html .= '<h2 style="'.$color.'">'.$empresa[0]["razonsocial"].'</h2>';
						$html .= '<h4 style="'.$color.'">'.$empresa[0]["direccion"].'</h4>';
					}else{
						$html .= '<h3 style="'.$color.'">'.$empresa[0]["razonsocial"].'</h3>';
						$html .= '<h4 style="'.$color.'">'.$empresa[0]["direccion"].'</h4>';
						$html .= '<img src="'.base_url().'public/img/empresa/'.$logo.'" style="height:40px;">';
					}
					
					$html .= '<h5 style="'.$color.'">EMAIL: '.$empresa[0]["email"].' - TELF: '.$empresa[0]["telefono"].'</h5>';
				$html .= '</th>';
				$html .= '<th style="width:3%;"></th>';
				$html .= '<th style="width:37%;border:1px solid #4E4E4E;color:#4E4E4E;">';
					$html .= '<h1 style="'.$color.'">R.U.C: '.$empresa[0]["documento"].' <br> '.$tipo.' <br> '.$comprobante[0]["seriecomprobante"].' - '.$comprobante[0]["nrocomprobante"].'</h1>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table> <h6></h6>';

		$html .= '<table cellpadding="4" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="'.$color.';width:20%;"> <b>RAZON SOCIAL</b> </td>';
				$html .= '<td style="'.$color.';width:40%;border-bottom:1px solid #ddd;">: '.$comprobante[0]["cliente"].'</td>';
				$html .= '<td style="'.$color.';width:20%;"> <b>FECHA EMISION</b> </td>';
				$html .= '<td style="'.$color.';width:20%;border-bottom:1px solid #ddd;">: '.$comprobante[0]["fechacomprobante"].'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$color.'"> <b>N° DNI/RUC</b> </td>';
				$html .= '<td style="'.$color.';border-bottom:1px solid #ddd;">: '.$comprobante[0]["documento"].'</td>';
				$html .= '<td style="'.$color.'"> <b>GUIA DE REMISION</b> </td>';
				$html .= '<td style="'.$color.';border-bottom:1px solid #ddd;">: </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$color.'"> <b>DIRECCION</b> </td>';
				$html .= '<td colspan="3" style="'.$color.';border-bottom:1px solid #ddd;">: '.$comprobante[0]["direccion"].' </td>';
			$html .= '</tr>';
		$html .= '</table> <h6></h6>';

		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #4E4E4E;">';
            $html .= '<tr style="background-color:#f2f2f2;">';
                $html .= '<td style="'.$estilo.'width:6%;'.$color.'"> <b>ITEM</b> </td>';
                $html .= '<td style="'.$estilo.'width:50%;'.$color.'"> <b>DESCRIPCION</b> </td>';
                $html .= '<td style="'.$estilo.'width:7%;'.$color.'"> <b>U. M.</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;'.$color.'"> <b>CANTIDAD</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;'.$color.'"> <b>S/. UNITARIO</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;'.$color.'"> <b>IMPORTE</b> </td>';
            $html .= '</tr>';
            foreach ($detalle as $key => $value) {
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo.'width:6%;'.$color.'"> '.$value["item"].' </td>';
	                $html .= '<td style="'.$estilo.'width:50%;'.$color.'"> '.strtoupper($value["producto"]." ".$value["descripcion"]).' </td>';
	                $html .= '<td style="'.$estilo.'width:7%;'.$color.'"> '.$value["unidad"].' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;'.$color.'" align="right"> '.number_format($value["cantidad"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;'.$color.'" align="right"> '.number_format($value["precioconigv"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;'.$color.'" align="right"> '.number_format($value["importe"],2).' </td>';
	            $html .= '</tr>';
            }
            $html .= '<tr>';
                $html .= '<td style="'.$estilo.'width:63%;'.$color.'" rowspan="7">';
                	$this->load->library("Number"); $number = new Number();
		            $tot_total = (String)(number_format($comprobante[0]["importe"],2,".","")); $imptotaltexto = explode(".", $tot_total);
		            $det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

		            $texto_importe = "SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES";

                	$html .= '<h4>'.$texto_importe.'</h4>';

                	$html .= '<table cellpadding="2">';
                		$html .= '<tr>';
                			$html .= '<td style="width:35%">';
                				$html .='<img src="'.base_url().'sunat/webnetix/qrcode.png" style="height:115px">';
                			$html .= '</td>';
	                		$html .= '<td style="width:65%;text-align:center"> <h1></h1> <h4></h4>';
	                			$html .= '<h5>REPRESENTACION IMPRESA DE LA '.$tipo.'</h5>';
	                			$html .= '<h5>AUTORIZADO MEDIANTE RESOLUCION DE INTERDENCIA N° 032-005 CONSULTA TU COMPROBANTE EN: http://www.netixperu.com/sunat</h5>';
                			$html .= '</td>';
	                	$html .= '</tr>';
                	$html .= '</table>';
                $html .= '</td>';

                $html .= '<td style="'.$estilo.'width:26%;'.$color.'" align="right"> <b>OP.GRAVADAS S/</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;'.$color.'" align="right">'.number_format($comprobante[0]["gravado"],2).'</td>';
            $html .= '</tr>';
            $html .= '<tr>';
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>OP.INAFECTAS S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["inafecto"],2).' </td>';
            $html .= '</tr>';
            $html .= '<tr>';
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>OP.EXONERADAS S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["exonerado"],2).' </td>';
            $html .= '</tr>';
            $html .= '<tr>';
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>OP.GRATUITAS S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["gratuito"],2).' </td>';
            $html .= '</tr>';
            $html .= '<tr>';               	
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>DESCUENTO S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["descuentoglobal"],2).' </td>';
            $html .= '</tr>';
            $html .= '<tr>';
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>I.G.V. S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["igv"],2).' </td>';
            $html .= '</tr>';
            $html .= '<tr>';
               	$html .= '<td style="'.$estilo.$color.'" align="right"> <b>TOTAL S/</b> </td>';
               	$html .= '<td style="'.$estilo.$color.'" align="right">'.number_format($comprobante[0]["importe"],2).' </td>';
            $html .= '</tr>';
        $html .= '</table>';

		

		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor("Netix Peru");
		$pdf->SetTitle("Netix Peru | ".$tipo);
		$pdf->SetSubject("Netix Peru");

		$pdf->setPrintHeader(false);

		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$pdf->setFontSubsetting(true);
		$pdf->SetFont('helvetica', '', 9);
		$pdf->AddPage("A");
		$pdf->writeHTML($html, true, 0, true, 0);

		$archivo = $empresa[0]["documento"].'-'.$comprobante[0]["seriecomprobante"].'-'.$comprobante[0]["nrocomprobante"].'.pdf';
		$nombre_archivo = utf8_decode($archivo);
		$pdf->Output($nombre_archivo, 'I');
	}
}