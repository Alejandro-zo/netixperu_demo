<?php defined('BASEPATH') OR exit('No direct script access allowed');
require ('file:///C:/xampp/htdocs/netixperu_demo/application/third_party/netix_fpdf/fpdf.php');

class RecepcionProforma extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }
    public function proforma(){

        $registro = $_GET['registro'];
        $recepcion = $this->db->query("select p.razonsocial as nombrepersona, 
            e.razonsocial as nombreempleado, 
            t.descripcion as tipopago, 
            r.* 
            from 
            public.recepcion r 
            inner join public.personas p on r.codpersona = p.codpersona 
            inner join public.personas e on r.codempleado = e.codpersona 
            inner join caja.tipopagos t on r.codtipopago = t.codtipopago
            where r.estado =1 and r.codrecepcion =".$registro)->result_array();
        $empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
        $sucursal = $this->db->query("select sucursal.*,empresa.publicidad,empresa.agradecimiento from public.sucursales as sucursal inner join public.empresas as empresa on(sucursal.codempresa=empresa.codempresa) where sucursal.codsucursal=".$_SESSION["netix_codsucursal"])->result_array();


        $this->load->view("atencionCliente/recepcion/ticket",compact("empresa","sucursal","recepcion","registro"));


    }
}