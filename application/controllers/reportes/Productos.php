<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$almacenes = $this->db->query("select *from almacen.almacenes where estado=1")->result_array();
			$lineas = $this->db->query("select *from almacen.lineas where estado=1 order by descripcion")->result_array();
			$monedas = $this->db->query("select *from caja.monedas where estado=1 order by codmoneda")->result_array();
			$this->load->view("reportes/productos/index",compact("almacenes","lineas","monedas"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function cambiar_fecha(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["fechakardex","fechacomprobante"]; $valores = [$this->request->fechakardex,$this->request->fechacomprobante];
			$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex",$this->request->codkardex);

			$campos = ["fechakardex"]; $valores = [$this->request->fechakardex];
			$estado_u = $this->Netix_model->netix_editar("kardex.kardexalmacen", $campos, $valores, "codkardex",$this->request->codkardex);

			$campos = ["fechacredito"]; $valores = [$this->request->fechacomprobante];
			$estado_u = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codkardex",$this->request->codkardex);
			$campos = ["fechamovimiento"]; $valores = [$this->request->fechacomprobante];
			$estado_u = $this->Netix_model->netix_editar("caja.movimientos", $campos, $valores, "codkardex",$this->request->codkardex);

			$kardex = $this->db->query("select codkardex_ref from kardex.kardex where codkardex=".$this->request->codkardex)->result_array();
			if (count($kardex)>0) {
				$campos = ["fechakardex","fechacomprobante"]; $valores = [$this->request->fechakardex,$this->request->fechacomprobante];
				$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex",$kardex[0]["codkardex_ref"]);

				$campos = ["fechakardex"]; $valores = [$this->request->fechakardex];
				$estado_u = $this->Netix_model->netix_editar("kardex.kardexalmacen", $campos, $valores, "codkardex",$kardex[0]["codkardex_ref"]);

				$campos = ["fechacredito"]; $valores = [$this->request->fechacomprobante];
				$estado_u = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codkardex",$kardex[0]["codkardex_ref"]);
				$campos = ["fechamovimiento"]; $valores = [$this->request->fechacomprobante];
				$estado_u = $this->Netix_model->netix_editar("caja.movimientos", $campos, $valores, "codkardex",$kardex[0]["codkardex_ref"]);
			}
			echo $estado;
		}
	}

	function netix_kardex(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$existencias_a = $this->db->query("select mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<'".$this->request->fechadesde."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1")->result_array();

			$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;
			foreach ($existencias_a as $key => $val) {
				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
				}
			}

			$existencias_a = $this->db->query("select ".number_format($existencia_cantidad,4,".","")." as existencia_cantidad, ".number_format($existencia_precio,4,".","")." as existencia_precio, ".number_format($existencia_total,4,".","")." as existencia_total")->result_array();

			$existencias = $this->db->query("select k.codkardex, k.fechakardex,k.fechacomprobante, substr(coalesce(k.cliente,p.razonsocial),0,20) as razonsocial, k.seriecomprobante,k.nrocomprobante,mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex>='".$this->request->fechadesde."' and k.fechakardex<='".$this->request->fechahasta."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();

			// $existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;

			foreach ($existencias as $key => $val) {
				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
				}

				$existencias[$key]["existencia_cantidad"] = number_format($existencia_cantidad,4);
				$existencias[$key]["existencia_precio"] = number_format($existencia_precio,4);
				$existencias[$key]["existencia_total"] = number_format($existencia_total,4);
			}
			$data["existencias_a"] = $existencias_a;
			$data["existencias"] = $existencias;
			echo json_encode($data);
		}
	}

	function kardexproducto_pdf(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$almacen = $this->db->query("select descripcion from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();
			$producto = $this->db->query("select descripcion from almacen.productos where codproducto=".$this->request->codproducto)->result_array();
			$unidad = $this->db->query("select descripcion from almacen.unidades where codunidad=".$this->request->codunidad)->result_array();

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("KARDEX DE PRODUCTO ".$almacen[0]["descripcion"],"DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta." - ".$almacen[0]["descripcion"]);

			$pdf->SetFont('Arial','B',9);
			$pdf->Cell(0,7,utf8_decode("KARDEX PRODUCTO DETALLADO - DESDE ".$this->request->fechadesde." HASTA ".$this->request->fechahasta),0,1,'C');
			$pdf->SetFillColor(230,230,230);
	        $pdf->Cell(0,7,utf8_decode($producto[0]["descripcion"]." | UNIDAD: ".$unidad[0]["descripcion"]),1,1,'C',True); $pdf->Ln();

	        $pdf->SetFont('Arial', 'B', 8); $pdf->Cell(40,5,' ','LTR',0,'L',0); 
	        $pdf->Cell(50,5,"ENTRADAS",1,0,'C',0); $pdf->Cell(50,5,"SALIDAS",1,0,'C',0); $pdf->Cell(50,5,"EXISTENCIAS",1,0,'C',0); $pdf->Ln();
			$columnas = array("FECHA","COMPROBANTE","CANTIDAD","P.U","TOTAL","CANTIDAD","P.U","TOTAL","CANTIDAD","P.U","TOTAL");
			$w = array(15,25,17,16,17,17,16,17,17,16,17); $pdf->pdf_tabla_head($columnas,$w,8);

			$existencias_a = $this->db->query("select mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<'".$this->request->fechadesde."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1")->result_array();

			$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;
			foreach ($existencias_a as $key => $val) {
				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
				}
			}

			$pdf->SetWidths(array(140,17,16,17)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);
			$datos = array("SALDO ANTERIOR");
			array_push($datos,number_format($existencia_cantidad,4,".",""));
			array_push($datos,number_format($existencia_precio,4,".","")); 
			array_push($datos,number_format($existencia_total,4,".",""));
            $pdf->Row($datos);

            $existencias = $this->db->query("select k.codkardex, k.fechakardex,k.fechacomprobante, substr(coalesce(k.cliente,p.razonsocial),0,20) as razonsocial, k.seriecomprobante,k.nrocomprobante,mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex>='".$this->request->fechadesde."' and k.fechakardex<='".$this->request->fechahasta."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();

            $pdf->SetWidths(array(15,25,17,16,17,17,16,17,17,16,17)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',6);
			foreach ($existencias as $key => $val) {
				$datos = array($val["fechakardex"]);
				array_push($datos,$val["seriecomprobante"]."-".$val["nrocomprobante"]);

				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
					array_push($datos,number_format($val["cantidad"],4,".",""));
					array_push($datos,number_format($val["preciounitario"],4,".","")); 
					array_push($datos,number_format($val["total"],4,".",""));
					array_push($datos,""); array_push($datos,""); array_push($datos,"");
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];

					array_push($datos,""); array_push($datos,""); array_push($datos,"");
					array_push($datos,number_format($val["cantidad"],4,".",""));
					array_push($datos,number_format($val["preciounitario"],4,".","")); 
					array_push($datos,number_format($val["total"],4,".",""));
				}
				array_push($datos,number_format($existencia_cantidad,4,".",""));
				array_push($datos,number_format($existencia_precio,4,".","")); 
				array_push($datos,number_format($existencia_total,4,".",""));
	            $pdf->Row($datos);
			}

			$pdf->SetTitle($producto[0]["descripcion"]." - ".$unidad[0]["descripcion"]); $pdf->Output();
		}
	}

	function kardexproducto_excel(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$almacen = $this->db->query("select descripcion from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();
			$producto = $this->db->query("select descripcion from almacen.productos where codproducto=".$this->request->codproducto)->result_array();
			$unidad = $this->db->query("select descripcion from almacen.unidades where codunidad=".$this->request->codunidad)->result_array();

			$existencias_a = $this->db->query("select mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<'".$this->request->fechadesde."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1")->result_array();

			$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;
			foreach ($existencias_a as $key => $val) {
				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
				}
			}

			$existencias_a = $this->db->query("select ".number_format($existencia_cantidad,4,".","")." as existencia_cantidad, ".number_format($existencia_precio,4,".","")." as existencia_precio, ".number_format($existencia_total,4,".","")." as existencia_total")->result_array();

			$existencias = $this->db->query("select k.codkardex, k.fechakardex,k.fechacomprobante, substr(coalesce(k.cliente,p.razonsocial),0,20) as razonsocial,p.documento, k.seriecomprobante,k.nrocomprobante,mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex>='".$this->request->fechadesde."' and k.fechakardex<='".$this->request->fechahasta."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$this->request->codproducto." and kd.codunidad=".$this->request->codunidad." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();

			foreach ($existencias as $key => $val) {
				if ($val["tipo"]==1) {
					// INGRESOS DEL PRODUCTO UNIDAD //
					$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
					$existencia_total = $existencia_total + $val["total"];
					if ($existencia_cantidad==0) {
						$existencia_precio = 0; 
					}else{
						$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
					}
				}else{
					// SALIDAS DEL PRODUCTO UNIDAD //
					$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
					$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
				}

				$existencias[$key]["existencia_cantidad"] = number_format($existencia_cantidad,4);
				$existencias[$key]["existencia_precio"] = number_format($existencia_precio,4);
				$existencias[$key]["existencia_total"] = number_format($existencia_total,4);
			}
			$info = $this->db->query("select '".$producto[0]["descripcion"]."' as producto, '".$unidad[0]["descripcion"]."' as unidad, '".$almacen[0]["descripcion"]."' as almacen, ".$this->request->fechadesde." as desde, ".$this->request->fechahasta." as hasta")->result_array();

			$this->load->view("reportes/productos/productoxls",compact("info","existencias_a","existencias"));
		}
	}


	function buscar_productos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.pventamin,2) as preciominimo,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

				if (count($lista)>0) {
					$tiene = 1;
					foreach ($lista as $k => $value) {
						$stock = $this->db->query("select stockactual, ventarecogo, comprarecogo from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
						if (count($stock)==0) {
							$lista[$k]["stock"] = 0;

							$lista[$k]["ventarecogo"] = 0; $lista[$k]["comprarecogo"] = 0; $lista[$k]["fisico"] = 0;
						}else{
							$lista[$k]["stock"] = round($stock[0]["stockactual"],2);

							$lista[$k]["ventarecogo"] = round($stock[0]["ventarecogo"],2);
							$lista[$k]["comprarecogo"] = round($stock[0]["comprarecogo"],2);
							$lista[$k]["fisico"] = round($stock[0]["stockactual"] + $stock[0]["comprarecogo"] - $stock[0]["ventarecogo"],2);
						}
					}
				}else{
					$tiene = 0;
					$lista = [];
				}
				$lineas[$key]["tiene"] = $tiene;
				$lineas[$key]["productos"] = $lista;
			}
			echo json_encode($lineas);
		}
	}

	function netix_recoger(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$lista = $this->db->query("select personas.documento,personas.razonsocial,personas.nombrecomercial,kardex.codkardex, kardex.codmovimientotipo, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago,kardex.nrocomprobante, kardex.fechakardex,round(kardex.importe,2) as importe,kardex.estado,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on (kardex.codkardex = detalle.codkardex) inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.retirar=0 and detalle.codproducto = ".$this->request->codproducto." and detalle.codunidad = ".$this->request->codunidad." and kardex.codalmacen = ".$this->request->codalmacen." and kardex.codmovimientotipo = ".$this->request->operacion." and kardex.estado=1 order by kardex.codkardex desc")->result_array();

			echo json_encode($lista);
		}
	}

	function netix_compraventas(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$moneda = "";
			if ((int)$this->request->codmoneda > 0) {
				$moneda = " and kardex.codmoneda = ".$this->request->codmoneda;
			}

			$lista = $this->db->query("select kardex.codkardex, personas.razonsocial, kardex.seriecomprobante, kardex.nrocomprobante, kardex.fechacomprobante,round(detalle.cantidad,2) as cantidad, round(detalle.preciounitario,3) as preciounitario, round(detalle.subtotal,2) as subtotal, moneda.descripcion as moneda from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on (kardex.codkardex = detalle.codkardex) inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.monedas as moneda on(kardex.codmoneda=moneda.codmoneda) where kardex.fechacomprobante>='".$this->request->fechadesde."' and kardex.fechacomprobante<='".$this->request->fechahasta."' and kardex.codalmacen=".$this->request->codalmacen." and detalle.codproducto = ".$this->request->codproducto." and detalle.codunidad = ".$this->request->codunidad." and kardex.codmovimientotipo = ".$this->request->operacion." ".$moneda." and kardex.estado=1 order by kardex.codkardex desc")->result_array();
			$total = $this->db->query("select coalesce(sum(detalle.subtotal), 0) as total from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on (kardex.codkardex = detalle.codkardex) inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.monedas as moneda on(kardex.codmoneda=moneda.codmoneda) where kardex.fechacomprobante>='".$this->request->fechadesde."' and kardex.fechacomprobante<='".$this->request->fechahasta."' and kardex.codalmacen=".$this->request->codalmacen." and detalle.codproducto = ".$this->request->codproducto." and detalle.codunidad = ".$this->request->codunidad." and kardex.codmovimientotipo = ".$this->request->operacion." ".$moneda." and kardex.estado=1")->result_array();

			$data = array(); $data["lista"] = $lista; $data["total"] = number_format($total[0]["total"], 2);

			echo json_encode($data);
		}
	}

	function netix_compraventas_pdf(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$moneda = "";
			if ((int)$this->request->codmoneda > 0) {
				$moneda = " and kardex.codmoneda = ".$this->request->codmoneda;
			}

			$lista = $this->db->query("select kardex.codkardex, personas.razonsocial, kardex.seriecomprobante, kardex.nrocomprobante, kardex.fechacomprobante,round(detalle.cantidad,2) as cantidad, round(detalle.preciounitario,3) as preciounitario, round(detalle.subtotal,2) as subtotal, moneda.descripcion as moneda from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on (kardex.codkardex = detalle.codkardex) inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.monedas as moneda on(kardex.codmoneda=moneda.codmoneda) where kardex.fechacomprobante>='".$this->request->fechadesde."' and kardex.fechacomprobante<='".$this->request->fechahasta."' and kardex.codalmacen=".$this->request->codalmacen." and detalle.codproducto = ".$this->request->codproducto." and detalle.codunidad = ".$this->request->codunidad." and kardex.codmovimientotipo = ".$this->request->operacion." ".$moneda." and kardex.estado=1 order by kardex.codkardex desc")->result_array();
			
			$producto = $this->db->query("select descripcion from almacen.productos where codproducto=".$this->request->codproducto)->result_array();
			$unidad = $this->db->query("select descripcion from almacen.unidades where codunidad=".$this->request->codunidad)->result_array();

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();
			if ($this->request->operacion == 20) {
				$pdf->pdf_header("VENTAS: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta,"");
			}else{
				$pdf->pdf_header("COMPRAS: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta,"");
			}
			$pdf->SetFillColor(230,230,230);
	        $pdf->Cell(0,7,utf8_decode($producto[0]["descripcion"]." | UNIDAD: ".$unidad[0]["descripcion"]),1,1,'C',True); $pdf->Ln();

			$columnas = array("FECHA","RAZON SOCIAL","COMPROBANTE","CANTIDAD","P.U.","TOTAL","MONEDA");
			$w = array(15,77,30,18,15,17,18); $pdf->pdf_tabla_head($columnas,$w,8); $subtotal = 0;
			$pdf->SetWidths($w); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);
			foreach ($lista as $key => $value) { $subtotal = $subtotal + $value["subtotal"];
				$datos = array($value["fechacomprobante"]);
				array_push($datos,utf8_decode($value["razonsocial"]));
				array_push($datos,$value["seriecomprobante"]."-".$value["nrocomprobante"]);
				array_push($datos,number_format($value["cantidad"],3));
				array_push($datos,number_format($value["preciounitario"],3));
				array_push($datos,number_format($value["subtotal"],3));
				array_push($datos,$value["moneda"]);
	            $pdf->Row($datos);
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetWidths(array(155,35)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','B',7);
			$datos = array("TOTAL");
			array_push($datos,number_format($subtotal,4,".",""));
            $pdf->Row($datos);

			$pdf->SetTitle($producto[0]["descripcion"]." - ".$unidad[0]["descripcion"]); $pdf->Output();
		}
	}

	public function netix_compraventas_excel(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$moneda = "";
			if ((int)$this->request->codmoneda > 0) {
				$moneda = " and kardex.codmoneda = ".$this->request->codmoneda;
			}

			$lista = $this->db->query("select kardex.codkardex, personas.razonsocial, kardex.seriecomprobante, kardex.nrocomprobante, kardex.fechacomprobante,round(detalle.cantidad,2) as cantidad, round(detalle.preciounitario,3) as preciounitario, round(detalle.subtotal,2) as subtotal, moneda.descripcion as moneda from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on (kardex.codkardex = detalle.codkardex) inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.monedas as moneda on(kardex.codmoneda=moneda.codmoneda) where kardex.fechacomprobante>='".$this->request->fechadesde."' and kardex.fechacomprobante<='".$this->request->fechahasta."' and kardex.codalmacen=".$this->request->codalmacen." and detalle.codproducto = ".$this->request->codproducto." and detalle.codunidad = ".$this->request->codunidad." and kardex.codmovimientotipo = ".$this->request->operacion." ".$moneda." and kardex.estado=1 order by kardex.codkardex desc")->result_array();
			
			$producto = $this->db->query("select descripcion from almacen.productos where codproducto=".$this->request->codproducto)->result_array();
			$unidad = $this->db->query("select descripcion from almacen.unidades where codunidad=".$this->request->codunidad)->result_array();
			$titulo = $producto[0]["descripcion"]." - ".$unidad[0]["descripcion"];
			if ($this->request->operacion == 20) {
				$subtitulo = "VENTAS: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta;
			}else{
				$subtitulo = "COMPRAS: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta;
			}

			$this->load->view("reportes/productos/compraventaxls.php",compact("titulo","subtitulo","lista"));
		}
	}

	function pdf_kardexproductos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("KARDEX DE PRODUCTOS","REPORTE KARDEX DE PRODUCTOS - ".$almacen[0]["descripcion"]);

			$pdf->SetFont("Arial","B", 8);
			$pdf->Cell(40,5,"DATOS DE LA OPERACION",'LTR',0,'L',0);
			$pdf->Cell(50,5,"ENTRADAS",'LTR',0,'L',0);
			$pdf->Cell(50,5,"SALIDAS",'LTR',0,'L',0);
			$pdf->Cell(50,5,"EXISTENCIAS",1,0,'L',0);$pdf->Ln();

			$columnas = array("FECHA","COMPROBANTE","CANTIDAD","P.U.","TOTAL","CANTIDAD","P.U.","TOTAL","CANTIDAD","PRECIO","TOTAL");
			$w = array(15,25,18,15,17,18,15,17,18,15,17); $pdf->pdf_tabla_head($columnas,$w,8);

			if ($this->request->codlinea==0) {
				$lineas = "";
			}else{
				$lineas = "p.codlinea=".$this->request->codlinea." and ";
			}

			$lista = $this->db->query("select p.codproducto,p.descripcion,u.codunidad,u.descripcion as unidad from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where ".$lineas." (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

			foreach ($lista as $value) {
				$existencias = $this->db->query("select k.fechakardex, k.seriecomprobante, k.nrocomprobante, mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<='".$this->request->fecha."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$value["codproducto"]." and kd.codunidad=".$value["codunidad"]." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();
				if (count($existencias)>0) {
					$pdf->SetFillColor(230,230,230); $pdf->SetFont('Arial','B',7);
			        $pdf->Cell(array_sum($w),5,utf8_decode($value["descripcion"]." | UNIDAD: ".$value["unidad"]),1,0,'C',True); 
			        $pdf->Ln();

			        $pdf->SetWidths(array(15,25,18,15,17,18,15,17,18,15,17));
		            $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);

					$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;
					foreach ($existencias as $key => $val) {
						if ($val["tipo"]==1) {
							// INGRESOS DEL PRODUCTO UNIDAD //
							$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
							$existencia_total = $existencia_total + $val["total"];
							if ($existencia_cantidad==0) {
								$existencia_precio = 0; 
							}else{
								$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
							}
						}else{
							// SALIDAS DEL PRODUCTO UNIDAD //
							$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
							$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
						}

						$datos = array($val["fechakardex"]);
						array_push($datos,$val["seriecomprobante"]."-".$val["nrocomprobante"]);

						if ($val["tipo"]==1) {
							array_push($datos,number_format($val["cantidad"],2));
							array_push($datos,number_format($val["preciounitario"],2));
							array_push($datos,number_format($val["total"],2));
							array_push($datos,""); array_push($datos,""); array_push($datos,"");
						}else{
							array_push($datos,""); array_push($datos,""); array_push($datos,"");
							array_push($datos,number_format($val["cantidad"],2));
							array_push($datos,number_format($val["preciounitario"],2));
							array_push($datos,number_format($val["total"],2));
						}
						array_push($datos,number_format($existencia_cantidad,2));
						array_push($datos,number_format($existencia_precio,2));
						array_push($datos,number_format($existencia_total,2));
		                $pdf->Row($datos);
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetTitle("Netix Peru - Reporte Productos - Kardex"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function excel_kardexproductos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();
			$titulo = "REPORTE KARDEX POR PRODUCTOS - ".$almacen[0]["descripcion"];

			if ($this->request->codlinea==0) {
				$lineas = "";
			}else{
				$lineas = "p.codlinea=".$this->request->codlinea." and ";
			}

			$lista = $this->db->query("select p.codproducto,p.descripcion,u.codunidad,u.descripcion as unidad from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where ".$lineas." (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

			foreach ($lista as $key => $value) {
				$existencias = $this->db->query("select p.documento, coalesce(k.cliente,p.razonsocial) as razonsocial, k.fechakardex, k.seriecomprobante, k.nrocomprobante, mt.tipo,round(kd.cantidad,4) as cantidad, round(kd.preciounitario,4) as preciounitario, round(kd.cantidad * kd.preciounitario,4) as total from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<='".$this->request->fecha."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$value["codproducto"]." and kd.codunidad=".$value["codunidad"]." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();

				$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;
				foreach ($existencias as $k => $val) {
					if ($val["tipo"]==1) {
						// INGRESOS DEL PRODUCTO UNIDAD //
						$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
						$existencia_total = $existencia_total + $val["total"];
						if ($existencia_cantidad==0) {
							$existencia_precio = 0; 
						}else{
							$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
						}
					}else{
						// SALIDAS DEL PRODUCTO UNIDAD //
						$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
						$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
					}
					$existencias[$k]["existencia_cantidad"] = $existencia_cantidad;
					$existencias[$k]["existencia_precio"] = $existencia_precio;
					$existencias[$k]["existencia_total"] = $existencia_total;
				}
				$lista[$key]["existencias"] = $existencias;
			}
			$this->load->view("reportes/productos/kardexls.php",compact("titulo","lista"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function pdf_precios(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PRODUCTOS","REPORTE GENERAL DE PRECIOS DE PRODUCTOS - ".$almacen[0]["descripcion"]);

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			$pdf->SetFont('Arial','B',10);
		    $pdf->setFillColor(245,245,245);

		    $columnas = array("N°","DESCRIPCION PRODUCTO","U.MEDIDA","P. COSTO","P. MINIMO","P. VENTA");
			$w = array(10,93,20,22,23,22); $pdf->pdf_tabla_head($columnas,$w,9);

			$pdf->SetWidths(array(10,93,20,22,23,22));
            $pdf->SetLineHeight(5);
			$pdf->SetFont('Arial','',7); $item = 0;

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.pventamin,2) as preciominimo,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();
				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(190,6,"LINEA DE PRODUCTO: ".utf8_decode($v["descripcion"]),1); $pdf->Ln();
					$pdf->SetFont('Arial','',8);

					foreach ($lista as $value) { $item = $item + 1;
						$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
						if (count($stock)==0) {
							$stock = 0;
						}else{
							$stock = round($stock[0]["stockactual"],2);
						}

						$background = "0"; $color = "";

						$datos = array("0".$item);
						array_push($datos,utf8_decode($value["descripcion"]));
						array_push($datos,utf8_decode($value["unidad"]));

						array_push($datos,number_format($value["preciocosto"],2));
						array_push($datos,number_format($value["preciominimo"],2));
						array_push($datos,number_format($value["precioventa"],2));

		                $pdf->Row_color($datos, $background, $color);
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetTitle("Netix Peru - Reporte Productos - Precios"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function pdf_precios_stock(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PRODUCTOS","REPORTE GENERAL DE PRECIOS DE PRODUCTOS - ".$almacen[0]["descripcion"]);

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			$pdf->SetFont('Arial','B',10);
		    $pdf->setFillColor(245,245,245);

		    $columnas = array("N°","DESCRIPCION PRODUCTO","U.MEDIDA","STOCK","P. COSTO","P. MINIMO","P. VENTA");
			$w = array(10,73,20,20,22,23,22); $pdf->pdf_tabla_head($columnas,$w,9);

			$pdf->SetWidths(array(10,73,20,20,22,23,22));
            $pdf->SetLineHeight(5);
			$pdf->SetFont('Arial','',7); $item = 0;

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.pventamin,2) as preciominimo,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();
				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',9);
					$pdf->Cell(190,6,"LINEA DE PRODUCTO: ".utf8_decode($v["descripcion"]),1); $pdf->Ln();
					$pdf->SetFont('Arial','',8);

					foreach ($lista as $value) { $item = $item + 1;
						$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
						if (count($stock)==0) {
							$stock = 0;
						}else{
							$stock = round($stock[0]["stockactual"],2);
						}

						$datos = array("0".$item);
						array_push($datos,utf8_decode($value["descripcion"]));
						array_push($datos,utf8_decode($value["unidad"]));

						array_push($datos,number_format($stock,2));
						array_push($datos,number_format($value["preciocosto"],2));
						array_push($datos,number_format($value["preciominimo"],2));
						array_push($datos,number_format($value["precioventa"],2));
		                $pdf->Row($datos);
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetTitle("Netix Peru - Reporte Productos - Precios"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function pdf_precios_stock_costo(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PRODUCTOS","REPORTE GENERAL DE PRECIOS DE PRODUCTOS - ".$almacen[0]["descripcion"]);

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			$pdf->SetFont('Arial','B',10);
		    $pdf->setFillColor(245,245,245);

		    $columnas = array("N°","DESCRIPCION PRODUCTO","U.MEDIDA","STOCK","P. COSTO","P. COMPRA","IMP.COSTO", "IMP.VENTA");
			$w = array(10,70,20,15,20,20,20,20); $pdf->pdf_tabla_head($columnas,$w,8);

			$pdf->SetWidths(array(10,70,20,15,20,20,20,20));
            $pdf->SetLineHeight(5);
			$pdf->SetFont('Arial','',6); $item = 0;

			$costo = 0; $compra = 0;
			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.preciocompra,2) as preciocompra,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();
				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(195,6,"LINEA DE PRODUCTO: ".utf8_decode($v["descripcion"]),1); $pdf->Ln();
					$pdf->SetFont('Arial','',7);

					foreach ($lista as $value) { $item = $item + 1;
						$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
						if (count($stock)==0) {
							$stock = 0;
						}else{
							$stock = round($stock[0]["stockactual"],2);
						}

						if ($this->request->stock==0) {
							$mostrar = 1;
						}elseif ($this->request->stock==1) {
							if ($stock>0) {
								$mostrar = 1;
							}else{
								$mostrar = 0;
							}
						}else{
							if ($stock<=0) {
								$mostrar = 1;
							}else{
								$mostrar = 0;
							}
						}

						if ($mostrar == 1) {
							$costo = $costo + ($value["preciocosto"] * $stock);
							$compra = $compra + ($value["preciocompra"] * $stock);

							$datos = array("0".$item);
							array_push($datos,utf8_decode($value["descripcion"]));
							array_push($datos,utf8_decode($value["unidad"]));

							array_push($datos,number_format($stock,2));
							array_push($datos,number_format($value["preciocosto"],2));
							array_push($datos,number_format($value["preciocompra"],2));
							array_push($datos,number_format($value["preciocosto"] * $stock,2));
							array_push($datos,number_format($value["preciocompra"] * $stock,2));
			                $pdf->Row($datos);
						}
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetFont('Arial','B',7);
			$pdf->Cell(155,5,"TOTALES",1,0,'R');
		    $pdf->Cell(20,5,number_format($costo,2),1,"R");
		    $pdf->Cell(20,5,number_format($compra,2),1,"R");

			$pdf->SetTitle("Netix Peru - Reporte Productos - Precios"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function excel_precios(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();
			$titulo = "REPORTE GENERAL DE PRECIOS DE PRODUCTOS - ".$almacen[0]["descripcion"];

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.pventamin,2) as preciominimo,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') ) and p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

				foreach ($lista as $k => $value){
					$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
					if (count($stock)==0) {
						$stock = 0;
					}else{
						$stock = round($stock[0]["stockactual"],2);
					}
					$lista[$k]["stock"] = $stock;
				}
				$lineas[$key]["lista"] = $lista;
			}

			$this->load->view("reportes/productos/preciosxls.php",compact("titulo","lineas"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function stock_general(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PRODUCTOS","REPORTE GENERAL DE STOCK DE PRODUCTOS - ".$almacen[0]["descripcion"]);

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			$pdf->SetFont('Arial','B',10);
		    $pdf->setFillColor(245,245,245);

		    $columnas = array("N°","DESCRIPCION PRODUCTO","U.MEDIDA","STOCK DISP.","V.X RECOGER","C.X RECOGER","STOCK FISICO");
			$w = array(10,73,20,20,22,23,22); $pdf->pdf_tabla_head($columnas,$w,8);

			$pdf->SetWidths(array(10,73,20,20,22,23,22));
            $pdf->SetLineHeight(5);
			$pdf->SetFont('Arial','',7); $item = 0;

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.preciocosto,2) as preciocosto,round(pu.pventapublico,2) as precioventa from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(190,6,"LINEA DE PRODUCTO: ".utf8_decode($v["descripcion"]),1); $pdf->Ln();
					$pdf->SetFont('Arial','',8);

					foreach ($lista as $value) { $item = $item + 1;
						$stock = $this->db->query("select *from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$this->request->codalmacen." and estado=1")->result_array();
						if (count($stock)==0) {
							$stockactual = 0; $ventas = 0; $compras = 0;
						}else{
							$stockactual = round($stock[0]["stockactual"],2);
							$ventas = round($stock[0]["ventarecogo"],2);
							$compras = round($stock[0]["comprarecogo"],2);
						}

						if ($this->request->stock==0) {
							$mostrar = 1;
						}elseif ($this->request->stock==1) {
							if ($stockactual>0) {
								$mostrar = 1;
							}else{
								$mostrar = 0;
							}
						}else{
							if ($stockactual<=0) {
								$mostrar = 1;
							}else{
								$mostrar = 0;
							}
						}

						if ($mostrar==1) {
							$datos = array("0".$item);
							array_push($datos,utf8_decode($value["descripcion"]));
							array_push($datos,utf8_decode($value["unidad"]));

							array_push($datos,number_format($stockactual,2));
							array_push($datos,number_format($ventas,2));
							array_push($datos,number_format($compras,2));
							array_push($datos,number_format($stockactual + $ventas,2));
			                $pdf->Row($datos);
						}
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetTitle("Netix Peru - Reporte Productos - Stock"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}

	function stock_valorizado(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$estilo = "border-top:1px solid #D5D8DC; border-left:1px solid #D5D8DC; border-right:1px solid #D5D8DC;";
			$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$this->request->codalmacen)->result_array();

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PRODUCTOS",$almacen[0]["descripcion"]." - REPORTE GENERAL DE KARDEX HASTA ".$this->request->fecha);

			if ($this->request->codlinea==0) {
				$lineas = $this->db->query("select * from almacen.lineas where estado=1")->result_array();
			}else{
				$lineas = $this->db->query("select * from almacen.lineas where codlinea=".$this->request->codlinea)->result_array();
			}

			$pdf->SetFont('Arial','B',10);
		    $pdf->setFillColor(245,245,245);

		    $columnas = array("N°","DESCRIPCION PRODUCTO","U.MEDIDA","CANTIDAD","PRECIO UNITARIO","TOTAL");
			$w = array(10,90,22,20,27,21); $pdf->pdf_tabla_head($columnas,$w,8);

			$pdf->SetWidths(array(10,90,22,20,27,21));
            $pdf->SetLineHeight(5);
			$pdf->SetFont('Arial','',7); $item = 0;

			foreach ($lineas as $key => $v) {
				$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where p.codlinea=".$v["codlinea"]." and p.estado=1 and pu.estado=1 and p.controlstock=".(int)$this->request->controlstock." and p.estado=".(int)$this->request->estado." order by p.codproducto desc")->result_array();

				if (count($lista)>0) {
					$pdf->SetFont('Arial','B',8);
					$pdf->Cell(190,6,"LINEA DE PRODUCTO: ".utf8_decode($v["descripcion"]),1); $pdf->Ln();
					$pdf->SetFont('Arial','',8);

					foreach ($lista as $value) { $item = $item + 1;
						$existencias = $this->db->query("select mt.tipo,kd.cantidad,kd.preciounitario, (kd.cantidad * kd.preciounitario) as total from kardex.kardex as k inner join almacen.movimientotipos as mt on(k.codmovimientotipo=mt.codmovimientotipo) inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where k.fechakardex<='".$this->request->fecha."' and k.codalmacen=".$this->request->codalmacen." and k.estado=1 and kd.codproducto=".$value["codproducto"]." and kd.codunidad=".$value["codunidad"]." and kd.estado=1 order by k.fechakardex,k.codmovimientotipo, k.codcomprobantetipo,k.seriecomprobante,k.nrocomprobante")->result_array();

						$existencia_cantidad = 0; $existencia_precio = 0; $existencia_total = 0;

						foreach ($existencias as $val) {
							if ($val["tipo"]==1) {
								// INGRESOS DEL PRODUCTO UNIDAD //
								$existencia_cantidad = $existencia_cantidad + $val["cantidad"];
								$existencia_total = $existencia_total + $val["total"];
								if ($existencia_cantidad==0) {
									$existencia_precio = 0; 
								}else{
									$existencia_precio = round(($existencia_total/$existencia_cantidad),4); 
								}
							}else{
								// SALIDAS DEL PRODUCTO UNIDAD //
								$existencia_total = $existencia_total - round(($val["cantidad"] * $existencia_precio),4);
								$existencia_cantidad = $existencia_cantidad - $val["cantidad"];
							}
						}
						
						$datos = array("0".$item);
						array_push($datos,utf8_decode($value["descripcion"]));
						array_push($datos,utf8_decode($value["unidad"]));

						array_push($datos,number_format($existencia_cantidad,2));
						array_push($datos,number_format($existencia_precio,2));
						array_push($datos,number_format($existencia_total,2));
		                $pdf->Row($datos);
					}
				}
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();
			
			$pdf->SetTitle("Netix Peru - Reporte Productos - kardex"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}
}