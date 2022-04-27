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

}
