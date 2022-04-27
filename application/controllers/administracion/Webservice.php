<?php header('Access-Control-Allow-Origin: *'); defined('BASEPATH') OR exit('No direct script access allowed');

class Webservice extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}
	
	public function comprobantes(){
		$sucursales = $this->db->query("select codsucursal as suc_cod, descripcion as suc_descripcion, 1 as suc_codempresa, estado as suc_estado from public.sucursales")->result_array(); $item = 0;
		foreach ($sucursales as $key => $value) { 
			$comprobantes = $this->db->query("select distinct(c.seriecomprobante) as com_serie, ct.oficial as com_tipocomp, c.nrocorrelativo as com_nrocom, c.codsucursal as com_sucursal, c.estado as com_estado, 0 as com_generar from caja.comprobantes as c inner join caja.comprobantetipos as ct on(c.codcomprobantetipo = ct.codcomprobantetipo) where (ct.oficial='01' or ct.oficial='03' or ct.oficial='07') and c.codsucursal=".$value["suc_cod"]." group by ct.oficial, c.seriecomprobante, c.nrocorrelativo, c.codsucursal, c.estado order by ct.oficial, c.seriecomprobante")->result_array();
			foreach ($comprobantes as $k => $v) { $item = $item + 1;
				$comprobantes[$k]["com_cod"] = $item;
			}
			$sucursales[$key]["comprobantes"] = $comprobantes;
		}
		echo json_encode($sucursales);
	}

	public function recepcionar($categoria){
		$nubecpe_service = file_get_contents("php://input");
        $request = json_decode($nubecpe_service); $alerta = "-";

        $this->db->trans_begin();

		if ($categoria=="cpeactivos") {
			foreach ($request as $key => $value){
				if ($value->det_tipocomp == "01") {
					$det_tipocomp = 10; $codtipo_movimiento = 20;
				}elseif($value->det_tipocomp == "03"){
					$det_tipocomp = 12; $codtipo_movimiento = 20;
				}else{
					$det_tipocomp = 14; $codtipo_movimiento = 8;
				}
        		$existe = $this->db->query("select seriecomprobante, nrocomprobante from kardex.kardex where codcomprobantetipo='".$det_tipocomp."' and seriecomprobante='".$value->det_seriecom."' and nrocomprobante='".$value->det_nrocom."'")->result_array();

        		if (count($existe)==0) {
        			$correlativo = $this->db->query("select codcomprobantetipo, seriecomprobante, (nrocorrelativo + 1) as correlativo from caja.comprobantes where codcomprobantetipo='".$det_tipocomp."' and seriecomprobante='".$value->det_seriecom."' and codsucursal=".$value->kar_sucursal)->result_array();

        			if( (int)($correlativo[0]["correlativo"]) == (int)($value->det_nrocom)){
	        			$kardex = $this->db->query("select COALESCE(MAX(codkardex)+1,1) as codkardex from kardex.kardex")->result_array();

	        			$persona = $this->db->query("select *from public.personas where documento='".$value->det_numdoc."'")->result_array();
	        			if (count($persona) == 0) {
	        				$det_tipodoc = 4;
	        				if ($value->det_tipodoc == "1") {
	        					$det_tipodoc = 2;
	        				}
	        				$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","codubigeo","estado"];
							$valores = [$det_tipodoc,$value->det_numdoc,$value->det_razsoc,$value->det_razsoc,$value->det_direccion,0,1];
							$codpersona = $this->Netix_model->netix_guardar("public.personas", $campos, $valores, "true");

							$campos = ["codpersona","codsociotipo","usuario","clave"];
							$valores = [$codpersona,3,$value->det_numdoc,$value->det_numdoc];
							$estado = $this->Netix_model->netix_guardar("public.socios", $campos, $valores);
	        			}else{
	        				$codpersona = $persona[0]["codpersona"];
	        			}

	        			$codmoneda = 1; $tipocambio = 1;
	        			if ($value->det_codmoneda == "USD") {
	        				$codmoneda = 2; $tipocambio = 3;
	        			}

	        			$datos_kardex = array(
	        				"codkardex" => $kardex[0]["codkardex"], "codpersona" => (int)$codpersona, 
	        				"codmovimientotipo" => $codtipo_movimiento, "condicionpago" => 1, "codusuario" => 1,
							"codsucursal" => (int)$value->kar_sucursal, "codalmacen" => (int)$value->kar_sucursal,
							"codmoneda" => (int)$codmoneda, "tipocambio" => $tipocambio,
							"fechacomprobante" => $value->det_fechadoc, "fechakardex" => $value->det_fechadoc,
							"codcomprobantetipo" => (int)$det_tipocomp,
							"seriecomprobante" => $value->det_seriecom, "nrocomprobante" => $value->det_nrocom,						
							"valorventa" => (double)$value->det_impvalorimporte, "porcigv" => 18, "igv" => (double)$value->det_impigv,
							"importe" => (double)$value->det_imptotal, "retirar" => 1, "descripcion" => $value->det_glosa,
							"cliente" => $value->det_razsoc, "direccion" => $value->det_direccion, 
							"codcentrocosto" => 0, "afectacaja" => 0
						);
						$estado = $this->db->insert("kardex.kardex", $datos_kardex);
		    			
						$datos = array("nrocorrelativo" => (int)($correlativo[0]["correlativo"]));
						$this->db->where("seriecomprobante", $correlativo[0]["seriecomprobante"]);
						$this->db->where("codcomprobantetipo", $correlativo[0]["codcomprobantetipo"]);
						$this->db->update("caja.comprobantes", $datos);

						if($estado > 0){
							if (count($value->detalle)>0) {
								foreach ($value->detalle as $k => $val) {
									$retirar = 0;
									$producto = $this->db->query("select *from almacen.productos where codproducto=".(int)$val->kad_codproducto)->result_array();

									if (count($producto) > 0) {
										$unidad = $this->db->query("select *from almacen.productoubicacion where codproducto=".$producto[0]["codproducto"])->result_array();

										$datos_detalle = array(
											"codkardex" => (int)$kardex[0]["codkardex"], "item" => $val->kad_item ,
											"codproducto" => (int)$producto[0]["codproducto"], "codunidad" => (int)$unidad[0]["codunidad"],
											"cantidad" => (double)$val->kad_cantidad,
											"preciobruto" => (double)$val->kad_preciosinigv,
											"descuento" => (double)$val->kad_dscto,
											"preciosinigv" => (double)$val->kad_preciosinigv,
											"preciounitario" => (double)$val->kad_precioconigv,
											"preciorefunitario" => (double)$val->kad_precioconigv,
											"codafectacionigv" => $val->kad_tipoafectacion,
											"igv" => (double)$val->kad_impigv,
											"valorventa" => (double)$val->kad_impsubtotal,
											"subtotal" => (double)$val->kad_imptotal,
											"descripcion" => $val->kad_descripcion." ".$val->kad_codunimed,
											"recoger" => (int)$retirar
										);
										$estado = $this->db->insert("kardex.kardexdetalle", $datos_detalle);

										$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=1 and codproducto=".$producto[0]["codproducto"]." and codunidad=".$unidad[0]["codunidad"])->result_array();
										if ($det_tipocomp == 14) {
											$stock = (double)round(($existe[0]["stockactual"] + $val->kad_cantidad),3);
										}else{
											$stock = (double)round(($existe[0]["stockactual"] - $val->kad_cantidad),3);
										}
										$data = array(
											"stockactual" => $stock
										);
										$this->db->where("codalmacen", 1);
										$this->db->where("codproducto", $producto[0]["codproducto"]);
										$this->db->where("codunidad", $unidad[0]["codunidad"]);
										$estado = $this->db->update("almacen.productoubicacion", $data);
									}else{
										$retirar = 1;
									}
								}
							}
						}
					}else{
						$alerta = "NUBECPE: EL COMPROBANTE ".$value->det_tipocomp."-".$value->det_seriecom."-".$correlativo[0]["correlativo"]." AUN FALTA REGISTRAR"; break;
					}
        		}else{
        			$alerta = "NUBECPE: EL COMPROBANTE ".$value->det_tipocomp."-".$value->det_seriecom."-".$value->det_nrocom." YA EXISTE"; break;
        		}
        	}
		}else{
			foreach ($request as $key => $value) {
    			$kardex = $this->db->query("select codkardex,seriecomprobante,nrocomprobante from kardex.kardex where seriecomprobante='".$value->kan_serie."' and nrocomprobante='".$value->kan_correlativo."'")->result_array();

    			if(count($kardex) > 0){
    				$existe = $this->db->query("select codkardex from kardex.kardexanulados where codkardex=".$kardex[0]["codkardex"])->result_array();
    				if(count($existe) == 0){
    					$datos = array(
    						"codkardex" => $kardex[0]["codkardex"], "codusuario" => 1,
    						"codsucursal" => $value->kan_codsucursal,
    						"fechaanulacion" => $value->kan_fechaanulado,
    						"observaciones" => $value->kan_motivo
    					);
    					$estado = $this->db->insert("kardex.kardexanulados", $datos);

    					$info = $this->db->query("select *from kardex.kardexdetalle where codkardex=".$kardex[0]["codkardex"])->result_array();
						foreach ($info as $key => $value) {
							$existe = $this->db->query("select *from almacen.productoubicacion where codalmacen=1 and codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"])->result_array();
							$stock = $existe[0]["stockactual"] + $value["cantidad"];

							$campos = ["stockactual"]; $valores = [(double)$stock];
							$f = ["codalmacen","codproducto","codunidad"];
							$v = [1,(int)$value["codproducto"],(int)$value["codunidad"]];
							$estado = $this->Netix_model->netix_editar_1("almacen.productoubicacion", $campos, $valores, $f, $v);
						}
						$estado = $this->Netix_model->netix_eliminar("kardex.kardex", "codkardex", $kardex[0]["codkardex"]);
    				}
    			}
    		}
		}

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback(); $alerta = "NUBECPE: OCURRIO UN ERROR AL REGISTRAR LOS COMPROBANTES";
		}else{
			$this->db->trans_commit();
		}

		echo $alerta;
	}
}