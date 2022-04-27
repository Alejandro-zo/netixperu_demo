<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH."/third_party/netix_facturacion/xmlseclibs.php";
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class Sunat extends CI_Controller {

	function netix_firmarXML($carpeta_netix,$netix){

        // 1: CARGAMOS EL ARCHIVO XML A FIRMAR //
        $doc = new DOMDocument();
        $doc->load($carpeta_netix.".xml");
        
        $objDSig = new XMLSecurityDSig();
        $objDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);
        $objDSig->addReference($doc,XMLSecurityDSig::SHA1,array("http://www.w3.org/2000/09/xmldsig#enveloped-signature"),array("force_uri" => true));

        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array("type" => "private"));
        $objKey->loadKey("./sunat/certificados/private_key.pem", true);
        $objDSig->sign($objKey);

        $objDSig->add509Cert(file_get_contents("./sunat/certificados/public_key.pem"), true, false, array("subjectName" => true));

        $objDSig->appendSignature($doc->getElementsByTagName("ExtensionContent")->item($netix));
        
        // 2: GUARDAMOS EL XML FIRMADO //
        $doc->save($carpeta_netix.".xml");
        chmod($carpeta_netix.".xml", 0777);
        
        if (file_exists($carpeta_netix.".xml")) {
            return 1;
        }else{
            return 0;
        }
    }

    function netix_sendBill($carpeta_netix, $archivo_netix, $credenciales){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>'.$credenciales[0].$credenciales[1].'</wsse:Username>
                        <wsse:Password>'.$credenciales[2].'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:sendBill>
                    <fileName>'.$archivo_netix.'.zip</fileName>
                    <contentFile>'.base64_encode(file_get_contents($carpeta_netix."/".$archivo_netix.".zip")).'</contentFile>
                </ser:sendBill>
            </soapenv:Body>
        </soapenv:Envelope>';
        return $xml;
    }
    
    function netix_sendSummary($carpeta_netix, $archivo_netix, $credenciales){
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>'.$credenciales[0].$credenciales[1].'</wsse:Username>
                        <wsse:Password>'.$credenciales[2].'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:sendSummary>
                    <fileName>'.$archivo_netix.'.zip</fileName>
                    <contentFile>'.base64_encode(file_get_contents($carpeta_netix."/".$archivo_netix.".zip")).'</contentFile>
                </ser:sendSummary>
            </soapenv:Body>
        </soapenv:Envelope>';
        return $xml;
    }
    
    function netix_getStatus($ticket, $credenciales){
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>'.$credenciales[0].$credenciales[1].'</wsse:Username>
                        <wsse:Password>'.$credenciales[2].'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:getStatus>
                    <ticket>'.$ticket.'</ticket>
                </ser:getStatus>
            </soapenv:Body>
        </soapenv:Envelope>';
        return $xml;
    }

    function netix_getStatusCDR($informacion, $credenciales){
        $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
            <soapenv:Header>
                <wsse:Security>
                    <wsse:UsernameToken>
                        <wsse:Username>'.$credenciales[0].$credenciales[1].'</wsse:Username>
                        <wsse:Password>'.$credenciales[2].'</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soapenv:Header>
            <soapenv:Body>
                <ser:getStatus>
                    <rucComprobante>'.$credenciales[0].'</rucComprobante>
                    <tipoComprobante>'.$informacion[0].'</tipoComprobante>
                    <serieComprobante>'.$informacion[1].'</serieComprobante>
                    <numeroComprobante>'.$informacion[2].'</numeroComprobante>
                </ser:getStatus>
            </soapenv:Body>
        </soapenv:Envelope>';
        return $xml;
    }

    function netix_enviarSUNAT($send, $carpeta_netix, $archivo_netix, $credenciales, $tipo = "electronico"){
        
        // 1: CREAMOS EL ARCHIVO ZIP CON EL XML DEL COMPROBANTE //

        $this->load->library("zip");
        $this->zip->read_file($carpeta_netix."/".$archivo_netix.".xml");
        $this->zip->archive($carpeta_netix."/".$archivo_netix.".zip");
        chmod($carpeta_netix."/".$archivo_netix.".zip", 0777);

        $webservice = $this->db->query("select * from public.webservice")->result_array();
        
        // NOTA: campo->sunatose = 0: SERVICIO SUNAT, campo->sunatose = 1: SERVICIO OSE //
        $camposervice = "servicesunat";
        if ($webservice[0]["sunatose"]==1) {
            $camposervice = "serviceose";
        }

        if ($tipo!="electronico") {
            $camposervice = $camposervice.$tipo;
        }

        // NOTA: campo->serviceweb = 0: PRODUCCION SUNAT, campo->serviceweb = 1: DEMO //
        if ($webservice[0]["serviceweb"]==1) {
            $camposervice = $camposervice."_demo";
        }
        $wsdlURL = $webservice[0][$camposervice];
        
        // 2: ESTRUCTURA DEL XML PARA LA CONEXION //

        if($send=="sendSummary"){
            $XMLString = $this->netix_sendSummary($carpeta_netix, $archivo_netix, $credenciales);
            $result = $this->soapCall($wsdlURL, $callFunction = $send, $XMLString);
            
            if($result["error"] == "si"){
                $estado = 0; $mensaje = $result["mensaje"];
            }else{
                // 3: DESCARGAMOS EL ARCHIVO RESPUESTA DE SUNAT //
                $archivoresponse = fopen($carpeta_netix."/R-".$archivo_netix.".xml","w+");
                fputs($archivoresponse,$result["mensaje"]); fclose($archivoresponse);

                // 4: LEEMOS EL ARCHIVO XML RESPONSE //
                $xml = simplexml_load_file($carpeta_netix."/R-".$archivo_netix.".xml"); 
                foreach ($xml->xpath('//ticket') as $response){ 
                    $ticket = $response;
                }

                if($ticket != ""){
                    // 5: CONSULTAMOS EL TICKET //

                    $update = array(
                        "fechaenvio" => date("Y-m-d"), 
                        "ticket" => $ticket
                    );
                    $this->db->where("codresumentipo",$credenciales[3]);
                    $this->db->where("periodo",$credenciales[4]);
                    $this->db->where("nrocorrelativo",$credenciales[5]);
                    $this->db->where("codempresa",$credenciales[6]);
                    $actualizarkardex = $this->db->update("sunat.resumenes", $update);

                    // 5: SI ES RESUMEN DE BOLETAS //

                    if ($credenciales[3]==3) {
                        $detalle = $this->db->query("select codkardex from sunat.kardexsunatdetalle where codresumentipo=".$credenciales[3]." and periodo='".$credenciales[4]."' and nrocorrelativo=".$credenciales[5]." and codempresa=".$credenciales[6])->result_array();
                        foreach ($detalle as $value) {
                            $update = array(
                                "fechaenvio" => date("Y-m-d")
                            );
                            $this->db->where("codkardex",$value["codkardex"]);
                            $actualizarkardex = $this->db->update("sunat.kardexsunat", $update);
                        }

                        $update = array(
                            "fechaenvio" => date("Y-m-d")
                        );
                        $this->db->where("codresumentipo",$credenciales[3]);
                        $this->db->where("periodo",$credenciales[4]);
                        $this->db->where("nrocorrelativo",$credenciales[5]);
                        $this->db->where("codempresa",$credenciales[6]);
                        $actualizarkardex = $this->db->update("sunat.kardexsunatdetalle", $update);
                    }

                    // 6: ELIMINAMOS EL ARCHIVO RESPONSE Y LA CARPETA TEMPORAL //

                    foreach(glob($carpeta_netix . "/*") as $archivos_carpeta){             
                        if (is_dir($archivos_carpeta)){
                            rmdir($carpeta_netix."/dummy");
                        } else {
                            unlink($archivos_carpeta);
                        }
                    }
                    rmdir($carpeta_netix);

                    // 7: CONSULTAMOS EL TICKET //

                    $estado = $this->netix_consultarTICKET($archivo_netix, $ticket, $credenciales);
                    $mensaje = $estado["mensaje"]; $estado = $estado["estado"];
                }else{
                    $estado = 0; $mensaje = "NO HAY RESPUESTA DE LA SUNAT !!! INTENTALO MAS TARDE";
                }
            }
        }
        
        if($send=="sendBill"){
            $XMLString = $this->netix_sendBill($carpeta_netix, $archivo_netix, $credenciales);
            $result = $this->soapCall($wsdlURL, $callFunction = $send, $XMLString);
            
            if($result["error"] == "si"){
                $estado = 0; $mensaje = $result["mensaje"];
            }else{
                // 3: DESCARGAMOS EL ARCHIVO RESPUESTA DE SUNAT //
                $archivoresponse = fopen($carpeta_netix."/C-".$archivo_netix.".xml","w+");
                fputs($archivoresponse,$result["mensaje"]); fclose($archivoresponse);

                // 4: LEEMOS EL ARCHIVO XML RESPONSE //
                $xml = simplexml_load_file($carpeta_netix."/C-".$archivo_netix.".xml");
                foreach ($xml->xpath('//applicationResponse') as $response){ }

                if($response != ""){
                    // 5: CREAMOS UNA CARPETA PARA ALMACENAR LOS CDR POR AÑO//
                    $carpeta_year  = "./sunat/comprobantes/".date("Y");
                    if (!file_exists($carpeta_year)) { 
                        mkdir($carpeta_year,0777); chmod($carpeta_year, 0777);
                    }
                    
                    // 5: CREAMOS UNA CARPETA PARA ALMACENAR LOS CDR POR MES //
                    $carpeta_month = $carpeta_year."/".date("m");
                    if (!file_exists($carpeta_month)) { 
                        mkdir($carpeta_month,0777); chmod($carpeta_month, 0777);
                    }

                    // 5: DESCARGAMOS EL ARCHIVO CDR (CONSTANCIA DE RECEPCIÓN) //
                    $cdr = base64_decode($response);
                    $archivoresponse = fopen($carpeta_month."/R-".$archivo_netix.".zip","w+");
                    fputs($archivoresponse, $cdr); fclose($archivoresponse);
                    // chmod($carpeta_month."/R-".$archivo_netix.".zip", 0777);

                    // 6: EXTRAEMOS EL ARCHIVO RESPUESTA //
                    $zip = new ZipArchive;
                    if ($zip->open($carpeta_month."/R-".$archivo_netix.".zip") === TRUE){
                        $zip->extractTo($carpeta_netix."/"); $zip->close();
                    }

                    // 7: LEEMOS EL CDR Y ACTUALIZAMOS EN LA BASE DE DATOS EN KARDEXSUNAT //
                    $xml_respuesta = simplexml_load_file($carpeta_netix."/R-".$archivo_netix.'.xml');
                    foreach ($xml_respuesta->xpath('//cbc:ResponseCode') as $responsecode){ 
                        $responsecode_texto = $responsecode;
                    }
                    foreach ($xml_respuesta->xpath('//cbc:Description') as $description) {
                        $description_texto = $description;
                    }

                    $descripcion_explode = explode("-",$description_texto);
                    if($responsecode_texto == 0){    
                        $estado = 1; $mensaje =  (string)($description_texto);
                    }elseif($responsecode_texto >= 100 and $responsecode_texto<=1999){
                        $estado = 2; $mensaje = (string)($descripcion_explode[1]);
                    }elseif($responsecode_texto >= 2000 and $responsecode_texto<=3999){
                        $estado = 3; $mensaje = (string)($descripcion_explode[1]);
                    }else{
                        $estado = 4; $mensaje = (string)($descripcion_explode[1]);
                    }

                    $update = array(
                        "fechaenvio" => date("Y-m-d"), 
                        "codigorespuesta" => $responsecode_texto, 
                        "ruta_cdr" => $carpeta_month."/R-".$archivo_netix, 
                        "descripcion_cdr" => $mensaje,
                        "estado" => $estado
                    );
                    $this->db->where("codkardex",$credenciales[3]);
                    $actualizarkardex = $this->db->update("sunat.kardexsunat", $update);

                    // 8: ELIMINAMOS EL ARCHIVO RESPONSE Y LA CARPETA TEMPORAL //

                    foreach(glob($carpeta_netix . "/*") as $archivos_carpeta){             
                        if (is_dir($archivos_carpeta)){
                            rmdir($carpeta_netix."/dummy");
                        } else {
                            unlink($archivos_carpeta);
                        }
                    }
                    rmdir($carpeta_netix);
                }else{
                    $estado = 0; $mensaje = "NO HAY RESPUESTA DE LA SUNAT !!! INTENTALO MAS TARDE";
                }
            }
        }

        $data["estado"] = $estado; $data["mensaje"] = $mensaje;
        return $data;
    }

    function netix_consultarTICKET($nombre_xml, $ticket, $credenciales, $tipo = "electronico"){

        $webservice = $this->db->query("select * from public.webservice")->result_array();
        
        // NOTA: campo->sunatose = 0: SERVICIO SUNAT, campo->sunatose = 1: SERVICIO OSE //
        $camposervice = "servicesunat";
        if ($webservice[0]["sunatose"]==1) {
            $camposervice = "serviceose";
        }

        if ($tipo!="electronico") {
            $camposervice = $camposervice.$tipo;
        }

        // NOTA: campo->serviceweb = 0: PRODUCCION SUNAT, campo->serviceweb = 1: DEMO //
        if ($webservice[0]["serviceweb"]==1) {
            $camposervice = $camposervice."_demo";
        }
        $wsdlURL = $webservice[0][$camposervice];

        // 1: ESTRUCTURA PARA LA CONEXION //

        $XMLString = $this->netix_getStatus($ticket, $credenciales);
        $result = $this->soapCall($wsdlURL, $callFunction = "getStatus", $XMLString);

        if($result["error"] == "si"){
            $estado = 0; $mensaje = $result["mensaje"];
        }else{
            // 3: DESCARGAMOS EL ARCHIVO RESPUESTA DE SUNAT //
            $carpeta_netix  = "./sunat/webnetix/".$ticket;
            if (!file_exists($carpeta_netix)) { 
                mkdir($carpeta_netix,0777); chmod($carpeta_netix, 0777);
            }

            $archivoresponse = fopen($carpeta_netix."/R-".$ticket.".xml","w+");
            fputs($archivoresponse,$result["mensaje"]); fclose($archivoresponse);

            // 4: LEEMOS EL ARCHIVO XML //
            $xml = simplexml_load_file($carpeta_netix."/R-".$ticket.".xml"); 
            foreach ($xml->xpath('//content') as $response){ }

            if($response != ""){
                // 5: CREAMOS UNA CARPETA PARA ALMACENAR LOS TICKETS POR AÑO//
                $carpeta_year  = "./sunat/resumenes/".date("Y");
                if (!file_exists($carpeta_year)) { 
                    mkdir($carpeta_year,0777); chmod($carpeta_year, 0777);
                }

                // 5: CREAMOS UNA CARPETA PARA ALMACENAR LOS CDR POR MES //
                $carpeta_month = $carpeta_year."/".date("m");
                if (!file_exists($carpeta_month)) {
                    mkdir($carpeta_month,0777); chmod($carpeta_month, 0777);
                }

                // 5: DESCARGAMOS EL ARCHIVO CDR (CONSTANCIA DE RECEPCIÓN) //
                $cdr = base64_decode($response);
                $archivoresponse = fopen($carpeta_month."/R-".$ticket.".zip","w+");
                fputs($archivoresponse, $cdr); fclose($archivoresponse);
                // chmod($carpeta_month."/R-".$ticket.".zip", 0777);

                // 6: EXTRAEMOS EL ARCHIVO RESPUESTA //
                $zip = new ZipArchive;
                if ($zip->open($carpeta_month."/R-".$ticket.".zip") === TRUE){
                    $zip->extractTo($carpeta_netix."/"); $zip->close();
                }

                // 7: LEEMOS EL CDR Y ACTUALIZAMOS EN LA BASE DE DATOS EN RESUMENES //
                $xml_respuesta = simplexml_load_file($carpeta_netix."/R-".$nombre_xml.'.xml');
                foreach ($xml_respuesta->xpath('//cbc:ResponseCode') as $responsecode){ 
                    $responsecode_texto = $responsecode;
                }
                foreach ($xml_respuesta->xpath('//cbc:Description') as $description) {
                    $description_texto = $description;
                }

                $descripcion_explode = explode("-",$description_texto);
                if($responsecode_texto == 0){    
                    $estado = 1; $mensaje =  (string)($description_texto);
                }elseif($responsecode_texto >= 100 and $responsecode_texto<=1999){
                    $estado = 2; $mensaje = (string)($descripcion_explode[1]);
                }elseif($responsecode_texto >= 2000 and $responsecode_texto<=3999){
                    $estado = 3; $mensaje = (string)($descripcion_explode[1]);
                }else{
                    $estado = 4; $mensaje = (string)($descripcion_explode[1]);
                }

                $update = array(
                    "codigorespuesta" => $responsecode_texto, 
                    "ruta_cdr" => $carpeta_month."/R-".$ticket, 
                    "descripcion_cdr" => $mensaje,
                    "estado" => $estado
                );
                $this->db->where("codresumentipo",$credenciales[3]);
                $this->db->where("periodo",$credenciales[4]);
                $this->db->where("nrocorrelativo",$credenciales[5]);
                $this->db->where("codempresa",$credenciales[6]);
                $actualizarkardex = $this->db->update("sunat.resumenes", $update);

                // 7: ACTUALIZAMOS LOS CAMPOS DE LAS TALAS DE SUNAT //
                
                if ($credenciales[3]==1 || $credenciales[3]==4) {
                    $detalle = $this->db->query("select codkardex from sunat.kardexsunatanulados where codresumentipo=".$credenciales[3]." and periodo='".$credenciales[4]."' and nrocorrelativo=".$credenciales[5]." and codempresa=".$credenciales[6])->result_array();
                    foreach ($detalle as $value) {
                        $update = array(
                            "estado" => $estado
                        );
                        $this->db->where("codkardex",$value["codkardex"]);
                        $actualizarkardex = $this->db->update("sunat.kardexsunatanulados", $update);
                    }
                }

                if ($credenciales[3]==3) {
                    $detalle = $this->db->query("select codkardex from sunat.kardexsunatdetalle where codresumentipo=".$credenciales[3]." and periodo='".$credenciales[4]."' and nrocorrelativo=".$credenciales[5]." and codempresa=".$credenciales[6])->result_array();
                    foreach ($detalle as $value) {
                        $update = array(
                            "codigorespuesta" => $responsecode_texto, 
                            "ruta_cdr" => $carpeta_month."/R-".$ticket,
                            "descripcion_cdr" => $mensaje,
                            "estado" => $estado
                        );
                        $this->db->where("codkardex",$value["codkardex"]);
                        $actualizarkardex = $this->db->update("sunat.kardexsunat", $update);
                    }

                    $update = array(
                        "descripcion_cdr" => $mensaje,
                        "estado" => $estado
                    );
                    $this->db->where("codresumentipo",$credenciales[3]);
                    $this->db->where("periodo",$credenciales[4]);
                    $this->db->where("nrocorrelativo",$credenciales[5]);
                    $this->db->where("codempresa",$credenciales[6]);
                    $actualizarkardex = $this->db->update("sunat.kardexsunatdetalle", $update);
                }

                // 8: ELIMINAMOS EL ARCHIVO RESPONSE Y LA CARPETA TEMPORAL //

                foreach(glob($carpeta_netix . "/*") as $archivos_carpeta){          
                    if (is_dir($archivos_carpeta)){
                        rmdir($carpeta_netix."/dummy");
                    } else {
                        unlink($archivos_carpeta);
                    }
                }
                rmdir($carpeta_netix);
            }else{
                $estado = 0; $mensaje = "NO HAY RESPUESTA DE LA SUNAT !!! INTENTALO MAS TARDE";
            }
        }

        $data["estado"] = $estado; $data["mensaje"] = $mensaje;
        return $data;
    } 
    
    function netix_consultarSUNAT($informacion, $credenciales, $tipo = "electronico"){
        $webservice = $this->db->query("select * from public.webservice")->result_array();
        
        // NOTA: campo->sunatose = 0: SERVICIO SUNAT, campo->sunatose = 1: SERVICIO OSE //
        $camposervice = "servicesunat";
        if ($webservice[0]["sunatose"]==1) {
            $camposervice = "serviceose";
        }

        if ($tipo!="electronico") {
            $camposervice = $camposervice.$tipo;
        }

        // NOTA: campo->serviceweb = 0: PRODUCCION SUNAT, campo->serviceweb = 1: DEMO //
        if ($webservice[0]["serviceweb"]==1) {
            $camposervice = $camposervice."_demo";
        }

        if ($tipo=="electronico") {
            if ($webservice[0][$camposervice]=="./sunat/billService.wsdl") {
                $wsdlURL = "https://e-factura.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl";
            }else{
                $wsdlURL = "https://www.sunat.gob.pe/ol-it-wsconscpegem/billConsultService?wsdl";
            }
        }else{
            $wsdlURL = $webservice[0][$camposervice];
        }
        
        $XMLString = $this->netix_getStatusCDR($informacion, $credenciales);
        $result = $this->soapCall($wsdlURL, $callFunction = "getStatus", $XMLString);
        if($result["error"] == "si"){
            $estado = 0; $mensaje = $result["mensaje"];
        }else{
            $estado = 1; $mensaje = $result["mensaje"];
        }

        $data["estado"] = $estado; $data["mensaje"] = $mensaje;
        return $data;
    }

    function soapCall($wsdlURL, $callFunction = "", $XMLString) {
        $client = new funcionSoap($wsdlURL, array("trace" => true));
        try{
            $reply  = $client->SoapClientCall($XMLString);
            $client->__call("$callFunction", array(), array());

            return array("error" => "no", "mensaje" => $client->__getLastResponse());
        }catch(Exception $e){
            return array("error" => "si", "mensaje" => $e->getMessage());
        }
    }

    function netix_qrcode($textoqr){
        $this->load->library('ciqrcode');
        $params['data'] = $textoqr; $params['level'] = 'H'; $params['size'] = 5;
        $params['savename'] = "./sunat/webnetix/qrcode.png";
        $this->ciqrcode->generate($params);
        // chmod("./sunat/webnetix/qrcode.png", 0777);
        
        $archivo_error = APPPATH."/logs/qrcode.png-errors.txt";
        unlink($archivo_error);
        
        return 1;
    }
}

class funcionSoap extends SoapClient{
    public $XMLStr = "";

    public function setXMLStr($value) {
        $this->XMLStr = $value;
    }
    
    public function getXMLStr() {
        return $this->XMLStr;
    }
    
    public function __doRequest($request, $location, $action, $version, $one_way = 0){
        $request = $this->XMLStr;
        $dom = new DOMDocument("1.0");
        try{
            $dom->loadXML($request);
        } catch (DOMException $e) {
            die($e->code);
        }
        $request = $dom->saveXML();
        //Para la solicitud //
        return parent::__doRequest($request, $location, $action, $version, $one_way = 0);
    }
    
    public function SoapClientCall($SOAPXML){
        return $this->setXMLStr($SOAPXML);
    }
}