<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH."/third_party/netix_ticket/autoload.php";
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class Ticket extends CI_Controller {

	public function imprimir_venta($codkardex){
		$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
		$configuracion = $this->db->query("select *from public.empresas limit 1")->result_array();

		$venta = $this->db->query("select k.seriecomprobante,k.nrocomprobante,k.fechacomprobante, k.condicionpago,k.subtotal, k.porcdescuento, k.descuentoglobal, k.porcigv, k.igv, 0.00 as inafectas, 0.00 as gratuitas, (select sum(importe) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select sum(importe) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, k.importe,dt.oficial as coddocumento, p.codpersona, p.documento, k.cliente,k.direccion,ct.descripcion as tipo,k.nroplaca,k.codempleado from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join public.documentotipos as dt on(p.coddocumentotipo=dt.coddocumentotipo) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
		$vendedor = $this->db->query("select razonsocial from public.personas where codpersona=".$venta[0]["codempleado"])->result_array();

		$detalle = $this->db->query("select kd.*,p.descripcion as producto,u.descripcion as unidad from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codkardex." and kd.estado=1 order by kd.item")->result_array();

		try {
		    $connector = new WindowsPrintConnector($configuracion[0]["ticketera"]);
			$printer = new Printer($connector);
		} catch (Exception $e) {
		    echo 'ERROR DE IMPRESION DE TICKET ',  $e->getMessage(), "\n"; exit();
		}

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		
		/* try{
			$logo = EscposImage::load("./public/img/".$_SESSION['netix_logo']);
			$printer->graphics($logo);
		}catch(Exception $e){
			// No hacemos nada si hay error
		} */

		$printer->text($empresa[0]["razonsocial"]."\n");
		$printer->text($empresa[0]["direccion"]."\n");
		$printer->text("RUC: ".$empresa[0]["documento"]."\n");
		$printer->text($empresa[0]["telefono"]."\n");
		$printer->text("---------------------------------------" . "\n");
		$printer->text(date("Y-m-d H:i:s A") ."\n");
		$printer->text($venta[0]["tipo"]."\n");
		$printer->text($venta[0]["seriecomprobante"]."-".$venta[0]["nrocomprobante"]."\n");

		$printer->setJustification(Printer::JUSTIFY_LEFT);

		$printer->text("CLIENTE: ".$venta[0]["cliente"]."\n");
		$printer->text($venta[0]["direccion"]."\n");
		$printer->text("DNI / RUC: ".$venta[0]["documento"]."\n");
		$printer->text("---------------------------------------" . "\n");
		$printer->text("CANT       PRODUCTO         P.U.  TOTAL" . "\n");
		$printer->text("---------------------------------------" . "\n");

		foreach ($detalle as $value) {
			$printer->setJustification(Printer::JUSTIFY_LEFT);
		    $printer->text(number_format($value["cantidad"],2)." ".$value["unidad"]." ".$value["producto"]." ".number_format($value["precioconigv"],2)." ".number_format($value["subtotal"],2). "\n");
		}
		 
		$printer->setJustification(Printer::JUSTIFY_RIGHT);
		$printer->text("--------------------------------------" . "\n");
		$printer->text("OP GRAVADAS S/: ".number_format($venta[0]["gravado"],2)."\n");
		$printer->text("OP INAFECTAS S/: ".number_format($venta[0]["inafectas"],2)."\n");
		$printer->text("OP EXONERADAS S/: ".number_format($venta[0]["exonerado"],2)."\n");
		$printer->text("OP GRATUITAS S/: ".number_format($venta[0]["gratuitas"],2)."\n");
		$printer->text("DESCUENTO S/: ".number_format($venta[0]["descuentoglobal"],2)."\n");
		$printer->text("IGV S/: ".number_format($venta[0]["igv"],2)."\n");
		$printer->text("TOTAL S/:".number_format($venta[0]["importe"],2)."\n");

		$printer->setJustification(Printer::JUSTIFY_LEFT);

		$this->load->library("Number"); $number = new Number();
		$tot_total = (String)(number_format($venta[0]["importe"],2)); $imptotaltexto = explode(".", $tot_total);
    	$det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

		$printer->text("SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES"."\n");

		$printer->text("--------------------------------------" . "\n");
		$printer->text("CAJERO(A): ".$_SESSION["netix_usuario"]." - ".$_SESSION["netix_caja"]."\n");
		if (count($vendedor)==0) {
			$printer->text("VENDEDOR(A): SIN VENDEDOR"."\n");
		}else{
			$printer->text("VENDEDOR(A): ".$vendedor[0]["razonsocial"]."\n");
		}

		$printer->text("NRO. AUTORIZACION: 0183845126059"."\n");
		$printer->text("SERIE: FFCF287092"."\n");
		$printer->text("GRACIAS POR SU COMPRA"."\n");

		$printer->feed(1);
		$printer->cut();
		
		$printer->pulse();
		$printer->close();
	}
}