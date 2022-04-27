<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH."third_party/netix_sunat/curl.php");
require_once(APPPATH."third_party/netix_sunat/sunat.php");

require_once(APPPATH."third_party/netix_reniec/curl.php");
require_once(APPPATH."third_party/netix_reniec/essalud.php");
require_once(APPPATH."third_party/netix_reniec/mintra.php");
require_once(APPPATH."third_party/netix_reniec/reniec.php");

require_once(APPPATH."third_party/netix_jne/autoload.php");
use Peru\Jne\DniFactory;

class Web extends CI_Controller {

	public function index(){
		// session_destroy(); 
		$this->load->view("netix/404"); // $this->load->view("netix/404");
	}

	public function netix_ruc($ruc){
        $ch = curl_init("https://e-factura.tuscomprobantes.pe/wsconsulta/ruc/".$ruc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response;

		// $info = new \Sunat\Sunat( true, true);
		// echo $info->search( $ruc, true);
	}

	public function netix_dni($dni){
		$ch = curl_init("https://e-factura.tuscomprobantes.pe/wsconsulta/dni/".$dni);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($ch);
        curl_close($ch);

        echo $response; exit();

		$factory = new DniFactory();
        $cs = $factory->create();

        $person = $cs->get($dni);
        if ($person) {
            $rpt = (object)array(
                "success"       => true,
                "source"        => "jne",
                "result"        => $person
            );
            echo json_encode($rpt); exit();
        }

		$this->reniec = new \Reniec\Reniec(); 
		$this->essalud = new \EsSalud\EsSalud();
		$this->mintra = new \MinTra\mintra();

		$response = $this->reniec->search( $dni );
		if($response->success == true){
			$rpt = (object)array(
				"success" 		=> true,
				"source" 		=> "reniec",
				"result" 		=> $response->result
			);
			echo json_encode($rpt); exit();
		}
		
		$response = $this->essalud->check( $dni );
		if($response->success == true){
			$rpt = (object)array(
				"success" 		=> true,
				"source" 		=> "essalud",
				"result" 		=> $response->result
			);
			echo json_encode($rpt); exit();
		}
					
		$response = $this->mintra->check( $dni );
		if( $response->success == true ){
			$rpt = (object)array(
				"success" 		=> true,
				"source" 		=> "mintra",
				"result" 		=> $response->result
			);
			echo json_encode($rpt); exit();
		}
		
		$rpt = (object)array(
			"success" 		=> false,
			"msg" 			=> "No se encontraron datos"
		);
		echo json_encode($rpt);
	}

	public function netix_buscarsocio($documento){
		if (isset($documento)) {
			$existe = $this->db->query("select *from public.personas where documento='".$documento."'")->result_array();
			echo json_encode($existe);
		}
	}

	public function netix($desarrollador){
		if (isset($desarrollador)) {
			$existe_empresa = $this->db->query("select *from public.empresas")->result_array();
			if ($desarrollador=="carlosyrigoin" && count($existe_empresa)==0) {
				$this->db->trans_begin();
				// CREAMOS EL USUARIO DE SOPORTE WEB NETIX //
				$data = array(
					"codpersona" => 0, "codubigeo" => 0, "coddocumentotipo" => 1, "documento" => "-", "razonsocial" => "USUARIO SOPORTE", "nombrecomercial" => "USUARIO SOPORTE",
					"direccion" => "TARAPOTO", "email" => "soporte@gmail.com", "telefono" => "964777055", "sexo" => "M"
				);
				$estado = $this->db->insert("public.personas", $data);

				$data = array("codpersona" => 0, "codarea" => 1, "codcargo" => 1, "tipoempleado" => 2);
				$estado = $this->db->insert("public.empleados", $data);

				$data = array("codempleado" => 0, "codperfil" => 1, "usuario" => "soporte", "clave" => "123");
				$estado = $this->db->insert("seguridad.usuarios", $data);

				// CREAMOS LOS PERMISOS INICIALES A WEB NETIX AL USUARIO SOPORTE //
				$data = array("codmodulo" => 68, "codperfil" => 1);
				$estado = $this->db->insert("seguridad.moduloperfiles", $data);

				// CREAMOS LA EMPRESA EN WEB NETIX //
				$data = array(
					"codubigeo" => 0, "tipopersona" => 2, "coddocumentotipo" => 4, "documento" => "10334343445", "razonsocial" => "NOMBRE DE LA EMPRESA", "nombrecomercial" => "NUEVA EMPRESA", "direccion" => "-", "email" => "empresa@gmail.com"
				);
				$estado = $this->db->insert("public.personas", $data);

				$data = array("codpersona" => 1);
				$estado = $this->db->insert("public.empresas", $data);

				$data = array("codempresa" => 1, "usuariosol" => "USUARIO1", "clavesol" => "CLAVE1");
				$estado = $this->db->insert("public.webservice", $data);

				$data = array( "codpersona" => 1, "codsociotipo" => 3);
				$estado = $this->db->insert("public.socios", $data);

				// CREAMOS LA SUCURSAL, CAJA Y ALMACEN POR DEFECTO EN WEB NETIX //
				$data = array("codempresa" => 1, "descripcion" => "SUCURSAL PRINCIPAL", "direccion" => "-");
				$estado = $this->db->insert("public.sucursales", $data);

				$data = array("codsucursal" => 1, "descripcion" => "CAJA PRINCIPAL", "direccion" => "-");
				$estado = $this->db->insert("caja.cajas", $data);

				$data = array("codsucursal" => 1, "descripcion" => "ALMACEN PRINCIPAL", "direccion" => "-");
				$estado = $this->db->insert("almacen.almacenes", $data);

				// CREAMOS EL SOCIO CLIENTES Y PROVEEDORES VARIOS //
				$data = array(
					"coddocumentotipo" => 1, "documento" => "00000000", "razonsocial" => "VARIOS", "nombrecomercial" => "VARIOS", "direccion" => "-"
				);
				$estado = $this->db->insert("public.personas", $data);

				$data = array( "codpersona" => 2, "codsociotipo" => 1);
				$estado = $this->db->insert("public.socios", $data);

				// CREAMOS EL EMPLEADO ADMINISTRADOR WEB NETIX //
				$data = array(
					"codubigeo" => 0, "coddocumentotipo" => 2, "documento" => "94949494", "razonsocial" => "ADMINISTRADOR", "nombrecomercial" => "ADMINISTRADOR DE LA EMPRESA", "direccion" => "-", "email" => "administrador@gmail.com", "telefono" => "964777055", "sexo" => "M"
				);
				$estado = $this->db->insert("public.personas", $data);

				$data = array("codpersona" => 3, "codarea" => 1, "codcargo" => 1);
				$estado = $this->db->insert("public.empleados", $data);

				$data = array("codempleado" => 3, "codperfil" => 2, "usuario" => "administrador", "clave" => "123");
				$estado = $this->db->insert("seguridad.usuarios", $data);

				// ASIGNAMOS LAS SUCURSALES A LOS USUARIOS WEB NETIX //
				$data = array("codsucursal" => 1, "codusuario" => 1);
				$estado = $this->db->insert("seguridad.sucursalusuarios", $data);

				$data = array("codsucursal" => 1, "codusuario" => 2);
				$estado = $this->db->insert("seguridad.sucursalusuarios", $data);

				$data = array("codsucursal" => 1);
				$estado = $this->db->update("public.empleados", $data);

				if ($this->db->trans_status() === FALSE){
					$this->db->trans_rollback();
					echo "OCURRIO UN ERROR AL INICIAR NETIX PERÚ";
				}else{
					$this->db->trans_commit();
					echo "<br> <br> <h1 align='center'>WEB NETIX BIENVENIDO - TODO SALIO BIEN</h1>";
				}
			}
		}
	}
}