<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/usuarios/index");
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
			$limit = 6; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select public.personas.razonsocial,public.personas.foto, seguridad.usuarios.*, seguridad.perfiles.descripcion as perfil from seguridad.usuarios inner join public.personas on(seguridad.usuarios.codempleado=public.personas.codpersona) inner join seguridad.perfiles on(seguridad.usuarios.codperfil=seguridad.perfiles.codperfil) where (UPPER(public.personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(seguridad.perfiles.descripcion) like UPPER('%".$this->request->buscar."%')) and seguridad.usuarios.estado=1 order by seguridad.usuarios.codusuario desc offset ".$offset." limit ".$limit)->result_array();
			foreach ($lista as $key => $value) {
				$lista[$key]["sucursales"] = $this->db->query("select sucursal.descripcion as sucursal from public.sucursales as sucursal inner join seguridad.sucursalusuarios as sucursalusuario on(sucursal.codsucursal=sucursalusuario.codsucursal) where sucursalusuario.codusuario=".$value["codusuario"]." and sucursal.estado=1 order by sucursal.codsucursal")->result_array();
			}			

			$total = $this->db->query("select count(*) as total from seguridad.usuarios inner join public.personas on(seguridad.usuarios.codempleado=public.personas.codpersona) inner join seguridad.perfiles on(seguridad.usuarios.codperfil=seguridad.perfiles.codperfil) where (UPPER(public.personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(seguridad.perfiles.descripcion) like UPPER('%".$this->request->buscar."%')) and seguridad.usuarios.estado=1")->result_array();

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
				$empleados = $this->db->query("select persona.codpersona, persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1")->result_array();
				$perfiles = $this->db->query("select *from seguridad.perfiles where estado=1")->result_array();
				$sucursales = $this->db->query("select * from public.sucursales where estado=1")->result_array();
				$this->load->view("administracion/usuarios/nuevo",compact("empleados","perfiles","sucursales"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["codempleado","codperfil","usuario","clave","editar_pventa"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->campos->codempleado,$this->request->campos->codperfil,$this->request->campos->usuario,$this->request->campos->clave,$this->request->campos->editar_pventa];

			if($this->request->campos->codregistro=="") {
				$existe = $this->db->query("select usuario from seguridad.usuarios where usuario='".$this->request->campos->usuario."'")->result_array();
				if (count($existe)>0) { 
					echo "e"; exit();
				}

				$codusuario = $this->Netix_model->netix_guardar("seguridad.usuarios", $campos, $valores, "true");
				$this->request->campos->codregistro = $codusuario;
			}else{
				$actual = $this->db->query("select usuario from seguridad.usuarios where codusuario=".$this->request->campos->codregistro)->result_array();
				$existe = $this->db->query("select usuario from seguridad.usuarios where usuario='".$this->request->campos->usuario."'")->result_array();
				if (count($existe)>0) {
					if ( $actual[0]["usuario"]!=$existe[0]["usuario"] ) {
						echo "e"; exit();
					}
				}

				$estado = $this->Netix_model->netix_editar("seguridad.usuarios", $campos, $valores, "codusuario", $this->request->campos->codregistro);
			}

			$this->db->where("codusuario", $this->request->campos->codregistro);
			$estado = $this->db->delete("seguridad.sucursalusuarios");

			if (isset($this->request->sucursales)) {
				foreach ($this->request->sucursales as $key => $value) {
					$data = array(
						"codsucursal" => $value, 
						"codusuario" => $this->request->campos->codregistro
					);
					$estado = $this->db->insert("seguridad.sucursalusuarios", $data);
				}
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codusuario as codregistro,* from seguridad.usuarios where codusuario=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function sucursales(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codsucursal from seguridad.sucursalusuarios where codusuario=".$this->request->codregistro)->result_array(); $data = array();
			foreach ($info as $key => $value) {
				$data[] = $value["codsucursal"];
			}
			echo json_encode($data);
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

	function cambiarclave(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$existe = $this->db->query("select *from seguridad.usuarios where codusuario=".$_SESSION["netix_codusuario"]." and clave='".$this->request->clave."' ")->result_array();
			if (count($existe)==0) {
				echo "e";
			}else{
				$campos = ["clave"]; $valores = [$this->request->nuevaclave];
				$estado = $this->Netix_model->netix_editar("seguridad.usuarios", $campos, $valores, "codusuario", $_SESSION["netix_codusuario"]);
				echo $estado;
			}
		}else{
			$this->load->view("netix/404");
		}
	}
}