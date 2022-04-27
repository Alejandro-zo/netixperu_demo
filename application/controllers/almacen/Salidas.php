<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Salidas extends CI_Controller {

	public function __construct(){
		parent::__construct(); 
		$this->load->model("Netix_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("almacen/salidas/index");
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

			$lista = $this->db->query("select kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.nrocomprobante, kardex.fechakardex,round(kardex.importe,2) as importe,kardex.seriecomprobante_ref,kardex.nrocomprobante_ref, usuarios.usuario,tipos.descripcion as tipomovimiento,comprobantes.descripcion as tipo,kardex.estado from kardex.kardex as kardex inner join seguridad.usuarios as usuarios on (kardex.codusuario=usuarios.codusuario) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo_ref=comprobantes.codcomprobantetipo) inner join almacen.movimientotipos as tipos on(kardex.codmovimientotipo=tipos.codmovimientotipo) where codalmacen=".$_SESSION["netix_codalmacen"]." and (UPPER(usuarios.usuario) like UPPER('%".$this->request->buscar."%') or UPPER(tipos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and tipos.tipo=2 and kardex.codmovimientotipo<>20 order by kardex.codkardex desc offset ".$offset." limit ".$limit)->result_array();
			$total = $this->db->query("select count(*) as total from kardex.kardex as kardex inner join seguridad.usuarios as usuarios on (kardex.codusuario=usuarios.codusuario) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo_ref=comprobantes.codcomprobantetipo) inner join almacen.movimientotipos as tipos on(kardex.codmovimientotipo=tipos.codmovimientotipo) where codalmacen=".$_SESSION["netix_codalmacen"]." and (UPPER(usuarios.usuario) like UPPER('%".$this->request->buscar."%') or UPPER(tipos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and tipos.tipo=2 and kardex.codmovimientotipo<>20")->result_array();

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
				$movimientos = $this->db->query("select *from almacen.movimientotipos where codmovimientotipo<>20 and tipo=2 and estado=1")->result_array();
				$serie = $this->db->query("select ct.abreviatura as comprobante,c.codcomprobantetipo, c.seriecomprobante from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo=ct.codcomprobantetipo) where c.codcomprobantetipo=4 and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codalmacen=".$_SESSION["netix_codalmacen"]." and c.estado=1")->result_array();
				$tipocomprobantes = $this->db->query("select *from caja.comprobantetipos where codcomprobantetipo>=10 and estado=1 order by codcomprobantetipo")->result_array();
				$almacenes = $this->db->query("select almacen.*, sucursal.descripcion as sucursal from almacen.almacenes as almacen inner join public.sucursales as sucursal on(almacen.codsucursal=sucursal.codsucursal) where almacen.codalmacen<>".$_SESSION["netix_codalmacen"]." and almacen.estado=1")->result_array();
				$this->load->view("almacen/salidas/nuevo",compact("movimientos","serie","tipocomprobantes","almacenes"));
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

				$this->load->view("almacen/salidas/ver",compact("info","detalle")); 
			}else{
	            $this->load->view("inicio/505");
	        }
	    }else{
			$this->load->view("inicio/404");
		}
	}
	
	function guardar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				// REGISTRO KARDEX //
				$campos = ["codsucursal","codalmacen","codalmacen_ref","codpersona","codusuario","codmovimientotipo","fechakardex","fechacomprobante","codcomprobantetipo","seriecomprobante","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","valorventa","porcigv","igv","importe","descripcion"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$this->request->campos->codalmacen_ref,1,
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmovimientotipo,
					$this->request->campos->fechakardex,$this->request->campos->fechakardex,
					(int)$this->request->campos->codcomprobantetipo,
					$this->request->campos->seriecomprobante,
					(int)$this->request->campos->codcomprobantetipo_ref,
					$this->request->campos->seriecomprobante_ref,
					$this->request->campos->nrocomprobante_ref,
					(double)$this->request->totales->valorventa,
					(double)$_SESSION["netix_igv"],
					(double)$this->request->totales->igv,
					(double)$this->request->totales->importe,
					$this->request->campos->descripcion
				];
				$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true");

				// REGISTRO KARDEX ALMACEN //
				$campos = ["codsucursal","codalmacen","codalmacen_ref","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$this->request->campos->codalmacen_ref,
					(int)$codkardex,
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmovimientotipo,
					$this->request->campos->fechakardex,
					(int)$this->request->campos->codcomprobantetipo,
					$this->request->campos->seriecomprobante
				];
				$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
				
				$nro_comprobante = $this->Kardex_model->netix_kardexcorrelativo($codkardex,$codkardexalmacen,$this->request->campos->codcomprobantetipo,$this->request->campos->seriecomprobante);

				// REGISTRO KARDEX DETALLE Y KARDEX ALMACEN DETALLE //
				$item = 0;
				foreach ($this->request->detalle as $key => $value) { $item = $item + 1;
					$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","valorventa","subtotal"];
					$valores =[
						(int)$codkardex,
						(int)$this->request->detalle[$key]->codproducto,
						(int)$this->request->detalle[$key]->codunidad, $item,
						(double)$this->request->detalle[$key]->cantidad,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->precio,'10',
						(double)$this->request->detalle[$key]->subtotal,
						(double)$this->request->detalle[$key]->subtotal
					];
					$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

					$campos =["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
					$valores =[
						(int)$codkardexalmacen,
						(int)$this->request->detalle[$key]->codproducto,
						(int)$this->request->detalle[$key]->codunidad, $item,
						(int)$_SESSION["netix_codalmacen"],
						(int)$_SESSION["netix_codsucursal"],
						(double)$this->request->detalle[$key]->cantidad
					];
					$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);

					$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad)->result_array();
					$stock = round($existe[0]["stockactual"] - $this->request->detalle[$key]->cantidad,3);

					$campos = ["stockactual"]; $valores = [(double)$stock];
					$f = ["codalmacen","codproducto","codunidad"]; 
					$v = [(int)$_SESSION["netix_codalmacen"],(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad];
					$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
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

	function editar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));
				$info = $this->db->query("select codkardex,fechacomprobante,fechakardex, seriecomprobante, nrocomprobante, descripcion,codmovimientotipo from kardex.kardex where codkardex=".$this->request->codregistro)->result_array();
				$movimientos = $this->db->query("select *from almacen.movimientotipos where codmovimientotipo<>20 and tipo=2 and estado=1")->result_array();
				$this->load->view("almacen/salidas/editar",compact("info","movimientos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar_guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codmovimientotipo","fechacomprobante","fechakardex","descripcion"];
			$valores = [
				$this->request->codmovimientotipo,
				$this->request->fechacomprobante,
				$this->request->fechakardex,
				$this->request->descripcion
			];
			$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex",$this->request->codregistro);

			$campos = ["fechakardex"]; $valores = [$this->request->fechakardex];
			$estado_u = $this->Netix_model->netix_editar("kardex.kardexalmacen", $campos, $valores, "codkardex",$this->request->codregistro);
			echo $estado;
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$this->db->trans_begin();

			// ACTUALIZAMOS PRODUCTOS UBICACION //
			$kardexalmacen = $this->db->query("select codkardexalmacen from kardex.kardexalmacen where codkardex=".$this->request->codregistro)->result_array();

			$info = $this->db->query("select *from kardex.kardexdetalle where codkardex=".$this->request->codregistro)->result_array();
			foreach ($info as $key => $value) {
				$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"])->result_array();
				$stock = $existe[0]["stockactual"] + $value["cantidad"];

				$campos = ["stockactual"]; $valores = [(double)$stock];
				$f = ["codalmacen","codproducto","codunidad"];
				$v = [(int)$_SESSION["netix_codalmacen"],(int)$value["codproducto"],(int)$value["codunidad"]];
				$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
			}
			$estado = $this->Netix_model->netix_eliminar("kardex.kardex", "codkardex", $this->request->codregistro);
			$estado = $this->Netix_model->netix_eliminar("kardex.kardexalmacen", "codkardexalmacen", $kardexalmacen[0]["codkardexalmacen"]);

			// REGISTRO KARDEX ANULADOS //
			$campos = ["codkardex","codsucursal","codusuario","fechaanulacion","observaciones"];
			$valores =[
				(int)$this->request->codregistro, (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),"SALIDA DE ALMACEN ANULADO"
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexanulados", $campos, $valores);

			// REGISTRO KARDEX ALMACEN ANULADOS //
			$campos = ["codkardexalmacen","codsucursal","codusuario","fechaanulacion","observaciones"];
			$valores =[
				(int)$kardexalmacen[0]["codkardexalmacen"], (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),
				"SALIDA DE ALMACEN ANULADO"
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacenanulado", $campos, $valores);

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

	function guardar_operacionstock(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$this->db->trans_begin();

			// REGISTRAMOS LA SALIDA DEL STOCK //

			$codcomprobantetipo = 4;
			$serie = $this->db->query("select c.seriecomprobante from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo=ct.codcomprobantetipo) where c.codcomprobantetipo=4 and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codalmacen=".$_SESSION["netix_codalmacen"]." and c.estado=1")->result_array();
			$seriecomprobante = $serie[0]["seriecomprobante"];

			$campos = ["codsucursal","codalmacen","codpersona","codusuario","codmovimientotipo","fechakardex","fechacomprobante","codcomprobantetipo","seriecomprobante","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","valorventa","porcigv","igv","importe","descripcion"];
			$subtotal = (double)($this->request->preciocosto) * (double)($this->request->cantidad);
			$valores = [
				(int)$_SESSION["netix_codsucursal"],
				(int)$_SESSION["netix_codalmacen"],1,
				(int)$_SESSION["netix_codusuario"],21,
				$this->request->fechakardex,$this->request->fechakardex,
				$codcomprobantetipo,$seriecomprobante,0,"SIN","00000000",
				(double)round($subtotal,2),
				(double)$_SESSION["netix_igv"],(double)(0),
				(double)round($subtotal,2),
				"SALIDA POR AJUSTES EN VENTA"
			];
			$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true"); $codkardex_ref = $codkardex;

			// REGISTRO KARDEX ALMACEN //
			$campos = ["codsucursal","codalmacen","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
			$valores = [
				(int)$_SESSION["netix_codsucursal"],
				(int)$_SESSION["netix_codalmacen"],
				(int)$codkardex,
				(int)$_SESSION["netix_codusuario"],21,$this->request->fechakardex,
				(int)$codcomprobantetipo,$seriecomprobante
			];
			$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
			
			$nro_comprobante = $this->Kardex_model->netix_kardexcorrelativo($codkardex,$codkardexalmacen,$codcomprobantetipo,$seriecomprobante);

			// REGISTRO EN LOS DETALLES //

			$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","valorventa","subtotal"];
			$valores =[
				(int)$codkardex,
				(int)$this->request->codproducto,
				(int)$this->request->codunidad,1,
				(double)$this->request->cantidad,
				(double)$this->request->preciocosto,
				(double)$this->request->preciocosto,
				(double)$this->request->preciocosto,
				(double)$this->request->preciocosto,20,
				(double)round($subtotal,2),(double)round($subtotal,2)
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

			$campos=["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
			$valores =[
				(int)$codkardexalmacen,
				(int)$this->request->codproducto,
				(int)$this->request->codunidad,1,
				(int)$_SESSION["netix_codalmacen"],
				(int)$_SESSION["netix_codsucursal"],
				(double)$this->request->cantidad
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);

			$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$this->request->codproducto." and codunidad=".$this->request->codunidad)->result_array();
			$stock = round($existe[0]["stockactual"] - $this->request->cantidad,3);

			$campos = ["stockactual"]; $valores = [(double)$stock];
			$f = ["codalmacen","codproducto","codunidad"]; 
			$v = [(int)$_SESSION["netix_codalmacen"],(int)$this->request->codproducto,(int)$this->request->codunidad];
			$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);


			// REGISTRAMOS EL INGRESO DEL STOCK //

			$codcomprobantetipo = 3;
			$serie = $this->db->query("select c.seriecomprobante from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo=ct.codcomprobantetipo) where c.codcomprobantetipo=3 and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codalmacen=".$_SESSION["netix_codalmacen"]." and c.estado=1")->result_array();
			$seriecomprobante = $serie[0]["seriecomprobante"];

			$campos = ["codkardex_ref","codsucursal","codalmacen","codpersona","codusuario","codmovimientotipo","fechakardex","fechacomprobante","codcomprobantetipo","seriecomprobante","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","valorventa","porcigv","igv","importe","descripcion"];

			$infounidad = $this->db->query("select codunidad,preciocosto from almacen.productounidades where codproducto=".$this->request->codproducto." and codunidad=".$this->request->codunidad_ingreso)->result_array();

			$cantidad = round( $this->request->cantidadingreso,3) ;
			$preciocosto = round($subtotal / $cantidad,2);
			$subtotal = (double)($preciocosto) * (double)($cantidad);
			$codunidad = $this->request->codunidad_ingreso;

			$valores = [
				(int)$codkardex_ref,
				(int)$_SESSION["netix_codsucursal"],
				(int)$_SESSION["netix_codalmacen"],1,
				(int)$_SESSION["netix_codusuario"],3,
				$this->request->fechakardex,$this->request->fechakardex,
				$codcomprobantetipo,$seriecomprobante,0,"SIN","00000000",
				(double)round($subtotal,2),
				(double)$_SESSION["netix_igv"],(double)(0),
				(double)round($subtotal,2),
				"INGRESO POR AJUSTES EN VENTA"
			];
			$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true");

			$campos = ["codkardex_ref"]; $valores = [$codkardex];
			$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex", $codkardex_ref);

			// REGISTRO KARDEX ALMACEN //
			$campos = ["codsucursal","codalmacen","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
			$valores = [
				(int)$_SESSION["netix_codsucursal"],
				(int)$_SESSION["netix_codalmacen"],
				(int)$codkardex,
				(int)$_SESSION["netix_codusuario"],3,$this->request->fechakardex,
				(int)$codcomprobantetipo,$seriecomprobante
			];
			$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
			
			$nro_comprobante = $this->Kardex_model->netix_kardexcorrelativo($codkardex,$codkardexalmacen,$codcomprobantetipo,$seriecomprobante);

			// REGISTRO EN LOS DETALLES //

			$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","valorventa","subtotal"];
			$valores =[
				(int)$codkardex,
				(int)$this->request->codproducto,
				(int)$codunidad,1,
				(double)round($cantidad,3),
				(double)round($preciocosto,2),
				(double)round($preciocosto,2),
				(double)round($preciocosto,2),
				(double)round($preciocosto,2),20,
				(double)round($subtotal,2),(double)round($subtotal,2)
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

			$campos=["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
			$valores =[
				(int)$codkardexalmacen,
				(int)$this->request->codproducto,
				(int)$codunidad,1,
				(int)$_SESSION["netix_codalmacen"],
				(int)$_SESSION["netix_codsucursal"],
				(double)$cantidad
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);

			$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$this->request->codproducto." and codunidad=".$codunidad)->result_array();
			
			$stock = round($existe[0]["stockactual"] + $cantidad,3);

			$campos = ["stockactual"]; $valores = [(double)$stock];
			$f = ["codalmacen","codproducto","codunidad"]; 
			$v = [(int)$_SESSION["netix_codalmacen"],(int)$this->request->codproducto,(int)$codunidad];
			$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);

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