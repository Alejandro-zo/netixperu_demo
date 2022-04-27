<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$this->load->view("reportes/pedidos/index");
		}else{
			$this->load->view("netix/404");
		}
	}

	function buscar_producto_pedidos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$filtro = "(REPLACE(UPPER(pro.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(pro.codigo) like UPPER('%".$this->request->buscar."%') )";
			$lista = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,per.razonsocial as cliente,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where ".$filtro)->result_array();
			$totales = $this->db->query("select round(sum(pd.cantidad),2) as cantidad, round(sum(pd.cantidad * pd.preciounitario),2) as total, round(sum(pd.preciorefunitario * pd.cantidad), 2) as totalref from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where ".$filtro)->result_array();
			$data["lista"] = $lista; $data["totales"] = $totales;
			echo json_encode($data);
		}
	}
	function pdf_producto_pedidos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE PEDIDOS POR PRODUCTOS","");

			$columnas = array("CODIGO","PRODUCTO","UNIDAD","CLIENTE","CANTIDAD","PRECIO DESC","TOTAL DES","PRECIO CAT","TOTAL CAT");
			$w = array(15,35,12,45,15,20,15,20,15); $pdf->pdf_tabla_head($columnas,$w,7);

			$filtro = "(REPLACE(UPPER(pro.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(pro.codigo) like UPPER('%".$this->request->buscar."%') )";
			$lista = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,per.razonsocial as cliente,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where ".$filtro)->result_array();
			
			$cantidad = 0; $total = 0; $totalref = 0;
			$pdf->SetWidths($w); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);
			foreach ($lista as $key => $value) {
				$cantidad = $cantidad + $value["cantidad"];
				$total = $total + $value["subtotal"];
				$totalref = $totalref + $value["subtotalref"];

				$datos = array($value["codigo"]);
				array_push($datos,utf8_decode($value["producto"]));
				array_push($datos,$value["unidad"]);
				array_push($datos,utf8_decode($value["cliente"]));
				array_push($datos,number_format($value["cantidad"],2));

				array_push($datos,number_format($value["preciounitario"],2));
				array_push($datos,number_format($value["subtotal"],2));
				array_push($datos,number_format($value["preciorefunitario"],2));
				array_push($datos,number_format($value["subtotalref"],2));
                $pdf->Row($datos);
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(107,5,"TOTALES",1,0,'R');
		    $pdf->Cell(15,5,number_format($cantidad,2),1,"R");
		    $pdf->Cell(20,5,"",1,"R");
		    $pdf->Cell(15,5,number_format($total,2),1,"R");
		    $pdf->Cell(20,5,"",1,"R");
		    $pdf->Cell(15,5,number_format($totalref,2),1,"R");

			$pdf->SetTitle("Netix Peru - Reporte Pedidos por Producto"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}
	function excel_producto_pedidos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			$filtro = "(REPLACE(UPPER(pro.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(pro.codigo) like UPPER('%".$this->request->buscar."%') )";
			$lista = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,per.razonsocial as cliente,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where ".$filtro)->result_array();

			$this->load->view("reportes/pedidos/xls_productos.php",compact("lista"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function buscar_cliente_pedidos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->codpersona==0) {
				$filtro = "";
			}else{
				$filtro = "where p.codpersona=".$this->request->codpersona;
			}
			$clientes = $this->db->query("select distinct(p.codpersona) as codpersona, per.razonsocial from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) ".$filtro)->result_array();
			foreach ($clientes as $key => $value) {
				$clientes[$key]["pedidos"] = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where p.codpersona=".$value["codpersona"])->result_array();
				$clientes[$key]["totales"] = $this->db->query("select round(sum(pd.cantidad),2) as cantidad, round(sum(pd.cantidad * pd.preciounitario),2) as total, round(sum(pd.preciorefunitario * pd.cantidad), 2) as totalref from kardex.pedidos as p inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where p.codpersona=".$value["codpersona"])->result_array();
			}
			echo json_encode($clientes);
		}
	}
	function pdf_cliente_pedidos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$this->load->library('Pdf2'); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header("REPORTE DE PEDIDOS POR CLIENTE","");

			if ($this->request->codpersona==0) {
				$filtro = "";
			}else{
				$filtro = "where p.codpersona=".$this->request->codpersona;
			}
			$clientes = $this->db->query("select distinct(p.codpersona) as codpersona, per.razonsocial from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) ".$filtro)->result_array();
			foreach ($clientes as $key => $value) {
				$pedidos = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where p.codpersona=".$value["codpersona"])->result_array();

				$pdf->SetFont('Arial','B',9);
				$pdf->Cell(0,5,"CLIENTE: ".utf8_decode($value["razonsocial"]),1,0,'L'); $pdf->Ln();
				$columnas = array("CODIGO","PRODUCTO","UNIDAD","CANTIDAD","PRECIO DESC.","TOTAL DES","PRECIO CAT","TOTAL CAT");
				$w = array(15,75,15,15,20,15,20,15); $pdf->pdf_tabla_head($columnas,$w,7);

				$cantidad = 0; $total = 0; $totalref = 0;
				$pdf->SetWidths($w); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',7);
				foreach ($pedidos as $v) {
					$cantidad = $cantidad + $v["cantidad"];
					$total = $total + $v["subtotal"];
					$totalref = $totalref + $v["subtotalref"];

					$datos = array($v["codigo"]);
					array_push($datos,utf8_decode($v["producto"]));
					array_push($datos,$v["unidad"]);
					array_push($datos,number_format($v["cantidad"],2));

					array_push($datos,number_format($v["preciounitario"],2));
					array_push($datos,number_format($v["subtotal"],2));
					array_push($datos,number_format($v["preciorefunitario"],2));
					array_push($datos,number_format($v["subtotalref"],2));
	                $pdf->Row($datos);
				}
				$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(105,5,"TOTALES",1,0,'R');
			    $pdf->Cell(15,5,number_format($cantidad,2),1,"R");
			    $pdf->Cell(20,5,"",1,"R");
			    $pdf->Cell(15,5,number_format($total,2),1,"R");
			    $pdf->Cell(20,5,"",1,"R");
			    $pdf->Cell(15,5,number_format($totalref,2),1,"R"); $pdf->Ln();
			}

			$pdf->SetTitle("Netix Peru - Reporte Pedidos por Cliente"); $pdf->Output();
		}else{
			$this->load->view("netix/404");
		}
	}
	function excel_cliente_pedidos(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);
			
			if ($this->request->codpersona==0) {
				$filtro = "";
			}else{
				$filtro = "where p.codpersona=".$this->request->codpersona;
			}
			$clientes = $this->db->query("select distinct(p.codpersona) as codpersona, per.razonsocial from kardex.pedidos as p inner join public.personas as per on(p.codpersona=per.codpersona) ".$filtro)->result_array();
			foreach ($clientes as $key => $value) {
				$clientes[$key]["pedidos"] = $this->db->query("select pro.codigo,pro.descripcion as producto,uni.descripcion as unidad,round(pd.cantidad,2) as cantidad, round(pd.preciounitario,2) as preciounitario,round(pd.subtotal,2) as subtotal, round(pd.preciorefunitario,2) as preciorefunitario, round(pd.preciorefunitario * pd.cantidad, 2) as subtotalref from kardex.pedidos as p inner join kardex.pedidosdetalle as pd on (p.codpedido=pd.codpedido) inner join almacen.productos as pro on(pd.codproducto=pro.codproducto) inner join almacen.unidades as uni on(pd.codunidad=uni.codunidad) where p.codpersona=".$value["codpersona"])->result_array();
			}
			$this->load->view("reportes/pedidos/xls_clientes.php",compact("clientes"));
		}else{
			$this->load->view("netix/404");
		}
	}
}