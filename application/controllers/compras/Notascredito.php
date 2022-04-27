<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Notascredito extends CI_Controller {

	public function __construct(){
		parent::__construct(); 
		$this->load->model("Netix_model"); $this->load->model("Caja_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("compras/notascredito/index");
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

			$lista = $this->db->query("select personas.documento,kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante, kardex.nrocomprobante, kardex.fechacomprobante,kardex.seriecomprobante_ref,kardex.nrocomprobante_ref,round(kardex.importe,2) as importe, kardex.estado, comprobantes.descripcion as tipo, kardex.descripcion, kardex.cliente from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=26 and kardex.codcomprobantetipo=14 order by kardex.codkardex desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=26 and kardex.codcomprobantetipo=14")->result_array();

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
				$motivos = $this->db->query("select *from kardex.motivonotas where tipo=7 and estado=1 order by codmotivonota limit 1")->result_array();
				$tipocomprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, ct.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where (c.codcomprobantetipo=10 or c.codcomprobantetipo=12) and c.estado=1")->result_array();
				$this->load->view("compras/notascredito/nuevo",compact("motivos","tipocomprobantes"));
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
				$info = $this->db->query("select kardex.*,personas.*,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.codkardex=".$codregistro)->result_array();

				$detalle = $this->db->query("select kd.*,p.descripcion as producto,u.descripcion as unidad from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codregistro." and kd.estado=1 order by kd.item")->result_array();

				$this->load->view("ventas/notascredito/ver",compact("info","detalle")); 
			}else{
	            $this->load->view("inicio/505");
	        }
	    }else{
			$this->load->view("inicio/404");
		}
	}

	function comprobantes($codpersona,$fechacomprobante){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select personas.documento,personas.razonsocial as cliente,personas.direccion,personas.nombrecomercial, kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.nrocomprobante,kardex.codmoneda,kardex.tipocambio, kardex.fechacomprobante,round(kardex.importe,2) as importe,kardex.estado from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.fechacomprobante='".$fechacomprobante."' and kardex.codpersona=".$codpersona." and kardex.codmovimientotipo=2 and kardex.codsucursal=".$_SESSION["netix_codsucursal"]." and kardex.estado=1 order by kardex.codkardex")->result_array();
			foreach ($lista as $key => $value) {
				$motivo = $this->db->query("select k.codmotivonota,mn.descripcion from kardex.kardex as k inner join kardex.motivonotas as mn on(k.codmotivonota=mn.codmotivonota) where k.codkardex_ref=".$value["codkardex"])->result_array();

				if (count($motivo)==0) {
					$lista[$key]["codmotivonota"] = 0;
					$lista[$key]["motivo"] = "";
				}else{
					$lista[$key]["codmotivonota"] = $motivo[0]["codmotivonota"];
					$lista[$key]["motivo"] = $motivo[0]["descripcion"];
				}
			}

			$data = array(); $data["comprobantes"] = $lista; $data["series"] = [];
			echo json_encode($data);
		}
	}

	function detalle($codregistro){
		if ($this->input->is_ajax_request()) {
			$detalle = $this->db->query("select kd.codproducto,kd.codunidad,round(kd.cantidad,2) as cantidad,round(kd.preciounitario,2) as precio,kd.preciosinigv,kd.preciorefunitario,kd.valorventa,round(kd.igv,2) as igv, round(kd.subtotal,2) as subtotal,kd.codafectacionigv,p.descripcion as producto,u.descripcion as unidad, kd.recoger,kd.recogido,kd.descripcion from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codregistro." and kd.estado=1 order by kd.item")->result_array();

			$totales = $this->db->query("select codkardex,round(valorventa,2) as valorventa,round(igv,2) as igv,round(importe,2) as importe from kardex.kardex where codkardex=".$codregistro)->result_array();

			$data["detalle"] = $detalle;
			$data["totales"] = $totales;
			echo json_encode($data);
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				// REGISTRO KARDEX //
				$comprobante_nota = 14;
				$campos = ["codsucursal","codalmacen","codkardex_ref","codpersona","codusuario","codmotivonota","codmovimientotipo","codmoneda","tipocambio","fechakardex","fechacomprobante","codcomprobantetipo","seriecomprobante","nrocomprobante","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","valorventa","porcigv","igv","importe","descripcion","cliente","direccion"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$this->request->campos->codkardex_ref,
					(int)$this->request->campos->codpersona,
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmotivonota,
					(int)$this->request->campos->codmovimientotipo,
					(int)$this->request->campos->codmoneda,
					(double)$this->request->campos->tipocambio, date("Y-m-d"),date("Y-m-d"),
					(int)$comprobante_nota,
					$this->request->campos->seriecomprobante,
					$this->request->campos->nrocomprobante,
					(int)$this->request->campos->codcomprobantetipo_ref,
					$this->request->campos->seriecomprobante_ref,
					$this->request->campos->nrocomprobante_ref,
					(double)$this->request->totales->valorventa,
					(double)$_SESSION["netix_igv"],
					(double)$this->request->totales->igv,
					(double)$this->request->totales->importe,
					$this->request->campos->descripcion,
					$this->request->campos->cliente,
					$this->request->campos->direccion
				];
				$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true");

				// REGISTRO KARDEX ALMACEN //
				$comprobante_almacen = 4;
				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobante_almacen." and codsucursal=".$_SESSION["netix_codsucursal"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();

				$campos = ["codsucursal","codalmacen","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$codkardex,
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmovimientotipo,date("Y-m-d"),
					(int)$comprobante_almacen, $series[0]["seriecomprobante"]
				];
				$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
				$nro_kardexalmacen = $this->Kardex_model->netix_kardexalmacencorrelativo($codkardexalmacen,$comprobante_almacen,$series[0]["seriecomprobante"]);

				// REGISTRO KARDEX DETALLE Y KARDEX ALMACEN DETALLE //
				$item = 0;
				foreach ($this->request->detalle as $key => $value) { $item = $item + 1;
					$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","igv","valorventa","subtotal","descripcion"];
					$valores =[
						(int)$codkardex,
						(int)$this->request->detalle[$key]->codproducto,
						(int)$this->request->detalle[$key]->codunidad, $item,
						(double)$this->request->detalle[$key]->cantidad,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->preciosinigv,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->preciorefunitario,
						$this->request->detalle[$key]->codafectacionigv,
						(double)$this->request->detalle[$key]->igv,
						(double)$this->request->detalle[$key]->valorventa,
						(double)$this->request->detalle[$key]->subtotal,
						$this->request->detalle[$key]->descripcion
					];
					$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

					if ($this->request->detalle[$key]->recoger==0) {
						$cantidad = $this->request->detalle[$key]->recogido;
					}else{
						$cantidad = $this->request->detalle[$key]->cantidad;
					}

					if ($cantidad!=0) {
						$campos =["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
						$valores =[
							(int)$codkardexalmacen,
							(int)$this->request->detalle[$key]->codproducto,
							(int)$this->request->detalle[$key]->codunidad, $item,
							(int)$_SESSION["netix_codalmacen"],
							(int)$_SESSION["netix_codsucursal"],
							(double)$cantidad
						];
						$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);
					}

					$existe_ubi = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad)->result_array();
					
					if (count($existe_ubi)>0) {
						$stock = $existe_ubi[0]["stockactual"] - $cantidad;

						$campos = ["stockactual"]; $valores = [(double)$stock];
						$f = ["codalmacen","codproducto","codunidad"]; 
						$v = [(int)$_SESSION["netix_codalmacen"],(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad];
						$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
					}
				}

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback(); $estado = 0;
				}else{
					if ($estado!=1) {
						$this->db->trans_rollback(); $estado = 0;
					}
					$this->db->trans_commit();
				}
				$data["estado"] = $estado; $data["codkardex"] = $codkardex;
				echo json_encode($data);
			}else{
				echo "e";
			}
		}else{
			$this->load->view("netix/404");
		}
	}
}