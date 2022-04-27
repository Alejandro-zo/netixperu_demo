<?php

class Creditos_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function socios_creditos($fecha_desde,$fecha_hasta,$tipo){
		$lista = $this->db->query("select p.codpersona,p.razonsocial,p.documento,p.direccion,p.telefono from kardex.creditos c inner join personas p on (c.codpersona=p.codpersona) where c.fechacredito between '".$fecha_desde."' and '".$fecha_hasta."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.estado>=1 and c.tipo=".(int)$tipo." group by p.codpersona order by p.razonsocial asc")->result_array();
		return $lista;
	}

	function estado_cuenta_anterior($fecha_desde,$tipo,$codpersona){
		$lista = $this->db->query("select * from (select c.codcredito as movimiento, 0 as orden, c.fechacredito as fecha, 0.00 as abono, round(c.total,2) as cargo,(select COALESCE(k.seriecomprobante || '-' || k.nrocomprobante,'') from kardex.kardex as k where c.codkardex=k.codkardex) as comprobante, (select COALESCE(string_agg(p.descripcion::text || ' || CANT: ' || round(kd.cantidad,2)::text || ' || P.U: ' || round(kd.preciounitario,2)::text,',') ,c.referencia) from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) where c.codkardex=k.codkardex) as referencia  from kardex.creditos as c where c.fechacredito < '".$fecha_desde."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona." and c.estado>=1
			UNION 
			select distinct(m.codmovimiento) as movimiento,1 as orden, m.fechamovimiento as fecha, round(m.importe,2) as abono, 0.00 as cargo, m.seriecomprobante || '-' || m.nrocomprobante as comprobante,m.referencia from caja.movimientos as m inner join kardex.cuotaspagos as cp on(m.codmovimiento=cp.codmovimiento) inner join kardex.creditos as c on(c.codcredito=cp.codcredito) where m.fechamovimiento < '".$fecha_desde."' and c.tipo=".(int)$tipo." and m.codpersona=".$codpersona." and m.estado=1) as operaciones order by fecha,orden")->result_array();
		$saldo = 0; $cargo = 0; $abono = 0;
		foreach ($lista as $k => $v) {
			$saldo = $saldo + $v["cargo"] - $v["abono"];
			$cargo = $cargo + $v["cargo"];
			$abono = $abono + $v["abono"];
		}
		$anterior = array();
		$anterior["cargo"] = (double)($cargo);
		$anterior["abono"] = (double)($abono);
		$anterior["saldo"] = (double)($saldo);

		return $anterior;
	}

	function estado_cuenta_cliente($fecha_desde,$fecha_hasta,$tipo,$codpersona){
		$lista = $this->db->query("select * from (select c.codcredito as movimiento, 0 as orden, c.fechacredito as fecha, 0.00 as abono, round(c.total,2) as cargo,(select COALESCE(k.seriecomprobante || '-' || k.nrocomprobante,'') from kardex.kardex as k where c.codkardex=k.codkardex) as comprobante, (select COALESCE(string_agg(p.descripcion::text || ' || CANT: ' || round(kd.cantidad,2)::text || ' || P.U: ' || round(kd.preciounitario,2)::text,',') ,c.referencia) from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) where c.codkardex=k.codkardex) as referencia  from kardex.creditos as c where c.fechacredito between '".$fecha_desde."' and '".$fecha_hasta."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona." and c.estado>=1
			UNION 
			select distinct(m.codmovimiento) as movimiento,1 as orden, m.fechamovimiento as fecha, round(m.importe,2) as abono, 0.00 as cargo, m.seriecomprobante || '-' || m.nrocomprobante as comprobante,m.referencia from caja.movimientos as m inner join kardex.cuotaspagos as cp on(m.codmovimiento=cp.codmovimiento) inner join kardex.creditos as c on(c.codcredito=cp.codcredito) where m.fechamovimiento between '".$fecha_desde."' and '".$fecha_hasta."' and c.tipo=".(int)$tipo." and m.codpersona=".$codpersona." and m.estado=1) as operaciones order by fecha,orden")->result_array();
		return $lista;
	}

	function estado_cuenta_creditos($fecha_desde,$fecha_hasta,$tipo,$codpersona){
		$lista = $this->db->query("select c.fechacredito as fecha, round(c.importe,2) as importe,round(c.interes) as interes, round(c.total,2) as total, round(c.total - c.saldo,2) as cobranza, round(c.saldo) as saldo, (select COALESCE(k.seriecomprobante || '-' || k.nrocomprobante,'') from kardex.kardex as k where c.codkardex=k.codkardex) as comprobante, (select COALESCE(string_agg(p.descripcion::text || ' || CANT: ' || round(kd.cantidad,2)::text || ' || P.U: ' || round(kd.preciounitario,2)::text,',') ,c.referencia) from kardex.kardex as k inner join kardex.kardexdetalle as kd on(k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) where c.codkardex=k.codkardex) as referencia  from kardex.creditos as c where c.fechacredito between '".$fecha_desde."' and '".$fecha_hasta."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona." and c.estado>=1")->result_array();
		return $lista;
	}

	function estado_cuenta_detallado($fecha_desde,$fecha_hasta,$tipo,$codpersona){
		$lista = $this->db->query("select * from (select 0 as movimiento, c.fechacredito as fecha, c.referencia, 1 as cantidad, round(c.total,2) as preciounitario, 0.00 as abono, round(c.total,2) as cargo from kardex.creditos as c where c.fechacredito between '".$fecha_desde."' and '".$fecha_hasta."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona." and c.estado>=1 and (c.codkardex=0 or c.codkardex is null) 
			UNION 
			select 0 as movimiento, c.fechacredito as fecha, p.descripcion as referencia, round(kd.cantidad,2) as cantidad,round(kd.preciounitario,2) as preciounitario, 0.00 as abono,round(kd.subtotal,2) as cargo from kardex.creditos as c inner join kardex.kardex as k on(c.codkardex=k.codkardex) inner join kardex.kardexdetalle as kd on (k.codkardex=kd.codkardex) inner join almacen.productos as p on(kd.codproducto=p.codproducto) where c.fechacredito between '".$fecha_desde."' and '".$fecha_hasta."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona." and c.estado>=1 
			UNION 
			select distinct(m.codmovimiento) as movimiento, m.fechamovimiento as fecha, m.referencia, 1 as cantidad, round(m.importe,2) as preciounitario, round(m.importe,2) as abono, 0.00 as cargo from caja.movimientos as m inner join kardex.cuotaspagos as cp on(m.codmovimiento=cp.codmovimiento) inner join kardex.creditos as c on(c.codcredito=cp.codcredito) where m.fechamovimiento between '".$fecha_desde."' and '".$fecha_hasta."' and c.tipo=".(int)$tipo." and m.codpersona=".$codpersona." and m.estado=1) as operaciones order by fecha,movimiento")->result_array();
		return $lista;
	}

	function socios_saldos($fecha_saldos,$tipo){
		$lista = $this->db->query("select p.codpersona,p.razonsocial,p.documento,p.direccion,p.telefono from kardex.creditos c inner join personas p on (c.codpersona=p.codpersona) where c.fechacredito<='".$fecha_saldos."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.estado > 0 and c.saldo > 0 and c.tipo=".(int)$tipo." group by p.codpersona order by p.razonsocial asc")->result_array();
		return $lista;
	}

	function netix_saldos($fecha_saldos,$tipo,$codpersona){
		$lista = $this->db->query("select c.codcredito, m.codmovimiento, c.fechacredito,c.fechavencimiento, m.seriecomprobante_ref, m.nrocomprobante_ref,c.referencia as referencia,round(c.importe,2) as importe, round(c.interes,2) as interes, round(c.total,2) as total, round(c.saldo,2) as saldo from kardex.creditos c inner join caja.movimientos m on(c.codmovimiento=m.codmovimiento) where c.fechacredito<='".$fecha_saldos."' and c.codsucursal=".$_SESSION["netix_codsucursal"]." and c.estado > 0 and c.saldo > 0 and c.tipo=".(int)$tipo." and c.codpersona=".$codpersona)->result_array();
		return $lista;
	}
}