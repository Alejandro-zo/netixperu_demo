<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Caja extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$ambientes = $this->db->query("select *from restaurante.ambientes where codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1 order by codambiente asc")->result_array();
				$lineas = $this->db->query("select *from almacen.lineas where estado=1 order by descripcion asc")->result_array();

				$comprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, ct.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codcomprobantetipo>=5 and c.estado=1")->result_array();
				$conceptos = $this->db->query("select *from caja.conceptos where codconcepto=13 or codconcepto=15")->result_array();
				$tipopagos = $this->db->query("select *from caja.tipopagos where ingreso=1 and estado=1 order by codtipopago")->result_array();
				$vendedores = $this->db->query("select persona.codpersona,persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1 and empleado.codcargo=4")->result_array();
				$configuracion = $this->db->query("select sinstockventa,itemrepetirventa from public.empresas where codempresa=".$_SESSION["netix_codempresa"])->result_array();
				$sucursal = $this->db->query("select codcomprobantetipo, seriecomprobante from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

				$this->load->view("restaurante/atender/index",compact("ambientes","lineas","comprobantes","conceptos","tipopagos","vendedores","configuracion","sucursal"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function avance_pedido($codpedido){
		if (isset($_SESSION["netix_usuario"])) {
			$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
			$info = $this->db->query("select pe.razonsocial, (select COALESCE(string_agg(nromesa::text,' - ')) from restaurante.mesaspedido where codpedido=".$codpedido.") as mesa, p.valorventa,p.igv,p.importe from kardex.pedidos as p inner join public.personas as pe on(p.codempleado=pe.codpersona) where p.codpedido=".$codpedido)->result_array();
			$detalle = $this->db->query("select kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal from kardex.pedidosdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codpedido=".$codpedido)->result_array();

			$html = "<div style='width:300px;text-aling:center;'>";
				$html .= "<img src='".base_url()."public/img/".$_SESSION['netix_logo']."'>";
				$html .= "<h2 align='center'>".utf8_decode($empresa[0]["nombrecomercial"])."</h2> ";
				$html .= "<h3 align='center'> AVANCE DE CUENTA PEDIDO </h3> ";
				$html .= "<p align='center'> COMPROBANTE NO AUTORIZADO ".utf8_decode("PEDIDO N° 0000".$codpedido)."</p> ";

				$html .= '<table cellpadding="2" width="300px" style="border:1px solid #f2f2f2;font-size:12px;">';
		            $html .= '<tr>';
		                $html .= '<td>CANT</td>';
		                $html .= '<td>DESCRIPCION</td>';
		                $html .= '<td>P.U.</td>';
		                $html .= '<td>IMPORT.</td>';
		            $html .= '</tr>';
			        foreach ($detalle as $key => $value) {
			        	$html .= '<tr>';
			                $html .= '<td>'.round($value["cantidad"],1)." ".$value["unidad"].'</td>';
			                $html .= '<td>'.utf8_decode($value["producto"]).'</td>';
			                $html .= '<td>'.number_format($value["preciounitario"],2).'</td>';
			                $html .= '<td>'.number_format($value["subtotal"],2).'</td>';
			            $html .= '</tr>';
					}

					$html .= '<tr>';
		                $html .= '<td colspan="3">SUBTOTAL</td>';
		                $html .= '<td>'.number_format($info[0]["valorventa"],2).'</td>';
		            $html .= '</tr>';
		            $html .= '<tr>';
		                $html .= '<td colspan="3">I.G.V.</td>';
		                $html .= '<td>'.number_format($info[0]["igv"],2).'</td>';
		            $html .= '</tr>';
		            $html .= '<tr>';
		                $html .= '<td colspan="3">TOTAL</td>';
		                $html .= '<td>'.number_format($info[0]["importe"],2).'</td>';
		            $html .= '</tr>';
				$html .= '</table>';

				$this->load->library("Number"); $number = new Number();
				$tot_total = (String)(number_format($info[0]["importe"],2)); $imptotaltexto = explode(".", $tot_total);
		    	$det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

				$html .= "<p align='center' style='font-size:11px;'>".utf8_decode("SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES")."</p> ";
				$html .= "<p align='center' style='font-size:11px;'>".utf8_decode("MESA:".$info[0]["mesa"]."	MOZO: ".$info[0]["razonsocial"])."</p> ";

				$html .= "<p align='center' style='font-size:11px;'>.... BOLETA ELECTRONICA  .... FACTURA ELECTRONICA</p> ";
				$html .= "<p style='font-size:11px;'>RAZON SOCIAL:</p> ";
				$html .= "<p style='font-size:11px;'>RUC:</p> ";
				$html .= "<p align='center' style='font-size:11px;'>GRACIAS POR SU PREFERENCIA</p> ";
			$html .= "</div>";

			echo $html;

			/* $this->load->library("Ticket");
			$pdf = new Ticket(); $pdf->AddPage(); $padding_x = 2;

			$pdf->Image('./public/img/'.$_SESSION['netix_logo'],13,10,50,30);

			$pdf->SetFont('Arial','B',14); $pdf->setY(45); $pdf->setX($padding_x);
			$pdf->MultiCell(75,4,utf8_decode($empresa[0]["nombrecomercial"]),0,"C",false); $pdf->Ln(2);

			$pdf->SetFont('Arial','B',10); $pdf->setX(2); $pdf->MultiCell(75,4,"AVANCE DE CUENTA PEDIDO",0,"C",false);
			$pdf->SetFont('Arial','B',9); 
			$pdf->setX(2); $pdf->MultiCell(75,3,"COMPROBANTE NO AUTORIZADO",0,"C",false); $pdf->Ln(3);
			$pdf->setX(2); $pdf->MultiCell(75,3,utf8_decode("PEDIDO N° 0000".$codpedido),0,"C",false);

			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,"FECHA: ".date("d-m-Y")." 		HORA: ".date("H:i:s"),0,"C",false);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false);
			$columnas = array("CANT","UNIDAD","DESCRIPCION","P.U.","IMP."); 
			$w = array(10,13,30,10,10); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,7);
			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false); 

			$pdf->SetWidths(array(10,13,30,10,10)); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',6);
			foreach ($detalle as $key => $value) {
				$pdf->setX(4); $datos = array(round($value["cantidad"],3));
				array_push($datos,utf8_decode($value["unidad"]));
				array_push($datos,utf8_decode($value["producto"]));
				array_push($datos,number_format($value["preciounitario"],2));
				array_push($datos,number_format($value["subtotal"],2));
                $pdf->Row($datos);
			}
			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false); 
			
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("SUBTOTAL: ".number_format($info[0]["valorventa"],2)."    "),0,"R",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("I.G.V: ".number_format($info[0]["igv"],2)."    "),0,"R",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("TOTAL: ".number_format($info[0]["importe"],2)."    "),0,"R",false);

			$this->load->library("Number"); $number = new Number();
			$tot_total = (String)(number_format($info[0]["importe"],2)); $imptotaltexto = explode(".", $tot_total);
	    	$det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

			$pdf->SetFont('Arial','',8);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES"),0,"L",false);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false);

			$pdf->SetFont('Arial','',7); 
			$pdf->setX(2); $pdf->MultiCell(75,3,utf8_decode("MESA:".$info[0]["mesa"]."	MOZO: ".$info[0]["razonsocial"]),0,"C",false); $pdf->Ln(1);
			$pdf->setX(2); $pdf->MultiCell(75,3,"... BOLETA ELECTRONICA  ... FACTURA ELECTRONICA",0,"C",false); $pdf->Ln(3); 
			$pdf->setX(4); $pdf->MultiCell(75,3,"RAZON SOCIAL:",0,"L",false);
			$pdf->setX(4); $pdf->MultiCell(75,3,"RUC:",0,"L",false); $pdf->Ln(3);
			$pdf->setX(2); $pdf->MultiCell(75,3,"GRACIAS POR SU PREFERENCIA",0,"C",false);
			$pdf->AutoPrint(true); $pdf->Output(); */
		}
	}

	function cobrar_pedido($codkardex){
		if (isset($_SESSION["netix_usuario"])) {
			$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
			$info = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$codkardex)->result_array();
			$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$codkardex." and codafectacionigv='21') as gratuito")->result_array();
			$detalle = $this->db->query("select kd.cantidad,p.descripcion as producto,u.descripcion as unidad,kd.preciounitario,kd.subtotal from kardex.kardexdetalle as kd inner join almacen.productos as p on(p.codproducto=kd.codproducto) inner join almacen.unidades as u on(u.codunidad=kd.codunidad) where kd.codkardex=".$codkardex)->result_array();

			$this->load->library("Ticket");
			$pdf = new Ticket(); $pdf->AddPage(); $padding_x = 2;
			$pdf->Image('./public/img/'.$_SESSION['netix_logo'],13,10,50,30);

			$pdf->SetFont('Arial','B',14); $pdf->setY(50); $pdf->setX($padding_x);
			$pdf->MultiCell(75,4,utf8_decode($empresa[0]["nombrecomercial"]),0,"C",false);
			$pdf->SetFont('Arial','B',9);
			$pdf->setX($padding_x); $pdf->MultiCell(75,4,utf8_decode($empresa[0]["razonsocial"]),0,"C",false);
			
			$pdf->SetFont('Arial','',7); $pdf->Ln(1);
			$pdf->setX($padding_x); $pdf->MultiCell(75,4,utf8_decode($empresa[0]["direccion"]),0,"C",false);
			$pdf->SetFont('Arial','',9); $pdf->Ln(1);
			$pdf->setX($padding_x); $pdf->MultiCell(75,4,"RESERVACIONES Y DELIBERY",0,"C",false);
			$pdf->SetFont('Arial','',7); $pdf->Ln(1);
			$pdf->setX($padding_x); $pdf->MultiCell(75,4,"TELF/CEL: ".utf8_decode($empresa[0]["telefono"]),0,"C",false); $pdf->Ln(3);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);
			$pdf->SetFont('Arial','',8);

			$pdf->SetFont('Arial','B',11);
			$pdf->setX($padding_x); $pdf->MultiCell(75,4,"RUC: ".utf8_decode($empresa[0]["documento"]),0,"C",false); $pdf->Ln(1);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,utf8_decode($info[0]["comprobante"]),0,"C",false); $pdf->Ln(1);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,$info[0]["seriecomprobante"]."-".$info[0]["nrocomprobante"],0,"C",false); $pdf->Ln(2);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);

			$pdf->SetFont('Arial','',8); $pdf->Ln(2);
			$pdf->setX(4); $pdf->MultiCell(75,3,"FECHA: ".$info[0]["fechacomprobante"]." 		HORA: ".date("H:i:s"),0,"L",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("NOMBRE O RAZON SOCIAL: ".$info[0]["cliente"]),0,"L",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("DNI/RUC: ".$info[0]["documento"]),0,"L",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("DIRECCION: ".$info[0]["direccion"]),0,"L",false); $pdf->Ln(1);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);
			$columnas = array("PRODUCTO","CANT","UNIDAD","P.U.","IMP."); 
			$w = array(30,10,13,10,10); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);

			$pdf->SetWidths(array(30,10,13,10,10)); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',7);
			foreach ($detalle as $key => $value) {
				$pdf->setX(4);
				$datos = array(utf8_decode($value["producto"]));
				array_push($datos,round("  ".$value["cantidad"],3));
				array_push($datos,utf8_decode(substr($value["unidad"],0,7)));
				array_push($datos,number_format($value["preciounitario"],2));
				array_push($datos,number_format($value["subtotal"],2));
                $pdf->Row($datos);
			}
			$pdf->SetFont('Arial','B',8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);
			$pdf->SetFont('Arial','',8);

			$pdf->SetWidths(array(30,33,10)); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',7);
			$pdf->setX(4);
			if ($info[0]["codcomprobantetipo"]==10 || $info[0]["codcomprobantetipo"]==12) {
				$pdf->setX(4); $datos = array(""); array_push($datos,"OP. GRAVADAS S/"); array_push($datos,number_format($totales[0]["gravado"],2));
                $pdf->Row($datos);
                $pdf->setX(4); $datos = array(""); array_push($datos,"OP. INAFECTAS S/"); array_push($datos,number_format($totales[0]["inafecto"],2));
                $pdf->Row($datos);
                $pdf->setX(4); $datos = array(""); array_push($datos,"OP. EXONERADAS S/"); array_push($datos,number_format($totales[0]["exonerado"],2));
                $pdf->Row($datos);
                $pdf->setX(4); $datos = array(""); array_push($datos,"OP. GRATUITAS S/"); array_push($datos,number_format($totales[0]["gratuito"],2));
                $pdf->Row($datos);
			}else{
				$pdf->setX(4); $datos = array(""); array_push($datos,"SUBTOTAL S/"); array_push($datos,number_format($info[0]["valorventa"],2));
                $pdf->Row($datos);
			}
			$pdf->setX(4); $datos = array(""); array_push($datos,"DESCUENTOS S/"); array_push($datos,number_format($info[0]["descglobal"],2));
            $pdf->Row($datos);
            $pdf->setX(4); $datos = array(""); array_push($datos,"I.G.V. S/"); array_push($datos,number_format($info[0]["igv"],2));
            $pdf->Row($datos);
            $pdf->setX(4); $datos = array(""); array_push($datos,"TOTAL S/"); array_push($datos,number_format($info[0]["importe"],2));
            $pdf->Row($datos);

            $pdf->SetFont('Arial','B',8);
			$pdf->setX($padding_x); $pdf->MultiCell(75,3,'----------------------------------------------------------------------------',0,"C",false);

			$pagos = $this->db->query("select tp.descripcion, md.* from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) inner join caja.tipopagos as tp on(tp.codtipopago=md.codtipopago) where m.codkardex=".$codkardex." order by tp.codtipopago desc")->result_array();
			foreach ($pagos as $vp) {
				$pdf->setX(4); $pdf->SetFont('Arial','',8); 
				$datos = array(""); array_push($datos,utf8_decode($vp["descripcion"]."  S/ ")); array_push($datos,number_format($vp["importeentregado"],2));
				$pdf->Row($datos);
				
				// $pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode($vp["descripcion"]."  S/ ".number_format($vp["importeentregado"],2)."    "),0,"R",false);
				if ($vp["codtipopago"]==1) {
					$pdf->setX(4); $pdf->SetFont('Arial','',8); 
					$datos = array(""); array_push($datos,utf8_decode("VUELTO    S/ ")); array_push($datos,number_format($vp["vuelto"],2));
					$pdf->Row($datos);

					// $pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("VUELTO    S/ ".number_format($vp["vuelto"],2)."    "),0,"R",false);
				}
			}

			$this->load->library("Number"); $number = new Number();
			$tot_total = (String)(number_format($info[0]["importe"],2)); $imptotaltexto = explode(".", $tot_total);
	    	$det_imptotaltexto = $number->convertirNumeroEnLetras($imptotaltexto[0]);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("SON ".strtoupper($det_imptotaltexto)." Y ".$imptotaltexto[1]."/100 SOLES"),0,"L",false);
			
			$pedido = $this->db->query("select codpedido from kardex.pedidos where codkardex=".$codkardex)->result_array();
			$infom = $this->db->query("select pe.razonsocial, (select COALESCE(string_agg(nromesa::text,' - ')) from restaurante.mesaspedido where codpedido=".$pedido[0]["codpedido"].") as mesa, p.valorventa,p.igv,p.importe from kardex.pedidos as p inner join public.personas as pe on(p.codempleado=pe.codpersona) where p.codpedido=".$pedido[0]["codpedido"])->result_array();

			$pdf->SetFont('Arial','',8); $pdf->Ln(4);
			$pdf->setX(4); $pdf->MultiCell(75,4,"ITEMS: ".count($detalle)." | MESA: ".$infom[0]["mesa"]." | CAJA: ".$_SESSION["netix_caja"],0,"L",false);
			$pdf->setX(4); $pdf->MultiCell(75,4,utf8_decode("ATENDIDO POR: ".$infom[0]["razonsocial"]),0,"L",false); $pdf->Ln(1);

			$textoqr = $empresa[0]["razonsocial"]."|".$info[0]["seriecomprobante"]."|".$info[0]["nrocomprobante"]."|".number_format($info[0]["igv"],2)."|".number_format($info[0]["importe"],2)."|".$info[0]["fechacomprobante"]."|".$info[0]["documento"];

			$this->load->library('ciqrcode');
	        $params['data'] = $textoqr; $params['level'] = 'H'; $params['size'] = 5;
	        $params['savename'] = "./sunat/webnetix/qrcode.png";
	        $this->ciqrcode->generate($params);
	        // chmod("./sunat/webnetix/qrcode.png", 0777);
	        
	        $archivo_error = APPPATH."/logs/qrcode.png-errors.txt"; unlink($archivo_error);
			$altura = (count($detalle) * 6) + (31 * 6);
			$pdf->Image('./sunat/webnetix/qrcode.png',30,$altura,20,20); $pdf->setY($altura + 20);

			$pdf->setX(2); $pdf->MultiCell(75,4,utf8_decode("CONSULTA TU COMPROBANTE EN"),0,"C",false);
			$pdf->setX(2); $pdf->MultiCell(75,4,utf8_decode("http://netixperu.com/sunat"),0,"C",false); $pdf->Ln(3);
			$pdf->SetFont('Arial','B',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,utf8_decode("BIENES TRANSFERIDOS / SERVICIOS PRESTADOS EN LA REGIÓN DE LA SELVA PARA SER CONSUMIDOS EN LA MISMA"),0,"C",false);

			$pdf->setX(2); $pdf->MultiCell(75,4,utf8_decode("___________________________________________"),0,"C",false); $pdf->Ln();

			$pdf->AutoPrint(); $pdf->Output();
		}
	}

	function venta_diaria($codcontroldiario = 0){
		if (isset($_SESSION["netix_usuario"])) {
			$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
	
			$this->load->library("Ticket");
			$pdf = new Ticket(); $pdf->AddPage();
			$pdf->Image('./public/img/'.$_SESSION['netix_logo'],13,10,50,30);

			$pdf->SetFont('Arial','B',12); $pdf->setY(45); $pdf->setX(2);
			$pdf->MultiCell(75,4,utf8_decode($empresa[0]["nombrecomercial"]),0,"C",false);
			$pdf->SetFont('Arial','B',9);
			$pdf->setX(2); $pdf->MultiCell(75,4,"DE: ".utf8_decode($empresa[0]["razonsocial"]),0,"C",false);
			$pdf->setX(2); $pdf->MultiCell(75,4,"RUC: ".utf8_decode($empresa[0]["documento"]),0,"C",false);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false);
			$pdf->SetFont('Arial','B',10); $pdf->setX(2); $pdf->MultiCell(75,4,"VENTA DIARIA POR PRODUCTO",0,"C",false);
			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,"FECHA: ".date("d-m-Y")." 		HORA: ".date("H:i:s"),0,"C",false); $pdf->Ln(5);

			$columnas = array("PRODUCTO O SERVICIO","CANTIDAD","IMPORTE"); 
			$w = array(35,20,20); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8);

			if ($codcontroldiario==0) {
				$codcontroldiario = $_SESSION["netix_codcontroldiario"];
			}

			$lineas = $this->db->query("select *from almacen.lineas")->result_array();
			
			$pdf->SetWidths(array(35,20, 20)); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',7);
			$total = 0; $cantidad = 0;
			foreach ($lineas as $v) {
				$lista = $this->db->query("select kd.codproducto,kd.codunidad,p.descripcion as producto,u.descripcion as unidad, coalesce(sum(kd.cantidad),0) as cantidad, coalesce(sum(kd.subtotal),0) as importe from kardex.kardex as k inner join caja.movimientos as mov on(k.codkardex=mov.codkardex) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where k.codmovimientotipo=20 and k.codsucursal=".$_SESSION["netix_codsucursal"]." and mov.codcontroldiario=".$codcontroldiario." and p.codlinea=".$v["codlinea"]." and k.estado=1 group by kd.codproducto, kd.codunidad, p.descripcion, u.descripcion")->result_array();
				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',8);
					$pdf->MultiCell(73,5,"LINEA: ".$v["descripcion"],0,"L",false);
					$pdf->SetFont('Arial','',7);
				}
				foreach ($lista as $key => $value) {
					$pdf->setX(4); $datos = array(utf8_decode($value["producto"]."-".$value["unidad"]));
					array_push($datos,number_format($value["cantidad"],2));
					array_push($datos,number_format($value["importe"],2));
	                $pdf->Row($datos);
	                $total = $total + $value["importe"]; $cantidad = $cantidad + $value["cantidad"];
				}
			}

			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false);
			$columnas = array("TOTALES",number_format($cantidad,2),number_format($total,2)); 
			$w = array(35,20,20); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8);
			$pdf->AutoPrint(); $pdf->Output();
		}
	}

	function balance_caja($codcontroldiario = 0){
		if (isset($_SESSION["netix_usuario"])) {
			$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
	
			$this->load->library("Ticket");
			$pdf = new Ticket(); $pdf->AddPage();
			$pdf->Image('./public/img/'.$_SESSION['netix_logo'],13,10,50,30);

			$pdf->SetFont('Arial','B',12); $pdf->setY(45); $pdf->setX(2);
			$pdf->MultiCell(75,4,utf8_decode($empresa[0]["nombrecomercial"]),0,"C",false);
			$pdf->SetFont('Arial','B',9);
			$pdf->setX(2); $pdf->MultiCell(75,4,"DE: ".utf8_decode($empresa[0]["razonsocial"]),0,"C",false);
			$pdf->setX(2); $pdf->MultiCell(75,4,"RUC: ".utf8_decode($empresa[0]["documento"]),0,"C",false);

			$pdf->SetFont('Arial','B',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-----------------------------------------------------------------------------',0,"C",false);
			$pdf->SetFont('Arial','B',10); $pdf->setX(2); $pdf->MultiCell(75,4,"BALANCE DE CAJA",0,"C",false);
			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,"FECHA: ".date("d-m-Y")." 		HORA: ".date("H:i:s"),0,"C",false); $pdf->Ln(5);
			
			if ($codcontroldiario == 0) {
				$codcontroldiario = $_SESSION["netix_codcontroldiario"];
			}

			$fechas = $this->db->query("select distinct(fechamovimiento) from caja.movimientos where codcontroldiario=".$codcontroldiario)->result_array();
			$total_efectivo_ingreso = 0; $total_efectivo_egreso = 0;

			foreach ($fechas as $key => $value) {
				$pdf->SetFillColor(230,230,230);
				$pdf->setX(2); $pdf->MultiCell(75,6,"FECHA PROCESO: ".$value["fechamovimiento"],0,"C",true); $pdf->Ln(5);

				$w = array(55,20); $pdf->SetWidths($w); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',8); $pdf->setX(4);
				$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();

				$otros = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex=0 and m.fechamovimiento='".$value["fechamovimiento"]."' and m.tipomovimiento=1 and m.estado=1")->result_array();
				$datos = array("OTROS INGRESOS"); array_push($datos,number_format($otros[0]["importe"],2)); 
				$pdf->Row($datos); $pdf->Ln(2);
				$columnas = array("VENTAS","IMPORTE"); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8); 
				$pdf->SetFont('Arial','',8); $pdf->Ln(1);

				$total_ventas = $otros[0]["importe"];
				foreach ($tipopagos as $key => $val) {
					$venta = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex>0 and m.fechamovimiento='".$value["fechamovimiento"]."' and m.tipomovimiento=1 and md.codtipopago=".$val["codtipopago"]." and m.estado=1")->result_array();
					$total_ventas = $total_ventas + $venta[0]["importe"];

					$pdf->setX(4); $datos = array(utf8_decode($val["descripcion"])); 
					array_push($datos,number_format($venta[0]["importe"],2));
	                $pdf->Row($datos); $pdf->Ln(1);
				}
				$columnas = array("TOTAL INGRESOS",number_format($total_ventas,2)); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8); $pdf->Ln(1);
				$pdf->SetFont('Arial','',8);
				$pdf->setX(2); $pdf->MultiCell(75,3,'-------------------------------------------------------------------------',0,"C",false);

				$totalingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.fechamovimiento='".$value["fechamovimiento"]."' and codkardex>0 and m.tipomovimiento=1 and md.codtipopago=1 and m.estado=1")->result_array();

				$totalegresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.fechamovimiento='".$value["fechamovimiento"]."' and m.tipomovimiento=2 and md.codtipopago=1 and m.estado=1")->result_array();

				$pdf->setX(4);
				$datos = array("TOTAL INGRESOS EFECTIVO"); array_push($datos,number_format($totalingresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(2);
				$pdf->setX(4);
				$datos = array("TOTAL EGRESOS EFECTIVO"); array_push($datos,number_format($totalegresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(0);
				$pdf->setX(4); $pdf->SetFont('Arial','',8);
				$pdf->setX(2); $pdf->MultiCell(75,3,'-------------------------------------------------------------------------',0,"C",false);
				$pdf->setX(4); $datos = array("TOTAL EFECTIVO DISP."); array_push($datos,number_format($totalingresos[0]["importe"] - $totalegresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(2);
			}
			
			$pdf->Ln(5); $pdf->setX(2); 
			$pdf->MultiCell(75,3,'-------------------------------------------------------------------------',0,"C",false);
			$pdf->SetFillColor(230,230,230);
			$pdf->setX(2); $pdf->MultiCell(75,6,"TOTAL GENERAL CAJA",0,"C",true); $pdf->Ln(5);

			$w = array(55,20); $pdf->SetWidths($w); $pdf->SetLineHeight(3); $pdf->SetFont('Arial','',8); $pdf->setX(4);
			$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();

			$otros = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex=0 and m.tipomovimiento=1 and m.estado=1")->result_array();
			$datos = array("OTROS INGRESOS"); array_push($datos,number_format($otros[0]["importe"],2)); 
			$pdf->Row($datos); $pdf->Ln(2);
			$columnas = array("VENTAS","IMPORTE"); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8); 
			$pdf->SetFont('Arial','',8); $pdf->Ln(1);

			$total_ventas = $otros[0]["importe"]; $total_otros = $otros[0]["importe"];
			foreach ($tipopagos as $key => $val) {
				$venta = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.codkardex>0 and m.tipomovimiento=1 and md.codtipopago=".$val["codtipopago"]." and m.estado=1")->result_array();
				if ($val["codtipopago"]==1) {
					$total_otros = $total_otros + $venta[0]["importe"];
				}
				$total_ventas = $total_ventas + $venta[0]["importe"];

				$pdf->setX(4); $datos = array(utf8_decode($val["descripcion"])); 
				array_push($datos,number_format($venta[0]["importe"],2));
                $pdf->Row($datos); $pdf->Ln(1);
			}
			$columnas = array("TOTAL INGRESOS",number_format($total_ventas,2)); $pdf->setX(4); $pdf->pdf_tabla_head($columnas,$w,8); $pdf->Ln(1);
			$pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-------------------------------------------------------------------------',0,"C",false);

			$totalingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and codkardex>0 and m.tipomovimiento=1 and md.codtipopago=1 and m.estado=1")->result_array();

			$totalegresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and md.codtipopago=1 and m.estado=1")->result_array();

			$pdf->setX(4);
			$datos = array("TOTAL INGRESOS EFECTIVO"); array_push($datos,number_format($totalingresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(2);
			$pdf->setX(4);
			$datos = array("TOTAL EGRESOS EFECTIVO"); array_push($datos,number_format($totalegresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(0);
			$pdf->setX(4); $pdf->SetFont('Arial','',8);
			$pdf->setX(2); $pdf->MultiCell(75,3,'-------------------------------------------------------------------------',0,"C",false);
			$pdf->setX(4); $datos = array("TOTAL EFECTIVO DISP."); array_push($datos,number_format($total_otros - $totalegresos[0]["importe"],2)); $pdf->Row($datos); $pdf->Ln(2);


			$pdf->AutoPrint(); $pdf->Output();
		}
	}

	function pdf_vendedores_caja_directo($codcontroldiario = 0){
		if ($codcontroldiario == 0) {
			$codcontroldiario = $_SESSION["netix_codcontroldiario"];
		}

		$productos = $this->db->query("select distinct(kd.codproducto),kd.codunidad,p.descripcion as producto,u.descripcion as unidad, round(avg(kd.preciounitario),2) as preciounitario from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) inner join caja.movimientos as m on(k.codkardex=m.codkardex) where k.codmovimientotipo=20 and p.codlinea=3 and m.codcontroldiario=".$codcontroldiario." and k.estado=1 group by kd.codproducto, kd.codunidad, p.descripcion, u.descripcion order by preciounitario desc, kd.codproducto")->result_array();
		$detalle = $productos;

		$vendedores = $this->db->query("select persona.codpersona,persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1 and empleado.codcargo=4 order by persona.razonsocial asc")->result_array();
		foreach ($vendedores as $key => $value) {
			$total = 0; $cantidad = 0; $cantidad_descontar = 2; $item = 0;
			foreach ($detalle as $k => $v) {
				$suventa = $this->db->query("select kd.codproducto,kd.codunidad,p.descripcion as producto,u.descripcion as unidad, coalesce(sum(kd.cantidad),0) as cantidad, avg(kd.preciounitario) as preciounitario, coalesce(sum(kd.subtotal),0) as importe from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) inner join caja.movimientos as m on(k.codkardex=m.codkardex) where k.codmovimientotipo=20 and k.codempleado=".$value["codpersona"]." and kd.codproducto=".$v["codproducto"]." and kd.codunidad=".$v["codunidad"]." and m.codcontroldiario=".$codcontroldiario." and k.estado=1 group by kd.codproducto, kd.codunidad, p.descripcion, u.descripcion order by preciounitario desc")->result_array();
				if (count($suventa)==0) {
					$detalle[$k]["cantidad"] = 0;
					$detalle[$k]["primeros"] = 0;
					$detalle[$k]["preciounitario"] = 0;
					$detalle[$k]["importe"] = 0;
					$detalle[$k]["dia"] = 0;
				}else{
					$cantidad = $suventa[0]["cantidad"];
					if ($cantidad_descontar > 0) {
						if ( $suventa[0]["cantidad"] >= $cantidad_descontar ) {
							$cantidad = $suventa[0]["cantidad"] - $cantidad_descontar; $cantidad_descontar = 0;
						}else{
							$cantidad = $suventa[0]["cantidad"] - 1; $cantidad_descontar = $cantidad_descontar - 1;
						}
					}

					$dia = ($suventa[0]["preciounitario"] * $cantidad * 0.40) - ($cantidad * 1);
					$total = $total + $dia;
					$detalle[$k]["cantidad"] = number_format($suventa[0]["cantidad"],0);
					$detalle[$k]["primeros"] = number_format($cantidad,0);
					$detalle[$k]["preciounitario"] = number_format($suventa[0]["preciounitario"],2);
					$detalle[$k]["importe"] = number_format($suventa[0]["preciounitario"] * $cantidad,2);
					$detalle[$k]["dia"] = number_format($dia,2);
				}
			}
			$vendedores[$key]["detalle"] = $detalle;
			$vendedores[$key]["total"] = number_format($total,2);
		}
		$this->load->view("restaurante/atender/ventas", compact("productos", "vendedores"));
	}

	function pdf_vendedores_caja($codcontroldiario = 0){
		$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
		$pdf->pdf_header("REPORTE VENTAS EMPLEADOS","");

		if ($codcontroldiario == 0) {
			$codcontroldiario = $_SESSION["netix_codcontroldiario"];
		}

		// $caja = $this->db->query("select *from caja.controldiario where codcontroldiario=".$codcontroldiario)->result_array();
		
		$vendedores = $this->db->query("select persona.codpersona,persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1 and empleado.codcargo=4 order by persona.razonsocial asc")->result_array();
		foreach ($vendedores as $key => $value) {
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(0,7,utf8_decode($value["razonsocial"]),0,1,'C',0); $pdf->Ln(2);

			$productos = $this->db->query("select kd.codproducto,kd.codunidad,p.descripcion as producto,u.descripcion as unidad, coalesce(sum(kd.cantidad),0) as cantidad, avg(kd.preciounitario) as preciounitario, coalesce(sum(kd.subtotal),0) as importe from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) inner join caja.movimientos as m on(k.codkardex=m.codkardex) where k.codmovimientotipo=20 and p.codlinea=3 and k.codempleado=".$value["codpersona"]." and m.codcontroldiario=".$codcontroldiario." and k.estado=1 group by kd.codproducto, kd.codunidad, p.descripcion, u.descripcion order by preciounitario desc")->result_array();

			$columnas = array("DESCRIPCION","CANTIDAD","CANTIDAD - 2","PRECIO UNI.","TOTAL", "COMISION 40%","DESC","PAGAR");
			$w = array(60,17,22,20,20,25,10,18); $pdf->pdf_tabla_head($columnas,$w,8);

			$pdf->SetWidths($w); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',8);
			$total = 0; $cantidad = 0; $cantidad_descontar = 2;
			foreach ($productos as $val) {
				$cantidad = $val["cantidad"];
				if ($cantidad_descontar > 0) {
					if ( $val["cantidad"] >= $cantidad_descontar ) {
						$cantidad = $val["cantidad"] - $cantidad_descontar; $cantidad_descontar = 0;
					}else{
						$cantidad = $val["cantidad"] - 1; $cantidad_descontar = $cantidad_descontar - 1;
					}
				}

				$datos = array(utf8_decode($val["producto"]." - ".$val["unidad"]));
				array_push($datos,number_format($val["cantidad"],2));
				array_push($datos,number_format($cantidad,2));
				array_push($datos,number_format($val["preciounitario"],2));
				array_push($datos,number_format($val["preciounitario"] * $cantidad,2));
				array_push($datos,number_format($val["preciounitario"] * $cantidad * 0.40,2));
				array_push($datos,number_format($cantidad * 1,2));
				array_push($datos,number_format( ($val["preciounitario"] * $cantidad * 0.40) - ($cantidad * 1),2));
                $pdf->Row($datos);
                
                $total = $total + ( ($val["preciounitario"] * $cantidad * 0.40) - ($cantidad * 1) );
			}
			$w = array(99,75,18); $pdf->SetWidths($w); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','B',8);

			$datos = array("");
			array_push($datos,"TOTAL A PAGAR POR EL DIA: S/.");
			array_push($datos,number_format($total,2));
            $pdf->Row($datos);
		}

		$pdf->SetTitle("Netix Peru - Ventas Empleados"); $pdf->Output();
	}
}