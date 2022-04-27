<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Despachos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("almacen/despachos/index");
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

			$lista = $this->db->query("select personas.documento,personas.razonsocial,personas.nombrecomercial,kardex.codkardex, kardex.codmovimientotipo, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago,kardex.nrocomprobante, kardex.fechakardex,round(kardex.importe,2) as importe,kardex.estado,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.retirar=0 and (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and (kardex.codmovimientotipo=2 or kardex.codmovimientotipo=20) and kardex.estado=1 order by kardex.codkardex desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.retirar=0 and (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and (kardex.codmovimientotipo=2 or kardex.codmovimientotipo=20) and kardex.estado=1")->result_array();

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

	public function nuevo($codkardex){
		if ($this->input->is_ajax_request()) {
			$info = $this->db->query("select * from kardex.kardex where codkardex=".$codkardex)->result_array();
			$this->load->view("almacen/despachos/nuevo",compact("info"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function filtrar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$lista = $this->db->query("select kardex.codkardex, kardex.codmovimientotipo, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago,kardex.nrocomprobante, kardex.fechakardex,round(kardex.importe,2) as importe,kardex.estado,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where codpersona=".$this->request->codpersona." and kardex.seriecomprobante='".$this->request->seriecomprobante."' and kardex.nrocomprobante='".$this->request->nrocomprobante."' and (kardex.codmovimientotipo=2 or kardex.codmovimientotipo=20)")->result_array();
			echo json_encode($lista);
		}else{
			$this->load->view("netix/404");
		}
	}

	function detalle($codkardex){
		if ($this->input->is_ajax_request()) {
			$detalle = $this->db->query("select p.descripcion as producto,u.descripcion as unidad, kd.codproducto,kd.codunidad, kd.item, kd.cantidad, kd.recogido, round((kd.cantidad - kd.recogido),2) as pendiente,0 as recoger from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codkardex." and kd.estado=1 and kd.recoger=0 order by kd.item ")->result_array();
			
			$entregados = $this->db->query("select ka.codkardexalmacen,ka.fechakardex, p.descripcion as producto, u.descripcion as unidad, kad.codproducto, kad.codunidad, kad.codalmacen, kad.cantidad from kardex.kardexalmacen as ka inner join kardex.kardexalmacendetalle as kad on(ka.codkardexalmacen=kad.codkardexalmacen) inner join almacen.productos as p on(kad.codproducto=p.codproducto) inner join almacen.unidades as u on(kad.codunidad=u.codunidad) where ka.codkardex=".$codkardex." and ka.codalmacen=".$_SESSION["netix_codalmacen"]." and ka.estado=1 and kad.estado=1 order by ka.codkardexalmacen ")->result_array();
			$data["detalle"] = $detalle;
			$data["entregados"] = $entregados;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();
				
				// REGISTRO KARDEX ALMACEN //
				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$this->request->campos->codcomprobantetipo." and codsucursal=".$_SESSION["netix_codsucursal"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();

				$campos = ["codsucursal","codalmacen","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$this->request->campos->codkardex,
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmovimientotipo,date("Y-m-d"),
					(int)$this->request->campos->codcomprobantetipo,
					$series[0]["seriecomprobante"]
				];
				$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
				$nro_comprobante = $this->Kardex_model->netix_kardexalmacencorrelativo($codkardexalmacen,$this->request->campos->codcomprobantetipo,$series[0]["seriecomprobante"]);

				foreach ($this->request->detalle as $key => $value) {
					if ((double)($this->request->detalle[$key]->recoger)!=0) {
						$factor = $this->db->query("select min(factor) as factor from almacen.productounidades where codproducto=".$this->request->detalle[$key]->codproducto)->result_array();

						$campos = ["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
						$valores =[
							(int)$codkardexalmacen,
							(int)$this->request->detalle[$key]->codproducto,
							(int)$this->request->detalle[$key]->codunidad,
							(int)$this->request->detalle[$key]->item,
							(int)$_SESSION["netix_codalmacen"],
							(int)$_SESSION["netix_codsucursal"],
							(double)$this->request->detalle[$key]->recoger
						];
						$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos,$valores);
						
						// REGISTRAMOS LOS DESCUENTOS DE LAS CANTIDADES POR RECOGER //

						$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad)->result_array();

						if ($this->request->campos->codmovimientotipo==2) {
							$cantidad = (double)$existe[0]["comprarecogo"] - (double)$this->request->detalle[$key]->recoger;
							$campos = ["comprarecogo"]; $valores = [(double)$cantidad];
						}else{
							$cantidad = (double)$existe[0]["ventarecogo"] - (double)$this->request->detalle[$key]->recoger;
							$campos = ["ventarecogo"]; $valores = [(double)$cantidad];
						}
						$f = ["codalmacen","codproducto","codunidad"]; 
						$v = [(int)$_SESSION["netix_codalmacen"],(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad];
						$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);

						// ACTUALIZAMOS EL KARDEX DETALLE //

						$detalle = $this->db->query("select cantidad,recoger,recogido from kardex.kardexdetalle where codkardex=".$this->request->campos->codkardex." and codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad)->result_array();
						$cantidadrecoger = (double)$detalle[0]["recogido"] + (double)$this->request->detalle[$key]->recoger;
						if ($cantidadrecoger==$detalle[0]["cantidad"]) {
							$recoger = 1;
						}else{
							$recoger = 0;
						}

						$campos = ["recoger","recogido"]; $valores = [(int)$recoger,(double)$cantidadrecoger];
						$f = ["codkardex","codproducto","codunidad"]; 
						$v = [(int)$this->request->campos->codkardex,(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad];
						$estado = $this->Netix_model->netix_editar_1("kardex.kardexdetalle", $campos, $valores, $f, $v);

						// ACTUALIZAMOS EL KARDEX //

						$detalle = $this->db->query("select count(*) as cantidad from kardex.kardexdetalle where codkardex=".$this->request->campos->codkardex." and recoger=0 and estado=1")->result_array();
						if ($detalle[0]["cantidad"]==0) {
							$data = array("retirar" => 1);
							$this->db->where("codkardex", $this->request->campos->codkardex);
							$estado = $this->db->update("kardex.kardex",$data);
						}
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
				echo $estado;
			}else{
				echo "e";
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$this->db->trans_begin();

			// ACTUALIZAMOS EL KARDEX ALMACEN DETALLE //
			$campos = ["estado"]; $valores = [0];
			$f = ["codkardexalmacen","codproducto","codunidad","codalmacen"]; 
			$v = [(int)$this->request->codkardexalmacen,(int)$this->request->codproducto,(int)$this->request->codunidad,(int)$this->request->codalmacen];
			$estado = $this->Netix_model->netix_editar_1("kardex.kardexalmacendetalle", $campos, $valores, $f, $v);

			// ACTUALIZAMOS EL KARDEX ALMACEN //
			$kardex = $this->db->query("select codkardex from kardex.kardexalmacen where codkardexalmacen=".$this->request->codkardexalmacen)->result_array();
			$detalle = $this->db->query("select codkardexalmacen from kardex.kardexalmacendetalle where codkardexalmacen=".$this->request->codkardexalmacen." and estado=1")->result_array();
			if (count($detalle)==0) {
				$data = array("estado" => 0);
				$this->db->where("codkardexalmacen", $this->request->codkardexalmacen);
				$estado = $this->db->update("kardex.kardexalmacen",$data);

				// REGISTRO KARDEX ALMACEN ANULADOS //
				$campos = ["codkardexalmacen","codsucursal","codusuario","fechaanulacion","observaciones"];
				$valores =[
					(int)$this->request->codkardexalmacen, (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),"DESPACHO O RECIBO EN ALMACEN ANULADO"
				];
				$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacenanulado", $campos, $valores);
			}

			// ACTUALIZAMOS EL KARDEX DETALLE //
			$detalle = $this->db->query("select cantidad,recoger,recogido from kardex.kardexdetalle where codkardex=".$kardex[0]["codkardex"]." and codproducto=".$this->request->codproducto." and codunidad=".$this->request->codunidad)->result_array();
			$cantidadrecoger = (double)$detalle[0]["recogido"] - (double)$this->request->cantidad;
			
			if ((double)$cantidadrecoger<=0) {
				$cantidadrecoger = 0;
			}

			if ($cantidadrecoger==$detalle[0]["cantidad"]) {
				$recoger = 1;
			}else{
				$recoger = 0;
			}

			$campos = ["recoger","recogido"]; $valores = [(int)$recoger,(double)$cantidadrecoger];
			$f = ["codkardex","codproducto","codunidad"]; 
			$v = [(int)$kardex[0]["codkardex"],(int)$this->request->codproducto,(int)$this->request->codunidad];
			$estado = $this->Netix_model->netix_editar_1("kardex.kardexdetalle", $campos, $valores, $f, $v);

			// ACTUALIZAMOS EL KARDEX //
			$detalle = $this->db->query("select count(*) as cantidad from kardex.kardexdetalle where codkardex=".$kardex[0]["codkardex"]." and recoger=0 and estado=1")->result_array();
			if ($detalle[0]["cantidad"]==0) {
				$retirar = 1;
			}else{
				$retirar = 0;
			}
			$data = array("retirar" => $retirar);
			$this->db->where("codkardex", $kardex[0]["codkardex"]);
			$estado = $this->db->update("kardex.kardex",$data);

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback(); $estado = 0;
			}else{
				if ($estado!=1) {
					$this->db->trans_rollback(); $estado = 0;
				}
				$this->db->trans_commit();
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}