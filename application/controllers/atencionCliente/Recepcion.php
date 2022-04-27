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
        if ($this->input->is_ajax_request()) {
            $this->request = json_decode(file_get_contents('php://input'));
            $limit = 8; $offset = $this->request->pagina * $limit - $limit;

            $lista = $this->db->query("Select p.razonsocial  as nombrepersona, e.razonsocial  as nombreempleado,r.* from recepcion r inner join personas p on r.codpersona = p.codpersona inner join personas e on r.codempleado = e.codpersona desc offset ".$offset." limit ".$limit)->result_array();
            foreach ($lista as $valor => $clave){
                echo $clave;
            }
            /*$total = $this->db->query("select count(*) as total from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) inner join public.areas as areas on(empleado.codarea=areas.codarea) inner join public.cargos as cargos on(empleado.codcargo=cargos.codcargo) where (UPPER(persona.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(persona.documento) like UPPER('%".$this->request->buscar."%')) and empleado.estado=1")->result_array();

            $paginas = floor($total[0]["total"] / $limit);
            if ( ($total[0]["total"] % $limit)!=0 ) {
                $paginas = $paginas + 1;
            }

            $paginacion = array();
            $paginacion["total"] = $total[0]["total"];
            $paginacion["actual"] = $this->request->pagina;
            $paginacion["ultima"] = $paginas;
            $paginacion["desde"] = $offset;
            $paginacion["hasta"] = $offset + $limit;

            echo json_encode(array("lista" => $lista,"paginacion" => $paginacion));*/
        }else{
            $this->load->view("netix/404");
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
