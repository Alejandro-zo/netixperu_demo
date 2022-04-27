<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Comprobantes extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("administracion/comprobantes/index");
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

			$lista = $this->db->query("select comprobantes.*, tipos.descripcion as tipo, sucursales.descripcion as sucursal from caja.comprobantes as comprobantes inner join caja.comprobantetipos as tipos on(comprobantes.codcomprobantetipo=tipos.codcomprobantetipo) inner join public.sucursales as sucursales on(comprobantes.codsucursal=sucursales.codsucursal) where (UPPER(tipos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(sucursales.descripcion) like UPPER('%".$this->request->buscar."%')) and comprobantes.estado=1 order by sucursales.codsucursal desc offset ".$offset." limit ".$limit)->result_array();
			foreach ($lista as $key => $value) {
				$caja = $this->db->query("select *from caja.cajas where codcaja=".$value["codcaja"])->result_array();
				if (count($caja)!=0) {
					$lista[$key]["referencia"] = $caja[0]["descripcion"];
				}else{
					$almacen = $this->db->query("select *from almacen.almacenes where codalmacen=".$value["codalmacen"])->result_array();
					if (count($almacen)!=0) {
						$lista[$key]["referencia"] = $almacen[0]["descripcion"];
					}else{
						$lista[$key]["referencia"] = "";
					}
				}
			}
			
			$total = $this->db->query("select count(*) as total from caja.comprobantes as comprobantes inner join caja.comprobantetipos as tipos on(comprobantes.codcomprobantetipo=tipos.codcomprobantetipo) inner join public.sucursales as sucursales on(comprobantes.codsucursal=sucursales.codsucursal) where (UPPER(tipos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(sucursales.descripcion) like UPPER('%".$this->request->buscar."%')) and comprobantes.estado=1")->result_array();

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
				$tipos = $this->db->query("select * from caja.comprobantetipos where estado=1 order by codcomprobantetipo")->result_array();
				$this->load->view("administracion/comprobantes/nuevo",compact("sucursales","tipos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function cajas($codsucursal){
		if ($this->input->is_ajax_request()) {
			$cajas = $this->db->query("select *from caja.cajas where codsucursal=".$codsucursal." and estado=1")->result_array();
			$html = '<option value="">SELECCIONE</option>';
			foreach ($cajas as $key => $value) {
				$html .= '<option value="'.$value["codcaja"].'">'.$value["descripcion"].'</option>';
			}
			echo $html;
		}
	}

	function cajas_existe($codcaja,$codcomprobantetipo){
		if ($this->input->is_ajax_request()) {
			$existe = $this->db->query("select *from caja.comprobantes where codcaja=".$codcaja." and codcomprobantetipo=".$codcomprobantetipo." and estado=1")->result_array();
			if (count($existe)==0) {
				echo "0";
			}else{
				echo "1";
			}
		}
	}

	function almacenes($codsucursal){
		if ($this->input->is_ajax_request()) {
			$almacenes = $this->db->query("select *from almacen.almacenes where codsucursal=".$codsucursal." and estado=1")->result_array();
			$html = '<option value="">SELECCIONE</option>';
			foreach ($almacenes as $key => $value) {
				$html .= '<option value="'.$value["codalmacen"].'">'.$value["descripcion"].'</option>';
			}
			echo $html;
		}
	}

	function almacen_existe($codalmacen,$codcomprobantetipo){
		if ($this->input->is_ajax_request()) {
			$existe = $this->db->query("select *from caja.comprobantes where codalmacen=".$codalmacen." and codcomprobantetipo=".$codcomprobantetipo." and estado=1")->result_array();
			if (count($existe)==0) {
				echo "0";
			}else{
				echo "1";
			}
		}
	}

	function notas($codsucursal){
		if ($this->input->is_ajax_request()) {
			$notas = $this->db->query("select c.*,ct.descripcion as tipo from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo=ct.codcomprobantetipo) where (c.codcomprobantetipo=10 or c.codcomprobantetipo=12) and c.codsucursal=".$codsucursal." and c.estado=1")->result_array();
			$html = '<option value="">SELECCIONE</option>';
			foreach ($notas as $key => $value) {
				$html .= '<option value="'.$value["codcomprobantetipo"].'-'.$value["seriecomprobante"].'">'.$value["tipo"].' (SERIE: '.$value["seriecomprobante"].')</option>';
			}
			echo $html;
		}
	}

	function notas_existe($codcomprobante,$codcomprobantetipo){
		if ($this->input->is_ajax_request()) {
			$datos = explode("-", $codcomprobante);

			$existe = $this->db->query("select *from caja.comprobantes where codcomprobantetipo_ref=".$datos[0]." and seriecomprobante_ref='".$datos[1]."' and codcomprobantetipo=".$codcomprobantetipo." and estado=1")->result_array();
			if (count($existe)==0) {
				echo "0";
			}else{
				echo "1";
			}
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codsucursal","codcomprobantetipo","codcaja","codalmacen","seriecomprobante","nroinicial","nrocorrelativo","codcomprobantetipo_ref","seriecomprobante_ref","impresion","formato","orientacion","impresora"];
			if ($this->request->codcaja=="") {
				$this->request->codcaja = 0;
			}
			if ($this->request->codalmacen=="") {
				$this->request->codalmacen = 0;
			}

			$comprobantetipo_ref = ""; $seriecomprobante_ref = "";
			if ($this->request->codcomprobantetipo_ref!="" && $this->request->codcomprobantetipo_ref!=0) {
				$datos = explode("-",$this->request->codcomprobantetipo_ref);
				$comprobantetipo_ref = $datos[0]; $seriecomprobante_ref = $datos[1];
			}

			$valores = [
				(int)$this->request->codsucursal,
				(int)$this->request->codcomprobantetipo,
				(int)$this->request->codcaja,
				(int)$this->request->codalmacen,
				strtoupper($this->request->seriecomprobante),
				(int)$this->request->nroinicial,
				(int)$this->request->nrocorrelativo, 
				(int)$comprobantetipo_ref,$seriecomprobante_ref,
				(int)$this->request->impresion,
				$this->request->formato,$this->request->orientacion,$this->request->impresora
			];

			if($this->request->codregistro=="") {
				$estado = $this->Netix_model->netix_guardar("caja.comprobantes", $campos, $valores);
			}else{
				$f = ["codsucursal","codcomprobantetipo","seriecomprobante"];
				$v = [$this->request->codsucursal,$this->request->codcomprobantetipo,$this->request->seriecomprobante_editar];
				$estado = $this->Netix_model->netix_editar_1("caja.comprobantes", $campos, $valores, $f, $v);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$codigo = explode("-", $this->request->codregistro);
			$info = $this->db->query("select codcomprobantetipo as codregistro,* from caja.comprobantes where codcomprobantetipo=".$codigo[0]." and seriecomprobante='".$codigo[1]."' ")->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function validar_serie($serie){
		if ($this->input->is_ajax_request()) {
			$codigo = explode("-", $serie);
			$estado = $this->db->query("select count(*) as cantidad from kardex.kardexalmacen where seriecomprobante='".$codigo[1]."'")->result_array();
			$data["serie"] = $codigo[1];
			$data["estado"] = $estado[0]["cantidad"];
			echo json_encode($data);
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$codigo = explode("-", $this->request->codregistro);

			$campos = ["estado"]; $valores = [0];
			$f = ["codcomprobantetipo","seriecomprobante"]; $v = [$codigo[0],$codigo[1]];
			$estado = $this->Netix_model->netix_editar_1("caja.comprobantes", $campos, $valores, $f, $v);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}