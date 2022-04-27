<?php defined('BASEPATH') OR exit('No direct script access allowed');
include("Sunat.php");

class Consultar extends Sunat {

	public function __construct(){
		parent::__construct(); 
		$this->load->model("Netix_model"); $this->load->model("Facturacion_model"); $this->load->model("Pdf_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$this->load->view("facturacion/facturacion/index");
		}else{
			$this->load->view("netix/404");
		}
	}

	function descarga($tipo,$codkardex,$hash){
		$comprobante = $this->db->select("codcomprobantetipo,seriecomprobante,nrocomprobante,fechacomprobante,importe")->get_where("kardex.kardex",array("codkardex" => (int)$codkardex))->result_array();

		if (count($comprobante)>0) {
			$qrcode = $comprobante[0]["codcomprobantetipo"].$comprobante[0]["seriecomprobante"].$comprobante[0]["nrocomprobante"].$comprobante[0]["fechacomprobante"].number_format($comprobante[0]["importe"],2);
			$hashlocal = hash("sha512",$qrcode);

			if ($hash==$hashlocal) {
				if ($tipo=="pdf") {
					$empresa = $this->db->query("select *from public.personas where codpersona=1")->result_array();
					$comprobante = $this->db->query("select codpersona,codcomprobantetipo,seriecomprobante,nrocomprobante, fechacomprobante,igv,importe from kardex.kardex where codkardex=".$codkardex)->result_array();
					$cliente = $this->db->query("select p.documento,dt.oficial from public.personas as p inner join public.documentotipos as dt on (p.coddocumentotipo=dt.coddocumentotipo) where p.codpersona=".$comprobante[0]["codpersona"])->result_array();

					$textoqr = $empresa[0]["documento"]."|".$comprobante[0]["codcomprobantetipo"]."|".$comprobante[0]["seriecomprobante"]."|".$comprobante[0]["nrocomprobante"]."|".number_format($comprobante[0]["igv"],2)."|".number_format($comprobante[0]["importe"],2)."|".$comprobante[0]["fechacomprobante"]."|".$cliente[0]["oficial"]."|".$cliente[0]["documento"]."|0.00|";
					$qrcode = Sunat::netix_qrcode($textoqr);

					if ($comprobante[0]["codcomprobantetipo"]==10) {
						$tipocomprobante = "FACTURA ELECTRONICA";
					}elseif ($comprobante[0]["codcomprobantetipo"]==12) {
						$tipocomprobante = "BOLETA ELECTRONICA";
					}elseif ($comprobante[0]["codcomprobantetipo"]==13) {
						$tipocomprobante = "NOTA DE CREDITO";
					}else{
						$tipocomprobante = "NOTA DE DEBITO";
					}
		            
					$this->Pdf_model->pdf_comprobante($tipocomprobante,$codkardex);
				}
				if ($tipo=="xml") {
					$comprobante = $this->db->query("select codcomprobantetipo from kardex.kardex where codkardex=".$codkardex)->result_array();
					if ($comprobante[0]["codcomprobantetipo"]==10) {
						$codcomprobante = "01";
					}else{
						$codcomprobante = "03";
					}
					$estado = $this->Facturacion_model->netix_crearXML($codcomprobante,$codkardex);
					if ($estado["estado"]!=0) {
						$firma = Sunat::netix_firmarXML($estado["carpeta_netix"]."/".$estado["archivo_netix"], 0);
						
						/* $carpeta_cpe = "./sunat/webnetix";
						foreach(glob($carpeta_cpe . "/*") as $archivos_carpeta){          
			                unlink($archivos_carpeta);
			            }
			            rmdir($carpeta_cpe); */

						$this->load->helper("download"); 
						$cpe_ruta = file_get_contents($estado["carpeta_netix"]."/".$estado["archivo_netix"].".xml");
						force_download($estado["archivo_netix"].".xml", $cpe_ruta);
					}
				}
			}
		}
	}
}