<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("compras/proveedores/index");
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

			$lista = $this->db->query("select personas.* from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=2 or socios.codsociotipo=3) and socios.estado=1 order by personas.codpersona desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=2 or socios.codsociotipo=3) and socios.estado=1")->result_array();

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
				$tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
				$departamentos = $this->db->query("select distinct(ubidepartamento), departamento from public.ubigeo order by ubidepartamento")->result_array();
				$this->load->view("compras/proveedores/nuevo",compact("tipodocumentos","departamentos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function nuevo_1(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
				$this->load->view("compras/proveedores/nuevo_1",compact("tipodocumentos"));
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

			$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","email","telefono","codubigeo","convenio","estado"];
			$campos_1 = ["codpersona","codsociotipo","usuario","clave"];
			$valores = [$this->request->coddocumentotipo,$this->request->documento,$this->request->razonsocial,$this->request->nombrecomercial,$this->request->direccion,$this->request->email,$this->request->telefono,$this->request->codubigeo,$this->request->convenio,1];

			if($this->request->codregistro=="") {
				$existe =$this->db->query("select codpersona from public.personas where documento='".$this->request->documento."'")->result_array();
				if (count($existe)>0) {
					echo "e"; exit();
				}else{
					$codpersona = $this->Netix_model->netix_guardar("public.personas", $campos, $valores,"true");
				}

				$valores_1 = [$codpersona,$this->request->codsociotipo,$this->request->documento,$this->request->documento];
				$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
			}else{
				$actual = $this->db->query("select documento from public.personas where codpersona=".$this->request->codregistro)->result_array();
				$existe = $this->db->query("select documento from public.personas where documento='".$this->request->documento."'")->result_array();
				if (count($existe)>0) {
					if ( $actual[0]["documento"]!=$existe[0]["documento"] ) {
						echo "e"; exit();
					}
				}
				
				$estado = $this->Netix_model->netix_editar("public.personas", $campos, $valores, "codpersona", $this->request->codregistro);

				$valores_1 = [$this->request->codregistro,$this->request->codsociotipo,$this->request->documento,$this->request->documento];
				$existe = $this->db->query("select codpersona from public.socios where codpersona=".$this->request->codregistro)->result_array();
				if (count($existe)==0) {
					$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
				}else{
					$estado = $this->Netix_model->netix_editar("public.socios", $campos_1, $valores_1, "codpersona", $this->request->codregistro);
				}
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar_1(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","email","telefono","estado"];
			$campos_1 = ["codpersona","codsociotipo","usuario","clave"];
			$valores = [$this->request->coddocumentotipo,$this->request->documento,$this->request->razonsocial,$this->request->nombrecomercial,$this->request->direccion,$this->request->email,$this->request->telefono,1];

			$this->db->trans_begin();

			$existe =$this->db->query("select codpersona from public.personas where documento='".$this->request->documento."'")->result_array();
			if (count($existe)>0) {
				echo "e"; exit();
			}else{
				$socio = array();
				$codpersona = $this->Netix_model->netix_guardar("public.personas", $campos, $valores, "true");
			}

			$valores_1 = [$codpersona,$this->request->codsociotipo,$this->request->documento,$this->request->documento];
			if (count($socio)>0) {
				$estado = $this->Netix_model->netix_editar("public.socios", $campos_1, $valores_1,"codpersona",$codpersona);
			}else{
				$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
			}

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback(); $estado = 0;
			}else{
				$this->db->trans_commit();
				$estado = $this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$codpersona)->result_array();
			}

			echo json_encode($estado);
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select personas.codpersona as codregistro,* from public.personas as personas inner join public.socios as socios on(personas.codpersona=socios.codpersona) where personas.codpersona=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function ubigeo(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$info = $this->db->query("select personas.codubigeo, socios.codpatrocinador from public.personas as personas inner join public.socios as socios on(personas.codpersona=socios.codpersona) where personas.codpersona=".$this->request->codregistro)->result_array();
			$ubigeo = $this->db->query("select * from public.ubigeo where codubigeo=".$info[0]["codubigeo"])->result_array();
			$patrocinador = $this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$info[0]["codpatrocinador"])->result_array();
			$data["ubigeo"] = $ubigeo; $data["patrocinador"] = $patrocinador;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("public.socios", "codpersona", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}