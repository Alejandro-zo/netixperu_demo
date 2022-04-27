<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH."/third_party/netix_email/class.phpmailer.php";
require_once APPPATH."/third_party/netix_email/class.smtp.php";

class Clientes extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model"); $this->load->model("Facturacion_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$this->load->view("ventas/clientes/index");
                //$this->load->view("atencionCliente/recepcion/recepcion");
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

			$lista = $this->db->query("select personas.* from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=1 or socios.codsociotipo=3) and socios.estado=1 order by personas.codpersona desc offset ".$offset." limit ".$limit)->result_array();

			$total = $this->db->query("select count(*) as total from public.socios as socios inner join public.personas as personas on (socios.codpersona=personas.codpersona) where (UPPER(personas.documento) like UPPER('%".$this->request->buscar."%') or UPPER(personas.razonsocial) like UPPER('%".$this->request->buscar."%') or UPPER(personas.nombrecomercial) like UPPER('%".$this->request->buscar."%') ) and (socios.codsociotipo=1 or socios.codsociotipo=3) and socios.estado=1")->result_array();

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
				$tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
				$departamentos = $this->db->query("select distinct(ubidepartamento), departamento from public.ubigeo order by ubidepartamento")->result_array();

				if ($_SESSION["netix_rubro"]==4) {
					$this->load->view("ventas/clientes/nuevo_perfumeria",compact("tipodocumentos","departamentos"));
				}else{
					$this->load->view("ventas/clientes/nuevo",compact("tipodocumentos","departamentos"));
				}
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	public function nuevo_1(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$tipodocumentos = $this->db->query("select *from public.documentotipos where estado=1")->result_array();
				$this->load->view("ventas/clientes/nuevo_1",compact("tipodocumentos"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function provincias($ubidepartamento){
		if ($this->input->is_ajax_request()) {
			$provincias = $this->db->query("select distinct(ubiprovincia), provincia from public.ubigeo where ubidepartamento='".$ubidepartamento."' order by ubiprovincia")->result_array();
			$html = '<option value="">SELECCIONE</option>';
			foreach ($provincias as $key => $value) {
				$html .= '<option value="'.$value["ubiprovincia"].'">'.$value["provincia"].'</option>';
			}
			echo $html;
		}
	}

	function distritos($ubidepartamento, $ubiprovincia){
		if ($this->input->is_ajax_request()) {
			$distritos = $this->db->query("select codubigeo, ubidistrito, distrito from public.ubigeo where ubidepartamento='".$ubidepartamento."' and ubiprovincia='".$ubiprovincia."' order by ubidistrito")->result_array();
			$html = '<option value="">SELECCIONE</option>';
			foreach ($distritos as $key => $value) {
				$html .= '<option value="'.$value["codubigeo"].'">'.$value["distrito"].'</option>';
			}
			echo $html;
		}
	}

	function buscar(){
		if ($this->input->is_ajax_request()) {
			if (isset($_POST["q"])) {
				if ($_POST["tipo"]==1) {
					$socios = $this->db->query("select public.personas.codpersona,public.personas.razonsocial, public.personas.documento from public.socios inner join public.personas on (public.socios.codpersona=public.personas.codpersona) where (UPPER(public.personas.documento) like UPPER('%".$_POST["q"]."%') or UPPER(public.personas.razonsocial) like UPPER('%".$_POST["q"]."%') ) and (public.socios.codsociotipo=1 or public.socios.codsociotipo=3) and public.socios.estado=1 limit 10")->result_array();
				}elseif($_POST["tipo"]==2){
					$socios = $this->db->query("select public.personas.codpersona,public.personas.razonsocial, public.personas.documento from public.socios inner join public.personas on (public.socios.codpersona=public.personas.codpersona) where (UPPER(public.personas.documento) like UPPER('%".$_POST["q"]."%') or UPPER(public.personas.razonsocial) like UPPER('%".$_POST["q"]."%') ) and (public.socios.codsociotipo=2 or public.socios.codsociotipo=3) and public.socios.estado=1 limit 10")->result_array();
				}else{
					$socios = $this->db->query("select public.personas.codpersona,public.personas.razonsocial, public.personas.documento from public.socios inner join public.personas on (public.socios.codpersona=public.personas.codpersona) where (UPPER(public.personas.documento) like UPPER('%".$_POST["q"]."%') or UPPER(public.personas.razonsocial) like UPPER('%".$_POST["q"]."%') ) and public.socios.estado=1 limit 10")->result_array();
				}
			}else{
				$socios = $this->db->query("select public.personas.codpersona,public.personas.razonsocial, public.personas.documento from public.socios inner join public.personas on (public.socios.codpersona=public.personas.codpersona) where public.socios.estado=1 limit 10")->result_array();
			}
			echo json_encode($socios);
		}
	}

	function infocliente($codpersona){
		if ($this->input->is_ajax_request()) {
			$info = $this->db->query("select coddocumentotipo,direccion from public.personas where codpersona=".$codpersona)->result_array();
			echo json_encode($info);
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","email","telefono","codubigeo","convenio","estado"];
			$campos_1 = ["codpersona","codsociotipo","usuario","clave","codpatrocinador"];
			$valores = [$this->request->coddocumentotipo,$this->request->documento,$this->request->razonsocial,$this->request->nombrecomercial,$this->request->direccion,$this->request->email,$this->request->telefono,$this->request->codubigeo,$this->request->convenio,1];

			if($this->request->codregistro=="") {
				$existe =$this->db->query("select codpersona from public.personas where documento='".$this->request->documento."'")->result_array();
				if (count($existe)>0) {
					echo "e"; exit();
				}else{
					$codpersona = $this->Netix_model->netix_guardar("public.personas", $campos, $valores, "true");
				}

				$valores_1 = [$codpersona,$this->request->codsociotipo,$this->request->documento,$this->request->documento,$this->request->codpatrocinador];
				$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
			}else{
				$actual = $this->db->query("select documento from public.personas where codpersona=".$this->request->codregistro)->result_array();
				$existe = $this->db->query("select documento from public.personas where documento='".$this->request->documento."'")->result_array();
				if (count($existe)>0) {
					if ( $actual[0]["documento"]!=$existe[0]["documento"] ) {
						echo "e"; exit();
					}
				}

				$estado = $this->Netix_model->netix_editar("public.personas", $campos, $valores, "codpersona", $this->request->codregistro);

				$valores_1 = [$this->request->codregistro,$this->request->codsociotipo,$this->request->documento,$this->request->documento,$this->request->codpatrocinador];
				$existe = $this->db->query("select codpersona from public.socios where codpersona=".$this->request->codregistro)->result_array();
				if (count($existe)==0) {
					$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
				}else{
					$estado = $this->Netix_model->netix_editar("public.socios", $campos_1, $valores_1, "codpersona", $this->request->codregistro);
				}
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar_1(){
		if ($this->input->is_ajax_request()) {
			$campos = ["coddocumentotipo","documento","razonsocial","nombrecomercial","direccion","email","telefono","estado"];
			$campos_1 = ["codpersona","codsociotipo","usuario","clave"];
			$this->request = json_decode(file_get_contents('php://input'));
			$valores = [$this->request->coddocumentotipo,$this->request->documento,$this->request->razonsocial,$this->request->nombrecomercial,$this->request->direccion,$this->request->email,$this->request->telefono,1];

			$this->db->trans_begin();

			$existe =$this->db->query("select codpersona from public.personas where documento='".$this->request->documento."'")->result_array();
			if (count($existe)>0) {
				echo "e"; exit();
			}else{
				$socio = array();
				$codpersona = $this->Netix_model->netix_guardar("public.personas", $campos, $valores, "true");
			}

			$valores_1 = [$codpersona,$this->request->codsociotipo,$this->request->documento,$this->request->documento];
			if (count($socio)>0) {
				$estado = $this->Netix_model->netix_editar("public.socios", $campos_1, $valores_1,"codpersona",$codpersona);
			}else{
				$estado = $this->Netix_model->netix_guardar("public.socios", $campos_1, $valores_1);
			}

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback(); $estado = 0;
			}else{
				$this->db->trans_commit();
				$estado = $this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$codpersona)->result_array();
			}

			echo json_encode($estado);
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select personas.codpersona as codregistro,* from public.personas as personas inner join public.socios as socios on(personas.codpersona=socios.codpersona) where personas.codpersona=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function ubigeo(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$info = $this->db->query("select personas.codubigeo, socios.codpatrocinador from public.personas as personas inner join public.socios as socios on(personas.codpersona=socios.codpersona) where personas.codpersona=".$this->request->codregistro)->result_array();
			$ubigeo = $this->db->query("select * from public.ubigeo where codubigeo=".$info[0]["codubigeo"])->result_array();
			$patrocinador = $this->db->query("select codpersona,razonsocial from public.personas where codpersona=".$info[0]["codpatrocinador"])->result_array();
			$data["ubigeo"] = $ubigeo; $data["patrocinador"] = $patrocinador;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("public.socios", "codpersona", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	// ENVIAR COMPROBANTE ELECTRONICO AL CLIENTE //

	function correo($documento){
		if ($this->input->is_ajax_request()) {
			$correo = $this->db->query("select email from public.personas where documento='".$documento."' ")->result_array();
			echo $correo[0]["email"];
		}
	}

	function enviar_correo(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$empresa = $this->db->query("select documento,razonsocial from public.personas where codpersona=1")->result_array();
			$sucursal = $this->db->query("select *from public.sucursales where codsucursal=".$_SESSION["netix_codsucursal"])->result_array();
			$parametros = $this->db->query("select envioemail,claveemail from public.empresas limit 1")->result_array();

			$venta = $this->db->query("select k.fechacomprobante,ct.descripcion as comprobante, k.codcomprobantetipo, k.seriecomprobante,k.nrocomprobante, p.documento,k.cliente,k.direccion,k.valorventa,k.descglobal,k.igv,k.importe, k.codempleado,k.condicionpago,k.nroplaca, k.codpersona from kardex.kardex as k inner join public.personas as p on(k.codpersona=p.codpersona) inner join caja.comprobantetipos as ct on(k.codcomprobantetipo=ct.codcomprobantetipo) where k.codkardex=".$this->request->codkardex)->result_array();
			$totales = $this->db->query("select (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$this->request->codkardex." and codafectacionigv='10') as gravado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$this->request->codkardex." and codafectacionigv='20') as exonerado, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$this->request->codkardex." and codafectacionigv='30') as inafecto, (select coalesce(sum(subtotal),0) from kardex.kardexdetalle where codkardex=".$this->request->codkardex." and codafectacionigv='21') as gratuito")->result_array();

			$mail = new PHPMailer;
			$mail->isSMTP();
			$mail->SMTPDebug = 0;
			$mail->Host = 'smtp.gmail.com';
			$mail->Port = 25;
			$mail->SMTPAuth = true;                     
			$mail->Username = $parametros[0]["envioemail"];
			$mail->Password = $parametros[0]["claveemail"];
			//$mail->SMTPSecure = 'tls';

			$mail->setFrom($parametros[0]["envioemail"],$empresa[0]["razonsocial"]);
			$mail->addAddress($this->request->email,$venta[0]["cliente"]);
			$mail->Subject = 'COMPROBANTE ELECTRONICO '.$venta[0]["seriecomprobante"].' - '.$venta[0]["nrocomprobante"];
			$mail->isHTML(true);
			$mail->CharSet = "utf-8";

			$mail->Body =' <div align="center">
				<table border="0" cellpadding="0" cellspacing="0" width="440px" style=color:rgb(0,0,0);font-family:"Times New Roman";font-size:medium">
					<tbody>
						<tr ><td colspan="2" style="color:#515559;font-family:Arial,Helvetica,sans-serif;font-size:14px;font-weight:bold;text-align:center" ><strong>'.$empresa[0]["razonsocial"].'</strong></td>
						</tr>
						<tr >
							<td> 													
								<table border="0" cellpadding="0" cellspacing="0" class="m_-8262498329391306224row">
									<tbody>
										<tr>
											<td>
												<table border="0" cellpadding="0" cellspacing="0" class="m_-8262498329391306224columns" width="570">
													<tbody>
														<tr>
															<td height="20">&nbsp;</td>
														</tr>
														<tr>
															<td style="color:#515559;font-family:Arial,Helvetica,sans-serif;font-size:12px;text-align:justify">Le informamos que su comprobante electrónico ha sido emitido exitosamente. Se adjunta el comprobante electrónico firmado en formato xml y una representación digital impresa en formato pdf. Tambien puede descargar los archivos desde el portal web: <b><a href="http://netixperu.com/sunat" style="color:rgb(38,89,107);text-decoration:none">http://netixperu.com/sunat</a></b>.<br><br> Para ingresar al portal web por favor utilice su RUC o DNI como Usuario y Contraseña, luego de ingresar cambie a una contraseña segura para próximos ingresos. A continuación los datos principales de su comprobante electrónico emitido:</td>
														</tr>
														<tr>
															<td height="20">&nbsp;</td>
														</tr>
													</tbody>
												</table>
											</td>
										</tr>
									</tbody>
								</table> 
							</td>
						</tr>
						<tr bgcolor="#eeeeee" style="border-radius:5px">
							<td align="left" valign="top" style="padding:0px 9px 0px 0px">
								<p>	
									<center><font color="#1c2927" face="Arial, Helvetica, sans-serif" style="font-size:25px;">DATOS DEL COMPROBANTE ELECTRONICO</font></center><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Raz&oacute;n Social:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px;">
										<font color="#384845">'.$venta[0]["cliente"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">RUC/DNI Cliente:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$venta[0]["documento"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Proveedor:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$empresa[0]["razonsocial"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">RUC Proveedor:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$empresa[0]["documento"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Tipo de Comprobante:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$venta[0]["comprobante"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Fecha de Emisión:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$venta[0]["fechacomprobante"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Nro de Comprobante:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.$venta[0]["seriecomprobante"].'-'.$venta[0]["nrocomprobante"].'</font>
									</strong><br>

									<font color="#405751" face="Arial, Helvetica, sans-serif"><span style="font-size:12px">Valor Total:&nbsp;</span></font>
									<strong style="color:rgb(166,174,172);font-family:Arial,Helvetica,sans-serif;font-size:12px">
										<font color="#384845">'.number_format($venta[0]["importe"],2).'</font>
									</strong><br>
									<center><font color="#1c2927" face="Arial, Helvetica, sans-serif" style="font-size:15px;">Este correo es emitido de manera automática por favor no responder este correo.</font></center><br>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</div>';

			// XML del Comprobante //
			$estado = $this->Facturacion_model->netix_crearXML("01",$this->request->codkardex);
			if ($estado["estado"]!=0) {
				// $firma = Sunat::netix_firmarXML($estado["carpeta_netix"]."/".$estado["archivo_netix"], 0);

				if(file_exists($estado["carpeta_netix"]."/".$estado["archivo_netix"].".xml")){
					$mail->addAttachment($estado["carpeta_netix"]."/".$estado["archivo_netix"].".xml", $estado["archivo_netix"]." C.E XML");
				}
			}

			if(!$mail->send()){
				$estado_correo = $mail->ErrorInfo();
			}else{
				$estado_correo = 1;
			}

			echo $estado_correo;
		}
	}
}