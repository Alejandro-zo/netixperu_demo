<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Compras extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$comprobante_almacen = $this->db->query("select count(*) as cantidad from caja.comprobantes where (codcomprobantetipo=3 or codcomprobantetipo=4) and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();
				$almacen = $comprobante_almacen[0]["cantidad"]; $caja = $_SESSION["netix_codcontroldiario"];
				$this->load->view("compras/compras/index",compact("almacen","caja"));
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
			$lista = $this->db->query("select personas.documento,personas.razonsocial,personas.nombrecomercial, kardex.codkardex, kardex.codcomprobantetipo, kardex.seriecomprobante,kardex.condicionpago, kardex.nrocomprobante,kardex.codmoneda,kardex.fechacomprobante,round(kardex.importe,2) as importe,kardex.estado,comprobantes.descripcion as tipo from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=2 and kardex.codsucursal=".$_SESSION["netix_codsucursal"]." order by kardex.codkardex desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) inner join caja.comprobantetipos as comprobantes on(kardex.codcomprobantetipo=comprobantes.codcomprobantetipo) where ".$fechas." (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') or UPPER(comprobantes.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(kardex.nrocomprobante) like UPPER('%".$this->request->buscar."%') ) and kardex.codmovimientotipo=2 and kardex.codsucursal=".$_SESSION["netix_codsucursal"])->result_array();

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
				$comprobantes = $this->db->query("select * from caja.comprobantetipos where codcomprobantetipo>6 and estado=1")->result_array();
				$conceptos = $this->db->query("select *from caja.conceptos where codconcepto=13 or codconcepto=15")->result_array();
				$tipopagos = $this->db->query("select *from caja.tipopagos where egreso=1 and estado=1")->result_array();
				$monedas = $this->db->query("select *from caja.monedas where estado=1 order by codmoneda asc")->result_array();
				$centrocostos = $this->db->query("select *from caja.centrocostos where estado=1")->result_array();
				$this->load->view("compras/compras/nuevo",compact("comprobantes","conceptos","tipopagos","monedas","centrocostos"));
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

				$pagos = $this->db->query("select p.descripcion as tipopago, md.importe,md.importeentregado, md.vuelto,md.nrodocbanco from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) inner join caja.tipopagos as p on(md.codtipopago=p.codtipopago) where m.codkardex=".$codregistro." and m.estado=1 order by p.codtipopago")->result_array();

				$otros = $this->db->query("select kardex.importe,personas.razonsocial from kardex.kardex as kardex inner join public.personas as personas on (kardex.codpersona=personas.codpersona) where kardex.codkardex_ref=".$codregistro." and kardex.estado=1")->result_array();

				$this->load->view("compras/compras/ver",compact("info","detalle","pagos","otros")); 
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
						$caja = $this->db->query("select afectacaja from kardex.kardex where codkardex=".$this->request->campos->codkardex)->result_array();
						if ($caja[0]["afectacaja"] == 0) {
							// Registro No afecta Caja
							$estado = 1;
						}else{
							$estado = 2;
						}
					}
					$pagos = $this->db->query("select cp.* from kardex.creditos as c inner join kardex.cuotaspagos as cp on(c.codcredito=cp.codcredito) where c.codkardex=".$this->request->campos->codkardex." and cp.estado=1")->result_array();
					if (count($pagos) > 0) {
						$estado = 2;
					}
					
					if ($estado == 2) {
						echo $estado; exit();
					}
				}

				$this->db->trans_begin();

				/* REGISTRO KARDEX Y KARDEXDETALLE */

				$codkardex = $this->Kardex_model->netix_kardex($this->request->campos, $this->request->totales, 1); 
				$codkardexalmacen = 0; $retirar = $this->request->campos->retirar; $estado = 1;
				if ($retirar == true) {
					$codkardexalmacen = $this->Kardex_model->netix_kardexalmacen($codkardex, 3, $this->request->campos);
				}
				$detalle = $this->Kardex_model->netix_kardexdetalle($codkardex, $codkardexalmacen, $this->request->detalle, $retirar, 1);

				/* REGISTRO MOVIMIENTO DE CAJA */

				if ($this->request->campos->afectacaja==true) {
					if ($this->request->campos->codmoneda != 1) {
						$importe = round($this->request->totales->importe * $this->request->campos->tipocambio,2);
					}else{
						$importe = $this->request->totales->importe;
					}

					$codmovimiento = $this->Caja_model->netix_movimientos($codkardex, 2, 2, $importe, $this->request->campos);
					if ($this->request->campos->condicionpago==1) {
						$campos = ["codmovimiento","codtipopago","codcontroldiario","codcaja","codmoneda","tipocambio","fechadocbanco","nrodocbanco","importe","importeentregado"];
						$valores = [
							(int)$codmovimiento,
							(int)$this->request->pagos->codtipopago,
							(int)$_SESSION["netix_codcontroldiario"],
							(int)$_SESSION["netix_codcaja"],
							(int)$this->request->campos->codmoneda,
							(double)$this->request->campos->tipocambio,
							$this->request->pagos->fechadocbanco,
							$this->request->pagos->nrodocbanco,
							(double)$importe,
							(double)$importe
						];
						$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos, $valores);
					}
				}else{
					$codmovimiento = 0;
				}

				/* REGISTRO CREDITO POR COMPRA */

				if ($this->request->campos->condicionpago==2) {
					if ($this->request->campos->codmoneda!=1) {
						$this->request->totales->importe = round($this->request->totales->importe * $this->request->campos->tipocambio,1);
						$this->request->campos->interes = round($this->request->campos->interes * $this->request->campos->tipocambio,1);
						$this->request->campos->totalcredito = round($this->request->campos->totalcredito * $this->request->campos->tipocambio,1);
					}
					$estado = $this->Caja_model->netix_credito($codkardex, $codmovimiento, 2, $this->request->campos, $this->request->totales, $this->request->cuotas);
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

	function guardar_gasto(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				// REGISTRO KARDEX //
				$campos = ["codkardex_ref","codsucursal","codalmacen","codusuario","codpersona","codmovimientotipo","condicionpago","fechacomprobante","fechakardex","codcomprobantetipo","seriecomprobante","nrocomprobante","valorventa","porcigv","igv","importe","descripcion"];
				$valores = [
					(int)$this->request->codkardex,
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$_SESSION["netix_codusuario"],
					(int)$this->request->codpersona,2,1,
					$this->request->fechadocbanco,$this->request->fechadocbanco,
					(int)$this->request->codcomprobantetipo_ref,
					$this->request->seriecomprobante_ref,
					$this->request->nrocomprobante_ref,
					(double)$this->request->importe,
					(double)$_SESSION["netix_igv"],(double)0,
					(double)$this->request->importe,
					"COMPRA DE UN SERVICIO"
				];
				$codkardex = $this->Netix_model->netix_guardar("kardex.kardex", $campos, $valores, "true");

				// REGISTRO KARDEX ALMACEN //
				$comprobante_almacen = 3;
				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobante_almacen." and codsucursal=".$_SESSION["netix_codsucursal"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();

				$campos = ["codsucursal","codalmacen","codusuario","codkardex","codmovimientotipo","fechakardex","codcomprobantetipo","seriecomprobante"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codalmacen"],
					(int)$_SESSION["netix_codusuario"],
					(int)$codkardex,2,
					$this->request->fechadocbanco,
					(int)$comprobante_almacen,
					$series[0]["seriecomprobante"]
				];
				$codkardexalmacen = $this->Netix_model->netix_guardar("kardex.kardexalmacen", $campos, $valores,"true");

				$nro_kardexalmacen = $this->Kardex_model->netix_kardexcorrelativo($codkardex,$codkardexalmacen,$comprobante_almacen,$series[0]["seriecomprobante"]);
				
				// REGISTRO KARDEX DETALLE Y KARDEX ALMACEN DETALLE //
				$campos = ["codkardex","codproducto","codunidad","item","cantidad","preciobruto","preciosinigv","preciounitario","preciorefunitario","codafectacionigv","valorventa","subtotal"];
				$valores =[
					(int)$codkardex,(int)$this->request->codproducto,18,1,1,
					(double)$this->request->importe,
					(double)$this->request->importe,
					(double)$this->request->importe,
					(double)$this->request->importe,'20',
					(double)$this->request->importe,
					(double)$this->request->importe
				];
				$estado = $this->Netix_model->netix_guardar("kardex.kardexdetalle", $campos, $valores);

				$campos = ["codkardexalmacen","codproducto","codunidad","item","codalmacen","codsucursal","cantidad"];
				$valores =[
					(int)$codkardexalmacen,
					(int)$this->request->codproducto,
					(int)18, 1,
					(int)$_SESSION["netix_codalmacen"],
					(int)$_SESSION["netix_codsucursal"],1
				];
				$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacendetalle", $campos, $valores);

				// REGISTRAMOS EL MOVIMIENTO DE CAJA //
				$campos = ["codcontroldiario","codcaja","codconcepto","codpersona","codusuario","codkardex","codcomprobantetipo","seriecomprobante","tipomovimiento","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","importe","referencia","codcaja_ref"];
				$campos_1 = ["codmovimiento","codtipopago","codcontroldiario","codcaja","fechadocbanco","nrodocbanco","importe","importeentregado"];

				$valores = [
					(int)$_SESSION["netix_codcontroldiario"],
					(int)$_SESSION["netix_codcaja"],
					(int)$this->request->codconcepto,
					(int)$this->request->codpersona,
					(int)$_SESSION["netix_codusuario"],
					(int)$codkardex,
					(int)$this->request->codcomprobantetipo,
					$this->request->seriecomprobante,
					(int)$this->request->tipomovimiento,
					(int)$this->request->codcomprobantetipo_ref,
					$this->request->seriecomprobante_ref,
					$this->request->nrocomprobante_ref,
					(double)$this->request->importe,
					$this->request->referencia,
					(int)$this->request->codcaja_ref
				];
				$codmovimiento = $this->Netix_model->netix_guardar("caja.movimientos", $campos, $valores, "true");
				$estado = $this->Caja_model->netix_correlativo($codmovimiento,$this->request->codcomprobantetipo,$this->request->seriecomprobante);

				$valores_1 = [(int)$codmovimiento,(int)$this->request->codtipopago,(int)$_SESSION["netix_codcontroldiario"],(int)$_SESSION["netix_codcaja"],$this->request->fechadocbanco,$this->request->nrodocbanco,(double)$this->request->importe,(double)$this->request->importe];
				$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos_1, $valores_1);

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

	// EDITAR DE COMPRAS CON TODO DETALLE //
	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$kardex = $this->db->query("select *from kardex.kardex where codkardex=".$this->request->codregistro)->result_array();
			$data["socio"] =$this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$kardex[0]["codpersona"])->result_array();
			$data["campos"] = $kardex;

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
				$this->load->view("compras/compras/editar",compact("info"));
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

			$campos = ["codpersona","fechacomprobante","fechakardex","descripcion"];
			$valores = [
				$this->request->codpersona,
				$this->request->fechacomprobante,
				$this->request->fechakardex,
				$this->request->descripcion
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
					
					$campos = ["codcredito","codsucursal","fechaanulacion","codusuario"];
					$valores =[
						(int)$credito[0]["codcredito"],
						(int)$_SESSION["netix_codsucursal"],date("Y-m-d"),
						(int)$_SESSION["netix_codusuario"]
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
				$stock = $existe[0]["stockactual"] - $value["cantidad"];

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
				(int)$this->request->codregistro, (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"),$this->request->observaciones
			];
			$estado = $this->Netix_model->netix_guardar("kardex.kardexanulados", $campos, $valores);

			// REGISTRO KARDEX ALMACEN ANULADOS //
			$campos = ["codkardexalmacen","codsucursal","codusuario","fechaanulacion","observaciones"];
			$valores =[
				(int)$kardexalmacen[0]["codkardexalmacen"], (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"), $this->request->observaciones
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

	function valorizar_precios($codkardex, $fechakardex){
		if ($this->input->is_ajax_request()) {
			$detalle = $this->db->query("select *from kardex.kardexdetalle where codkardex=".$codkardex)->result_array();
			$estado = 1;
			
			foreach ($detalle as $key => $value) {
				$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$_SESSION["netix_codalmacen"])->result_array();
				$stockactual = $stock[0]["stockactual"] - $value["cantidad"];

				$detalle_anterior = $this->db->query("select detalle.codkardex, detalle.cantidad, tipo.tipo as tipomovimiento from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on(kardex.codkardex=detalle.codkardex) inner join almacen.movimientotipos as tipo on(kardex.codmovimientotipo=tipo.codmovimientotipo) where detalle.codproducto=".$value["codproducto"]." and detalle.codunidad=".$value["codunidad"]." and kardex.fechakardex<='".$fechakardex."' and kardex.codalmacen=".$_SESSION["netix_codalmacen"]." and kardex.codkardex<>".$codkardex." and kardex.estado=1 order by kardex.fechakardex desc, kardex.codkardex desc")->result_array();
				
				// TIPO MOVIMIENTO 1: INGRESO STOCK, 2: SALIDA STOCK //

				$codkardex_inicio = 0; $fechakardex_inicio = date("Y-m-d"); 
				// echo $stockactual."<br>";
				foreach ($detalle_anterior as $v) {
					if ($v["tipomovimiento"]==1) {
						$stockactual = round(($stockactual - $value["cantidad"]),3);
						// echo "resta ".$value["cantidad"]." = ".$stockactual."<br>";
					}else{
						$stockactual = round(($stockactual + $value["cantidad"]),3);
						// echo "aumenta ".$value["cantidad"]." = ".$stockactual."<br>";
					}
					if ($stockactual==0) {
						$codkardex_inicio = $v["codkardex"]; $fechakardex_inicio = $v["fechakardex"];  break;
					}
				}
				 
				$compras_anterior = $this->db->query("select coalesce(sum(detalle.cantidad),0) as cantidad, coalesce(sum((detalle.cantidad * detalle.preciounitario) + detalle.icbper),0) as total from kardex.kardex as kardex inner join kardex.kardexdetalle as detalle on(kardex.codkardex=detalle.codkardex) where detalle.codproducto=".$value["codproducto"]." and detalle.codunidad=".$value["codunidad"]." and kardex.codkardex>".$codkardex_inicio." and kardex.codmovimientotipo=2 and kardex.codalmacen=".$_SESSION["netix_codalmacen"]." and kardex.estado=1")->result_array();
				$suma_anterior = $compras_anterior[0]["total"];
				$suma_actual = ($value["cantidad"] * $value["preciounitario"]) + $value["icbper"];
				$cantidad_anterior = $compras_anterior[0]["cantidad"];
				$cantidad_actual = $value["cantidad"];
				$preciocosto = round( ($suma_anterior + $suma_actual)/($cantidad_anterior + $cantidad_actual) ,3);

				$campos = ["preciocompra","preciocosto"]; $valores = [$preciocosto,$preciocosto];
				$f = ["codproducto","codunidad"]; $v = [(int)$value["codproducto"],(int)$value["codunidad"]];
				$estado = $this->Netix_model->netix_editar_1("almacen.productounidades", $campos, $valores, $f, $v);
			}
			echo $estado;
		}
	}
}