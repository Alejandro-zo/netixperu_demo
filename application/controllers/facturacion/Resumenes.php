<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Resumenes extends CI_Controller {

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("facturacion/resumenes/index");
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
			$limit = 10; $offset = $this->request->pagina * $limit - $limit;

			if ($this->request->fechas->desde==$this->request->fechas->hasta) {
				$fechas = "resumen.fecharesumen='".$this->request->fechas->desde."' and";
			}else{
				$fechas = "resumen.fecharesumen>='".$this->request->fechas->desde."' and resumen.fecharesumen<='".$this->request->fechas->hasta."' and";
			}
			$lista = $this->db->query("select resumen.*, tipo.descripcion as tiporesumen from sunat.resumenes as resumen inner join sunat.resumentipos as tipo on (resumen.codresumentipo=tipo.codresumentipo) where ".$fechas." (UPPER(resumen.periodo) like UPPER('%".$this->request->buscar."%') or UPPER(tipo.descripcion) like UPPER('%".$this->request->buscar."%') ) order by resumen.codresumentipo,resumen.periodo,resumen.nrocorrelativo desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from sunat.resumenes as resumen inner join sunat.resumentipos as tipo on (resumen.codresumentipo=tipo.codresumentipo) where ".$fechas." (UPPER(resumen.periodo) like UPPER('%".$this->request->buscar."%') or UPPER(tipo.descripcion) like UPPER('%".$this->request->buscar."%') )")->result_array();

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
}