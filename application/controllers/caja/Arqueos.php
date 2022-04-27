<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Arqueos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("caja/arqueos/index");
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

			$lista = $this->db->query("select *, round(saldoinicialcaja + saldofinalcaja,2) as cierre from caja.controldiario where codsucursal=".$_SESSION["netix_codsucursal"]." and fechaapertura>='".$this->request->filtro->desde."' and fechaapertura<='".$this->request->filtro->hasta."' and estado=1 order by fechaapertura desc offset ".$offset." limit ".$limit)->result_array();
			$total = $this->db->query("select count(*) as total from caja.controldiario where codsucursal=".$_SESSION["netix_codsucursal"]." and fechaapertura>='".$this->request->filtro->desde."' and fechaapertura<='".$this->request->filtro->hasta."' and estado=1")->result_array();

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
}