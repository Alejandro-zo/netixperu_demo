<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$stockminimos = $this->db->query("select p.descripcion,u.descripcion as unidad,round(pu.stockactual) as stock,m.descripcion as marca from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"]." and pu.stockactual<=10 order by pu.stockactual asc limit 3")->result_array();

			$stockmaximos = $this->db->query("select p.descripcion,u.descripcion as unidad,round(pu.stockactual) as stock,m.descripcion as marca from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"]." order by pu.stockactual desc limit 3")->result_array();

			$clientes = $this->db->query("select personas.codpersona,personas.razonsocial, count(kardex.codpersona) as cantidad,sum(kardex.importe) as importe from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) where kardex.codmovimientotipo=20 and kardex.codsucursal=".$_SESSION["netix_codsucursal"]." and kardex.estado=1 group by personas.codpersona,personas.razonsocial order by cantidad desc limit 3")->result_array();

			$proveedores = $this->db->query("select personas.codpersona,personas.razonsocial, count(kardex.codpersona) as cantidad,sum(kardex.importe) as importe from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) where kardex.codmovimientotipo=2 and kardex.codsucursal=".$_SESSION["netix_codsucursal"]." and kardex.estado=1 group by personas.codpersona,personas.razonsocial order by cantidad desc limit 3")->result_array();

			$this->load->view("administracion/dashboard/index",compact("stockminimos","stockmaximos","clientes","proveedores"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function netix_totales(){
		if ($this->input->is_ajax_request()) {
			$caja = $this->Caja_model->netix_estadocaja();
			if (count($caja) == 0) {
				$estado = "CERRADA";
			}else{
				$estado = "APERTURADA";
			}

			$saldocaja = $this->Caja_model->netix_saldocaja_general($_SESSION["netix_codcaja"]); 
			$saldobanco = $this->Caja_model->netix_saldobanco_general($_SESSION["netix_codcaja"]); 

			$data = array();
			$data["estado"] = $estado;
			$data["caja"] = (double)round($saldocaja["total"],2);
			$data["banco"] = (double)round($saldobanco["total"],2);
			$data["general"] = (double)round( ($saldocaja["total"] + (double)$saldobanco["total"]),2);

			echo json_encode($data);
		}	
	}

	function netix_pagos(){
		if ($this->input->is_ajax_request()) {
			$tipopagos = $this->db->query("select *from caja.tipopagos where estado=1 order by codtipopago")->result_array();
			$ingresos = array(); $egresos = array(); $data = array();

			foreach ($tipopagos as $key => $value) {
				$total = $this->Caja_model->netix_saldotipopago_general($_SESSION["netix_codcaja"],$value["codtipopago"]);

				$ingresos[$key]["name"] = $value["descripcion"];
				$ingresos[$key]["y"] = (double)$total["ingresos"];

				$egresos[$key]["name"] = $value["descripcion"];
				$egresos[$key]["y"] = (double)$total["egresos"];
			}

			$data["ingresos"] = $ingresos; $data["egresos"] = $egresos;
			echo json_encode($data);
		}
	}
}