<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Formato extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function a4_principal($codkardex){
		$estilo = "border-left:1px solid #000; border-right:1px solid #000;";
		$estilo1 = "border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000;text-align:right";

		$empresa = $this->db->query("select documento,razonsocial,nombrecomercial from public.personas where codpersona=1")->result_array();
		$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
		$principal = $this->db->query("select *from public.sucursales where principal=1 and estado=1")->result_array();
		$parametros = $this->db->query("select *from public.empresas limit 1")->result_array();

		$venta = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe, k.codempleado,k.condicionpago,k.nroplaca, k.codpersona, k.icbper, seriecomprobante_ref, nrocomprobante_ref from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
		$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito")->result_array();
		$detalle = $this->db->query("select kd.item,kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal, kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codkardex=".$codkardex." order by kd.item")->result_array();

		$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:60%">';
					$html .= '<h3>'.$empresa[0]["razonsocial"].'</h3>';
					$html .= '<h3 style="color:#00564c">'.$empresa[0]["nombrecomercial"].'</h3>';
					$html .= '<p>'.$parametros[0]["slogan"].'</p>';
				$html .= '</th>';
				$html .= '<th style="width:40%;border:1px solid #000;color:#000;">';
					$html .= '<h2>RUC: '.$empresa[0]["documento"].'</h2> <h6></h6> <h3>'.$venta[0]["comprobante"].'</h3>';
					$html .= '<h3>'.$venta[0]["seriecomprobante"].' - '.$venta[0]["nrocomprobante"].'</h3>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="2" width="100%">';
			if (count($principal)>0) {
				if ($_SESSION["netix_codsucursal"]!=$principal[0]["codsucursal"]) {
					$html .= '<tr>';
						$html .= '<td style="width:100%;"><b>PRINCIPAL: '.$principal[0]["direccion"].'</b> </td>';
					$html .= '</tr>';
					
					$html .= '<tr>';
						$html .= '<td style="width:100%;"><b>SUCURSAL: '.$sucursal[0]["direccion"].'</b> </td>';
						//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
					$html .= '</tr>';
				}else{
					$html .= '<tr>';
						$html .= '<td style="width:100%;"><b>'.$sucursal[0]["direccion"].'</b> </td>';
						//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
					$html .= '</tr>';
				}
			}else{
				$html .= '<tr>';
					$html .= '<td style="width:100%;"><b>'.$sucursal[0]["direccion"].'</b> </td>';
					//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
				$html .= '</tr>';
			}
			
			$html .= '<tr>';
				$html .= '<td><b>'.$sucursal[0]["telefonos"].'</b> </td>';
				// $html .= '<td> E-MAIL: '.$empresa[0]["email"].' </td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<table cellpadding="0" width="100%"> <tr> <th style="height:5px;"></th> </tr> </table>';

		$html .= '<table cellpadding="3" width="100%" style="border:1px solid #000;">';
			$html .= '<tr>';
				$html .= '<td style="width:16%;"> <b>CODIGO CLIENTE</b> </td>';
				$html .= '<td style="width:54%;">: 000'.$venta[0]["codpersona"].'</td>';
				$html .= '<td style="width:18%"> <b>CONDICION PAGO</b> </td>';
				if ($venta[0]["condicionpago"]==1) {
					$html .= '<td style="width:12%;">: CONTADO</td>';
				}else{
					$html .= '<td style="width:12%;">: CREDITO</td>';
				}
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>RAZON SOCIAL</b> </td>';
				$html .= '<td>: '.$venta[0]["cliente"].'</td>';
				$html .= '<td> <b>GUIA N°</b> </td>';
				$html .= '<td>: </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DIRECCION</b> </td>';
				$html .= '<td>: '.$venta[0]["direccion"].' </td>';
				$html .= '<td> <b>MONEDA</b> </td>';
				$html .= '<td>: SOLES</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DNI / RUC</b> </td>';
				$html .= '<td>: '.$venta[0]["documento"].'</td>';
				$html .= '<td> <b>FECHA</b> </td>';
				$html .= '<td>: '.$venta[0]["fechacomprobante"].'</td>';
			$html .= '</tr>';
			if ($venta[0]["codcomprobantetipo"] == 14) {
				$html .= '<tr>';
					$html .= '<td> <b>REFERENCIA</b> </td>';
					$html .= '<td>: '.$venta[0]["seriecomprobante_ref"].'-'.$venta[0]["nrocomprobante_ref"].'</td>';
					$html .= '<td colspan="2">ANULACIÓN DE LA OPERACION </td>';
				$html .= '</tr>';
			}
		$html .= '</table>';
		$html .= '<table cellpadding="1" width="100%"> <tr> <th style="height:10px;"></th> </tr> </table>';

        $html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;">';
            $html .= '<tr>';
                $html .= '<td style="'.$estilo.'width:6%;"> <b>ITEM</b> </td>';
                $html .= '<td style="'.$estilo.'width:40%;"> <b>DESCRIPCION</b> </td>';
                $html .= '<td style="'.$estilo.'width:15%;"> <b>UND MEDIDA</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>CANTIDAD</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>P.UNITARIO</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>IMPORTE</b> </td>';
            $html .= '</tr>';
        $html .= '</table>';
        $html .= '<table width="100%"> <tr> <th style="height:0.1px;"></th> </tr> </table>';

        $html .= '<table cellpadding="1" width="100%" style="border:1px solid #000;">';
            foreach ($detalle as $value) {
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo.'width:6%;"> 0'.$value["item"].' </td>';
	                $html .= '<td style="'.$estilo.'width:40%;font-size:10px;"> '.$value["producto"].' '.$value["descripcion"].' </td>';
	                $html .= '<td style="'.$estilo.'width:15%;"> '.$value["unidad"].' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["cantidad"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["preciounitario"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["subtotal"],2).' </td>';
	            $html .= '</tr>';
            }
            /* for ($i=0; $i < 7 - count($detalle); $i++) { 
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	                $html .= '<td style="'.$estilo.'"> </td>';
	            $html .= '</tr>';
            } */
        $html .= '</table>';
        $html .= '<table cellpadding="1" width="100%"> <tr> <th style="height:10px;"></th> </tr> </table>';

        $this->load->library("Number"); $number = new Number(); 
        $total_texto = $number->convertirNumeroEnLetras(round($venta[0]["importe"],2));

        $html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo.' width:65%" rowspan="10" align="center"> <br> ';
					$html .='<h3> SON: '.strtoupper($total_texto).' Y 00/100 SOLES</h3> <br> ';
					$html .= '<h6></h6> <h4 style="color:#000;" align="center">'.$parametros[0]["publicidad"].'</h4>';
				$html .= '</td>';
				$html .= '<td style="'.$estilo.' width:20%;text-align:right"> <b>OP.GRAVADAS S/</b> </td>';
				$html .= '<td style="'.$estilo.' width:15%;text-align:right">'.number_format($totales[0]["gravado"] - $venta[0]["igv"],2).' </td>';
			$html .= '</tr>';

			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.INAFECTAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["inafecto"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.EXONERADAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["exonerado"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>O.GRATUITAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["gratuito"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>OTROS CARGOS S/</b> </td> <td style="'.$estilo1.'">0.00</td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>OTROS TRIBUTOS S/</b> </td> <td style="'.$estilo1.'">0.00</td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>DESCUENTO S/</b>  </td> <td style="'.$estilo1.'">'.number_format($venta[0]["descglobal"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>IGV S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["igv"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>ICBPER S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["icbper"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>TOTAL S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["importe"],2).' </td> </tr>';
		$html .= '</table>';

		$this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("WEB NETIX");
        $pdf->SetTitle("WEB NETIX | IMPRIMIR VENTA");
        $pdf->SetSubject("WEB NETIX");

        $pdf->setPrintHeader(false);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage("A");
        $pdf->writeHTML($html, true, 0, true, 0);
        //$pdf->writeHTML($html, true, false, true, false, '');

        $nombre_archivo = utf8_decode("ImprimirVenta.pdf");
        $pdf->Output($nombre_archivo, 'I');
	}

	public function a4($codkardex){
		$estilo = "border-left:1px solid #000; border-right:1px solid #000;";
		$estilo1 = "border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000;text-align:right";

		$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
		$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
		$principal = $this->db->query("select *from public.sucursales where principal=1 and estado=1")->result_array();
		$parametros = $this->db->query("select *from public.empresas limit 1")->result_array();

		$venta = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe, k.codempleado,k.condicionpago,k.nroplaca, k.codpersona, k.icbper, seriecomprobante_ref, nrocomprobante_ref from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
		$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito")->result_array();
		$detalle = $this->db->query("select kd.item,kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal, kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codkardex=".$codkardex." order by kd.item")->result_array();

		$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:20%">';
					$html .='<h4></h4> <img src="'.base_url().'public/img/'.$_SESSION['netix_logo'].'" style="height:100px">';
				$html .= '</th>';
				$html .= '<th style="width:40%">';
					$html .= '<h3>'.$empresa[0]["razonsocial"].'</h3>';
					$html .= '<p>'.$parametros[0]["slogan"].'</p>';
				$html .= '</th>';
				$html .= '<th style="width:40%;border:1px solid #000;color:#000;">';
					$html .= '<h2>RUC: '.$empresa[0]["documento"].'</h2> <h6></h6> <h3>'.$venta[0]["comprobante"].'</h3>';
					$html .= '<h3>'.$venta[0]["seriecomprobante"].' - '.$venta[0]["nrocomprobante"].'</h3>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="2" width="100%">';
			if (count($principal)>0) {
				if ($_SESSION["netix_codsucursal"]!=$principal[0]["codsucursal"]) {
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>PRINCIPAL: '.$principal[0]["direccion"].'</b> </td>';
					$html .= '</tr>';
					
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>SUCURSAL: '.$sucursal[0]["direccion"].'</b> </td>';
						//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
					$html .= '</tr>';
				}else{
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>'.$sucursal[0]["direccion"].'</b> </td>';
						//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
					$html .= '</tr>';
				}
			}else{
				$html .= '<tr>';
					$html .= '<td style="width:100%;"> <b>'.$sucursal[0]["direccion"].'</b> </td>';
					//$html .= '<td style="width:40%;"> <b> SOPORTE TECNICO: 997644742 </b> </td>';
				$html .= '</tr>';
			}
			
			$html .= '<tr>';
				$html .= '<td> <b>'.$sucursal[0]["telefonos"].'</b> </td>';
				// $html .= '<td> E-MAIL: '.$empresa[0]["email"].' </td>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;">';
			$html .= '<tr>';
				$html .= '<td style="width:16%;"> <b>CODIGO CLIENTE</b> </td>';
				$html .= '<td style="width:62%;">: 000'.$venta[0]["codpersona"].'</td>';
				$html .= '<td style="width:10%"> <b>PAGO AL</b> </td>';
				if ($venta[0]["condicionpago"]==1) {
					$html .= '<td style="width:12%;">: CONTADO</td>';
				}else{
					$html .= '<td style="width:12%;">: CREDITO</td>';
				}
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>RAZON SOCIAL</b> </td>';
				$html .= '<td>: '.$venta[0]["cliente"].'</td>';
				$html .= '<td> <b>GUIA N°</b> </td>';
				$html .= '<td>: </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DIRECCION</b> </td>';
				$html .= '<td>: <span style="font-size:10px">'.$venta[0]["direccion"].'</span> </td>';
				$html .= '<td> <b>MONEDA</b> </td>';
				$html .= '<td>: SOLES</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DNI / RUC</b> </td>';
				$html .= '<td>: '.$venta[0]["documento"].'</td>';
				$html .= '<td> <b>FECHA</b> </td>';
				$html .= '<td>: '.$venta[0]["fechacomprobante"].'</td>';
			$html .= '</tr>';
			if ($venta[0]["codcomprobantetipo"] == 14) {
				$html .= '<tr>';
					$html .= '<td> <b>REFERENCIA</b> </td>';
					$html .= '<td>: '.$venta[0]["seriecomprobante_ref"].'-'.$venta[0]["nrocomprobante_ref"].'</td>';
					$html .= '<td colspan="2">ANULACIÓN DE LA OPERACION </td>';
				$html .= '</tr>';
			}
		$html .= '</table>';
		$html .= '<table width="100%"> <tr> <th style="height:0.1px;"></th> </tr> </table>';

        $html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;">';
            $html .= '<tr>';
                $html .= '<td style="'.$estilo.'width:6%;"> <b>ITEM</b> </td>';
                $html .= '<td style="'.$estilo.'width:40%;"> <b>DESCRIPCION</b> </td>';
                $html .= '<td style="'.$estilo.'width:15%;"> <b>UND MEDIDA</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>CANTIDAD</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>P.UNITARIO</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>IMPORTE</b> </td>';
            $html .= '</tr>';
        $html .= '</table>';

        $html .= '<table cellpadding="1" width="100%" style="border:1px solid #000;">';
            foreach ($detalle as $value) {
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo.'width:6%;"> 0'.$value["item"].' </td>';
	                $html .= '<td style="'.$estilo.'width:40%;font-size:10px;"> '.$value["producto"].' '.$value["descripcion"].' </td>';
	                $html .= '<td style="'.$estilo.'width:15%;"> '.$value["unidad"].' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["cantidad"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["preciounitario"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["subtotal"],2).' </td>';
	            $html .= '</tr>';
            }
        $html .= '</table>';
        $html .= '<table width="100%"> <tr> <th style="height:0.1px;"></th> </tr> </table>';

        $this->load->library("Number"); $number = new Number(); 
        $total_texto = $number->convertirNumeroEnLetras(round($venta[0]["importe"],2));

        $html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo.' width:65%" rowspan="8" align="center"> <br> ';
					$html .='<h3> SON: '.strtoupper($total_texto).' Y 00/100 SOLES</h3> <br> ';
				$html .= '</td>';
				$html .= '<td style="'.$estilo.' width:20%;text-align:right"> <b>OP.GRAVADAS S/</b> </td>';
				$html .= '<td style="'.$estilo.' width:15%;text-align:right">'.number_format($totales[0]["gravado"] - $venta[0]["igv"],2).' </td>';
			$html .= '</tr>';

			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.INAFECTAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["inafecto"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.EXONERADAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["exonerado"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>O.GRATUITAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["gratuito"],2).' </td> </tr>';
			// $html .= '<tr> <td style="'.$estilo1.'"> <b>OTROS CARGOS S/</b> </td> <td style="'.$estilo1.'">0.00</td> </tr>';
			// $html .= '<tr> <td style="'.$estilo1.'"> <b>OTROS TRIBUTOS S/</b> </td> <td style="'.$estilo1.'">0.00</td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>DESCUENTO S/</b>  </td> <td style="'.$estilo1.'">'.number_format($venta[0]["descglobal"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>IGV S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["igv"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>ICBPER S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["icbper"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>TOTAL S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["importe"],2).' </td> </tr>';
		$html .= '</table>';

		$textoqr = $empresa[0]["razonsocial"]."|".$venta[0]["seriecomprobante"]."|".$venta[0]["nrocomprobante"]."|".number_format($venta[0]["igv"],2)."|".number_format($venta[0]["importe"],2)."|".$venta[0]["fechacomprobante"]."|".$venta[0]["documento"];

		$this->load->library('ciqrcode');
        $params['data'] = $textoqr; $params['level'] = 'H'; $params['size'] = 5;
        $params['savename'] = "./sunat/webnetix/qrcode.png";
        $this->ciqrcode->generate($params);
        
        $archivo_error = APPPATH."/logs/qrcode.png-errors.txt";
        unlink($archivo_error);

		$html .= '<table width="100%"> <tr> <th style="height:0.1px;"></th> </tr> </table>';
		$html .= '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:20%">';
					$html .='<img src="'.base_url().'sunat/webnetix/qrcode.png" style="height:94px">';
				$html .= '</th>';
				$html .= '<th style="width:80%">';
					$html .= '<h6></h6> <h4 style="color:#000;" align="center">'.$parametros[0]["publicidad"].'</h4>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("WEB NETIX");
        $pdf->SetTitle("WEB NETIX | IMPRIMIR VENTA");
        $pdf->SetSubject("WEB NETIX");

        $pdf->setPrintHeader(false);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage("A");
        $pdf->writeHTML($html, true, 0, true, 0);
        //$pdf->writeHTML($html, true, false, true, false, '');

        $nombre_archivo = utf8_decode("ImprimirVenta.pdf");
        $pdf->Output($nombre_archivo, 'I');
	}

	public function a5($codkardex){
		$estilo = "border-left:1px solid #000; border-right:1px solid #000;";
		$estilo1 = "border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000;text-align:right";

		$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
		$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
		$parametros = $this->db->query("select *from public.empresas limit 1")->result_array();

		$venta = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe, k.codempleado,k.condicionpago,k.nroplaca from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
		$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito")->result_array();
		$detalle = $this->db->query("select kd.item,kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal, kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codkardex=".$codkardex." order by kd.item")->result_array();

		//$html = $this->load->view("facturacion/formato/a5",compact("empresa","parametros","venta"),true);

		$vendedor = $this->db->query("select razonsocial from public.personas where codpersona=".$venta[0]["codempleado"])->result_array();
    	$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:20%">';
					$html .='<img src="'.base_url().'public/img/'.$_SESSION['netix_logo'].'" style="height:100px;">';
					$html .= '<h6>DE: '.$empresa[0]["razonsocial"].'</h6>';
				$html .= '</th>';
				$html .= '<th style="width:40%">';
					$html .= '<h4>'.$parametros[0]["slogan"].'</h4>';
				$html .= '</th>';
				$html .= '<th style="width:2%;"></th>';
				$html .= '<th style="width:38%;border:1px solid #000;color:#000;">';
					$html .= '<h2>RUC: '.$empresa[0]["documento"].'</h2> <h3>'.$venta[0]["comprobante"].'</h3>';
					$html .= '<h3>'.$venta[0]["seriecomprobante"].' - '.$venta[0]["nrocomprobante"].'</h3>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="2" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="width:100%;"><b>'.$sucursal[0]["direccion"].'</b> </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td>TELF: '.$sucursal[0]["telefonos"].'</td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<table cellpadding="0" width="100%"> <tr> <th style="height:5px;"></th> </tr> </table>';

		$html .= '<table cellpadding="2" width="100%" style="border:1px solid #000;font-size:9px;">';
			$html .= '<tr>';
				$html .= '<td style="width:16%;"> <b>CLIENTE</b> </td>';
				$html .= '<td style="width:54%;">: '.$venta[0]["cliente"].'</td>';
				$html .= '<td style="width:15%"> <b>PAGO AL</b> </td>';
				if ($venta[0]["condicionpago"]==1) {
					$html .= '<td style="width:15%;">: CONTADO</td>';
				}else{
					$html .= '<td style="width:15%;">: CREDITO</td>';
				}
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DIRECCION</b> </td>';
				$html .= '<td>: '.$venta[0]["direccion"].' </td>';
				$html .= '<td> <b>MONEDA</b> </td>';
				$html .= '<td>: SOLES</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>DNI / RUC</b> </td>';
				$html .= '<td>: '.$venta[0]["documento"].'</td>';
				$html .= '<td> <b>FECHA</b> </td>';
				$html .= '<td>: '.$venta[0]["fechacomprobante"].'</td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td> <b>NRO PLACA</b> </td>';
				$html .= '<td colspan="3">: '.$venta[0]["nroplaca"].'</td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<table cellpadding="1" width="100%"> <tr> <th style="height:10px;"></th> </tr> </table>';

		$this->load->library("Number"); $number = new Number(); 
        $total_texto = $number->convertirNumeroEnLetras(round($venta[0]["importe"],2));

        $html .= '<table cellpadding="4" width="100%" style="border:1px solid #000;font-size:8px">';
            $html .= '<tr>';
                $html .= '<td style="'.$estilo.'width:7%;"> <b>ITEM</b> </td>';
                $html .= '<td style="'.$estilo.'width:40%;"> <b>DESCRIPCION</b> </td>';
                $html .= '<td style="'.$estilo.'width:15%;"> <b>UND MEDIDA</b> </td>';
                $html .= '<td style="'.$estilo.'width:12%;"> <b>CANTIDAD</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>P.UNITARIO</b> </td>';
                $html .= '<td style="'.$estilo.'width:13%;"> <b>IMPORTE</b> </td>';
            $html .= '</tr>';
            foreach ($detalle as $value) {
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo.'width:7%;"> 0'.$value["item"].' </td>';
	                $html .= '<td style="'.$estilo.'width:40%;"> '.$value["producto"].' '.$value["descripcion"].' </td>';
	                $html .= '<td style="'.$estilo.'width:15%;"> '.$value["unidad"].' </td>';
	                $html .= '<td style="'.$estilo.'width:12%;text-align:right"> '.number_format($value["cantidad"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["preciounitario"],2).' </td>';
	                $html .= '<td style="'.$estilo.'width:13%;text-align:right"> '.number_format($value["subtotal"],2).' </td>';
	            $html .= '</tr>';
            }
        $html .= '</table>';
        $html .= '<table cellpadding="1" width="100%"> <tr> <th style="height:10px;"></th> </tr> </table>';
        
        $textoqr = $empresa[0]["razonsocial"]."|".$venta[0]["seriecomprobante"]."|".$venta[0]["nrocomprobante"]."|".number_format($venta[0]["igv"],2)."|".number_format($venta[0]["importe"],2)."|".$venta[0]["fechacomprobante"]."|".$venta[0]["documento"];

        $this->load->library('ciqrcode');
        $params['data'] = $textoqr; $params['level'] = 'H'; $params['size'] = 5;
        $params['savename'] = "./sunat/webnetix/qrcode.png";
        $this->ciqrcode->generate($params);
        
        $archivo_error = APPPATH."/logs/qrcode.png-errors.txt";
        unlink($archivo_error);

        $html .= '<table cellpadding="4" width="100%" style="border:1px solid #000;font-size:8px">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo.' width:62%" rowspan="7" align="center"> <br> ';
					$html .='<h3> SON: '.strtoupper($total_texto).' Y 00/100 SOLES</h3> <br> ';
					$html .='<img src="'.base_url().'sunat/webnetix/qrcode.png" style="height:80px">';
				$html .= '</td>';
				$html .= '<td style="'.$estilo.' width:25%;text-align:right"> <b>OP.GRAVADAS S/</b> </td>';
				$html .= '<td style="'.$estilo.' width:13%;text-align:right">'.number_format($totales[0]["gravado"],2).' </td>';
			$html .= '</tr>';

			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.INAFECTAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["inafecto"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>OP.EXONERADAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["exonerado"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>O.GRATUITAS S/</b> </td> <td style="'.$estilo1.'">'.number_format($totales[0]["gratuito"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>DESCUENTO S/</b>  </td> <td style="'.$estilo1.'">'.number_format($venta[0]["descglobal"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>IGV S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["igv"],2).' </td> </tr>';
			$html .= '<tr> <td style="'.$estilo1.'"> <b>TOTAL S/</b> </td> <td style="'.$estilo1.'">'.number_format($venta[0]["importe"],2).' </td> </tr>';
		$html .= '</table>';

		$html .= '<h5 style="color:#000;" align="center">'.$parametros[0]["publicidad"].'</h5>'; 

		$this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("WEB NETIX");
        $pdf->SetTitle("WEB NETIX | IMPRIMIR VENTA");
        $pdf->SetSubject("WEB NETIX");

        $pdf->setPrintHeader(false);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage('P', 'A5');
		//$pdf->AddPage('L', 'A5');
		// $pdf->SetLeftMargin(0);
        $pdf->writeHTML($html, true, 0, true, 0);

        $nombre_archivo = utf8_decode("ImprimirVenta.pdf");
        $pdf->Output($nombre_archivo, 'I');
	}

	public function ticket($codkardex){
		if (isset($_SESSION["netix_codusuario"])) {
			$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
			$sucursal = $this->db->query("select sucursal.*,empresa.publicidad,empresa.agradecimiento from public.sucursales as sucursal inner join public.empresas as empresa on(sucursal.codempresa=empresa.codempresa) where sucursal.codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

			$venta = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe, k.codempleado,k.condicionpago,k.nroplaca from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
			$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito")->result_array();
			$detalle = $this->db->query("select kd.item,kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal, kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codkardex=".$codkardex." order by kd.item")->result_array();

			$vendedor = $this->db->query("select razonsocial from public.personas where codpersona=".$venta[0]["codempleado"])->result_array();
			if ($venta[0]["condicionpago"]==2) {
				$credito = $this->db->query("select fechavencimiento from kardex.creditos where codkardex=".$codkardex)->result_array();
			}else{
				$credito = [];
			}

			$textoqr = $empresa[0]["razonsocial"]."|".$venta[0]["seriecomprobante"]."|".$venta[0]["nrocomprobante"]."|".number_format($venta[0]["igv"],2)."|".number_format($venta[0]["importe"],2)."|".$venta[0]["fechacomprobante"]."|".$venta[0]["documento"];

	        $this->load->library('ciqrcode');
	        $params['data'] = $textoqr; $params['level'] = 'H'; $params['size'] = 5;
	        $params['savename'] = "./sunat/webnetix/qrcode.png";
	        $this->ciqrcode->generate($params);

	        $archivo_error = APPPATH."/logs/qrcode.png-errors.txt";
        	unlink($archivo_error);

			$this->load->library("Number"); $number = new Number();
			$tot_total = (String)(number_format($venta[0]["importe"],2,".","")); $imptotaltexto = explode(".", $tot_total);
	    	$det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

			$texto_importe = "SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES";

			$ticket = "ticket";
			if ($empresa[0]["documento"]=="20209000831") {
				$ticket = "ticket_20209000831";
			}
			if ($empresa[0]["documento"]=="20602165869") {
				$ticket = "ticket_20602165869";
			}
			if ($empresa[0]["documento"]=="20570793986") {
				$ticket = "ticket_20570793986";
			}

			$this->load->view("facturacion/formato/".$ticket,compact("empresa","sucursal","venta","totales","detalle","vendedor","credito","texto_importe"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function a4_nota($codkardex){
		if (isset($_SESSION["netix_codusuario"])) {
			$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
			$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();

			$pdf->Image('./public/img/'.$_SESSION['netix_logo'], 10, 8, 35);
	        $pdf->SetFont('Arial', 'B', 12);

	        $pdf->Cell(35, 5,"",0,0,'C');
	        $pdf->Cell(100, 5, utf8_decode(substr($_SESSION["netix_empresa"],0,35)),0,0,'L');
	        $pdf->Cell(100, 5, utf8_decode($_SESSION["netix_sucursal"]));
	        $pdf->Ln(8); $pdf->SetFont('Arial', 'B', 10);
	        $pdf->Cell(35, 5,"",0,0,'C');
	        $pdf->Cell(100, 5, utf8_decode("sdkjdsjksd"),0,0,'L');
	        $pdf->Cell(100, 5, utf8_decode($_SESSION["netix_caja"])); $pdf->Ln(5);


			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(0,7,utf8_decode("KARDEX PRODUCTO DETALLADO - DESDE "),0,1,'C');
			$pdf->SetFillColor(230,230,230);
	        $pdf->Cell(0,7,utf8_decode("sdkjjksd | UNIDAD: jdsjhsdjhsd"),1,1,'C',True); $pdf->Ln();

	        $pdf->SetFont('Arial', 'B', 8);
			$pdf->Cell(25,5,' ','LTR',0,'L',0);   // empty cell with left,top, and right borders
			$pdf->Cell(50,5,"DOCUMENTO QUE SE MODIFICA",1,0,'L',0);$pdf->Ln();

			$columnas = array("FECHA","T.DOC","N°DOC","DOC.IDEN","RAZON SOCIAL","VALOR VENTA","IGV","TOTAL");
			$w = array(15,15,20,20,20,23,10,15); $pdf->pdf_tabla_head($columnas,$w,8);

	        $pdf->SetTitle("Netix Peru - Nota de Credito"); $pdf->Output();
	    }
	}

	function guia($codguia){
		$estilo = "border-left:1px solid #000; border-right:1px solid #000;";
		$estilo1 = "border-top:1px solid #000; border-left:1px solid #000; border-right:1px solid #000;border-bottom:1px solid #000;";

		$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
		$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
		$principal = $this->db->query("select *from public.sucursales where principal=1 and estado=1")->result_array();
		$parametros = $this->db->query("select *from public.empresas limit 1")->result_array();

		$guia = $this->db->query("select guia.*, personas.documento, (up.distrito || ', ' || up.provincia || ', ' || up.departamento) as ubi_partida, (ud.distrito || ', ' || ud.provincia || ', ' || ud.departamento) as ubi_llegada from kardex.guias as guia inner join public.personas as personas on (guia.coddestinatario=personas.codpersona) inner join public.ubigeo as up on(guia.ubigeo_partida = up.codubigeo) inner join public.ubigeo as ud on(guia.ubigeo_llegada = ud.codubigeo) where guia.codguia=".$codguia)->result_array();
		$transportista = $this->db->query("select documento from public.personas where codpersona=".(int)$guia[0]["codempresa_traslado"])->result_array();
		
		$detalle = $this->db->query("select gd.*, p.descripcion as producto,u.descripcion as unidad from kardex.guiasdetalle as gd inner join almacen.productos as p on(gd.codproducto=p.codproducto) inner join almacen.unidades as u on(gd.codunidad=u.codunidad) where gd.codguia=".$codguia." and gd.estado=1 order by gd.item")->result_array();

		$html = '<table width="100%" align="center">';
			$html .= '<tr>';
				$html .= '<th style="width:20%">';
					$html .='<h4></h4> <img src="'.base_url().'public/img/'.$_SESSION['netix_logo'].'" style="height:100px">';
				$html .= '</th>';
				$html .= '<th style="width:40%">';
					$html .= '<h3>'.$empresa[0]["razonsocial"].'</h3>';
					$html .= '<p>'.$parametros[0]["slogan"].'</p>';
				$html .= '</th>';
				$html .= '<th style="width:40%;border:1px solid #000;color:#000;">';
					$html .= '<h2>RUC: '.$empresa[0]["documento"].'</h2> <h6></h6> <h3>GUIA DE REMISIÓN</h3>';
					$html .= '<h3>'.$guia[0]["seriecomprobante"].' - '.$guia[0]["nrocomprobante"].'</h3>';
				$html .= '</th>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="2" width="100%">';
			if (count($principal)>0) {
				if ($_SESSION["netix_codsucursal"]!=$principal[0]["codsucursal"]) {
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>PRINCIPAL: '.$principal[0]["direccion"].'</b> </td>';
					$html .= '</tr>';
					
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>SUCURSAL: '.$sucursal[0]["direccion"].'</b> </td>';
					$html .= '</tr>';
				}else{
					$html .= '<tr>';
						$html .= '<td style="width:100%;"> <b>'.$sucursal[0]["direccion"].'</b> </td>';
					$html .= '</tr>';
				}
			}else{
				$html .= '<tr>';
					$html .= '<td style="width:100%;"> <b>'.$sucursal[0]["direccion"].'</b> </td>';
				$html .= '</tr>';
			}
			
			$html .= '<tr>';
				$html .= '<td> <b>'.$sucursal[0]["telefonos"].'</b> </td>';
			$html .= '</tr>';
		$html .= '</table>';
		$html .= '<table cellpadding="0" width="100%"> <tr> <th style="height:5px;"></th> </tr> </table>';

		$html .= '<table cellpadding="4" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:20%;"> <b>FECHA EMISION</b> </td>';
				$html .= '<td style="'.$estilo1.';width:20%;">: '.$guia[0]["fechaemision"].' </td>';
				$html .= '<td style="'.$estilo1.';width:30%;"> <b>FECHA INICIO TRASLADO</b> </td>';
				$html .= '<td style="'.$estilo1.';width:30%;">: '.$guia[0]["fechatraslado"].' </td>';
			$html .= '</tr>';
		$html .= '</table>';

		$html .= '<table cellpadding="4" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:20%;"> <b>UBIGEO PARTIDA</b> </td>';
				$html .= '<td style="'.$estilo1.';width:80%;">: '.$guia[0]["ubi_partida"].' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:20%;"> <b>PUNTO DE PARTIDA</b> </td>';
				$html .= '<td style="'.$estilo1.';width:80%;">: '.$guia[0]["punto_partida"].' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.'"> <b>UBIGEO LLEGADA</b> </td>';
				$html .= '<td style="'.$estilo1.'">: '.$guia[0]["ubi_llegada"].' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.'"> <b>PUNTO DE LLEGADA</b> </td>';
				$html .= '<td style="'.$estilo1.'">: '.$guia[0]["punto_llegada"].' </td>';
			$html .= '</tr>';
		$html .= '</table> <h6></h6>';

		$html .= '<table cellpadding="4" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:50%;text-align:center;"> <b>DESTINATARIO</b> </td>';
				$html .= '<td style="'.$estilo1.';width:50%;text-align:center;"> <b>UNIDAD DE TRASPORTE Y CONDUCTOR</b> </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:50%;"> <b>APELLIDOS Y NOMBRES O RAZON SOCIAL</b> <br> '.$guia[0]["destinatario"].' </td>';
				$html .= '<td style="'.$estilo1.';width:50%;"> <b>NRO PLACA: </b>'.$guia[0]["nroplaca"].' <br> <b>DNI CONDUCTOR: </b>'.$guia[0]["dniconductor"].'</td>';
			$html .= '</tr>';
		$html .= '</table> <h6></h6>';

		$html .= '<table cellpadding="4" width="100%" style="border:1px solid #4E4E4E;">';
            $html .= '<tr style="background-color:#f2f2f2;">';
                $html .= '<td style="'.$estilo1.'width:6%;"> <b>ITEM</b> </td>';
                $html .= '<td style="'.$estilo1.'width:46%;"> <b>DESCRIPCION</b> </td>';
                $html .= '<td style="'.$estilo1.'width:10%;"> <b>U.M.</b> </td>';
                $html .= '<td style="'.$estilo1.'width:14%;"> <b>CANTIDAD</b> </td>';
                $html .= '<td style="'.$estilo1.'width:14%;"> <b>PRECIO UNIT.</b> </td>';
                $html .= '<td style="'.$estilo1.'width:10%;"> <b>PESO</b> </td>';
            $html .= '</tr>';
            foreach ($detalle as $key => $value) {
            	$html .= '<tr>';
	                $html .= '<td style="'.$estilo1.'width:6%;"> '.$value["item"].' </td>';
	                $html .= '<td style="'.$estilo1.'width:46%;"> '.strtoupper($value["producto"]).' </td>';
	                $html .= '<td style="'.$estilo1.'width:10%;"> '.$value["unidad"].' </td>';
	                $html .= '<td style="'.$estilo1.'width:14%;" align="right"> '.number_format($value["cantidad"],2).' </td>';
	                $html .= '<td style="'.$estilo1.'width:14%;" align="right"> '.number_format($value["preciounitario"],2).' </td>';
	                $html .= '<td style="'.$estilo1.'width:10%;" align="right"> '.number_format($value["pesokg"],2).'</td>';
	            $html .= '</tr>';
            }
            $html .= '<tr>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="4"> <b>VALOR GUIA S/</b> </td>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="2">'.number_format($guia[0]["valorguia"],2).' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="4"> <b>IGV S/</b> </td>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="2">'.number_format($guia[0]["igv"],2).' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="4"> <b>TOTAL S/</b> </td>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="2">'.number_format($guia[0]["importe"],2).' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="4"> <b>PESO TOTAL KG S/</b> </td>';
				$html .= '<td style="'.$estilo1.'text-align:right" colspan="2">'.number_format($guia[0]["pesototal"],2).' </td>';
			$html .= '</tr>';
        $html .= '</table> <h6></h6>';

        $html .= '<table cellpadding="4" width="100%">';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo1.';width:25%;"> <b>TRANSPORTISTA</b> : </td>';
				$html .= '<td style="'.$estilo1.';width:75%;">: '.$guia[0]["transportista"].' </td>';
			$html .= '</tr>';
			$html .= '<tr>';
				$html .= '<td style="'.$estilo.';"> <b>DOC. TRANSPORTISTA</b> : </td>';
				if (count($transportista) == 0) {
					$html .= '<td style="'.$estilo1.';">: - </td>';
				}else{
					$html .= '<td style="'.$estilo1.';">: '.$transportista[0]["documento"].' </td>';
				}
			$html .= '</tr>';
		$html .= '</table> <h6></h6>';

		$html .= '<table cellpadding="1" width="100%"> <tr> <th style="height:10px;"></th> </tr> </table>';
		$html .= '<h4 style="color:#000;" align="center">'.$parametros[0]["publicidad"].'</h4>';

		$this->load->library('Pdf');

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor("WEB NETIX");
        $pdf->SetTitle("WEB NETIX | IMPRIMIR GUIA");
        $pdf->SetSubject("WEB NETIX");

        $pdf->setPrintHeader(false);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 9);
        $pdf->AddPage("A");
        $pdf->writeHTML($html, true, 0, true, 0);
        //$pdf->writeHTML($html, true, false, true, false, '');

        $nombre_archivo = utf8_decode("Guia de remision.pdf");
        $pdf->Output($nombre_archivo, 'I');
	}
}