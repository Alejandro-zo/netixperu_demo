<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Sucursales extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/sucursales/index");
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

			$lista = $this->db->query("select * from public.sucursales where UPPER(descripcion) like UPPER('%".$this->request->buscar."%') and estado=1 order by codsucursal desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from public.sucursales where UPPER(descripcion) like UPPER('%".$this->request->buscar."%') and estado=1")->result_array();

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
				$comprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, ct.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where c.codcomprobantetipo>=5 and c.estado=1")->result_array();
				$this->load->view("administracion/sucursales/nuevo",compact("comprobantes"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["codempresa","descripcion","direccion","telefonos","principal","codcomprobantetipo","seriecomprobante"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$_SESSION["netix_codempresa"],$this->request->descripcion,$this->request->direccion,$this->request->telefonos,$this->request->principal,$this->request->codcomprobantetipo,strtoupper($this->request->seriecomprobante)];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("public.sucursales", $campos, $valores);
			}else{
				$estado = $this->Netix_model->netix_editar("public.sucursales", $campos, $valores, "codsucursal", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codsucursal as codregistro,* from public.sucursales where codsucursal=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("public.sucursales", "codsucursal", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}