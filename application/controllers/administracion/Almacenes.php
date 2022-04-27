<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Almacenes extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/almacenes/index");
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
			$limit = 4; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select almacen.*, sucursal.descripcion as sucursal from almacen.almacenes as almacen inner join public.sucursales as sucursal on(almacen.codsucursal=sucursal.codsucursal) where (UPPER(almacen.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(sucursal.descripcion) like UPPER('%".$this->request->buscar."%')) and almacen.estado=1 order by almacen.codalmacen desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from almacen.almacenes as almacen inner join public.sucursales as sucursal on(almacen.codsucursal=sucursal.codsucursal) where (UPPER(almacen.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(sucursal.descripcion) like UPPER('%".$this->request->buscar."%')) and almacen.estado=1")->result_array();

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
				$sucursales = $this->db->query("select * from public.sucursales where estado=1")->result_array();
				$this->load->view("administracion/almacenes/nuevo",compact("sucursales"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["codsucursal","descripcion","direccion","telefonos","controlstock"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->codsucursal,$this->request->descripcion,$this->request->direccion,$this->request->telefonos,$this->request->controlstock];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("almacen.almacenes", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("almacen.almacenes", $campos, $valores, "codalmacen", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codalmacen as codregistro,* from almacen.almacenes where codalmacen=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("almacen.almacenes", "codalmacen", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}