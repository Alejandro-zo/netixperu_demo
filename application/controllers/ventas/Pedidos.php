<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos extends CI_Controller {

	public function __construct(){
		parent::__construct(); 
		$this->load->model("Netix_model"); $this->load->model("Caja_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("ventas/pedidos/index");
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

			$lista = $this->db->query("select personas.* from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=1 or socios.codsociotipo=3) and socios.estado=1 order by personas.codpersona desc offset ".$offset." limit ".$limit)->result_array();

			foreach ($lista as $key => $value) {
				$cantidad = $this->db->query("select count(*) as cantidad from kardex.pedidos where codpersona=".$value["codpersona"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();
				$lista[$key]["pedidos"] = $cantidad[0]["cantidad"];
			}

			$total = $this->db->query("select count(*) as total from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=1 or socios.codsociotipo=3) and socios.estado=1")->result_array();

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
	public function nuevo($codpersona){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$persona = $this->db->query("select codpersona, razonsocial from public.personas where codpersona=".$codpersona)->result_array();
				$this->load->view("ventas/pedidos/nuevo",compact("persona"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}
	public function historial($codpersona){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$persona = $this->db->query("select codpersona, razonsocial from public.personas where codpersona=".$codpersona)->result_array();
				$this->load->view("ventas/pedidos/historial",compact("persona"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function filtro_pedidos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->filtro==1) {
				$filtro = " and fechapedido>='".$this->request->fechadesde."' and fechapedido<='".$this->request->fechahasta."' ";
			}else{
				$filtro = "";
			}

			if ($this->request->estado!="") {
				$filtro = $filtro." and estado=".$this->request->estado;
			}

			$pedidos = $this->db->query("select codpedido from kardex.pedidos where codpersona=".$this->request->codpersona." and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();
			foreach ($pedidos as $key => $value) {
				$detalle = $this->db->query("select coalesce(sum(cantidad),0) as cantidad from kardex.pedidosdetalle where codpedido=".$value["codpedido"]." and estado=1")->result_array();
				$atendido = $this->db->query("select coalesce(sum(cantidad),0) as cantidad from restaurante.atendidos where codpedido=".$value["codpedido"])->result_array();
				if ($detalle[0]["cantidad"] == $atendido[0]["cantidad"]) {
					$data = array('estado' => 2);
					$this->db->where("codpedido", $value["codpedido"]);
					$estado = $this->db->update("kardex.pedidos", $data);
				}
			}

			$pedidos = $this->db->query("select codpedido,fechapedido,cliente,direccion, importe, estado from kardex.pedidos where codpersona=".$this->request->codpersona." and codsucursal=".$_SESSION["netix_codsucursal"]." ".$filtro." order by codpedido")->result_array();
			$total = 0;
			foreach ($pedidos as $key => $value) {
				$total = $total + (double)$value["importe"];
			}
			$totales = $this->db->query("select ".number_format($total,2,".","")." as total")->result_array();

			$data["pedidos"] = $pedidos;
			$data["totales"] = $totales;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}
	
	function atender($codpedido){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$info = $this->db->query("select pedido.* from kardex.pedidos as pedido where pedido.codpedido=".$codpedido)->result_array();
				$this->load->view("ventas/pedidos/atender",compact("info"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function ver($codpedido){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])){
				$info = $this->db->query("select pedido.* from kardex.pedidos as pedido where pedido.codpedido=".$codpedido)->result_array();
				$detalle = $this->db->query("select kd.codpedido, kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad, kd.item, round(kd.cantidad) as cantidad, kd.preciounitario, (select round(coalesce(sum(cantidad),0)) from restaurante.atendidos where codpedido=".$codpedido." and kd.codproducto=codproducto and kd.codunidad=codunidad and kd.item=item) as atendido, kd.descripcion from kardex.pedidosdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codpedido=".$codpedido." and kd.estado=1 order by kd.item")->result_array();
				$cantidad = 0; $atendido = 0;
				foreach ($detalle as $key => $value) {
					$detalle[$key]["atender"] = round($value["cantidad"] - $value["atendido"]);
					$detalle[$key]["falta"] = round($value["cantidad"] - $value["atendido"]);
					$cantidad = $cantidad + $value["cantidad"]; $atendido = $atendido + $value["atendido"];
				}
				$totales = $this->db->query("select ".round($cantidad,2)." as cantidad, ".round($atendido,2)." as atendido")->result_array();
				$atendidos = $this->db->query("select kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad, round(kd.cantidad) as cantidad,kd.fecha,to_char(kd.hora,'HH12:MI:SS') as hora from restaurante.atendidos as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codpedido=".$codpedido." and kd.estado=1 order by kd.item")->result_array();
				
				$this->load->view("ventas/pedidos/ver",compact("info","detalle","totales","atendidos")); 
			}else{
	            $this->load->view("netix/505");
	        }
	    }else{
			$this->load->view("netix/404");
		}
	}
	
	function guardar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				$campos = ["codsucursal","codalmacen","codusuario","codpersona","fechapedido","valorventa","porcigv","igv","importe","cliente","direccion","codempleado", "afectastock","afectacaja","descripcion"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codpersona,
					$this->request->campos->fechapedido,
					(double)$this->request->totales->subtotal,
					(double)$_SESSION["netix_igv"],
					(double)$this->request->totales->igv,
					(double)$this->request->totales->importe,
					$this->request->campos->cliente,
					$this->request->campos->direccion,
					$this->request->campos->codempleado,
					(int)$this->request->campos->afectastock,
					(int)$this->request->campos->afectacaja,
					$this->request->campos->descripcion
				];
				$codpedido = $this->Netix_model->netix_guardar("kardex.pedidos", $campos, $valores, "true");

				$item = 0;
				foreach ($this->request->detalle as $key => $value) { 
					$item = $item + 1;

					$campos = ["codpedido","codproducto","codunidad","item","cantidad","preciounitario","valorventa","preciorefunitario","codafectacionigv","subtotal","descripcion"];
					$valores =[
						(int)$codpedido,
						(int)$this->request->detalle[$key]->codproducto,
						(int)$this->request->detalle[$key]->codunidad, $item,
						(double)$this->request->detalle[$key]->cantidad,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->subtotal,
						(double)$this->request->detalle[$key]->preciorefunitario,
						$this->request->detalle[$key]->codafectacionigv,
						(double)$this->request->detalle[$key]->subtotal,
						$this->request->detalle[$key]->descripcion
					];
					$estado = $this->Netix_model->netix_guardar("kardex.pedidosdetalle", $campos, $valores);
				}

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback(); $estado = 0;
				}else{
					if ($estado!=1) {
						$this->db->trans_rollback(); $estado = 0;
					}
					$this->db->trans_commit();
				}
				$data["estado"] = $estado; $data["codpedido"] = $codpedido;
				echo json_encode($data);
			}else{
				echo json_encode("e");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$pedido = $this->db->query("select codpedido from kardex.pedidos where codpedido=".$this->request->codregistro." and estado=2")->result_array();
			if (count($pedido)==0) {
				$estado = $this->Netix_model->netix_eliminar("kardex.pedidos", "codpedido", $this->request->codregistro);
				if ($estado == 1) {
					$mensaje = "PEDIDO ANULADO CORRECTAMENTE";
				}else{
					$mensaje = "OCURRIO UN ERROR AL ANULAR EL PEDIDO";
				}
			}else{
				$estado = 0;
				$mensaje = "EL PEDIDO FUE REGISTRADO EN UNA VENTA NO PUEDE ANULAR";
			}
			
			$data["estado"] = $estado; $data["mensaje"] = $mensaje;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	// NETIX PERU - RESTOBAR //

	function netix_pedido(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$pedido = $this->db->query("select max(codpedido) as codpedido from restaurante.mesaspedido where codmesa=".$this->request->codmesa." and estado=1")->result_array();
			if ($pedido[0]["codpedido"]!="") {
				$estado = 0;
				$info = $this->db->query("select valorventa,descglobal,igv,importe, codempleado, codcomprobante from kardex.pedidos where codpedido=".$pedido[0]["codpedido"])->result_array();

				$detalle = $this->db->query("select kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad,kd.item,round(kd.cantidad) as cantidad, (select stockactual from almacen.productoubicacion where kd.codproducto=codproducto and kd.codunidad=codunidad and codalmacen=".$_SESSION["netix_codalmacen"].") as stock,p.controlstock as control,
					kd.preciounitario as preciobruto, 0 as descuento, 0 as porcdescuento, kd.preciounitario as preciosinigv, 20 as codafectacionigv, 0 as conicbper, 0 as icbper, 0 as igv, kd.valorventa,
					round(kd.preciounitario,3) as precio,kd.preciorefunitario, p.calcular, round(kd.subtotal,3) as subtotal, kd.descripcion, 
					(select round(coalesce(sum(cantidad),0)) from restaurante.atendidos where codpedido=".$pedido[0]["codpedido"]." and kd.codproducto=codproducto and kd.codunidad=codunidad and kd.item=item) as atendido 
					from kardex.pedidosdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codpedido=".$pedido[0]["codpedido"]." and kd.estado=1 order by kd.item")->result_array();
			}else{
				$pedido = $this->db->query("select (coalesce(max(codpedido),0) + 1) as codpedido from kardex.pedidos")->result_array();
				$estado = 1; $info = []; $detalle = [];
			}
			$data["pedidonuevo"] = $estado;
			$data["codpedido"] = $pedido[0]["codpedido"];
			$data["pedido"] = $info;
			$data["detalle"] = $detalle;
			echo json_encode($data);
		}
	}

	function netix_atenciones(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$detalle = $this->db->query("select kd.codpedido, kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad, kd.item, round(kd.cantidad) as cantidad, (select round(coalesce(sum(cantidad),0)) from restaurante.atendidos where codpedido=".$this->request->codpedido." and kd.codproducto=codproducto and kd.codunidad=codunidad and kd.item=item) as atendido, kd.descripcion from kardex.pedidosdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codpedido=".$this->request->codpedido." and kd.estado=1 order by kd.item")->result_array();
			$cantidad = 0; $atendido = 0;
			foreach ($detalle as $key => $value) {
				$detalle[$key]["atender"] = round($value["cantidad"] - $value["atendido"]);
				$detalle[$key]["falta"] = round($value["cantidad"] - $value["atendido"]);
				$cantidad = $cantidad + $value["cantidad"]; $atendido = $atendido + $value["atendido"];
			}
			$totales = $this->db->query("select ".round($cantidad,2)." as cantidad, ".round($atendido,2)." as atendido")->result_array();
			$atendidos = $this->db->query("select kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad, round(kd.cantidad) as cantidad,kd.fecha,to_char(kd.hora,'HH12:MI:SS') as hora from restaurante.atendidos as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codpedido=".$this->request->codpedido." and kd.estado=1 order by kd.item")->result_array();
			
			$data["detalle"] = $detalle;
			$data["totales"] = $totales;
			$data["atendidos"] = $atendidos;
			echo json_encode($data);
		}
	}

	function guardar_pedido(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				$campos = ["codsucursal","codalmacen","codusuario","codpersona","fechapedido","valorventa","porcdescuento","descglobal","descuentos","porcigv","igv","importe","cliente","direccion","codcomprobante","codempleado","tipopedido","codcontroldiario"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codpersona,
					$this->request->campos->fechapedido,
					(double)$this->request->totales->valorventa,
					(double)$this->request->campos->porcdescuento,
					(double)$this->request->totales->descglobal,
					(double)$this->request->totales->descuentos,
					(double)$_SESSION["netix_igv"],
					(double)$this->request->totales->igv,
					(double)$this->request->totales->importe,
					$this->request->campos->cliente,
					$this->request->campos->direccion,
					(int)$this->request->campos->codcomprobante,
					(int)$this->request->campos->codempleado,
					(int)$this->request->campos->tipopedido,
					(int)$_SESSION["netix_codcontroldiario"]
				];

				if((int)$this->request->campos->pedidonuevo==1){
					$codpedido = $this->Netix_model->netix_guardar("kardex.pedidos", $campos, $valores, "true");
				}else{
					$codpedido = $this->request->campos->codpedido;
					$estado = $this->Netix_model->netix_editar("kardex.pedidos", $campos, $valores, "codpedido", $codpedido);

					$campos = ["estado"]; $valores = [0];
					$estado = $this->Netix_model->netix_editar("restaurante.atendidos", $campos, $valores, "codpedido", $codpedido);
					$estado = $this->Netix_model->netix_editar("kardex.pedidosdetalle", $campos, $valores, "codpedido", $codpedido);
				}

				$items = $this->db->query("select coalesce(max(item),0) as item from kardex.pedidosdetalle where codpedido=".$codpedido)->result_array();
				$item = $items[0]["item"]; $suma_total = 0;
				foreach ($this->request->detalle as $key => $value) {
					if ($this->request->detalle[$key]->item==0) {
						$item = $item + 1; $this->request->detalle[$key]->item = $item;
					}
					$codafectacionigv = "20";
					if ((double)$this->request->detalle[$key]->precio==0) {
						$codafectacionigv = "21";
					}

					$suma_total = $suma_total + $this->request->detalle[$key]->subtotal;
					$campos = ["codpedido","codproducto","codunidad","item","cantidad","preciounitario","valorventa","preciorefunitario","codafectacionigv","subtotal","descripcion","estado"];
					$valores =[
						(int)$codpedido,
						(int)$this->request->detalle[$key]->codproducto,
						(int)$this->request->detalle[$key]->codunidad,
						(int)$this->request->detalle[$key]->item,
						(double)$this->request->detalle[$key]->cantidad,
						(double)$this->request->detalle[$key]->precio,
						(double)$this->request->detalle[$key]->subtotal,
						(double)$this->request->detalle[$key]->preciorefunitario,
						$codafectacionigv,
						(double)$this->request->detalle[$key]->subtotal,
						$this->request->detalle[$key]->descripcion,1
					];
					$existe = $this->db->query("select *from kardex.pedidosdetalle where codpedido=".$codpedido." and codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad." and item=".$this->request->detalle[$key]->item)->result_array();
					if (count($existe)==0) {
						$estado = $this->Netix_model->netix_guardar("kardex.pedidosdetalle", $campos, $valores);
					}else{
						$f = ["codpedido","codproducto","codunidad","item"]; 
						$v = [(int)$codpedido,(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad,(int)$this->request->detalle[$key]->item];
						$estado = $this->Netix_model->netix_editar_1("kardex.pedidosdetalle", $campos, $valores, $f, $v);

						$campos = ["estado"]; $valores = [1];
						$f = ["codpedido","codproducto","codunidad","item"]; 
						$v = [(int)$codpedido,(int)$this->request->detalle[$key]->codproducto,(int)$this->request->detalle[$key]->codunidad,(int)$this->request->detalle[$key]->item];
						$estado = $this->Netix_model->netix_editar_1("restaurante.atendidos", $campos, $valores, $f, $v);
					}
				}
				$this->db->where("codpedido", $codpedido); $this->db->where("estado",0); 
				$estado = $this->db->delete("restaurante.atendidos");
				$this->db->where("codpedido", $codpedido); $this->db->where("estado",0); 
				$estado = $this->db->delete("kardex.pedidosdetalle");

				$campos = ["valorventa","importe"]; $valores = [(double)round($suma_total,2),(double)round($suma_total,2)];
				$estado = $this->Netix_model->netix_editar("kardex.pedidos", $campos, $valores, "codpedido", $codpedido);

				if((int)$this->request->campos->pedidonuevo==1){
					$campos = ["codpedido","codmesa","nromesa"];
					$valores = [(int)$codpedido,(int)$this->request->campos->codmesa,$this->request->campos->mesa];
					$estado = $this->Netix_model->netix_guardar("restaurante.mesaspedido", $campos, $valores);
				}
				$campos = ["situacion"]; $valores = [2];
				$estado = $this->Netix_model->netix_editar("restaurante.mesas", $campos, $valores, "codmesa", $this->request->campos->codmesa);

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback(); $estado = 0;
				}else{
					if ($estado!=1) {
						$this->db->trans_rollback(); $estado = 0;
					}
					$this->db->trans_commit();
				}
				$data["estado"] = $estado; $data["codpedido"] = $codpedido;
				echo json_encode($data);
			}else{
				echo json_encode("e");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar_atencion(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codpedido","codproducto","codunidad","item","nro","cantidad"];
			foreach ($this->request->atender as $key => $value) {
				$nroatencion = $this->db->query("select coalesce(max(nro),0) as nro from restaurante.atendidos where codpedido=".$this->request->atender[$key]->codpedido." and codproducto=".$this->request->atender[$key]->codproducto." and codunidad=".$this->request->atender[$key]->codunidad." and item=".$this->request->atender[$key]->item)->result_array();
				
				$nro = $nroatencion[0]["nro"] + 1;
				if ((int)$this->request->atender[$key]->atender > 0) {
					$valores =[
						(int)$this->request->atender[$key]->codpedido,
						(int)$this->request->atender[$key]->codproducto,
						(int)$this->request->atender[$key]->codunidad, 
						(int)$this->request->atender[$key]->item, $nro,
						(double)$this->request->atender[$key]->atender
					];
					$estado = $this->Netix_model->netix_guardar("restaurante.atendidos", $campos, $valores);
				}
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function cobrar_pedido(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				// REGISTRO KARDEX //

				$codkardex = $this->Kardex_model->netix_kardex($this->request->campos, $this->request->totales, 0); 
				$codkardexalmacen = 0; $retirar = $this->request->campos->retirar; $estado = 1;
				if ($retirar == true) {
					$codkardexalmacen = $this->Kardex_model->netix_kardexalmacen($codkardex, 4, $this->request->campos);
				}
				$detalle = $this->Kardex_model->netix_kardexdetalle($codkardex, $codkardexalmacen, $this->request->detalle, $retirar, 0);
				
				// REGISTRO MOVIMIENTO DE CAJA //

				$codmovimiento = $this->Caja_model->netix_movimientos($codkardex, 1, 1, $this->request->totales->importe, $this->request->campos);
				if ($this->request->campos->condicionpago==1) {
					$estado = $this->Caja_model->netix_movimientosdetalle($codmovimiento, $this->request->pagos);
				}

				// REGISTRO CREDITO DE LA VENTA //

				if ($this->request->campos->condicionpago==2) {
					$estado = $this->Caja_model->netix_credito($codkardex, $codmovimiento, 1, $this->request->campos, $this->request->totales, $this->request->cuotas);
				}
				
				// COMPROBANTE ELECTRONICO PARA SUNAT: REGISTRO EN KARDEX SUNAT //

				$codkardex_return = $codkardex;
				if ($this->request->campos->codcomprobantetipo==10 || $this->request->campos->codcomprobantetipo==12) {
					$kardex = $this->db->query("select nrocomprobante from kardex.kardex where codkardex=".$codkardex)->result_array();
					if ($this->request->campos->codcomprobantetipo==10) {
						$xml = $_SESSION["netix_ruc"]."-01-".$this->request->campos->seriecomprobante."-".$kardex[0]["nrocomprobante"];
					}else{
						$xml = $_SESSION["netix_ruc"]."-03-".$this->request->campos->seriecomprobante."-".$kardex[0]["nrocomprobante"];
					}
					$campos = ["codkardex","codsucursal","codusuario","fechacreado","nombre_xml"];
					$valores = [
						(int)$codkardex,(int)$_SESSION["netix_codsucursal"],(int)$_SESSION["netix_codusuario"],
						$this->request->campos->fechacomprobante,$xml
					];
					$estado = $this->Netix_model->netix_guardar("sunat.kardexsunat", $campos, $valores);
				}

				// ACTUALIZAMOS EL PEDIDO Y LAS MESAS //

				$campos = ["codkardex","estado"]; $valores = [$codkardex,0];
				$estado = $this->Netix_model->netix_editar("kardex.pedidos", $campos, $valores, "codpedido",$this->request->campos->codpedido);
				
				$campos = ["estado"]; $valores = [0];
				$estado = $this->Netix_model->netix_editar("restaurante.mesaspedido", $campos, $valores, "codpedido",$this->request->campos->codpedido);

				$campos = ["situacion"]; $valores = [1];
				$estado = $this->Netix_model->netix_editar("restaurante.mesas", $campos, $valores, "codmesa", $this->request->campos->codmesa);


				// KARDEX PEDIDO SALIDAS DE RECETAS //

				$serie = $this->db->query("select ct.abreviatura as comprobante,c.codcomprobantetipo, c.seriecomprobante from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo=ct.codcomprobantetipo) where c.codcomprobantetipo=4 and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codalmacen=".$_SESSION["netix_codalmacen"]." and c.estado=1")->result_array();

				$campos = ["codkardex_ref","codsucursal","codalmacen","codpersona","codusuario","codmovimientotipo","fechacomprobante","fechakardex","codcomprobantetipo","seriecomprobante","codcomprobantetipo_ref"];
				$valores = [
					(int)$codkardex,(int)$_SESSION["netix_codsucursal"],(int)$_SESSION["netix_codalmacen"],1,(int)$_SESSION["netix_codusuario"],
					(int)28,$this->request->campos->fechacomprobante, $this->request->campos->fechakardex,
					(int)$serie[0]["codcomprobantetipo"],$serie[0]["seriecomprobante"],0
				];
				$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true");

				$campos = ["codsucursal","codalmacen","codkardex","codusuario","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codalmacen"], (int)$codkardex, (int)$_SESSION["netix_codusuario"],
					(int)$this->request->campos->codmovimientotipo, $this->request->campos->fechakardex,(int)$serie[0]["codcomprobantetipo"],
					$serie[0]["seriecomprobante"]
				];
				$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores, "true");
				$nro_comprobante = $this->Kardex_model->netix_kardexcorrelativo($codkardex,$codkardexalmacen,(int)$serie[0]["codcomprobantetipo"],$serie[0]["seriecomprobante"]);

				$totalsalida = 0;
				foreach ($this->request->detalle as $key => $value) { 
					$recetas = $this->db->query("select *from restaurante.recetas where codproducto=".$this->request->detalle[$key]->codproducto." and codunidad=".$this->request->detalle[$key]->codunidad." and estado=1")->result_array();
					$item = 0;
					foreach ($recetas as $v) { $item = $item + 1;
						$costo = $this->db->query("select preciocosto from almacen.productounidades where codproducto=".$v["codproducto_receta"]." and codunidad=".$v["codunidad_receta"]." and estado=1")->result_array();

						$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","valorventa","subtotal"];
						$cantidad = round(($this->request->detalle[$key]->cantidad * $v["cantidad"]),3);
						$subtotal = round(($cantidad * $costo[0]["preciocosto"]),2); $totalsalida = $totalsalida + $subtotal;
						$valores = [
							(int)$codkardex, (int)$v["codproducto_receta"], (int)$v["codunidad_receta"], $item, (double)$cantidad,
							(double)$costo[0]["preciocosto"],(double)$costo[0]["preciocosto"],(double)$costo[0]["preciocosto"],
							(double)$costo[0]["preciocosto"],'10',(double)$subtotal,(double)$subtotal
						];
						$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

						$campos =["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
						$valores =[
							(int)$codkardexalmacen,(int)$v["codproducto_receta"], (int)$v["codunidad_receta"], $item,
							(int)$_SESSION["netix_codalmacen"], (int)$_SESSION["netix_codsucursal"], (double)$cantidad
						];
						$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);

						$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$v["codproducto_receta"]." and codunidad=".$v["codunidad_receta"])->result_array();
						$stock = round($existe[0]["stockactual"] - $cantidad,3);

						$campos = ["stockactual"]; $valores = [(double)$stock];
						$f = ["codalmacen","codproducto","codunidad"]; 
						$v = [(int)$_SESSION["netix_codalmacen"],(int)$v["codproducto_receta"], (int)$v["codunidad_receta"]];
						$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
					}
				}
				$campos = ["valorventa","igv","importe"]; $valores = [round($totalsalida,2),18,round($totalsalida,2)];
				$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex", $codkardex);

				if ($this->db->trans_status() === FALSE){
				    $this->db->trans_rollback(); $estado = 0;
				}else{
					if ($estado!=1) {
						$this->db->trans_rollback(); $estado = 0;
					}
					$this->db->trans_commit();
				}
				$data["estado"] = $estado; $data["codkardex"] = $codkardex_return;
				echo json_encode($data);
			}else{
				echo json_encode("e");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function anular_pedido(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$estado = $this->Netix_model->netix_eliminar("kardex.pedidos", "codpedido", $this->request->codregistro);
			
			$campos = ["estado"]; $valores = [0];
			$estado = $this->Netix_model->netix_editar("restaurante.mesaspedido", $campos, $valores, "codpedido", $this->request->codregistro);

			$campos = ["situacion"]; $valores = [1];
			$estado = $this->Netix_model->netix_editar("restaurante.mesas", $campos, $valores, "codmesa", $this->request->codmesa);

			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
	
}