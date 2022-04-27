<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Mesas extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("restaurante/mesas/index");
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
			$limit = 20; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select m.*, a.descripcion as ambiente from restaurante.mesas as m inner join restaurante.ambientes as a on(m.codambiente=a.codambiente) where UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') and m.estado=1 order by m.codmesa asc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from restaurante.mesas where UPPER(descripcion) like UPPER('%".$this->request->buscar."%') and estado=1")->result_array();

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

	public function mesas_ambiente($codambiente){
		if ($this->input->is_ajax_request()) {
			$mesas = $this->db->query("select *from restaurante.mesas where codambiente=".$codambiente." and estado=1 order by codmesa")->result_array();
			foreach ($mesas as $key => $value) {
				if($value["situacion"]==1){
					$color = "netix-libre"; $texto = "LIBRE";
				}elseif ($value["situacion"]==2) {
					$color = "netix-ocupada"; $texto = "OCUPADA";
				}elseif ($value["situacion"]==3) {
					$color = "netix-reservada"; $texto = "RESERVADA";
				}else{
					$color = "netix-avancecta"; $texto = "AVANCE CTA";
				}
				$mesas[$key]["color"] = $color;
				$mesas[$key]["texto"] = $texto;
			}
			echo json_encode($mesas);
		}
	}

	public function nuevo(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$ambientes = $this->db->query("select * from restaurante.ambientes where codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();
				$this->load->view("restaurante/mesas/nuevo",compact("ambientes"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["codambiente","descripcion","nromesa","capacidad"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->codambiente,"MESA NRO ".$this->request->nromesa,$this->request->nromesa,(int)$this->request->capacidad];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("restaurante.mesas", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("restaurante.mesas", $campos, $valores, "codmesa", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codmesa as codregistro,* from restaurante.mesas where codmesa=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("restaurante.mesas", "codmesa", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}