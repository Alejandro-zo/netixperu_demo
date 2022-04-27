<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cuentaspagar extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("creditos/cuentaspagar/index");
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

			foreach ($lista as $key => $value) {
				$cantidad = $this->db->query("select count(*) as cantidad from kardex.creditos where codpersona=".$value["codpersona"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1 and tipo=2")->result_array();
				$lista[$key]["creditos"] = $cantidad[0]["cantidad"];
			}

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

	public function nuevo($codpersona){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$tipopagos = $this->db->query("select *from caja.tipopagos where (egreso=1) and estado=1 order by codtipopago")->result_array();
				$persona = $this->db->query("select codpersona, razonsocial from public.personas where codpersona=".$codpersona)->result_array();
				$responsables = $this->db->query("select *from public.personas where convenio=1 and estado=1")->result_array();
				$this->load->view("creditos/cuentaspagar/nuevo",compact("tipopagos","persona","responsables"));
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

				// REGISTRO MOVIMIENTO CAJA //
				if ($this->request->campos->afectacaja == true) {
					$condicionpago = 1;
				}else{
					$condicionpago = 2;
				}
				
				$comprobante_ingresos = 1;
				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobante_ingresos." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

				$campos = ["codcontroldiario","codcaja","codconcepto","codpersona","codusuario","fechamovimiento","codcomprobantetipo","seriecomprobante","tipomovimiento","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","importe","referencia","condicionpago"];
				$valores = [
					(int)$_SESSION["netix_codcontroldiario"],
					(int)$_SESSION["netix_codcaja"],
					(int)$this->request->campos->codcajaconcepto,
					(int)$this->request->campos->codpersona,
					(int)$_SESSION["netix_codusuario"],
					$this->request->campos->fechacredito,
					(int)$comprobante_ingresos,
					$series[0]["seriecomprobante"],1,0,"","",
					(double)$this->request->campos->importe, "INGRESO POR CREDITO",
					(int)$condicionpago
				];
				$codmovimiento = $this->Netix_model->netix_guardar("caja.movimientos", $campos, $valores, "true");
				$estado = $this->Caja_model->netix_correlativo($codmovimiento,$comprobante_ingresos,$series[0]["seriecomprobante"]);

				if ($this->request->campos->afectacaja == true) {
					$campos = ["codmovimiento","codtipopago","codcontroldiario","codcaja","fechadocbanco","nrodocbanco","importe","importeentregado"];
					$valores = [
						(int)$codmovimiento,
						(int)$this->request->campos->codtipopago,
						(int)$_SESSION["netix_codcontroldiario"],
						(int)$_SESSION["netix_codcaja"],
						$this->request->campos->fechadocbanco,
						$this->request->campos->nrodocbanco,
						(double)$this->request->campos->importe,
						(double)$this->request->campos->importe
					];
					$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos, $valores);
				}

				// REGISTRO DEL CREDITO //

				$campos = ["codsucursal","codcaja","codcreditoconcepto","codpersona","codmovimiento","codusuario","tipo","fechacredito","fechainicio","nrodias","nrocuotas","importe","tasainteres","interes","saldo","total","referencia","codpersona_convenio"];
				$valores = [
					(int)$_SESSION["netix_codsucursal"],
					(int)$_SESSION["netix_codcaja"],
					(int)$this->request->campos->codcreditoconcepto,
					(int)$this->request->campos->codpersona,
					(int)$codmovimiento,
					(int)$_SESSION["netix_codusuario"],2,
					$this->request->campos->fechacredito,
					$this->request->campos->fechainicio,
					(int)$this->request->campos->nrodias,
					(int)$this->request->campos->nrocuotas,
					(double)$this->request->campos->importe,
					(double)$this->request->campos->tasainteres,
					(double)$this->request->campos->interes,
					(double)$this->request->campos->total,
					(double)$this->request->campos->total,
					$this->request->campos->referencia,
					$this->request->campos->codpersona_convenio
				];

				if($this->request->campos->codregistro=="") {
					$codcredito = $this->Netix_model->netix_guardar("kardex.creditos", $campos, $valores, "true");

					foreach ($this->request->cuotas as $key => $value) {
						$campos = ["codcredito","nrocuota","codsucursal","fechavence","importe","saldo","interes","total"];
						$valores = [
							(int)$codcredito,
							(int)$this->request->cuotas[$key]->nrocuota,
							(int)$_SESSION["netix_codsucursal"],
							$this->request->cuotas[$key]->fechavence,
							(double)$this->request->cuotas[$key]->importe,
							(double)$this->request->cuotas[$key]->total,
							(double)$this->request->cuotas[$key]->interes,
							(double)$this->request->cuotas[$key]->total
						];
						$estado = $this->Netix_model->netix_guardar("kardex.cuotas", $campos, $valores);
						$fechavence = $this->request->cuotas[$key]->fechavence;
					}

					$campos = ["fechavencimiento"]; $valores = [$fechavence];
					$estado = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codcredito", $codcredito);
				}else{
					$estado = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codcredito", $this->request->campos->codregistro);
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

	public function cobranza($codpersona){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$tipopagos = $this->db->query("select *from caja.tipopagos where (egreso=1) and estado=1 order by codtipopago")->result_array();
				$persona = $this->db->query("select codpersona, razonsocial from public.personas where codpersona=".$codpersona)->result_array();
				$this->load->view("creditos/cuentaspagar/pagos",compact("tipopagos","persona"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function cuotas($codpersona){
		if ($this->input->is_ajax_request()) {
			$cuotas = $this->db->query("select cuo.codcredito,cuo.nrocuota,cuo.fechavence,cuo.fecha,round(cuo.saldo,2) as saldo,round(cuo.total,2) as total,cre.codkardex from kardex.creditos as cre inner join kardex.cuotas as cuo on(cre.codcredito=cuo.codcredito) where cre.codpersona=".$codpersona." and cre.codsucursal=".$_SESSION["netix_codsucursal"]." and cre.estado=1 and cre.tipo=2 and cuo.estado=1 order by cre.codcredito")->result_array();
			foreach ($cuotas as $key => $value) {
				$comprobante = $this->db->query("select k.seriecomprobante as serie,k.nrocomprobante as nro, ct.abreviatura from kardex.kardex as k inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".(int)$value["codkardex"])->result_array();
				if (count($comprobante)>0) {
					$cuotas[$key]["comprobante"] = $comprobante[0]["abreviatura"]."-".$comprobante[0]["serie"]."-".(int)$comprobante[0]["nro"];
				}else{
					$cuotas[$key]["comprobante"] = "CRED. DIRECTO";
				}

				$total = $this->db->query("select round(COALESCE(sum(importe),0),2) as cobrado from kardex.cuotaspagos where codcredito=".$value["codcredito"]." and nrocuota=".$value["nrocuota"]." and estado=1")->result_array();
				$cuotas[$key]["cobrado"] = $total[0]["cobrado"];
			}
			echo json_encode($cuotas);
		}else{
			$this->load->view("netix/404");
		}
	}

	function pagar(){
		if ($this->input->is_ajax_request()) {
			if (isset( $_SESSION["netix_codusuario"]) ) {
				$this->request = json_decode(file_get_contents('php://input'));

				$this->db->trans_begin();

				$campos = ["codcontroldiario","codcaja","codconcepto","codpersona","codusuario","fechamovimiento","codcomprobantetipo","seriecomprobante","tipomovimiento","importe","referencia","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref"];

				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$this->request->campos->codcomprobantetipo." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

				$valores = [
					(int)$_SESSION["netix_codcontroldiario"],
					(int)$_SESSION["netix_codcaja"],
					(int)$this->request->campos->codconcepto,
					(int)$this->request->campos->codpersona,
					(int)$_SESSION["netix_codusuario"],
					$_SESSION["netix_fechaproceso"],
					(int)$this->request->campos->codcomprobantetipo,
					$series[0]["seriecomprobante"],2,
					(double)$this->request->campos->total,
					$this->request->campos->descripcion,
					18,"REF",$this->request->campos->nrodocbanco
				];
				
				$codmovimiento = $this->Netix_model->netix_guardar("caja.movimientos", $campos, $valores, "true");
				$estado = $this->Caja_model->netix_correlativo($codmovimiento,$this->request->campos->codcomprobantetipo,$series[0]["seriecomprobante"]);

				$campos = ["codmovimiento","codtipopago","codcontroldiario","codcaja","fechadocbanco","nrodocbanco","importe","importeentregado","vuelto"];
				$valores = [
					(int)$codmovimiento,
					(int)$this->request->campos->codtipopago,
					(int)$_SESSION["netix_codcontroldiario"],
					(int)$_SESSION["netix_codcaja"],
					$this->request->campos->fechadocbanco,
					$this->request->campos->nrodocbanco,
					(double)$this->request->campos->total,
					(double)$this->request->campos->importe,
					(double)$this->request->campos->vuelto
				];
				$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos, $valores);

				foreach ($this->request->cuotas as $key => $value) {
					$campos = ["codcredito","nrocuota","codsucursal","codmovimiento","codusuario","importe","saldocuota"];
					$valores =[
						(int)$this->request->cuotas[$key]->codcredito,
						(int)$this->request->cuotas[$key]->nrocuota,
						(int)$_SESSION["netix_codsucursal"],
						(int)$codmovimiento,
						(int)$_SESSION["netix_codusuario"],
						(double)$this->request->cuotas[$key]->cobrar,
						(double)$this->request->cuotas[$key]->saldo
					];
					$estado = $this->Netix_model->netix_guardar("kardex.cuotaspagos", $campos, $valores);

					if ( (double)$this->request->cuotas[$key]->saldo==0 ) {
						$campos = ["saldo","estado"]; $valores = [(double)$this->request->cuotas[$key]->saldo,0];
					}else{
						$campos = ["saldo"]; $valores = [(double)$this->request->cuotas[$key]->saldo];
					}
					$f = ["codcredito","nrocuota"]; 
					$v = [(int)$this->request->cuotas[$key]->codcredito,(int)$this->request->cuotas[$key]->nrocuota];
					$estado = $this->Netix_model->netix_editar_1("kardex.cuotas", $campos, $valores, $f, $v);

					// ACTUALIZAMOS EL CREDITO //

					$cobrado = $this->db->query("select count(*) as cantidad from kardex.cuotas where codcredito=".$this->request->cuotas[$key]->codcredito." and estado=1")->result_array();
					if ($cobrado[0]["cantidad"]==0) {
						$campos = ["saldo","estado"]; $valores = [0,2];
					}else{
						$credito = $this->db->query("select saldo from kardex.creditos where codcredito=".$this->request->cuotas[$key]->codcredito)->result_array();
						$campos = ["saldo"]; $valores = [(double)$credito[0]["saldo"] - (double)$this->request->cuotas[$key]->cobrar];
					}
					$estado = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codcredito", $this->request->cuotas[$key]->codcredito);
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

	function historial($codpersona){
		if ($this->input->is_ajax_request()) {
			$persona = $this->db->query("select codpersona, razonsocial from public.personas where codpersona=".$codpersona)->result_array();
			$this->load->view("creditos/cuentaspagar/historial",compact("persona"));
		}else{
			$this->load->view("netix/404");
		}
	}

	function anularpago(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$cuotas = $this->db->query("select c.codcredito,cp.nrocuota, cp.importe from kardex.creditos as c inner join kardex.cuotaspagos as cp on(c.codcredito=cp.codcredito) where cp.codmovimiento=".$this->request->codmovimiento)->result_array();
			foreach ($cuotas as $key => $value) {
				$info = $this->db->query("select *from kardex.cuotas where codcredito=".$value["codcredito"]." and nrocuota=".$value["nrocuota"])->result_array();
				$saldo = round($info[0]["saldo"] + $value["importe"],2);

				$campos = ["saldo","estado"]; $valores = [(double)$saldo,1];
				$f = ["codcredito","nrocuota"]; 
				$v = [(int)$value["codcredito"],(int)$value["nrocuota"]];
				$estado = $this->Netix_model->netix_editar_1("kardex.cuotas", $campos, $valores, $f, $v);

				// ACTUALIZAMOS EL CREDITO //
				$credito = $this->db->query("select saldo from kardex.creditos where codcredito=".$value["codcredito"])->result_array();
				$campos = ["saldo","estado"]; $valores = [(double)$credito[0]["saldo"] + (double)$value["importe"],1];
				
				$estado = $this->Netix_model->netix_editar("kardex.creditos", $campos, $valores, "codcredito", $value["codcredito"]);
			}

			$campos = ["estado"]; $valores = [0];
			$f = ["codmovimiento"]; $v = [(int)$this->request->codmovimiento];
			$estado = $this->Netix_model->netix_editar_1("kardex.cuotaspagos", $campos, $valores, $f, $v);

			$estado = $this->Netix_model->netix_eliminar("caja.movimientos", "codmovimiento", $this->request->codmovimiento);

			echo $estado;
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$pagos = $this->db->query("select count(*) as cantidad from kardex.cuotaspagos where codcredito=".$this->request->codregistro." and estado=1")->result_array();
			if ($pagos[0]["cantidad"]==0) {
				$estado = $this->Netix_model->netix_eliminar("kardex.creditos", "codcredito", $this->request->codregistro);

				$movimiento = $this->db->query("select codmovimiento, codkardex from kardex.creditos where codcredito=".$this->request->codregistro)->result_array();
				$estado = $this->Netix_model->netix_eliminar("caja.movimientos", "codmovimiento", $movimiento[0]["codmovimiento"]);

				// REGISTRAMOS EL CREDITO ANULADO EN CREDITOS ANULADOS //
				
				$campos = ["codcredito","codsucursal","fechaanulacion","codusuario"];
				$valores =[
					(int)$this->request->codregistro,
					(int)$_SESSION["netix_codsucursal"],date("Y-m-d"),
					(int)$_SESSION["netix_codusuario"]
				];
				$estado = $this->Netix_model->netix_guardar("kardex.creditosanulados", $campos, $valores);

				// Si el credito es por COMPRA //

				if ((int)$movimiento[0]["codkardex"] > 0) {
					$codkardex = $movimiento[0]["codkardex"];

					// ACTUALIZAMOS PRODUCTOS UBICACION //
					$kardexalmacen = $this->db->query("select codkardexalmacen from kardex.kardexalmacen where codkardex=".$codkardex)->result_array();

					$info = $this->db->query("select *from kardex.kardexdetalle where codkardex=".$codkardex)->result_array();
					foreach ($info as $key => $value) {
						$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"])->result_array();
						$stock = $existe[0]["stockactual"] - $value["cantidad"];

						$campos = ["stockactual"]; $valores = [(double)$stock];
						$f = ["codalmacen","codproducto","codunidad"];
						$v = [(int)$_SESSION["netix_codalmacen"],(int)$value["codproducto"],(int)$value["codunidad"]];
						$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
					}
					$estado = $this->Netix_model->netix_eliminar("kardex.kardex", "codkardex", $codkardex);
					$estado = $this->Netix_model->netix_eliminar("kardex.kardexalmacen", "codkardexalmacen", $kardexalmacen[0]["codkardexalmacen"]);

					// REGISTRO KARDEX ANULADOS //
					$campos = ["codkardex","codsucursal","codusuario","fechaanulacion","observaciones"];
					$valores =[(int)$codkardex, (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"), "ANULADO"];
					$estado = $this->Netix_model->netix_guardar("kardex.kardexanulados", $campos, $valores);

					// REGISTRO KARDEX ALMACEN ANULADOS //
					$campos = ["codkardexalmacen","codsucursal","codusuario","fechaanulacion","observaciones"];
					$valores =[
						(int)$kardexalmacen[0]["codkardexalmacen"], (int)$_SESSION["netix_codsucursal"], (int)$_SESSION["netix_codusuario"], date("Y-m-d"), "ANULADO"
					];
					$estado = $this->Netix_model->netix_guardar("kardex.kardexalmacenanulado", $campos, $valores);
				}
			}else{
				$estado = 0;
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
}