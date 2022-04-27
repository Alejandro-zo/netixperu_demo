<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cajas extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");

	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/cajas/index");
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

			$lista = $this->db->query("select caja.*, sucursal.descripcion as sucursal from caja.cajas as caja inner join public.sucursales as sucursal on(caja.codsucursal=sucursal.codsucursal) where (UPPER(caja.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(sucursal.descripcion) like UPPER('%".$this->request->buscar."%')) and caja.estado=1 order by caja.codcaja desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from caja.cajas as caja inner join public.sucursales as sucursal on(caja.codsucursal=sucursal.codsucursal) where (UPPER(caja.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(sucursal.descripcion) like UPPER('%".$this->request->buscar."%')) and caja.estado=1")->result_array();

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
				$this->load->view("administracion/cajas/nuevo",compact("sucursales"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["codsucursal","descripcion","direccion","telefonos"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->codsucursal,$this->request->descripcion,$this->request->direccion,$this->request->telefonos];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("caja.cajas", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("caja.cajas", $campos, $valores, "codcaja", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codcaja as codregistro,* from caja.cajas where codcaja=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("caja.cajas", "codcaja", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}