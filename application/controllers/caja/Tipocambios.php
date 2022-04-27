<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Tipocambios extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("caja/tipocambios/index");
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

			if ($this->request->buscar=="") {
				$lista = $this->db->query("select * from caja.tipocambios where estado=1 order by fecha desc offset ".$offset." limit ".$limit)->result_array();
			}else{
				$lista = $this->db->query("select * from caja.tipocambios where fecha='".$this->request->buscar."' and estado=1 order by fecha desc offset ".$offset." limit ".$limit)->result_array();
			}

			$total = $this->db->query("select count(*) as total from caja.tipocambios where estado=1")->result_array();

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
				$monedas = $this->db->query("select *from caja.monedas where codmoneda>1 and estado=1 order by codmoneda")->result_array();
				$this->load->view("caja/tipocambios/nuevo",compact("monedas"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$campos = ["codmoneda","fecha","compra","venta","estado"];
			$valores = [
				(int)$this->request->codmoneda,
				$this->request->fecha,
				(double)$this->request->compra,
				(double)$this->request->venta,1
			];

			$fecha_existe = $this->db->query("select *from caja.tipocambios where fecha='".$this->request->fecha."'")->result_array();
			if (count($fecha_existe)>0) {
				$this->request->codregistro = $fecha_existe[0]["codtipocambio"];
			}

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("caja.tipocambios", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("caja.tipocambios", $campos, $valores, "codtipocambio", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codtipocambio as codregistro,* from caja.tipocambios where codtipocambio=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function consulta($fecha){
		$fecha_existe = $this->db->query("select *from caja.tipocambios where fecha='".$fecha."'")->result_array();
		if (count($fecha_existe)>0) {
			$venta = $fecha_existe[0]["venta"];
		}else{
			$venta = 3;
		}
		echo round($venta,2);
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("caja.tipocambios", "codtipocambio", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}