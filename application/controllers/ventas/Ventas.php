<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {

				/* CODIGO TEMPORAL DE LA IMPRESION */

				$formato = $this->db->query("select formato from caja.comprobantes where codcomprobantetipo=10")->result_array();
				if (count($formato)==0) {
					$_SESSION["netix_formato"] = "a4";
				}else{
					$_SESSION["netix_formato"] = $formato[0]["formato"];
				}

				/* FIN CODIGO TEMPORAL DE LA IMPRESION */

				$comprobante_almacen = $this->db->query("select count(*) as cantidad from caja.comprobantes where (codcomprobantetipo=3 or codcomprobantetipo=4) and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();
				$almacen = $comprobante_almacen[0]["cantidad"]; $caja = $_SESSION["netix_codcontroldiario"];
				$this->load->view("ventas/ventas/index",compact("almacen","caja"));
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

			if ($this->request->fechas->filtro == 0) {
				$fechas = "";
			}else{
				if ($this->request->fechas->desde==$this->request->fechas->hasta) {
					$fechas = "kardex.fechacomprobante='".$this->request->fechas->desde."' and";
				}else{
					$fechas = "kardex.fechacomprobante>='".$this->request->fechas->desde."' and kardex.fechacomprobante<='".$this->request->fechas->hasta."' and";
				}
			}
			$lista = $this->db->query("select personas.documento,kardex.cliente,kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago, kardex.nrocomprobante, kardex.fechacomprobante,round(kardex.importe,2) as importe,kardex.estado, comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.cliente) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=20 and kardex.codsucursal=".$_SESSION["netix_codsucursal"]." order by kardex.codkardex desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=20 and kardex.codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

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
				$comprobantes = $this->db->query("select distinct(ct.codcomprobantetipo) as codigo, ct.* from caja.comprobantetipos as ct inner join caja.comprobantes as c on(ct.codcomprobantetipo=c.codcomprobantetipo) where c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.codcomprobantetipo>=5 and c.estado=1")->result_array();
				$conceptos = $this->db->query("select *from caja.conceptos where codconcepto=13 or codconcepto=15")->result_array();
				$tipopagos = $this->db->query("select *from caja.tipopagos where ingreso=1 and estado=1 order by codtipopago")->result_array();
				$vendedores = $this->db->query("select persona.codpersona,persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1 and empleado.codcargo=4")->result_array();
				$sucursal = $this->db->query("select coalesce(codcomprobantetipo,12) as codcomprobantetipo, seriecomprobante from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
				$centrocostos = $this->db->query("select *from caja.centrocostos where estado=1")->result_array();
				$responsables = $this->db->query("select *from public.personas where convenio=1 and estado=1")->result_array();
				$this->load->view("ventas/ventas/nuevo",compact("comprobantes","conceptos","tipopagos","vendedores","sucursal","centrocostos","responsables"));
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
				$info = $this->db->query("select kardex.*,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.codkardex=".$codregistro)->result_array();

				$detalle = $this->db->query("select kd.*,p.descripcion as producto,u.descripcion as unidad from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$codregistro." and kd.estado=1 order by kd.item")->result_array();

				$pagos = $this->db->query("select p.descripcion as tipopago, md.importe,md.importeentregado,md.vuelto,md.nrodocbanco from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) inner join caja.tipopagos as p on(md.codtipopago=p.codtipopago) where m.codkardex=".$codregistro." and m.estado=1 order by p.codtipopago")->result_array();
				$this->load->view("ventas/ventas/ver",compact("info","detalle","pagos")); 
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

				if ((int)$this->request->campos->codkardex > 0 || $this->request->campos->codkardex != "") {
					$caja = $this->db->query("select codmovimiento, codcontroldiario from caja.movimientos where codkardex=".$this->request->campos->codkardex." and codcontroldiario=".$_SESSION["netix_codcontroldiario"])->result_array();
					$estado = 1;
					if (count($caja) == 0) {
						$estado = 2;
					}
					$pagos = $this->db->query("select cp.* from kardex.creditos as c inner join kardex.cuotaspagos as cp on(c.codcredito=cp.codcredito) where c.codkardex=".$this->request->campos->codkardex." and cp.estado=1")->result_array();
					if (count($pagos) > 0) {
						$estado = 2;
					}
					
					if ($estado == 2) {
						$data["estado"] = $estado; $data["codkardex"] = 0;
						echo json_encode($data); exit();
					}
				}

				$this->db->trans_begin();

				/* REGISTRO KARDEX Y KARDEXDETALLE */

				$codkardex = $this->Kardex_model->netix_kardex($this->request->campos, $this->request->totales, 0); 
				$codkardexalmacen = 0; $retirar = $this->request->campos->retirar; $estado = 1;
				if ($retirar == true) {
					$codkardexalmacen = $this->Kardex_model->netix_kardexalmacen($codkardex, 4, $this->request->campos);
				}
				$detalle = $this->Kardex_model->netix_kardexdetalle($codkardex, $codkardexalmacen, $this->request->detalle, $retirar, 0);

				/* REGISTRO MOVIMIENTO DE CAJA */

				$codmovimiento = $this->Caja_model->netix_movimientos($codkardex, 1, 1, $this->request->totales->importe, $this->request->campos);
				if ($this->request->campos->condicionpago==1) {
					$estado = $this->Caja_model->netix_movimientosdetalle($codmovimiento, $this->request->pagos);
				}

				/* REGISTRO CREDITO POR COBRAR */

				if ($this->request->campos->condicionpago==2) {
					$estado = $this->Caja_model->netix_credito($codkardex, $codmovimiento, 1, $this->request->campos, $this->request->totales, $this->request->cuotas);
				}

				/* COMPROBANTE ELECTRONICO PARA SUNAT: REGISTRO EN KARDEX SUNAT */

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
						$this->request->campos->fechacomprobante, $xml
					];
					if ((int)$this->request->campos->codkardex == 0) {
						$estado = $this->Netix_model->netix_guardar("sunat.kardexsunat", $campos, $valores);
					}else{
						$estado = $this->Netix_model->netix_editar("sunat.kardexsunat", $campos, $valores, "codkardex", $this->request->campos->codkardex);
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
				echo json_encode("e");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$kardex = $this->db->query("select k.*,  c.nrocuotas  from kardex.kardex k inner join kardex.creditos c on k.codkardex =c.codkardex  where k.codkardex=".$this->request->codregistro)->result_array();
			$data["socio"] =$this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$kardex[0]["codpersona"])->result_array();
			$data["campos"] = $kardex;
            if($kardex[0]["condicionpago"]==2){
                $cuot = $this->db->query("select c.nrocuotas,cu.* from kardex.creditos c inner join kardex.cuotas cu on c.codcredito =cu.codcredito where c.codkardex =".$this->request->codregistro."order by cu.nrocuota asc")->result_array();
                $data["cuot"] = $cuot;
            }

			$detalle = $this->db->query("select kd.codproducto,p.descripcion as producto,kd.codunidad,u.descripcion as unidad, 0 as control, 0 as stock, round(kd.cantidad,3) as cantidad, round(kd.preciobruto,3) as preciobrutosinigv, round(kd.preciobruto, 3) as preciobruto, round(kd.preciosinigv,3) as preciosinigv,round(kd.preciounitario,3) as precio, kd.preciorefunitario, round(kd.porcdescuento,2) as porcdescuento, round(kd.descuento,3) as descuento, kd.codafectacionigv,round(kd.igv) as igv, kd.conicbper, round(kd.icbper,1) as icbper, round(kd.valorventa,3) as valorventa, round(kd.subtotal,3) as subtotal, round(kd.subtotal,3) as subtotal_tem, kd.descripcion, p.calcular from kardex.kardexdetalle as kd inner join almacen.productos as p on(kd.codproducto=p.codproducto) inner join almacen.unidades as u on(kd.codunidad=u.codunidad) where kd.codkardex=".$this->request->codregistro." and kd.estado=1 order by kd.item")->result_array();
			foreach ($detalle as $key => $value) {
				$porcentaje = 1;
				if ($value["codafectacionigv"]=="10") {
					$porcentaje = (1 + $_SESSION["netix_igv"]) / 100;
				}
				$detalle[$key]["preciobrutosinigv"] = round(($value["preciobruto"] / $porcentaje),3);

				$detalle[$key]["cantidad"] = (double)$value["cantidad"];
				$detalle[$key]["preciobruto"] = (double)$value["preciobruto"];
				$detalle[$key]["preciosinigv"] = (double)$value["preciosinigv"];
				$detalle[$key]["precio"] = (double)$value["precio"];
				$detalle[$key]["preciorefunitario"] = (double)$value["preciorefunitario"];

				$detalle[$key]["porcdescuento"] = (double)$value["porcdescuento"];
				$detalle[$key]["descuento"] = (double)$value["descuento"];
				$detalle[$key]["igv"] = (double)$value["igv"];
				$detalle[$key]["conicbper"] = (double)$value["conicbper"];
				$detalle[$key]["icbper"] = (double)$value["icbper"];
				$detalle[$key]["valorventa"] = (double)$value["valorventa"];

				$detalle[$key]["subtotal"] = (double)$value["subtotal"];
				$detalle[$key]["subtotal_tem"] = (double)$value["subtotal_tem"];
			}
			$data["detalle"] = $detalle;
			echo json_encode($data);
		}
	}

	/* function editar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));
				$info = $this->db->query("select kardex.codkardex,kardex.fechacomprobante,kardex.fechakardex, kardex.seriecomprobante, kardex.nrocomprobante,kardex.nroplaca,kardex.cliente,kardex.direccion,kardex.descripcion,personas.codpersona, personas.razonsocial,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where kardex.codkardex=".$this->request->codregistro)->result_array();
				$sunat_existe = $this->db->query("select estado from sunat.kardexsunat where codkardex=".$this->request->codregistro)->result_array();
				if (count($sunat_existe)==0) {
					$sunat = 0;
				}else{
					if ($sunat_existe[0]["estado"]==0) {
						$sunat = 0;
					}else{
						$sunat = 1;
					}
				}
				$this->load->view("ventas/ventas/editar",compact("info","sunat"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	} */

	function editar_guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codpersona","fechacomprobante","fechakardex","cliente","direccion","descripcion","nroplaca"];
			$valores = [
				$this->request->codpersona,
				$this->request->fechacomprobante,
				$this->request->fechakardex,
				$this->request->cliente,
				$this->request->direccion,
				$this->request->descripcion,
				$this->request->nroplaca
			];
			$estado = $this->Netix_model->netix_editar("kardex.kardex", $campos, $valores, "codkardex",$this->request->codregistro);

			$campos = ["fechakardex"]; $valores = [$this->request->fechakardex];
			$estado_u = $this->Netix_model->netix_editar("kardex.kardexalmacen", $campos, $valores, "codkardex",$this->request->codregistro);

			$campos = ["codpersona","fechacredito"]; $valores = [$this->request->codpersona,$this->request->fechacomprobante];
			$estado_u = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codkardex",$this->request->codregistro);
			$campos = ["codpersona","fechamovimiento"]; $valores = [$this->request->codpersona,$this->request->fechacomprobante];
			$estado_u = $this->Netix_model->netix_editar("caja.movimientos", $campos, $valores, "codkardex",$this->request->codregistro);

			echo $estado;
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$this->db->trans_begin();

			// SI EXISTE EN CREDITOS //
			$credito = $this->db->query("select *from kardex.creditos where codkardex=".$this->request->codregistro." and estado<>0")->result_array();
			if (count($credito)>0) {
				
				$pagos = $this->db->query("select count(*) as cantidad from kardex.cuotaspagos where codcredito=".$credito[0]["codcredito"]." and estado=1")->result_array();
				if ($pagos[0]["cantidad"]==0) {
					$estado = $this->Netix_model->netix_eliminar("kardex.creditos", "codcredito", $credito[0]["codcredito"]);

					// REGISTRAMOS EL CREDITO ANULADO EN CREDITOS ANULADOS //
					
					$campos = ["codcredito","codsucursal","fechaanulacion","codusuario","observaciones"];
					$valores =[
						(int)$credito[0]["codcredito"],
						(int)$_SESSION["netix_codsucursal"],date("Y-m-d"),
						(int)$_SESSION["netix_codusuario"],
						$this->request->observaciones
					];
					$estado = $this->Netix_model->netix_guardar("kardex.creditosanulados", $campos, $valores);
				}else{
					$this->db->trans_rollback(); $estado = 2; echo $estado; exit();
				}
			}

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
				(int)$this->request->codregistro, (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),
				$this->request->observaciones
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexanulados", $campos, $valores);

			// REGISTRO KARDEX ALMACEN ANULADOS //
			$campos = ["codkardexalmacen","codsucursal","codusuario","fechaanulacion","observaciones"];
			$valores =[
				(int)$kardexalmacen[0]["codkardexalmacen"], (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),
				$this->request->observaciones
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacenanulado", $campos, $valores);

			// ANULAMOS EL MOVIMIENTO DE CAJA //
			$movi = $this->db->query("select codmovimiento from caja.movimientos where codkardex=".$this->request->codregistro)->result_array();
			$estado = $this->Netix_model->netix_eliminar("caja.movimientos", "codmovimiento", $movi[0]["codmovimiento"]);

			$campos = ["estado"]; $valores = [0];
			$f = ["codmovimiento"]; $v = [(int)$movi[0]["codmovimiento"]];
			$estado = $this->Netix_model->netix_editar_1("caja.movimientosdetalle", $campos, $valores, $f, $v);

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

	function formato($formato){
		if ($this->input->is_ajax_request()) {
			$campos = ["formato"]; $valores = [$formato];
			$f = ["codsucursal","codcomprobantetipo"]; $v = [$_SESSION["netix_codsucursal"],10];
			$estado = $this->Netix_model->netix_editar_1("caja.comprobantes", $campos, $valores, $f, $v);

			echo $formato;
		}
	}
}