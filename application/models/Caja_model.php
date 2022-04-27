<?php

class Caja_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function netix_movimientos($codkardex, $comprobantecaja, $tipomovimiento, $importe, $campos){
		$existe = [];
		if ((int)$codkardex > 0) {
			$existe = $this->db->query("select codmovimiento from caja.movimientos where codkardex=".$codkardex)->result_array();
		}

		$kardex = $this->db->query("select nrocomprobante from kardex.kardex where codkardex=".$codkardex)->result_array(); $nrocomprobante = "";
		if (count($kardex) > 0) {
			$nrocomprobante = $kardex[0]["nrocomprobante"];
		}
		$descripcion = "INGRESO POR VENTA";
		if ($tipomovimiento == 2) {
			$descripcion = "EGRESO POR COMPRA";
		}
		$serie = $this->db->query("select seriecomprobante from caja.comprobantes where codcomprobantetipo=".$comprobantecaja." and codsucursal=".$_SESSION["netix_codsucursal"]." and codcaja=".$_SESSION["netix_codcaja"]." and estado=1")->result_array();

		$data = array(
			"codcontroldiario" => (int)$_SESSION["netix_codcontroldiario"],
			"codcaja" => (int)$_SESSION["netix_codcaja"],
			"codusuario" => (int)$_SESSION["netix_codusuario"],
			"codconcepto" => $campos->codconcepto,
			"codpersona" => $campos->codpersona,
			"codcomprobantetipo" => $comprobantecaja,
			"seriecomprobante" => $serie[0]["seriecomprobante"],
			"tipomovimiento" => $tipomovimiento,
			"codkardex" => $codkardex,
			"fechamovimiento" => $campos->fechacomprobante,
			"codcomprobantetipo_ref" => $campos->codcomprobantetipo,
			"seriecomprobante_ref" => $campos->seriecomprobante,
			"nrocomprobante_ref" => $nrocomprobante,
			"importe" => (double)$importe,
			"referencia" => $descripcion,
			"condicionpago" => $campos->condicionpago
		);

		if (count($existe) == 0) {
			$estado = $this->db->insert("caja.movimientos", $data);
			$codmovimiento = $this->db->insert_id("caja.movimientos_codmovimiento_seq");

			/* GENERAR CORRELATIVO DEL MOVIMIENTO DE CAJA */

			$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$comprobantecaja." and seriecomprobante='".$serie[0]["seriecomprobante"]."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

			$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
			$data = array(
				"nrocorrelativo" => $nrocorrelativo
			);
			$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
			$this->db->where("codcomprobantetipo", $comprobantecaja);
			$this->db->where("seriecomprobante", $serie[0]["seriecomprobante"]);
			$estado = $this->db->update("caja.comprobantes", $data);

			$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
			$data = array(
				"nrocomprobante" => $nrocorrelativo
			);
			$this->db->where("codmovimiento", $codmovimiento);
			$estado = $this->db->update("caja.movimientos", $data);
		}else{
			$codmovimiento = $existe[0]["codmovimiento"];

			$this->db->where("codmovimiento", $codmovimiento);
			$estado = $this->db->update("caja.movimientos", $data);

			$this->db->where("codmovimiento", $codmovimiento);
			$estado = $this->db->delete("caja.movimientosdetalle");
		}

		return $codmovimiento;
	}

	function netix_movimientosdetalle($codmovimiento, $pagos){
		$estado = 1;
		if ((double)$pagos->monto_efectivo > 0) {
			$data = array(
				"codmovimiento" => (int)$codmovimiento,
				"codtipopago" => (int)$pagos->codtipopago_efectivo,
				"codcontroldiario" => (int)$_SESSION["netix_codcontroldiario"],
				"codcaja" => (int)$_SESSION["netix_codcaja"],
				"fechadocbanco" => date("Y-m-d"),
				"importe" => round( ((double)$pagos->monto_efectivo - (double)$pagos->vuelto_efectivo),2),
				"importeentregado" => (double)$pagos->monto_efectivo,
				"vuelto" => (double)$pagos->vuelto_efectivo
			);
			$estado = $this->db->insert("caja.movimientosdetalle", $data);
		}
		if ((int)$pagos->codtipopago_tarjeta > 0) {
			$data = array(
				"codmovimiento" => (int)$codmovimiento,
				"codtipopago" => (int)$pagos->codtipopago_tarjeta,
				"codcontroldiario" => (int)$_SESSION["netix_codcontroldiario"],
				"codcaja" => (int)$_SESSION["netix_codcaja"],
				"fechadocbanco" => date("Y-m-d"),
				"nrodocbanco" => $pagos->nrovoucher,
				"importe" => (double)$pagos->monto_tarjeta,
				"importeentregado" => (double)$pagos->monto_tarjeta
			);
			$estado = $this->db->insert("caja.movimientosdetalle", $data);
		}
		return $estado;
	}

	function netix_credito($codkardex, $codmovimiento, $tipocredito, $campos, $totales, $cuotas){
		$existe = [];
		if ((int)$codkardex > 0) {
			$existe = $this->db->query("select codcredito from kardex.creditos where codkardex=".$codkardex)->result_array();
		}

		$data = array(
			"codsucursal" => (int)$_SESSION["netix_codsucursal"],
			"codcaja" => (int)$_SESSION["netix_codcaja"],
			"codusuario" => (int)$_SESSION["netix_codusuario"],
			"codcreditoconcepto" => (int)$campos->codcreditoconcepto,
			"codpersona" => (int)$campos->codpersona,
			"codmoneda" => (int)$campos->codmoneda, "tipocambio" => (double)$campos->tipocambio,
			"codmovimiento" => (int)$codmovimiento,
			"codkardex" => (int)$codkardex,
			"tipo" => (int)$tipocredito,
			"fechacredito" => $campos->fechacomprobante,
			"fechainicio" => $campos->fechacomprobante,
			"nrodias" => (int)$campos->nrodias,
			"nrocuotas" => (int)$campos->nrocuotas,
			"importe" => (double)$totales->importe,
			"tasainteres" => (double)$campos->tasainteres,
			"interes" => (double)$campos->interes,
			"saldo" => (double)$campos->totalcredito,
			"total" => (double)$campos->totalcredito,
			"codpersona_convenio" => (int)$campos->codpersona_convenio
		);

		if (count($existe) == 0) {
			$estado = $this->db->insert("kardex.creditos", $data);
			$codcredito = $this->db->insert_id("kardex.creditos_codcredito_seq");
		}else{
			$codcredito = $existe[0]["codcredito"];

			$this->db->where("codcredito", $codcredito);
			$estado = $this->db->update("kardex.creditos", $data);

			$this->db->where("codcredito", $codcredito);
			$estado = $this->db->delete("kardex.cuotas");
		}
		
		foreach ($cuotas as $key => $value) {
			$importe = (double)$cuotas[$key]->importe;
			$interes = (double)$cuotas[$key]->interes;
			$total = (double)$cuotas[$key]->total;
			if ($campos->codmoneda!=1) {
				$importe = round((double)$cuotas[$key]->importe * $campos->tipocambio,1);
				$interes = round($cuotas[$key]->interes * $campos->tipocambio,1);
				$total = round($cuotas[$key]->total * $campos->tipocambio,1);
			}
			$data = array(
				"codcredito" => (int)$codcredito,
				"nrocuota" => (int)$cuotas[$key]->nrocuota,
				"codsucursal" => (int)$_SESSION["netix_codsucursal"],
				"fechavence" => $cuotas[$key]->fechavence,
				"importe" => (double)$importe,
				"saldo" => (double)$total,
				"interes" => (double)$interes,
				"total" => (double)$total
			);
			$estado = $this->db->insert("kardex.cuotas", $data);
			$fechavence = $cuotas[$key]->fechavence;
		}
		$data = array(
			"fechavencimiento" => $fechavence,
		);
		$this->db->where("codcredito", $codcredito);
		$estado = $this->db->update("kardex.creditos", $data);

		return $estado;
	}

	function netix_estadocaja(){
		$caja = $this->db->query("select *from caja.controldiario where codcaja=".$_SESSION["netix_codcaja"]." and codsucursal=".$_SESSION["netix_codsucursal"]." and cerrado=1 and estado=1")->result_array();
		if (count($caja)>0) {
			$_SESSION["netix_codcontroldiario"] = $caja[0]["codcontroldiario"];
			$_SESSION["netix_fechaproceso"] = $caja[0]["fechaapertura"];
		}else{
			$_SESSION["netix_codcontroldiario"] = 0; $_SESSION["netix_fechaproceso"] = date("Y-m-d");
		}
		$empresa = $this->db->query("select *from public.empresas")->result_array();
		if ($empresa[0]["fechaoperaciones"] == 0) {
			$_SESSION["netix_fechaproceso"] = date("Y-m-d");
		}
		
		return $caja;
	}

	function netix_correlativo($codmovimiento, $codcomprobantetipo, $seriecomprobante){
		$actual = $this->db->query("select nrocorrelativo from caja.comprobantes where codcomprobantetipo=".$codcomprobantetipo." and seriecomprobante='".$seriecomprobante."' and codsucursal=".$_SESSION["netix_codsucursal"]." and estado=1")->result_array();

		$nrocorrelativo = (int)($actual[0]["nrocorrelativo"]) + 1;
		$data = array(
			"nrocorrelativo" => $nrocorrelativo
		);
		$this->db->where("codsucursal", $_SESSION["netix_codsucursal"]);
		$this->db->where("codcomprobantetipo", $codcomprobantetipo);
		$this->db->where("seriecomprobante", $seriecomprobante);
		$estado = $this->db->update("caja.comprobantes", $data);

		// ACTUALIZAMOS EL NRO COMPROBANTE DE MOVIMIENTOS //
		$nrocorrelativo = str_pad($nrocorrelativo, 8, "0", STR_PAD_LEFT);
		$data = array(
			"nrocomprobante" => $nrocorrelativo
		);
		$this->db->where("codmovimiento", $codmovimiento);
		$estado = $this->db->update("caja.movimientos", $data);
		return $estado;
	}

	function netix_saldotipopago($codcontroldiario,$codtipopago){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and md.codtipopago=".$codtipopago." and m.estado=1")->result_array();

		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and md.codtipopago=".$codtipopago." and m.estado=1")->result_array();

		$transacciones = $this->db->query("select count(md.*) as transacciones from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where md.codcontroldiario=".$codcontroldiario." and md.codtipopago=".$codtipopago." and m.estado=1")->result_array();

		$total = array();
		$total["ingresos"] = (double)($ingresos[0]["importe"]);
		$total["egresos"] = (double)($egresos[0]["importe"]);
		$total["transacciones"] = (int)($transacciones[0]["transacciones"]);
		
		return $total;
	}

	function netix_saldotipopago_general($codcaja,$codtipopago){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=1 and md.codtipopago=".$codtipopago." and m.estado=1")->result_array();

		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=2 and md.codtipopago=".$codtipopago." and m.estado=1")->result_array();

		$total = array();
		$total["ingresos"] = (double)($ingresos[0]["importe"]);
		$total["egresos"] = (double)($egresos[0]["importe"]);
		
		return $total;
	}

	function netix_saldocomprobantes($codcontroldiario,$codcomprobantetipo){
		$ingresos = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codcontroldiario=".$codcontroldiario." and codcomprobantetipo_ref=".$codcomprobantetipo." and tipomovimiento=1 and estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(importe),0),2) as importe from caja.movimientos where codcontroldiario=".$codcontroldiario." and codcomprobantetipo_ref=".$codcomprobantetipo." and tipomovimiento=2 and estado=1")->result_array();

		$total = array();
		$total["ingresos"] = (double)($ingresos[0]["importe"]);
		$total["egresos"] = (double)($egresos[0]["importe"]);
		
		return $total;
	}

	function netix_saldocaja($codcontroldiario){
		$saldoinicial = $this->db->query("select saldoinicialcaja from caja.controldiario where codcontroldiario=".$codcontroldiario)->result_array();
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

		$saldo = array();
		if (count($saldoinicial)==0) {
			$saldo["saldoinicial"] = 0.00;
		}else{
			$saldo["saldoinicial"] = (double)($saldoinicial[0]["saldoinicialcaja"]);
		}
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);

		return $saldo;
	}

	function netix_saldobanco($codcontroldiario){
		$saldoinicial = $this->db->query("select saldoinicialbanco from caja.controldiario where codcontroldiario=".$codcontroldiario)->result_array();
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago<>1 and md.codtipopago<>2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago<>1 and md.codtipopago<>3) and m.estado=1")->result_array();

		$saldo = array();
		if (count($saldoinicial)==0) {
			$saldo["saldoinicial"] = 0.00;
		}else{
			$saldo["saldoinicial"] = (double)($saldoinicial[0]["saldoinicialbanco"]);
		}
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);
		
		return $saldo;
	}

	function netix_saldocaja_general($codcaja){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

		$saldo = array();
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);
		
		return $saldo;
	}

	function netix_saldobanco_general($codcaja){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=1 and (md.codtipopago<>1 and md.codtipopago<>2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcaja=".$codcaja." and m.tipomovimiento=2 and (md.codtipopago<>1 and md.codtipopago<>3) and m.estado=1")->result_array();

		$saldo = array();
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);
		
		return $saldo;
	}

	function netix_saldocaja_diario($codcontroldiario){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago=1 or md.codtipopago=2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago=1 or md.codtipopago=3) and m.estado=1")->result_array();

		$saldo = array();
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);
		
		return $saldo;
	}

	function netix_saldobanco_diario($codcontroldiario){
		$ingresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=1 and (md.codtipopago<>1 and md.codtipopago<>2) and m.estado=1")->result_array();
		$egresos = $this->db->query("select round(COALESCE(sum(md.importe),0),2) as importe from caja.movimientos as m inner join caja.movimientosdetalle as md on(m.codmovimiento=md.codmovimiento) where m.codcontroldiario=".$codcontroldiario." and m.tipomovimiento=2 and (md.codtipopago<>1 and md.codtipopago<>3) and m.estado=1")->result_array();

		$saldo = array();
		$saldo["ingresos"] = (double)($ingresos[0]["importe"]);
		$saldo["egresos"] = (double)($egresos[0]["importe"]);
		$saldo["total"] = (double)($ingresos[0]["importe"] - $egresos[0]["importe"]);
		
		return $saldo;
	}
}