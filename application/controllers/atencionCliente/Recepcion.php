<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recepcion extends CI_Controller{

    function __construct(){
        parent::__construct();
        //$this->load->model("Recepcion_Moldel");
    }
    public function index(){
        if ($this->input->is_ajax_request()) {
            if (isset($_SESSION["netix_usuario"])) {
                $this->load->view("atencionCliente/recepcion/index");
            }else{
                $this->load->view("netix/505");
            }
        }else{
            $this->load->view("netix/404");
        }
    }
    public function lista(){
        if ($this->input->is_ajax_request()){
            $list= $this->db->query("");
        }

    }
    public function nuevo(){
        if ($this->input->is_ajax_request()) {
            if (isset($_SESSION["netix_usuario"])) {
                
                $this->load->view("atencionCliente/recepcion/nuevo");
                /*
                este es el completo de la linea anterior :
                $this->load->view("atencionCliente/recepcion/nuevo",compact("tipodocumentos","departamentos"));
                $tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
                $departamentos = $this->db->query("select distinct(ubidepartamento), departamento from public.ubigeo order by ubidepartamento")->result_array();

                if ($_SESSION["netix_rubro"]==4) {
                    $this->load->view("atencionCliente/recepcion/nuevo",compact("tipodocumentos","departamentos"));
                }else{
                    $this->load->view("atencionCliente/recepcion/nuevo",compact("tipodocumentos","departamentos"));
                }*/
            }else{
                $this->load->view("netix/505");
            }
        }else{
            $this->load->view("netix/404");
        }

    }   




    public function eliminar(){

    }
    public function actualizar(){

    }




}
