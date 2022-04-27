<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Atender extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$ambientes = $this->db->query("select *from restaurante.ambientes where codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1 order by codambiente asc")->result_array();
				$lineas = $this->db->query("select *from almacen.lineas where estado=1 order by descripcion asc")->result_array();

				$comprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, ct.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codcomprobantetipo>=5 and c.estado=1")->result_array();
				$conceptos = $this->db->query("select *from caja.conceptos where codconcepto=13 or codconcepto=15")->result_array();
				$tipopagos = $this->db->query("select *from caja.tipopagos where ingreso=1 and estado=1 order by codtipopago")->result_array();
				$vendedores = $this->db->query("select persona.codpersona,persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1 and empleado.codcargo=4")->result_array();
				$sucursal = $this->db->query("select codcomprobantetipo, seriecomprobante from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

				$this->load->view("restaurante/atender/index",compact("ambientes","lineas","comprobantes","conceptos","tipopagos","vendedores","sucursal"));
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
}