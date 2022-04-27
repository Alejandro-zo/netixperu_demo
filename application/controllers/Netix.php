<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Netix extends CI_Controller {

	function __construct(){
		parent::__construct();
		$this->load->model("Netix_model");
	}

	public function index(){
		if (isset($_SESSION["netix_usuario"])) {
			$info = $this->db->query("select sucursal.* from public.sucursales as sucursal inner join seguridad.sucursalusuarios as sucursalusuario on(sucursal.codsucursal=sucursalusuario.codsucursal) where sucursalusuario.codusuario=".$_SESSION["netix_codusuario"]." and sucursal.estado=1 order by sucursal.codsucursal")->result_array();
			foreach ($info as $key => $value) {
				$info[$key]["almacenes"] = $this->db->query("select *from almacen.almacenes where estado=1 and codsucursal=".$value["codsucursal"])->result_array();
				$info[$key]["cajas"] = $this->db->query("select *from caja.cajas where estado=1 and codsucursal=".$value["codsucursal"])->result_array();
			}
			$this->load->view("netix/administrar",compact("info"));
		}else{
			$this->load->view("netix/login");
		}
	}

	public function netix_login(){
		if ($this->input->is_ajax_request()) {
			// $this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_login($this->input->post("usuario"),$this->input->post("clave"));
	        echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	public function netix_web(){
		if (isset($_SESSION["netix_codusuario"])){
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_web($this->request->sucursal,$this->request->almacen,$this->request->caja);
	        echo $estado;
        }else{
            $this->load->view("netix/404");
        }
	}

	public function netix_perfil(){
		if (isset($_SESSION["netix_codusuario"])){
			$usuario = $this->db->query("select *from seguridad.usuarios where codusuario=".$_SESSION["netix_codusuario"])->result_array();
			$persona = $this->db->query("select *from public.personas where codpersona=".$usuario[0]["codempleado"])->result_array();
			$this->load->view("netix/perfil",compact("usuario","persona"));
        }else{
            $this->load->view("netix/404");
        }
	}

	public function w($netix_modulo = "", $netix_submodulo = ""){
		if (isset($_SESSION["netix_codsucursal"])){
			$netix_modulos = $this->Netix_model->netix_modulos();
			// FALTA CONSULTAR SI TIENE PERMISO A ESTE MODULO //
            $this->load->view("netix/index",compact("netix_modulos"));
        }else{
            $this->load->view("netix/404");
        }
	}

	public function netix_logout(){
		session_destroy(); header("Location: ".base_url());
	}
}