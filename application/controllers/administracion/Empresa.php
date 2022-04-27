<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Empresa extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model("Netix_model"); $this->load->model("Caja_model"); $this->load->model("Kardex_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			$info = $this->db->query("select *from public.personas where codpersona=".$_SESSION["netix_codempresa"])->result_array();
			$empresa = $this->db->query("select *from public.empresas where codempresa=".$_SESSION["netix_codempresa"])->result_array();
			$service = $this->db->query("select *from public.webservice where codempresa=".$_SESSION["netix_codempresa"])->result_array();

			if (file_exists("./sunat/certificados/public_key.pem")) {
				$pen = "private_key.pem - public_key.pem GENERADOS";
			}else{
				$pen = "NO GENERADOS";
			}
			$this->load->view("administracion/empresa/index",compact("info","empresa","service","pen"));
		}else{
			$this->load->view("netix/404");
		}
	}

	public function editar(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->request = json_decode(file_get_contents('php://input'));

				$empresa = $this->db->query("select *from public.empresas where codpersona=".$this->request->codregistro)->result_array();
				$service = $this->db->query("select *from public.webservice where codempresa=".$this->request->codregistro)->result_array();
				$this->load->view("administracion/empresa/editar",compact("empresa","service"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$campos = ["rubro","facturacion","ubigeo","departamento","provincia","distrito"]; 
			$valores = [$_POST["rubro"],1,$_POST["ubigeo"],$_POST["departamento"],$_POST["provincia"],$_POST["distrito"]];
			$estado = $this->Netix_model->netix_editar("public.empresas", $campos, $valores,"codempresa",$_POST["codempresa"]);

			// NOTA: campo->sunatose = 0: SERVICIO SUNAT, campo->sunatose = 1: SERVICIO OSE //
	        $service = "servicesunat"; $service_guia = "servicesunatguia"; $service_retencion = "servicesunatretencion";
	        if ($_POST["sunatose"]==1) {
	            $service = "serviceose"; $service_guia = "serviceoseguia"; $service_retencion = "serviceoseretencion";
	        }
	        // NOTA: campo->serviceweb = 0: PRODUCCION SUNAT, campo->serviceweb = 1: DEMO //
	        if ($_POST["serviceweb"]==1) {
	            $service = $service."_demo"; $service_guia = $service_guia."_demo"; $service_retencion = $service_retencion."_demo";
	        }

			$campos = ["usuariosol","clavesol","envioemail","claveemail","certificado_clave","sunatose","serviceweb",$service,$service_guia,$service_retencion];
			$valores = [$_POST["usuariosol"],$_POST["clavesol"],$_POST["envioemail"],$_POST["claveemail"],$_POST["certificado_clave"],$_POST["sunatose"],$_POST["serviceweb"],$_POST[$service],$_POST[$service_guia],$_POST[$service_retencion]];
			$estado = $this->Netix_model->netix_editar("public.webservice", $campos, $valores,"codempresa",$_POST["codempresa"]);

			if ($_FILES["certificado_pfx"]["name"]!="") {
				$file = $_FILES["certificado_pfx"]["name"];
				move_uploaded_file($_FILES["certificado_pfx"]["tmp_name"],"./sunat/certificados/".$file);

				$data = array("certificado_pfx" => $file);
				$this->db->where("codempresa",$_POST["codempresa"]);
				$estado = $this->db->update("public.webservice",$data);

                // Creamos los archivos PEM //
                $carpeta_certificados = "./sunat/certificados"; 
                $carpeta_archivo = $carpeta_certificados."/".$file; $clave = $_POST["certificado_clave"];
                chmod($carpeta_archivo, 0777); $pkcs12 = file_get_contents($carpeta_archivo);

                $certificados = array(); $respuesta = openssl_pkcs12_read($pkcs12, $certificados, $clave);
                if ($respuesta) {
                    $publicKeyPem  = $certificados["cert"]; // Archivo público
                    $privateKeyPem = $certificados["pkey"]; // Archivo privado

                    // guardo la clave publica y privada en mi directorio en formato .pem //
                    file_put_contents($carpeta_certificados."/private_key.pem", $privateKeyPem);
                    file_put_contents($carpeta_certificados."/public_key.pem", $publicKeyPem);
                    chmod($carpeta_certificados."/private_key.pem", 0777);
                    chmod($carpeta_certificados."/public_key.pem", 0777);
                }else {
                    $estado = 0;
                }
			}

			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function copia_seguridad(){
		/* echo exec('whoami');
		$comando = '/usr/bin/pg_dump --host localhost --port 5432 --username "postgres" --no-password  --format custom --blobs --verbose --file "/var/www/html/netixperu_demo/netixperu_1.backup" "netixperu_demo" 2>&1';
		echo exec($comando); */
   		
   		$this->load->database();
   		$this->load->dbutil();

		// Realice una copia de seguridad de toda su base de datos y asigne una variable 
		$backup = & $this->dbutil->backup ();

		// Cargue el archivo de ayuda y escriba el archivo en su servidor 
		$this->load->helper('file'); 
		write_file ( '/public/mybackup.zip' ,  $backup );

		// Cargue el asistente de descarga y envíe el archivo a su escritorio 
		$this->load->helper( 'download' ); 
		force_download('mybackup.zip' ,  $backup);
	}

	function actualizar(){
		// $lista = $this->db->query("select *from kardex.kardex where codcomprobantetipo=12 order by codkardex asc")->result_array();
		
		/* $lista = $this->db->query("select *from kardex.kardex where codcomprobantetipo=12 order by codkardex asc")->result_array();
		$correlativo = 4317;
		foreach ($lista as $key => $value) {
			$correlativo = $correlativo + 1;
			$data = array(
				'nrocomprobante' => str_pad($correlativo, 8, "0", STR_PAD_LEFT)
			);
			$this->db->where("codkardex",$value["codkardex"]);
			$estado = $this->db->update("kardex.kardex",$data);

			$data = array(
				'nrocomprobante_ref' => str_pad($correlativo, 8, "0", STR_PAD_LEFT)
			);
			$this->db->where("codkardex",$value["codkardex"]);
			$estado = $this->db->update("caja.movimientos",$data);
		}

		$data = array(
			"nrocorrelativo" => $correlativo
		);
		$this->db->where("codcomprobantetipo", 12);
		$this->db->where("seriecomprobante", "BE01");
		$estado = $this->db->update("caja.comprobantes", $data); */

		/* $lista = $this->db->query("select *from kardex.kardex where codkardex=132 or codkardex=133 or codkardex=135")->result_array();
		foreach ($lista as $key => $value) {
			$total = $this->db->query("select sum(subtotal) as total from kardex.kardexdetalle where codkardex=".$value["codkardex"])->result_array();
			$data = array(
				'valorventa' => (double)$total[0]["total"],
				'importe' => (double)$total[0]["total"] 
			);
			$this->db->where("codkardex",$value["codkardex"]);
			$estado = $this->db->update("kardex.kardex",$data);

			$movimiento = $this->db->query("select codmovimiento from caja.movimientos where codkardex=".$value["codkardex"])->result_array();
			$data = array(
				'importe' => (double)$total[0]["total"] 
			);
			$this->db->where("codmovimiento",$movimiento[0]["codmovimiento"]);
			$estado = $this->db->update("caja.movimientos",$data);

			$data = array(
				'importe' => (double)$total[0]["total"],
				'importeentregado' => (double)$total[0]["total"] 
			);
			$this->db->where("codmovimiento",$movimiento[0]["codmovimiento"]);
			$estado = $this->db->update("caja.movimientosdetalle",$data);			
		} */

		echo $estado;
	}
}
