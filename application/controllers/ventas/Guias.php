<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Guias extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("ventas/guias/index");
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

			$fechas = "guia.fechaemision>='".$this->request->fechas->desde."' and guia.fechaemision<='".$this->request->fechas->hasta."' and";
			$lista = $this->db->query("select guia.* from kardex.guias as guia inner join public.personas as personas on (guia.coddestinatario=personas.codpersona) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(guia.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(guia.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and guia.codsucursal=".$_SESSION["netix_codsucursal"]." order by guia.codguia desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from kardex.guias as guia inner join public.personas as personas on (guia.coddestinatario=personas.codpersona) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(guia.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(guia.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and guia.codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

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
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$comprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, c.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codcomprobantetipo=16 and c.estado=1")->result_array();
				$this->load->view("ventas/guias/nuevo",compact("comprobantes"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}
	
	function ver($codregistro){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])){
				$this->db->query("select codubigeo, (distrito || ', ' || provincia || ', ' || departamento) as ubigeo from public.ubigeo order by distrito asc limit 20")->result_array();

				$info = $this->db->query("select guia.*, personas.documento, (up.distrito || ', ' || up.provincia || ', ' || up.departamento) as ubi_partida, (ud.distrito || ', ' || ud.provincia || ', ' || ud.departamento) as ubi_llegada from kardex.guias as guia inner join public.personas as personas on (guia.coddestinatario=personas.codpersona) inner join public.ubigeo as up on(guia.ubigeo_partida = up.codubigeo) inner join public.ubigeo as ud on(guia.ubigeo_llegada = ud.codubigeo) where guia.codguia=".$codregistro)->result_array();

				$detalle = $this->db->query("select gd.*, p.descripcion as producto,u.descripcion as unidad from kardex.guiasdetalle as gd inner join almacen.productos as p on(gd.codproducto=p.codproducto) inner join almacen.unidades as u on(gd.codunidad=u.codunidad) where gd.codguia=".$codregistro." and gd.estado=1 order by gd.item")->result_array();

				$this->load->view("ventas/guias/ver",compact("info", "detalle")); 
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

			$campos = [
				"codsucursal", "codalmacen", "codusuario", "coddestinatario", "fechaemision", "codcomprobantetipo", 
				"seriecomprobante", "valorguia", "porcigv", "igv", "importe", "descripcion", 
				"fechatraslado", "ubigeo_partida", "ubigeo_llegada", "punto_partida", "punto_llegada",
				"codmodalidadtraslado", "modalidadtraslado", "tipo_trasporte", "codempresa_traslado", 
				"nroplaca", "dniconductor", "conductor", "pesototal", "destinatario", "transportista"
			];
			$valores = [
				(int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codalmacen"], (int)$_SESSION["netix_codusuario"],
				(int)$this->request->campos->coddestinatario, $this->request->campos->fechaemision, 16, $this->request->campos->seriecomprobante,
				(double)$this->request->campos->valorguia, $_SESSION["netix_igv"], (double)$this->request->campos->igv, 
				(double)$this->request->campos->importe, $this->request->campos->descripcion, 
				$this->request->campos->fechatraslado, $this->request->campos->ubigeo_partida, $this->request->campos->ubigeo_llegada, 
				$this->request->campos->punto_partida, $this->request->campos->punto_llegada, $this->request->campos->codmodalidadtraslado, 
				$this->request->campos->modalidadtraslado, $this->request->campos->tipo_trasporte, (int)$this->request->campos->codempresa_traslado,
				$this->request->campos->nroplaca, $this->request->campos->dniconductor, $this->request->campos->conductor, 
				$this->request->campos->pesototal, $this->request->campos->destinatario, $this->request->campos->transportista
			];

			if($this->request->campos->codguia == 0) {
				$estado = $this->Netix_model->netix_guardar("kardex.guias", $campos, $valores);
				$codguia = $this->db->insert_id("kardex.guias_codguia_seq");

				$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=16 and seriecomprobante='".$this->request->campos->seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

				$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
				$data = array(
					"nrocorrelativo" => $nrocorrelativo
				);
				$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
				$this->db->where("codcomprobantetipo", 16);
				$this->db->where("seriecomprobante", $this->request->campos->seriecomprobante);
				$estado = $this->db->update("caja.comprobantes", $data);

				$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
				$data = array(
					"nrocomprobante" => $nrocorrelativo
				);
				$this->db->where("codguia", $codguia);
				$estado = $this->db->update("kardex.guias", $data);
			}else{
				$estado = $this->Netix_model->netix_editar("kardex.guias", $campos, $valores, "codguia", $this->request->codguia);
				$codguia = $this->request->codguia;
			}
			
			$campos = ["codguia", "codkardex"]; $valores = [(int)$codguia, (int)$this->request->campos->codkardex_ref];
			$estado = $this->Netix_model->netix_guardar("kardex.guiaskardex", $campos, $valores);

			$item = 0; $estado = 1;
			foreach ($this->request->detalle as $key => $value) { $item = $item + 1;
				$data = array(
					"codguia" => (int)$codguia, 
					"codproducto" => (int)$this->request->detalle[$key]->codproducto, 
					"codunidad" => (int)$this->request->detalle[$key]->codunidad, "item" => $item,
					"cantidad" => (double)$this->request->detalle[$key]->cantidad,
					"preciosinigv" => (double)$this->request->detalle[$key]->preciosinigv,
					"preciounitario" => (double)$this->request->detalle[$key]->precio,
					"preciorefunitario" => (double)$this->request->detalle[$key]->precio,
					"codafectacionigv" => $this->request->detalle[$key]->codafectacionigv,
					"igv" => (double)$this->request->detalle[$key]->igv,
					"valorguia" => (double)$this->request->detalle[$key]->valorguia,
					"subtotal" => (double)$this->request->detalle[$key]->subtotal,
					"descripcion" => $this->request->detalle[$key]->descripcion,
					"pesokg" => (double)$this->request->detalle[$key]->pesokg,
					"pesototal" => (double)$this->request->detalle[$key]->pesototal
				);
				$estado = $this->db->insert("kardex.guiasdetalle", $data);
			}

			$data = array(); $data["estado"] = $estado; $data["codguia"] = $codguia;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codguia as codregistro,* from kardex.guias where codguia=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("kardex.guias", "codguia", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function ubigeos(){
		if ($this->input->is_ajax_request()) {
			if (isset($_POST["q"])) {
				$ubigeos = $this->db->query("select codubigeo, (distrito || ', ' || provincia || ', ' || departamento) as ubigeo from public.ubigeo  where UPPER(distrito) like UPPER('%".$_POST["q"]."%') or UPPER(provincia) like UPPER('%".$_POST["q"]."%') or UPPER(departamento) like UPPER('%".$_POST["q"]."%') order by distrito asc limit 20")->result_array();
			}else{
				$ubigeos = $this->db->query("select codubigeo, (distrito || ', ' || provincia || ', ' || departamento) as ubigeo from public.ubigeo order by distrito asc limit 20")->result_array();
			}
			echo json_encode($ubigeos);
		}
	}

	function comprobantes($codpersona, $desde, $hasta){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select codkardex,codcomprobantetipo,seriecomprobante,nrocomprobante,fechacomprobante,round(kardex.importe,2) as importe,kardex.estado,kardex.cliente,kardex.direccion from kardex.kardex where codpersona=".$codpersona." and fechacomprobante>='".$desde."' and fechacomprobante<='".$hasta."' and codmovimientotipo=20 and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1 order by fechacomprobante desc")->result_array();

			echo json_encode($lista);
		}
	}

	function detalle($codregistro){
		if ($this->input->is_ajax_request()) {
			$detalle = $this->db->query("select kd.codproducto,kd.codunidad,round(kd.cantidad,2) as cantidad,round(kd.preciounitario,2) as precio,kd.preciosinigv,kd.preciorefunitario,kd.valorventa,round(kd.igv,2) as igv, round(kd.subtotal,2) as subtotal,kd.codafectacionigv,p.descripcion as producto,u.descripcion as unidad, kd.recoger,kd.recogido,kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codregistro." and kd.estado=1 order by kd.item")->result_array();

			echo json_encode($detalle);
		}
	}
}