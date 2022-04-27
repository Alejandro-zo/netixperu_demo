<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Extenciones extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function lista($carpeta,$tabla){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select *from ".$carpeta.".".$tabla." where estado=1 order by descripcion asc")->result_array();
			echo json_encode($lista);
		}
	}

	public function nuevo($carpeta,$tabla){
		if ($this->input->is_ajax_request()) {
			$this->load->view($carpeta."/".$tabla."/nuevo_1");
		}
	}

	function guardar($carpeta,$tabla){
		if ($this->input->is_ajax_request()) {
			$campos = ["descripcion","estado"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->descripcion,1];

			$existe = $this->db->query("select *from ".$carpeta.".".$tabla." where descripcion='".$this->request->descripcion."'")->result_array();
			if(count($existe)==0) {
				$estado = $this->Netix_model->netix_guardar($carpeta.".".$tabla, $campos, $valores, "true");
			}else{
				$estado = $this->Netix_model->netix_editar($carpeta.".".$tabla, $campos, $valores, $this->request->codigo, $existe[0][$this->request->codigo]);
				$estado = $existe[0][$this->request->codigo];
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}