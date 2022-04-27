<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Perfiles extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/perfiles/index");
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function lista(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$limit = 10; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select * from seguridad.perfiles where UPPER(descripcion) like UPPER('%".$this->request->buscar."%') and estado=1 order by codperfil desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from seguridad.perfiles where UPPER(descripcion) like UPPER('%".$this->request->buscar."%') and estado=1")->result_array();

			$paginas = floor($total[0]["total"] / $limit);
			if ( ($total[0]["total"] % $limit)!=0 ) {
				$paginas = $paginas + 1;
			}

			$paginacion = array();
			$paginacion["total"] = $total[0]["total"];
			$paginacion["actual"] = $this->request->pagina;
			$paginacion["ultima"] = $paginas;
			$paginacion["desde"] = $offset;
			$paginacion["hasta"] = $offset + $limit;

			echo json_encode(array("lista" => $lista,"paginacion" => $paginacion));
		}else{
			$this->load->view("netix/404");
		}
	}

	public function nuevo(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/perfiles/nuevo");
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["descripcion"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->descripcion];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("seguridad.perfiles", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("seguridad.perfiles", $campos, $valores, "codperfil", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codperfil as codregistro,* from seguridad.perfiles where codperfil=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("seguridad.perfiles", "codperfil", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	public function permisos(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->request = json_decode(file_get_contents('php://input'));

				$modulos = $this->db->query("select *from seguridad.modulos where codpadre=0 and estado=1 order by orden")->result_array();
				foreach ($modulos as $key => $value) {
					$modulos[$key]["submodulos"] = $this->db->query("select *from seguridad.modulos where codpadre=".$value["codmodulo"]." and estado=1 order by orden")->result_array();
				}
				$permisos = $this->db->query("select *from seguridad.moduloperfiles where codperfil=".$this->request->codregistro)->result_array();
				$this->load->view("administracion/perfiles/permisos",compact("modulos","permisos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar_permisos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$this->db->where("codperfil", $this->request->codperfil);
			$estado = $this->db->delete("seguridad.moduloperfiles");

			if (isset($this->request->modulos)) {
				foreach ($this->request->modulos as $key => $value) {
					$data = array(
						"codmodulo" => $value, 
						"codperfil" => $this->request->codperfil
					);
					$estado = $this->db->insert("seguridad.moduloperfiles", $data);
				}
			}
			echo $estado;
		}else{
			$this->load->view("inicio/404");
		}
	}
}