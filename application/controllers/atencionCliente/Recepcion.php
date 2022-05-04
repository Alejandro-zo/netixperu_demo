<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Recepcion extends CI_Controller{

    function __construct(){
        parent::__construct();
        parent::__construct(); $this->load->model("Netix_model");
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

            $lista = $this->db->query("Select p.razonsocial as nombrepersona, e.razonsocial as nombreempleado, t.descripcion as tipopago, r.* from public.recepcion r inner join public.personas p on r.codpersona = p.codpersona inner join public.personas e on r.codempleado = e.codpersona inner join caja.tipopagos t on r.codtipopago = t.codtipopago  where (UPPER(p.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(e.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(p.documento) like UPPER('%".$this->request->buscar."%')) and r.estado = 1 offset ".$offset." limit ".$limit)->result_array();
            $total = $this->db->query("select count(*) as total from public.recepcion r inner join public.personas p on r.codpersona = p.codpersona inner join public.personas e on r.codempleado = e.codpersona where (UPPER(p.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(e.razonsocial) like UPPER('%".$this->request->buscar."%')) and p.estado=1")->result_array();

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

            echo json_encode(array("lista" => $lista,"paginacion" => $paginacion));
        }else{
            $this->load->view("netix/404");
        }
    }
    public function nuevo(){
        if ($this->input->is_ajax_request()) {
            if (isset($_SESSION["netix_usuario"])) {
                $empleados = $this->db->query("select persona.codpersona, persona.razonsocial from public.personas as persona inner join public.empleados as empleado on(persona.codpersona=empleado.codpersona) where empleado.estado=1")->result_array();
                $area = $this->db->query("select a.codarea, a.descripcion from public.areas a where a.estado =1")->result_array();
                $pago =  $this->db->query("select t.*from caja.tipopagos t where t.estado =1")->result_array();
                $tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
                $marca= $this->db->query(" select * from almacen.marcas m where m.estado =1")->result_array();
                $departamentos = $this->db->query("select distinct(ubidepartamento), departamento from public.ubigeo order by ubidepartamento")->result_array();
                //$cargos = $this->db->query("select * from public.cargos where estado=1")->result_array();
                $this->load->view("atencionCliente/recepcion/nuevo",compact("empleados","area","pago","tipodocumentos","marca","departamentos"));
            }else{
                $this->load->view("netix/505");
            }
        }else{
            $this->load->view("netix/404");
        }
    }
    function guardar(){
        if ($this->input->is_ajax_request()) {
            $campos = ["codpersona","codempleado","codtipopago","importe","producto","marca","modelo","fecharecepcion","descripcion"];
            $this->request = json_decode(file_get_contents('php://input'));
            $codpersona=$this->db->query("select codpersona from public.personas where documento='".$this->request->documento."'");
            $valores = [$codpersona,$this->request->codempleado,$this->request->codtipopago,$this->request->importe,$this->request->producto,$this->request->marca,$this->request->modelo,$this->request->fecharecepcion,$this->request->descripcion];


            if($this->request->codregistro=="") {
                $estado = $this->Netix_model->netix_guardar("public.recepcion", $campos, $valores);

            }else{
                $estado = $this->Netix_model->netix_guardar("public.recepcion", $campos, $valores);
            }
            echo $estado;
        }else{
            $this->load->view("netix/404");
        }
    }
    function editar(){
        if ($this->input->is_ajax_request()) {
            $this->request = json_decode(file_get_contents('php://input'));
            $info = $this->db->query("Select p.razonsocial as razonsocial, e.razonsocial as nombreempleado, t.descripcion as coddocumentotipo, r.* from public.recepcion r inner join public.personas p on r.codpersona = p.codpersona inner join public.personas e on r.codempleado = e.codpersona inner join caja.tipopagos t on r.codtipopago = t.codtipopago  where r.codrecepcion =".$this->request->codregistro)->result_array();
            echo json_encode($info);
        }else{
            $this->load->view("netix/404");
        }
    }

}
