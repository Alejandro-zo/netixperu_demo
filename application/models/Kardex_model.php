<?php

class Kardex_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function netix_kardex($campos, $totales, $operacion = 0){
		$data = array(
			"codsucursal" => (int)$_SESSION["netix_codsucursal"], "codalmacen" => (int)$_SESSION["netix_codalmacen"],
			"codusuario" => (int)$_SESSION["netix_codusuario"],
			"codpersona" => (int)$campos->codpersona,
			"codmovimientotipo" => (int)$campos->codmovimientotipo,
			"condicionpago" => (int)$campos->condicionpago,
			"codmoneda" => (int)$campos->codmoneda, "tipocambio" => (double)$campos->tipocambio,
			"fechacomprobante" => $campos->fechacomprobante, "fechakardex" => $campos->fechakardex,
			"codcomprobantetipo" => (int)$campos->codcomprobantetipo,
			"seriecomprobante" => $campos->seriecomprobante,
			"nrocomprobante" => $campos->nro,
			"valorventa" => (double)$totales->valorventa,
			"porcdescuento" => (double)$campos->porcdescuento,
			"descglobal" => (double)$totales->descglobal,
			"descuentos" => (double)$totales->descuentos,
			"porcigv" => (double)$_SESSION["netix_igv"], "igv" => (double)$totales->igv,
			"porcicbper" => (double)$_SESSION["netix_icbper"], "icbper" => (double)$totales->icbper,
			"importe" => (double)$totales->importe,
			"flete" => (double)$totales->flete, "gastos" => (double)$totales->gastos,
			"retirar" => (int)$campos->retirar,
			"descripcion" => $campos->descripcion,
			"nroplaca" => $campos->nroplaca,
			"cliente" => $campos->cliente,
			"direccion" => $campos->direccion,
			"codempleado" => (int)$campos->codempleado,
			"codcentrocosto" => (int)$campos->codcentrocosto,
			"afectacaja" => (int)$campos->afectacaja
		);
		if ((int)$this->request->campos->codkardex == 0) {
			$estado = $this->db->insert("kardex.kardex", $data);
			$codkardex = $this->db->insert_id("kardex.kardex_codkardex_seq");
		}else{
			$codkardex = (int)$this->request->campos->codkardex;

			$this->db->where("codkardex", $codkardex);
			$estado = $this->db->update("kardex.kardex", $data);
		}

		/* GENERAR CORRELATIVO DEL KARDEX */

		if ($operacion == 0 && (int)$this->request->campos->codkardex == 0) {
			$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$campos->codcomprobantetipo." and seriecomprobante='".$campos->seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

			$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
			$data = array(
				"nrocorrelativo" => $nrocorrelativo
			);
			$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
			$this->db->where("codcomprobantetipo", $campos->codcomprobantetipo);
			$this->db->where("seriecomprobante", $campos->seriecomprobante);
			$estado = $this->db->update("caja.comprobantes", $data);

			$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
			$data = array(
				"nrocomprobante" => $nrocorrelativo
			);
			$this->db->where("codkardex", $codkardex);
			$estado = $this->db->update("kardex.kardex", $data);
		}

		return $codkardex;
	}

	function netix_kardexalmacen($codkardex, $comprobantealmacen, $campos){
		$existe = [];
		if ((int)$codkardex > 0) {
			$existe = $this->db->query("select codkardexalmacen from kardex.kardexalmacen where codkardex=".$codkardex)->result_array();
		}

		$serie = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobantealmacen." and codsucursal=".$_SESSION["netix_codsucursal"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();

		$data = array(
			"codsucursal" => (int)$_SESSION["netix_codsucursal"], "codalmacen" => (int)$_SESSION["netix_codalmacen"],
			"codusuario" => (int)$_SESSION["netix_codusuario"],
			"codkardex" => (int)$codkardex,
			"codmovimientotipo" => (int)$campos->codmovimientotipo,
			"fechakardex" => $campos->fechakardex,
			"codcomprobantetipo" => $comprobantealmacen, "seriecomprobante" => $serie[0]["seriecomprobante"]
		);
		if (count($existe) == 0) {
			$estado = $this->db->insert("kardex.kardexalmacen", $data);
			$codkardexalmacen = $this->db->insert_id("kardex.kardexalmacen_codkardexalmacen_seq");

			/* GENERAR CORRELATIVO DEL KARDEX ALMACEN */

			$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$comprobantealmacen." and seriecomprobante='".$serie[0]["seriecomprobante"]."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

			$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
			$data = array(
				"nrocorrelativo" => $nrocorrelativo
			);
			$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
			$this->db->where("codcomprobantetipo", $comprobantealmacen);
			$this->db->where("seriecomprobante", $serie[0]["seriecomprobante"]);
			$estado = $this->db->update("caja.comprobantes", $data);

			$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
			$data = array(
				"nrocomprobante" => $nrocorrelativo
			);
			$this->db->where("codkardexalmacen", $codkardexalmacen);
			$estado = $this->db->update("kardex.kardexalmacen", $data);
		}else{
			$codkardexalmacen = $existe[0]["codkardexalmacen"];

			$this->db->where("codkardexalmacen", $codkardexalmacen);
			$estado = $this->db->update("kardex.kardexalmacen", $data);
		}

		return $codkardexalmacen;
	}

	function netix_kardexdetalle($codkardex, $codkardexalmacen, $detalle, $retirar, $operacion = 0){
		$detalle_registrado = $this->db->query("select *from kardex.kardexdetalle where codkardex=".$codkardex)->result_array();
		foreach ($detalle_registrado as $key => $value) {
			$cantidad_recoger = 0;
			if ($value["recoger"]==0) {
				$cantidad_recoger = (double)$value["cantidad"];
			}

			$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"])->result_array();
			
			if ($operacion == 0) {
				$data = array(
					"stockactual" => (double)round(($existe[0]["stockactual"] + $value["cantidad"]),3), 
					"ventarecogo" => (double)$existe[0]["ventarecogo"] - (double)$cantidad_recoger
				);
			}else{
				$data = array(
					"stockactual" => (double)round(($existe[0]["stockactual"] - $value["cantidad"]),3), 
					"comprarecogo" => (double)$existe[0]["comprarecogo"] - (double)$cantidad_recoger
				);
			}
			$this->db->where("codalmacen", $_SESSION["netix_codalmacen"]);
			$this->db->where("codproducto", $value["codproducto"]);
			$this->db->where("codunidad", $value["codunidad"]);
			$estado = $this->db->update("almacen.productoubicacion", $data);
		}
		$this->db->where("codkardex", $codkardex);
		$estado = $this->db->delete("kardex.kardexdetalle");

		$this->db->where("codkardexalmacen", $codkardexalmacen);
		$estado = $this->db->delete("kardex.kardexalmacendetalle");


		$item = 0; $estado = 1;
		foreach ($detalle as $key => $value) { $item = $item + 1;
			$data = array(
				"codkardex" => (int)$codkardex, 
				"codproducto" => (int)$detalle[$key]->codproducto, "codunidad" => (int)$detalle[$key]->codunidad, "item" => $item,
				"cantidad" => (double)$detalle[$key]->cantidad,
				"preciobruto" => (double)$detalle[$key]->preciobruto,
				"porcdescuento" => (double)$detalle[$key]->porcdescuento,
				"descuento" => (double)$detalle[$key]->descuento,
				"preciosinigv" => (double)$detalle[$key]->preciosinigv,
				"preciounitario" => (double)$detalle[$key]->precio,
				"preciorefunitario" => (double)$detalle[$key]->preciorefunitario,
				"codafectacionigv" => $detalle[$key]->codafectacionigv,
				"igv" => (double)$detalle[$key]->igv,
				"conicbper" => (double)$detalle[$key]->conicbper,
				"icbper" => (double)$detalle[$key]->icbper,
				"valorventa" => (double)$detalle[$key]->valorventa,
				"subtotal" => (double)$detalle[$key]->subtotal,
				"descripcion" => $detalle[$key]->descripcion,
				"recoger" => (int)$retirar
			);
			$estado = $this->db->insert("kardex.kardexdetalle", $data);

			$cantidad_recoger = 0;
			if ($retirar==true) {
				$data = array(
					"codkardexalmacen" => (int)$codkardexalmacen, 
					"codproducto" => (int)$detalle[$key]->codproducto, "codunidad" => (int)$detalle[$key]->codunidad, "item" => $item,
					"codalmacen" => (int)$_SESSION["netix_codalmacen"], "codsucursal" => (int)$_SESSION["netix_codsucursal"],
					"cantidad" => (double)$detalle[$key]->cantidad
				);
				$estado = $this->db->insert("kardex.kardexalmacendetalle", $data);
			}else{
				$cantidad_recoger = (double)$detalle[$key]->cantidad;
			}

			$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$detalle[$key]->codproducto." and codunidad=".$detalle[$key]->codunidad)->result_array();
			if (count($existe) == 0) {
				$data = array(
					"codalmacen" => (int)$_SESSION["netix_codalmacen"], 
					"codproducto" => (int)$detalle[$key]->codproducto,
					"codunidad" => (int)$detalle[$key]->codunidad, 
					"codsucursal" => (int)$_SESSION["netix_codsucursal"],
					"stockactual" => 0, "stockactualreal" => 0
				);
				$estado = $this->db->insert("almacen.productoubicacion", $data);
				
				$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$detalle[$key]->codproducto." and codunidad=".$detalle[$key]->codunidad)->result_array();
			}

			if ($operacion == 0) {
				$data = array(
					"stockactual" => (double)round(($existe[0]["stockactual"] - $detalle[$key]->cantidad),3), 
					"ventarecogo" => (double)$existe[0]["ventarecogo"] + (double)$cantidad_recoger
				);
			}else{
				$data = array(
					"stockactual" => (double)round(($existe[0]["stockactual"] + $detalle[$key]->cantidad),3), 
					"comprarecogo" => (double)$existe[0]["comprarecogo"] + (double)$cantidad_recoger
				);
			}
			$this->db->where("codalmacen", $_SESSION["netix_codalmacen"]);
			$this->db->where("codproducto", $detalle[$key]->codproducto);
			$this->db->where("codunidad", $detalle[$key]->codunidad);
			$estado = $this->db->update("almacen.productoubicacion", $data);
		}
		return $estado;
	}

	function netix_kardexcorrelativo($codkardex,$codkardexalmacen, $codcomprobantetipo, $seriecomprobante){
		$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and seriecomprobante='".$seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

		$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
		$data = array(
			"nrocorrelativo" => $nrocorrelativo
		);
		$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
		$this->db->where("codcomprobantetipo", $codcomprobantetipo);
		$this->db->where("seriecomprobante", $seriecomprobante);
		$estado = $this->db->update("caja.comprobantes", $data);

		// ACTUALIZAMOS EL NRO COMPROBANTE DE KARDEX //
		
		$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
		$data = array(
			"nrocomprobante" => $nrocorrelativo
		);
		$this->db->where("codkardex", $codkardex);
		$estado = $this->db->update("kardex.kardex", $data);

		$this->db->where("codkardexalmacen", $codkardexalmacen);
		$estado = $this->db->update("kardex.kardexalmacen", $data);

		return $estado;
	}

	function netix_kardexalmacencorrelativo($codkardexalmacen, $codcomprobantetipo, $seriecomprobante){
		$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and seriecomprobante='".$seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

		$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
		$data = array(
			"nrocorrelativo" => $nrocorrelativo
		);
		$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
		$this->db->where("codcomprobantetipo", $codcomprobantetipo);
		$this->db->where("seriecomprobante", $seriecomprobante);
		$estado = $this->db->update("caja.comprobantes", $data);

		// ACTUALIZAMOS EL NRO COMPROBANTE DE KARDEX //
		
		$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
		$data = array(
			"nrocomprobante" => $nrocorrelativo
		);
		$this->db->where("codkardexalmacen", $codkardexalmacen);
		$estado = $this->db->update("kardex.kardexalmacen", $data);

		return $estado;
	}
}