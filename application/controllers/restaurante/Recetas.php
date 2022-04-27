<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recetas extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("restaurante/recetas/index");
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function lista(){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,u.codunidad,u.descripcion as unidad,round(pu.stockactual) as stock,m.descripcion as marca from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"]." order by pu.stockactual desc")->result_array(); $item = 0;
			foreach ($lista as $key => $value) { $item = $item + 1;
				$precio = $this->db->query("select factor,pventapublico,pventamin,pventacredito,pventaxmayor,preciocosto from almacen.productounidades where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and estado=1")->result_array();
				$lista[$key]["nro"] = $item;
				if (count($precio)==0) {
					$lista[$key]["precioventa"] = 0.00; $lista[$key]["preciocosto"] = 0.00;
				}else{
					$lista[$key]["precioventa"] = number_format($precio[0]["pventapublico"],2);
					$lista[$key]["preciocosto"] = number_format($precio[0]["preciocosto"],2);
				}

				$lista[$key]["receta"] = $this->db->query("select r.codproducto_receta as codproducto,p.descripcion as producto,r.codunidad_receta as codunidad, u.descripcion as unidad, round(r.cantidad,3) as cantidad from restaurante.recetas as r inner join almacen.productos as p on(r.codproducto_receta=p.codproducto) inner join almacen.unidades as u on(r.codunidad_receta=u.codunidad) where r.codproducto=".$value["codproducto"]." and r.codunidad=".$value["codunidad"])->result_array();
			}
			echo json_encode($lista);
		}else{
			$this->load->view("netix/404");
		}
	}

	function detalle_receta($codproducto,$codunidad){
		if ($this->input->is_ajax_request()) {
			$detalle = $this->db->query("select r.codproducto_receta as codproducto,p.descripcion as producto,r.codunidad_receta as codunidad, u.descripcion as unidad, round(r.cantidad,3) as cantidad from restaurante.recetas as r inner join almacen.productos as p on(r.codproducto_receta=p.codproducto) inner join almacen.unidades as u on(r.codunidad_receta=u.codunidad) where r.codproducto=".$codproducto." and r.codunidad=".$codunidad)->result_array();
			echo json_encode($detalle);
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input')); $item = 0; $estado = 1;

			$this->db->where("codproducto",$this->request->campos->codproducto); 
			$this->db->where("codunidad",$this->request->campos->codunidad);
			$this->db->delete("restaurante.recetas");

			foreach ($this->request->detalle as $key => $value) { $item = $item + 1;
				$campos = ["codproducto","codunidad","item","codproducto_receta","codunidad_receta","cantidad"];
				$valores = [
					(int)$this->request->campos->codproducto,(int)$this->request->campos->codunidad,$item,
					(int)$this->request->detalle[$key]->codproducto,
					(int)$this->request->detalle[$key]->codunidad,
					(double)$this->request->detalle[$key]->cantidad
				];
				$estado = $this->Netix_model->netix_guardar("restaurante.recetas", $campos, $valores);
			}
			echo $estado;
		}
	}

	function consumo_total_pdf(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header_titulo("CONSUMO INGREDIENTES TOTALIZADO: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta);

			$columnas = array("N°","DESCRIPCION PRODUCTO","UNIDAD","CANTIDAD","S/ P.COSTO","S/ VALORIZADO");
			$w = array(10,85,30,20,20,25); $pdf->pdf_tabla_head($columnas,$w,8);

			$recetas = $this->db->query("select r.codproducto_receta,p.descripcion as producto,r.codunidad_receta,u.descripcion as unidad,pu.preciocosto from restaurante.recetas as r inner join almacen.productos as p on(r.codproducto_receta=p.codproducto) inner join almacen.unidades as u on(r.codunidad_receta=u.codunidad) inner join almacen.productounidades as pu on(pu.codproducto=r.codproducto_receta and pu.codunidad=r.codunidad_receta) where r.estado=1 group by r.codproducto_receta,r.codunidad_receta,p.descripcion,u.descripcion,pu.preciocosto")->result_array();

			$pdf->SetWidths(array(10,85,30,20,20,25)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',8); 
			$item = 0; $cantidad = 0; $valorizado = 0;
			foreach ($recetas as $key => $value) { $item = $item + 1;
				$salidas = $this->db->query("select coalesce(sum(kd.cantidad),0) as cantidad from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where kd.codproducto=".$value["codproducto_receta"]." and kd.codunidad=".$value["codunidad_receta"]." and k.fechakardex>='".$this->request->fechadesde."' and k.fechakardex<='".$this->request->fechahasta."' and k.codmovimientotipo>=20 and k.estado=1")->result_array();

				$datos = array($item);
				array_push($datos,utf8_decode($value["producto"]));
				array_push($datos,utf8_decode($value["unidad"]));
				array_push($datos,number_format($salidas[0]["cantidad"],2));
				array_push($datos,number_format($value["preciocosto"],2));
				array_push($datos,number_format($value["preciocosto"] * $salidas[0]["cantidad"],2));
	            $pdf->Row($datos);

	            $cantidad = $cantidad + $salidas[0]["cantidad"];
	            $valorizado = $valorizado + ($value["preciocosto"] * $salidas[0]["cantidad"]);
			}
			$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

			$pdf->SetFont('Arial','B',8);
			$pdf->Cell(125,5,"TOTALES",1,0,'R');
		    $pdf->Cell(40,5,number_format($cantidad,2),1,"R");
		    $pdf->Cell(25,5,number_format($valorizado,2),1,"R");

			$pdf->SetTitle("Consumo Ingredientes"); $pdf->Output();
		}
	}

	function consumo_fechas_pdf(){
		if ($_GET["datos"]) {
			$this->request = json_decode($_GET["datos"]);

			$this->load->library("Pdf2"); $pdf = new Pdf2(); $pdf->AddPage();
			$pdf->pdf_header_titulo("CONSUMO INGREDIENTES POR FECHAS: DESDE ".$this->request->fechadesde." A ".$this->request->fechahasta);

			$columnas = array("N°","DESCRIPCION PRODUCTO","UNIDAD","CANTIDAD","S/ P.COSTO","S/ VALORIZADO");
			$w = array(10,85,30,20,20,25); $pdf->pdf_tabla_head($columnas,$w,8);

			$recetas = $this->db->query("select r.codproducto_receta,p.descripcion as producto,r.codunidad_receta,u.descripcion as unidad,pu.preciocosto from restaurante.recetas as r inner join almacen.productos as p on(r.codproducto_receta=p.codproducto) inner join almacen.unidades as u on(r.codunidad_receta=u.codunidad) inner join almacen.productounidades as pu on(pu.codproducto=r.codproducto_receta and pu.codunidad=r.codunidad_receta) where r.estado=1 group by r.codproducto_receta,r.codunidad_receta,p.descripcion,u.descripcion,pu.preciocosto")->result_array();

			$fecha = $this->request->fechadesde;
			while ($fecha <= $this->request->fechahasta) {
				$pdf->SetFont('Arial','B',10); $pdf->setFillColor(245,245,245);
				$pdf->Cell(0,7,"SALIDAS DE INGREDIENTES EN LA FECHA: ".$fecha,0,1,'L',1); $pdf->Ln(2);

				$pdf->SetWidths(array(10,85,30,20,20,25)); $pdf->SetLineHeight(5); $pdf->SetFont('Arial','',8); 
				$item = 0; $cantidad = 0; $valorizado = 0;
				foreach ($recetas as $key => $value) { $item = $item + 1;
					$salidas = $this->db->query("select coalesce(sum(kd.cantidad),0) as cantidad from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) where kd.codproducto=".$value["codproducto_receta"]." and kd.codunidad=".$value["codunidad_receta"]." and k.fechakardex='".$fecha."' and k.codmovimientotipo>=20 and k.estado=1")->result_array();

					$datos = array($item);
					array_push($datos,utf8_decode($value["producto"]));
					array_push($datos,utf8_decode($value["unidad"]));
					array_push($datos,number_format($salidas[0]["cantidad"],2));
					array_push($datos,number_format($value["preciocosto"],2));
					array_push($datos,number_format($value["preciocosto"] * $salidas[0]["cantidad"],2));
		            $pdf->Row($datos);

		            $cantidad = $cantidad + $salidas[0]["cantidad"];
		            $valorizado = $valorizado + ($value["preciocosto"] * $salidas[0]["cantidad"]);
				}
				$pdf->Cell(array_sum($w),0,'','T'); $pdf->Ln();

				$pdf->SetFont('Arial','B',8);
				$pdf->Cell(125,5,"TOTALES",1,0,'R');
			    $pdf->Cell(40,5,number_format($cantidad,2),1,"R");
			    $pdf->Cell(25,5,number_format($valorizado,2),1,"R"); $pdf->Ln();

				$fecha = date("Y-m-d",strtotime($fecha."+ 1 days"));
			}
			$pdf->SetTitle("Consumo Ingredientes"); $pdf->Output();
		}
	}
}
