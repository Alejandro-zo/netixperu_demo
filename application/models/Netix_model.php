<?php

class Netix_model extends CI_Model {

	public function __construct(){
		parent::__construct();
	}

	function netix_login($usuario,$clave){
		$usuario = stripslashes($usuario);
    	$array = array("'", "=", "/", "\"", "<", ">", "|", "&", "*");
    	$usuario = str_replace($array, "", $usuario );

    	$clave = stripslashes($clave);
    	$array = array("'", "=", "/", "\"", "<", ">", "|", "&", "*");
    	$clave = str_replace($array, "", $clave );

		$existe = $this->db->query("select *from seguridad.usuarios where usuario='".$usuario."' and clave='".$clave."' and estado=1")->result_array();

		if (count($existe)>0) {
			$empleado = $this->db->query("select *from public.personas where codpersona=".$existe[0]["codempleado"])->result_array();

			$empresa = $this->db->query("select personas.documento, personas.nombrecomercial, personas.foto, empresas.igvsunat,empresas.icbpersunat,empresas.rubro,itemrepetircomprobante from empresas as empresas inner join public.personas as personas on (empresas.codpersona=personas.codpersona) where empresas.codempresa=1")->result_array();

			$_SESSION["netix_codusuario"] = $existe[0]["codusuario"];
            $_SESSION["netix_usuario"] = $existe[0]["usuario"];
            $_SESSION["netix_codperfil"] = $existe[0]["codperfil"];

            $_SESSION["netix_foto"] = $empleado[0]["foto"];
            $_SESSION["netix_empleado"] = $empleado[0]["razonsocial"];
            $_SESSION["netix_ruc"] = $empresa[0]["documento"];
            $_SESSION["netix_empresa"] = $empresa[0]["nombrecomercial"];
			$_SESSION["netix_igv"] = $empresa[0]["igvsunat"];
			$_SESSION["netix_icbper"] = $empresa[0]["icbpersunat"];
			$_SESSION["netix_itemrepetir"] = $empresa[0]["itemrepetircomprobante"];
			$_SESSION["netix_rubro"] = $empresa[0]["rubro"];

			$logo = "default.png";
			if ($empresa[0]["foto"]!="") {
				$logo = $empresa[0]["foto"];
			}
			$_SESSION["netix_logo"] = "empresa/".$logo;

			$estado = 1;
		}else{
			$estado = 0;
		}
		return $estado;
	}

	function netix_web($sucursal, $almacen, $caja){
		$info = $this->db->query("select *from public.sucursales where codsucursal=".$sucursal)->result_array();
		$_SESSION["netix_codempresa"] = $info[0]["codempresa"];
		$_SESSION["netix_codsucursal"] = $info[0]["codsucursal"];
		$_SESSION["netix_sucursal"] = $info[0]["descripcion"];

		$info = $this->db->query("select *from almacen.almacenes where codalmacen=".$almacen)->result_array();
		$_SESSION["netix_codalmacen"] = $info[0]["codalmacen"];
		$_SESSION["netix_almacen"] = $info[0]["descripcion"];
		$_SESSION["netix_stockalmacen"] = $info[0]["controlstock"];

        $info = $this->db->query("select *from caja.cajas where codcaja=".$caja)->result_array();
		$_SESSION["netix_codcaja"] = $info[0]["codcaja"];
		$_SESSION["netix_caja"] = $info[0]["descripcion"];

		// Verficiar el estado de la caja //
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

		return 1;
	}

	function netix_modulos(){
		$modulos = $this->db->query("select *from seguridad.modulos where codpadre=0 and estado=1 order by orden asc")->result_array();
        foreach ($modulos as $key => $value) {
            $modulos[$key]["submodulos"] = $this->db->query("select seguridad.modulos.* from seguridad.modulos inner join seguridad.moduloperfiles on(seguridad.modulos.codmodulo=seguridad.moduloperfiles.codmodulo) where seguridad.moduloperfiles.codperfil=".$_SESSION["netix_codperfil"]." and seguridad.modulos.codpadre=".$value["codmodulo"]." and seguridad.modulos.estado=1 order by seguridad.modulos.orden asc")->result_array();
        }
        return $modulos;
	}

	function netix_guardar($tabla, $campos, $valores, $return_id="false"){
		for($i = 0 ; $i < count($campos); $i++) {
			$data[$campos[$i]] = $valores[$i];
		}
		$estado = $this->db->insert($tabla, $data);
		
		if ($return_id=="true") {
			$estado = $this->db->insert_id();
		}
		return $estado;
	}

	public function netix_editar($tabla, $campos, $valores, $codregistro, $valor){
		for($i = 0 ; $i < count($campos); $i++) {
			$data[$campos[$i]] = $valores[$i];
		}
		$this->db->where($codregistro, $valor);
		$estado = $this->db->update($tabla, $data);
		return $estado;
	}

	public function netix_editar_1($tabla, $campos, $valores1, $filtro, $valores2){
		for($i = 0 ; $i < count($campos); $i++) {
			$data[$campos[$i]] = $valores1[$i];
		}
		for($i = 0 ; $i < count($filtro); $i++) {
			$this->db->where($filtro[$i], $valores2[$i]);
		}
		$estado = $this->db->update($tabla, $data);
		return $estado;
	}

	public function netix_eliminar($tabla, $codregistro, $valor){
		$data = array( "estado" => 0 );
		$this->db->where($codregistro, $valor);
		$estado = $this->db->update($tabla, $data);
		return $estado;
	}
}