<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Configuraciones extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$info = $this->db->query("select *from public.personas where codpersona=".$_SESSION["netix_codempresa"])->result_array();
			$empresa = $this->db->query("select *from public.empresas where codempresa=".$_SESSION["netix_codempresa"])->result_array();
			$this->load->view("administracion/configuraciones/index",compact("info","empresa"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			/* $dep = substr($_POST["ubigeo"],0,2); $pro = substr($_POST["ubigeo"],2,2); $dis = substr($_POST["ubigeo"],4,2); $codubigeo = 0;
			$ubigeo = $this->db->query("select codubigeo from public.ubigeo where ubidepartamento='".$dep."' and ubiprovincia='".$pro."' and ubidistrito='".$dis."'")->result_array();
			if(count($ubigeo)>0){
				$codubigeo = $ubigeo[0]["codubigeo"];
			} */
			$codubigeo = 0;
			$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","email","telefono","codubigeo"];
			$valores = [4,$_POST["documento"],$_POST["razonsocial"],$_POST["nombrecomercial"],$_POST["direccion"],$_POST["email"],$_POST["telefono"],$codubigeo];
			$estado = $this->Netix_model->netix_editar("public.personas", $campos, $valores,"codpersona",$_POST["codpersona"]);

			$campos = ["igvsunat","icbpersunat","iscsunat","slogan","itemrepetircomprobante","claveseguridad","publicidad","agradecimiento", "fechaoperaciones"];
			$valores = [(double)$_POST["igvsunat"],(double)$_POST["icbpersunat"],(double)$_POST["iscsunat"],$_POST["slogan"],$_POST["itemrepetircomprobante"],$_POST["claveseguridad"],$_POST["publicidad"],$_POST["agradecimiento"],$_POST["fechaoperaciones"]];
			$estado = $this->Netix_model->netix_editar("public.empresas", $campos, $valores,"codempresa",$_POST["codempresa"]);

			if ($_FILES["logo"]["name"]!="") {
				$file = "logo_".substr($_FILES["logo"]["name"],-5);
				move_uploaded_file($_FILES["logo"]["tmp_name"],"./public/img/empresa/".$file);
				
				$data = array("foto" => $file);
				$this->db->where("codpersona",$_POST["codpersona"]);
				$estado = $this->db->update("public.personas",$data);
			}
			if ($_FILES["auspiciador"]["name"]!="") {
				$file = "auspiciador_".substr($_FILES["auspiciador"]["name"],-5);
				move_uploaded_file($_FILES["auspiciador"]["tmp_name"],"./public/img/empresa/".$file);
				
				$data = array("foto" => $file);
				$this->db->where("codempresa",$_POST["codempresa"]);
				$estado = $this->db->update("public.empresas",$data);
			}

			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}
