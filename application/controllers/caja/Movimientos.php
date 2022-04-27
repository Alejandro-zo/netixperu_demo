<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Caja_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$caja = $this->Caja_model->netix_estadocaja();
				$transferencias = $this->db->query("select count(*) as cantidad from caja.movimientos where codcaja_ref=".$_SESSION["netix_codcaja"]." and transferido=0 and estado=1")->result_array();
				$this->load->view("caja/movimientos/index",compact("transferencias"));
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

			$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$_SESSION["netix_codcontroldiario"]." and (UPPER(movimientos.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(conceptos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') ) and movimientos.condicionpago=1 and movimientos.estado=1 order by movimientos.codmovimiento desc offset ".$offset." limit ".$limit)->result_array();
			foreach ($lista as $key => $value) {
				if ($value["codkardex"]==0) {
					$creditos = $this->db->query("select codmovimiento from kardex.creditos where codmovimiento=".$value["codmovimiento"])->result_array();
					$pagos = $this->db->query("select codmovimiento from kardex.cuotaspagos where codmovimiento=".$value["codmovimiento"])->result_array();

					if (count($creditos)>0 || count($pagos)>0) {
						$lista[$key]["ver"] = 1;
					}else{
						$lista[$key]["ver"] = 0;
					}
				}else{
					$lista[$key]["ver"] = 1;
				}
			}

			$total = $this->db->query("select count(*) as total from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) where movimientos.codcontroldiario=".$_SESSION["netix_codcontroldiario"]." and (UPPER(movimientos.seriecomprobante) like UPPER('%".$this->request->buscar."%') or UPPER(conceptos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') ) and movimientos.condicionpago=1 and movimientos.estado=1")->result_array();

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
				$tipocomprobantes = $this->db->query("select *from caja.comprobantetipos where codcomprobantetipo>=10 and estado=1 order by codcomprobantetipo")->result_array();
				$cajas = $this->db->query("select caja.codcaja,caja.descripcion,sucursal.descripcion as sucursal from caja.cajas as caja inner join public.sucursales as sucursal on(caja.codsucursal=sucursal.codsucursal) where caja.estado=1 and caja.codcaja<>".$_SESSION["netix_codcaja"])->result_array();
				$this->load->view("caja/movimientos/nuevo",compact("tipocomprobantes","cajas"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function nuevo_1($tipomovimiento,$codkardex){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$tipocomprobantes = $this->db->query("select *from caja.comprobantetipos where codcomprobantetipo>=10 and estado=1 order by codcomprobantetipo")->result_array();
				if ($tipomovimiento==1) {
					$comprobante_caja = $this->db->query("select caja.comprobantetipos.* from caja.comprobantetipos inner join caja.comprobantes on(caja.comprobantetipos.codcomprobantetipo=caja.comprobantes.codcomprobantetipo) where caja.comprobantetipos.codcomprobantetipo=1 and caja.comprobantes.codsucursal=".$_SESSION["netix_codsucursal"]." and caja.comprobantes.codcaja=".$_SESSION["netix_codcaja"]." and caja.comprobantes.estado=1 order by caja.comprobantetipos.codcomprobantetipo")->result_array();
				}else{
					$comprobante_caja = $this->db->query("select caja.comprobantetipos.* from caja.comprobantetipos inner join caja.comprobantes on(caja.comprobantetipos.codcomprobantetipo=caja.comprobantes.codcomprobantetipo) where caja.comprobantetipos.codcomprobantetipo=2 and caja.comprobantes.codsucursal=".$_SESSION["netix_codsucursal"]." and caja.comprobantes.codcaja=".$_SESSION["netix_codcaja"]." and caja.comprobantes.estado=1 order by caja.comprobantetipos.codcomprobantetipo")->result_array();
				}
				$conceptos = $this->db->query("select *from caja.conceptos where tipo=".$tipomovimiento." and estado=1 order by codconcepto")->result_array();

				$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobante_caja[0]["codcomprobantetipo"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

				if ($comprobante_caja[0]["codcomprobantetipo"]==1) {
					$tipopagos = $this->db->query("select *from caja.tipopagos where (ingreso=1 or abono=1) and estado=1 order by codtipopago")->result_array();
				}else{
					$tipopagos = $this->db->query("select *from caja.tipopagos where (egreso=1 or cargo=1) and estado=1 order by codtipopago")->result_array();
				}

				if ($codkardex==0) {
					$productos = [];
				}else{
					$productos = $this->db->query("select codproducto,descripcion from almacen.productos where controlstock=0 and estado=1")->result_array();
				}

				$this->load->view("caja/movimientos/nuevo_1",compact("tipocomprobantes","tipomovimiento","codkardex","comprobante_caja","conceptos","series","tipopagos","productos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function tipomovimiento($tipomovimiento){
		if ($this->input->is_ajax_request()) {
			if ($tipomovimiento==1) {
				$tipocomprobantes = $this->db->query("select caja.comprobantetipos.* from caja.comprobantetipos inner join caja.comprobantes on(caja.comprobantetipos.codcomprobantetipo=caja.comprobantes.codcomprobantetipo) where caja.comprobantetipos.codcomprobantetipo=1 and caja.comprobantes.codsucursal=".$_SESSION["netix_codsucursal"]." and caja.comprobantes.codcaja=".$_SESSION["netix_codcaja"]." and caja.comprobantes.estado=1 order by caja.comprobantetipos.codcomprobantetipo")->result_array();
			}else{
				$tipocomprobantes = $this->db->query("select caja.comprobantetipos.* from caja.comprobantetipos inner join caja.comprobantes on(caja.comprobantetipos.codcomprobantetipo=caja.comprobantes.codcomprobantetipo) where caja.comprobantetipos.codcomprobantetipo=2 and caja.comprobantes.codsucursal=".$_SESSION["netix_codsucursal"]." and caja.comprobantes.codcaja=".$_SESSION["netix_codcaja"]." and caja.comprobantes.estado=1 order by caja.comprobantetipos.codcomprobantetipo")->result_array();
			}
			$conceptos = $this->db->query("select *from caja.conceptos where tipo=".$tipomovimiento." and estado=1 order by codconcepto")->result_array();

			$data = array();
			$data["comprobantes"] = $tipocomprobantes;
			$data["conceptos"] = $conceptos;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function tipopagos($comprobantetipo){
		if ($this->input->is_ajax_request()) {
			if ($comprobantetipo==1) {
				$tipopagos = $this->db->query("select *from caja.tipopagos where (ingreso=1 or abono=1) and estado=1 order by codtipopago")->result_array();
			}else{
				$tipopagos = $this->db->query("select *from caja.tipopagos where (egreso=1 or cargo=1) and estado=1 order by codtipopago")->result_array();
			}

			$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobantetipo." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

			$data = array();
			$data["serie"] = $series[0]["seriecomprobante"];
			$data["tipopagos"] = $tipopagos;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codcontroldiario","codcaja","codconcepto","codpersona","codusuario","fechamovimiento","codcomprobantetipo","seriecomprobante","tipomovimiento","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","importe","referencia","codcaja_ref"];
			$campos_1 = ["codmovimiento","codtipopago","codcontroldiario","codcaja","fechadocbanco","nrodocbanco","importe","importeentregado"];

			$valores = [
				(int)$_SESSION["netix_codcontroldiario"],
				(int)$_SESSION["netix_codcaja"],
				(int)$this->request->codconcepto,
				(int)$this->request->codpersona,
				(int)$_SESSION["netix_codusuario"],
				$_SESSION["netix_fechaproceso"],
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

			if($this->request->codregistro=="") {
				$codmovimiento = $this->Netix_model->netix_guardar("caja.movimientos", $campos, $valores, "true");
				$estado = $this->Caja_model->netix_correlativo($codmovimiento,$this->request->codcomprobantetipo,$this->request->seriecomprobante);

				$valores_1 = [(int)$codmovimiento,(int)$this->request->codtipopago,(int)$_SESSION["netix_codcontroldiario"],(int)$_SESSION["netix_codcaja"],$this->request->fechadocbanco,$this->request->nrodocbanco,(double)$this->request->importe,(double)$this->request->importe];
				$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos_1, $valores_1);
			}else{
				$estado = $this->Netix_model->netix_editar("caja.movimientos", $campos, $valores, "codmovimiento", $this->request->codregistro);

				$valores_1 = [(int)$this->request->codregistro,(int)$this->request->codtipopago,(int)$_SESSION["netix_codcontroldiario"],(int)$_SESSION["netix_codcaja"],$this->request->fechadocbanco,$this->request->nrodocbanco,(double)$this->request->importe,(double)$this->request->importe];
				$estado = $this->Netix_model->netix_editar("caja.movimientosdetalle", $campos_1, $valores_1,"codmovimiento", $this->request->codregistro);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codmovimiento as codregistro,* from caja.movimientos where codmovimiento=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function editarmovimiento(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select mov.codpersona, mov.codcontroldiario, mov.tipomovimiento,mov.codcomprobantetipo,mov.codconcepto, movdetalle.codtipopago, movdetalle.fechadocbanco, movdetalle.nrodocbanco from caja.movimientos as mov inner join caja.movimientosdetalle as movdetalle on(mov.codmovimiento=movdetalle.codmovimiento) where mov.codmovimiento=".$this->request->codregistro)->result_array();
			if ($_SESSION["netix_codcontroldiario"]==$info[0]["codcontroldiario"]) {
				$editar = 1;
			}else{
				$editar = 0;
			}
			$socio =$this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$info[0]["codpersona"])->result_array();

			$data = array();
			$data["info"] = $info;
			$data["socio"] = $socio;
			$data["editar"] = $editar;
			echo json_encode($data);
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("caja.movimientos", "codmovimiento", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function transferencias(){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select movimientos.*, round(movimientos.importe,2) as importe_r, personas.razonsocial,conceptos.descripcion as concepto,cajas.descripcion as caja from caja.movimientos as movimientos inner join public.personas as personas on(movimientos.codpersona=personas.codpersona) inner join caja.conceptos as conceptos on(movimientos.codconcepto=conceptos.codconcepto) inner join caja.cajas as cajas on(movimientos.codcaja=cajas.codcaja) where movimientos.codcaja_ref=".$_SESSION["netix_codcaja"]." and movimientos.transferido=0 and movimientos.estado=1")->result_array();
			foreach ($lista as $key => $value) {
				$tipopago = $this->db->query("select codtipopago,fechadocbanco,nrodocbanco from caja.movimientosdetalle where codmovimiento=".$value["codmovimiento"])->result_array();
				$lista[$key]["codtipopago"] = $tipopago[0]["codtipopago"];
				$lista[$key]["fechadocbanco"] = $tipopago[0]["fechadocbanco"];
				$lista[$key]["nrodocbanco"] = $tipopago[0]["nrodocbanco"];
			}
			echo json_encode($lista);
		}
	}

	function aceptar_transferencia(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$comprobante_ingresos = 1;
			$series = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobante_ingresos." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

			$campos = ["codmovimiento_ref","codcontroldiario","codcaja","codconcepto","codpersona","codusuario","codcomprobantetipo","seriecomprobante","tipomovimiento","codcomprobantetipo_ref","seriecomprobante_ref","nrocomprobante_ref","importe","referencia","codcaja_ref","transferido"];

			$valores = [
				(int)$this->request->campos->codmovimiento,
				(int)$_SESSION["netix_codcontroldiario"],
				(int)$_SESSION["netix_codcaja"],26,
				(int)$this->request->campos->codpersona,
				(int)$_SESSION["netix_codusuario"],
				(int)$comprobante_ingresos,
				$series[0]["seriecomprobante"],1,
				(int)$this->request->campos->codcomprobantetipo,
				$this->request->campos->seriecomprobante,
				$this->request->campos->nrocomprobante,
				(double)$this->request->campos->importe,
				$this->request->referencia,
				(int)$this->request->campos->codcaja,1
			];
			$codmovimiento = $this->Netix_model->netix_guardar("caja.movimientos", $campos, $valores, "true");
			$estado = $this->Caja_model->netix_correlativo($codmovimiento,$comprobante_ingresos,$series[0]["seriecomprobante"]);

			$campos_1 = ["codmovimiento","codtipopago","codcontroldiario","codcaja","fechadocbanco","nrodocbanco","importe","importeentregado"];
			$valores_1 = [
				(int)$codmovimiento,
				(int)$this->request->campos->codtipopago,
				(int)$_SESSION["netix_codcontroldiario"],
				(int)$_SESSION["netix_codcaja"],
				$this->request->campos->fechadocbanco,
				$this->request->campos->nrodocbanco,
				(double)$this->request->campos->importe,
				(double)$this->request->campos->importe
			];
			$estado = $this->Netix_model->netix_guardar("caja.movimientosdetalle", $campos_1, $valores_1);

			$campos = ["transferido"]; $valores = [1];
			$estado = $this->Netix_model->netix_editar("caja.movimientos", $campos, $valores, "codmovimiento", $this->request->campos->codmovimiento);

			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}
	
}