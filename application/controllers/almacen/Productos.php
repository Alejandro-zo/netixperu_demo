<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {

	public function __construct(){
		parent::__construct(); $this->load->model("Netix_model");
	}

	public function index(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				if($_SESSION["netix_ruc"]=="20603454112"){
					$this->load->view("almacen/productos/index_lista");
				}else{
					$this->load->view("almacen/productos/index");
				}
				// $this->load->view("almacen/productos/index_lista");
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
			$limit = 12; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select productos.*, marcas.descripcion as marca from almacen.productos as productos inner join almacen.marcas as marcas on(productos.codmarca=marcas.codmarca) where (UPPER(productos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(productos.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(marcas.descripcion) like UPPER('%".$this->request->buscar."%') ) and productos.estado=1 order by productos.descripcion, productos.codproducto asc offset ".$offset." limit ".$limit)->result_array();

			foreach ($lista as $key => $value) {
				$precio = $this->db->query("select pventapublico,codunidad from almacen.productounidades where codproducto=".$value["codproducto"]." order by factor")->result_array();

				if (count($precio)==0) {
					$lista[$key]["precio"] = 0.00; $codunidad = 0;
				}else{
					$lista[$key]["precio"] = number_format(round($precio[0]["pventapublico"],2) ,2); 
					$codunidad = $precio[0]["codunidad"];
				}

				$stock = $this->db->query("select pu.stockactual,u.descripcion as unidad from almacen.productoubicacion as pu inner join almacen.unidades as u on(pu.codunidad=u.codunidad) where pu.codproducto=".$value["codproducto"]." and pu.codunidad=".$codunidad." and pu.codalmacen=".$_SESSION["netix_codalmacen"]." and pu.estado=1")->result_array();
				if (count($stock)==0) {
					$lista[$key]["stock"] = 0; $lista[$key]["unidad"] = "SIN UNIDAD";
				}else{
					$lista[$key]["stock"] = round($stock[0]["stockactual"],2); $lista[$key]["unidad"] =$stock[0]["unidad"];
				}
			}

			$total = $this->db->query("select count(*) as total from almacen.productos as productos inner join almacen.marcas as marcas on(productos.codmarca=marcas.codmarca) where (UPPER(productos.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(productos.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(marcas.descripcion) like UPPER('%".$this->request->buscar."%') ) and productos.estado=1")->result_array();

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

	function buscar_productos(){
		if ($this->input->is_ajax_request()) {
			if (isset($_POST["q"])) {
				$productos = $this->db->query("select producto.codproducto, producto.codigo, producto.descripcion, marca.descripcion as marca from almacen.productos as producto inner join almacen.marcas as marca on (producto.codmarca=marca.codmarca) where (REPLACE(UPPER(producto.descripcion),' ','%') like REPLACE (UPPER('%".$_POST["q"]."%'),' ','%') or UPPER(producto.codigo) like UPPER('%".$_POST["q"]."%') or UPPER(marca.descripcion) like UPPER('%".$_POST["q"]."%') ) and producto.estado=1 limit 10")->result_array();
			}else{
				$productos = $this->db->query("select producto.codproducto, producto.codigo, producto.descripcion, marca.descripcion as marca from almacen.productos as producto inner join almacen.marcas as marca on (producto.codmarca=marca.codmarca) where producto.estado=1 limit 10")->result_array();
			}
			echo json_encode($productos);
		}
	}

	public function nuevo(){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_usuario"])) {
				$unidades = $this->db->query("select *from almacen.unidades where estado=1 order by descripcion")->result_array();
				$atenciones = $this->db->query("select *from almacen.atenciones where estado=1 order by descripcion")->result_array();
				if($_SESSION["netix_rubro"]==4){
					$this->load->view("almacen/productos/nuevo_perfumeria",compact("unidades","atenciones"));
				}else{
					$this->load->view("almacen/productos/nuevo",compact("unidades","atenciones"));
				}
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function ver($codregistro){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])){
				$info = $this->db->query("select almacen.productos.*, almacen.marcas.descripcion as marca, almacen.familias.descripcion as familia, almacen.lineas.descripcion as linea from almacen.productos inner join almacen.marcas on(almacen.productos.codmarca=almacen.marcas.codmarca) inner join almacen.familias on(almacen.productos.codfamilia=almacen.familias.codfamilia) inner join almacen.lineas on(almacen.productos.codlinea=almacen.lineas.codlinea) where codproducto=".$codregistro)->result_array();

				$unidades = $this->db->query("select almacen.productounidades.*, almacen.unidades.descripcion as unidad from almacen.productounidades inner join almacen.unidades on(almacen.productounidades.codunidad=almacen.unidades.codunidad) where almacen.productounidades.codproducto=".$codregistro." and almacen.productounidades.estado=1 order by almacen.productounidades.factor")->result_array();
				foreach ($unidades as $key => $value) {
					$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();
					$unidades[$key]["stock"] = 0;
					if (count($stock)>0) {
						$unidades[$key]["stock"] = round($stock[0]["stockactual"],2);
					}
				}
				$this->load->view("almacen/productos/ver",compact("info","unidades")); 
			}else{
	            $this->load->view("inicio/505");
	        }
	    }else{
			$this->load->view("inicio/404");
		}
	}

	function guardar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			$campos = ["codfamilia","codlinea","codmarca","codempresa","codigo","descripcion","afectoicbper","codatencion","paraventa","calcular","controlstock","afectoigvcompra","afectoigvventa","caracteristicas"];
			$valores = [
				(int)$this->request->campos->codfamilia,(int)$this->request->campos->codlinea,
				(int)$this->request->campos->codmarca,(int)$_SESSION["netix_codempresa"],
				$this->request->campos->codigo,$this->request->campos->descripcion,
				(int)$this->request->campos->afectoicbper,
				(int)$this->request->campos->codatencion,
				(int)$this->request->campos->paraventa,
				(int)$this->request->campos->calcular,
				(int)$this->request->campos->controlstock,
				(int)$this->request->campos->afectoigvcompra,
				(int)$this->request->campos->afectoigvventa,
				$this->request->campos->caracteristicas
			];

			$campos_1 = ["codproducto","codunidad","codsucursal","factor","preciocompra","preciocosto","pventapublico","pventamin","pventacredito","pventaxmayor","pventaadicional","codigobarra","estado"];

			$this->db->trans_begin();

			if($this->request->campos->codregistro=="") {
				$codproducto = $this->Netix_model->netix_guardar("almacen.productos", $campos, $valores, "true");
				
				$data = array( "codigo" => "000".$codproducto);
				$this->db->where("codproducto", $codproducto);
				$estado = $this->db->update("almacen.productos", $data);

				if (isset($this->request->unidades)) {
					foreach ($this->request->unidades as $key => $value) {
						$valores_1 = [
							(int)$codproducto,(int)$this->request->unidades[$key]->codunidad,
							(int)$_SESSION["netix_codsucursal"],
							(int)$this->request->unidades[$key]->factor,
							(double)$this->request->unidades[$key]->preciocompra,
							(double)$this->request->unidades[$key]->preciocompra,
							(double)$this->request->unidades[$key]->pventapublico,
							(double)$this->request->unidades[$key]->pventamin,
							(double)$this->request->unidades[$key]->pventacredito,
							(double)$this->request->unidades[$key]->pventaxmayor,
							(double)$this->request->unidades[$key]->pventaadicional,
							$this->request->unidades[$key]->codigobarra,1
						];
						$estado = $this->Netix_model->netix_guardar("almacen.productounidades", $campos_1, $valores_1);
					}
				}
			}else{
				$codproducto = $this->request->campos->codregistro;
				$estado = $this->Netix_model->netix_editar("almacen.productos", $campos, $valores, "codproducto", $codproducto);

				$campos_2 = ["estado"]; $valores_2 = [0]; $f = ["codproducto"]; $v = [$codproducto];
				$estado = $this->Netix_model->netix_editar_1("almacen.productounidades",$campos_2,$valores_2,$f,$v);

				if (isset($this->request->unidades)) {
					foreach ($this->request->unidades as $key => $value) {
						$valores_1 = [
							(int)$codproducto,(int)$this->request->unidades[$key]->codunidad,(int)$_SESSION["netix_codsucursal"],
							(int)$this->request->unidades[$key]->factor,
							(double)$this->request->unidades[$key]->preciocompra,
							(double)$this->request->unidades[$key]->preciocompra,
							(double)$this->request->unidades[$key]->pventapublico,
							(double)$this->request->unidades[$key]->pventamin,
							(double)$this->request->unidades[$key]->pventacredito,
							(double)$this->request->unidades[$key]->pventaxmayor,
							(double)$this->request->unidades[$key]->pventaadicional,
							$this->request->unidades[$key]->codigobarra,1
						];

						$existe = $this->db->query("select *from almacen.productounidades where codproducto=".$codproducto." and codunidad=".$this->request->unidades[$key]->codunidad)->result_array();

						if (count($existe)==0) {
							$estado = $this->Netix_model->netix_guardar("almacen.productounidades", $campos_1, $valores_1);
						}else{
							$f = ["codproducto","codunidad"]; 
							$v = [(int)$codproducto,$this->request->unidades[$key]->codunidad];
							$estado = $this->Netix_model->netix_editar_1("almacen.productounidades", $campos_1, $valores_1, $f, $v);
						}
					}
				}
			}

			// REGISTRO EN LA TABLA PRODUCTOS UBICACION //

			if (isset($this->request->unidades)) {
				foreach ($this->request->unidades as $key => $value) {
					$existe_ubi = $this->db->query("select *from almacen.productoubicacion where codalmacen=".$_SESSION["netix_codalmacen"]." and codproducto=".$codproducto." and codunidad=".$this->request->unidades[$key]->codunidad)->result_array();

					if(count($existe_ubi)==0){
						$campos = ["codalmacen","codproducto","codunidad","codsucursal"];
						$valores =[
							(int)$_SESSION["netix_codalmacen"],
							(int)$codproducto,(int)$this->request->unidades[$key]->codunidad,
							(int)$_SESSION["netix_codsucursal"]
						];
						$estado = $this->Netix_model->netix_guardar("almacen.productoubicacion", $campos, $valores);
					}
				}
			}

			if ($this->db->trans_status() === FALSE){
			    $this->db->trans_rollback(); $estado = 0;
			}else{
				$this->db->trans_commit();
				$estado = $codproducto;
			}

			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function guardar_foto(){
		if ($this->input->is_ajax_request()) {
			$estado = 1;
			if ($_FILES["foto"]["name"]!="") {
				$file = $this->input->post("codproducto")."_".substr($_FILES["foto"]["name"],-5);
				move_uploaded_file($_FILES["foto"]["tmp_name"],"./public/img/productos/".$file);
				chmod("./public/img/productos/".$file, 0777);
				
				$data = array("foto" => $file);
				$this->db->where("codproducto", $this->input->post("codproducto"));
				$estado = $this->db->update("almacen.productos",$data);
			}
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}

	function editar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$info = $this->db->query("select codproducto as codregistro,* from almacen.productos where codproducto=".$this->request->codregistro)->result_array();
			echo json_encode($info);
		}else{
			$this->load->view("netix/404");
		}
	}

	function unidades(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$unidades = $this->db->query("select pu.*,u.descripcion as unidad from almacen.productounidades as pu inner join almacen.unidades as u on(pu.codunidad=u.codunidad) where pu.codproducto=".$this->request->codregistro." and pu.estado=1 order by pu.factor asc")->result_array();
			$campos = $this->db->query("select codfamilia,codlinea,codmarca from almacen.productos where codproducto=".$this->request->codregistro)->result_array();

			$data["unidades"] = $unidades;
			$data["campos"] = $campos;
			echo json_encode($data);
		}else{
			$this->load->view("netix/404");
		}
	}

	function unidades_venta($codproducto, $factor){
		if ($this->input->is_ajax_request()) {
			$unidades = $this->db->query("select u.codunidad,u.descripcion,pu.factor from almacen.productounidades as pu inner join almacen.unidades as u on(pu.codunidad=u.codunidad) where pu.codproducto=".$codproducto." and pu.factor<".$factor." order by pu.factor asc")->result_array();
			echo json_encode($unidades);
		}
	}

	function eliminar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$estado = $this->Netix_model->netix_eliminar("almacen.productos", "codproducto", $this->request->codregistro);
			echo $estado;
		}else{
			$this->load->view("netix/404");
		}
	}


	// BUSCAR PRODUCTOS EN COMPRAS, EN VENTAS, EN INGRESOS Y EGRESOS ALMACEN //

	function buscar($operacion){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("almacen/productos/buscar",compact("operacion"));
			}else{
				$this->load->view("netix/505");
			}
		}else{
			$this->load->view("netix/404");
		}
	}

	function buscar_codigobarra($codigobarra){
		if ($this->input->is_ajax_request()) {
			$info = $this->db->query("select p.codproducto,p.descripcion,p.caracteristicas, p.afectoicbper,p.controlstock, p.afectoigvcompra, p.afectoigvventa, p.codigo,p.calcular,p.foto,u.codunidad,u.descripcion as unidad,round(pu.stockactual,3) as stock, m.descripcion as marca, puv.factor, puv.factor as factormaximo, round(puv.pventapublico,2) as precio, round(puv.pventamin,2) as preciomin, round(puv.pventacredito,2) as preciocredito, round(puv.pventaxmayor,2) as preciomayor, round(puv.preciocosto,2) as preciocosto, round(puv.pventaadicional,2) as precioadicional from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) inner join almacen.productounidades as puv on(pu.codproducto=puv.codproducto and pu.codunidad=puv.codunidad) where puv.codigobarra='".$codigobarra."' and p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"])->result_array();
			$data = array(); $precio = 0;
			if (count($info) > 0) {
				$precio = $info[0]["precio"];
			}
			$data["cantidad"] = count($info); $data["info"] = $info; $data["precio"] = (double)$precio;

			echo json_encode($data);
		}
	}

	function buscar_salidas(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$limit = 10; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select p.codproducto,p.descripcion,p.caracteristicas, p.afectoicbper,p.controlstock,p.afectoigvcompra, p.afectoigvventa,  p.codigo,p.calcular,p.foto,u.codunidad,u.descripcion as unidad,round(pu.stockactual,3) as stock,m.descripcion as marca from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"]." order by stock desc offset ".$offset." limit ".$limit)->result_array();

			foreach ($lista as $key => $value) {
				$factormaximo = $this->db->query("select max(factor) as factor from almacen.productounidades where codproducto=".$value["codproducto"]." and estado=1")->result_array();

				$precio = $this->db->query("select factor,pventapublico,pventamin,pventacredito,pventaxmayor, preciocosto,pventaadicional from almacen.productounidades where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and estado=1")->result_array();
				if (count($precio)==0) {
					$lista[$key]["factor"] = 0; $lista[$key]["factormaximo"] = 0;
					$lista[$key]["precio"] = 0.00; $lista[$key]["preciomin"] = 0.00; $lista[$key]["preciocredito"] = 0.00;
					$lista[$key]["preciomayor"] = 0.00; $lista[$key]["preciocosto"] = 0.00; $lista[$key]["precioadicional"] = 0.00;
				}else{
					$lista[$key]["factor"] = $precio[0]["factor"]; $lista[$key]["factormaximo"] = $factormaximo[0]["factor"];
					$lista[$key]["precio"] = round($precio[0]["pventapublico"],2);
					$lista[$key]["preciomin"] = round($precio[0]["pventamin"],2);
					$lista[$key]["preciocredito"] = round($precio[0]["pventacredito"],2);
					$lista[$key]["preciomayor"] = round($precio[0]["pventaxmayor"],2);
					$lista[$key]["preciocosto"] = round($precio[0]["preciocosto"],2);
					$lista[$key]["precioadicional"] = round($precio[0]["pventaadicional"],2);
				}
			}

			$total = $this->db->query("select count(*) as total from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where (UPPER(p.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"])->result_array();

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

	function buscar_ingresos(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));
			$limit = 10; $offset = $this->request->pagina * $limit - $limit;

			$lista = $this->db->query("select p.codproducto,p.descripcion,p.caracteristicas, p.afectoicbper,p.controlstock, p.afectoigvcompra,p.afectoigvventa, p.codigo, p.calcular, p.foto,u.codunidad,u.descripcion as unidad,pu.factor,round(pu.preciocosto,2) as precio,m.descripcion as marca from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1 order by p.codproducto desc offset ".$offset." limit ".$limit)->result_array();

			foreach ($lista as $key => $value) {
				$stock = $this->db->query("select stockactual from almacen.productoubicacion where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and codalmacen=".$_SESSION["netix_codalmacen"]." and estado=1")->result_array();
				if (count($stock)==0) {
					$lista[$key]["stock"] = 0;
				}else{
					$lista[$key]["stock"] = round($stock[0]["stockactual"],2);
				}
			}

			$total = $this->db->query("select count(*) as total from almacen.productos as p inner join almacen.productounidades as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) where p.controlstock=1 and (UPPER(p.descripcion) like UPPER('%".$this->request->buscar."%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') ) and p.estado=1 and pu.estado=1")->result_array();

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


	function restobar($codlinea){
		if ($this->input->is_ajax_request()) {
			if (isset($_SESSION["netix_codusuario"])) {
				$this->load->view("almacen/productos/restaurant",compact("codlinea"));
			}
		}
	}

	function buscando_restobar(){
		if ($this->input->is_ajax_request()) {
			$this->request = json_decode(file_get_contents('php://input'));

			if ($this->request->codlinea==0) {
				$linea = "";
			}else{
				$linea = "p.codlinea=".$this->request->codlinea." and ";
			}

			$lista = $this->db->query("select p.codproducto,p.codigo,p.descripcion,p.controlstock,p.afectoigvcompra,p.afectoigvventa, p.codigo, p.calcular, p.foto, u.codunidad,u.descripcion as unidad,round(pu.stockactual,3) as stock,
				(select coalesce(sum(pd.cantidad),0) from kardex.pedidos as pedi inner join kardex.pedidosdetalle as pd on(pedi.codpedido=pd.codpedido) where pedi.estado=1 and pu.codproducto=pd.codproducto and pu.codunidad=pd.codunidad) as comprometido, m.descripcion as marca,l.background,l.color from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) inner join almacen.marcas as m on(p.codmarca=m.codmarca) inner join almacen.lineas as l on(p.codlinea=l.codlinea) where ".$linea." (REPLACE(UPPER(p.descripcion),' ','%') like REPLACE (UPPER('%".$this->request->buscar."%'),' ','%') or UPPER(p.codigo) like UPPER('%".$this->request->buscar."%') or UPPER(m.descripcion) like UPPER('%".$this->request->buscar."%') ) and p.paraventa=1 and p.estado=1 and pu.estado=1 and pu.codalmacen=".$_SESSION["netix_codalmacen"]." order by p.codproducto desc")->result_array();

			foreach ($lista as $key => $value) {
				$factormaximo = $this->db->query("select max(factor) as factor from almacen.productounidades where codproducto=".$value["codproducto"]." and estado=1")->result_array();

				$precio = $this->db->query("select factor,pventapublico,pventamin,pventacredito,pventaxmayor, preciocosto,pventaadicional from almacen.productounidades where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and estado=1")->result_array();
				$lista[$key]["mostrarstock"] = "STOCK: ".round($value["stock"] - $value["comprometido"],3);
				$lista[$key]["stockdisponible"] = round($value["stock"] - $value["comprometido"],3);
				if (count($precio)==0) {
					$lista[$key]["factor"] = 0; $lista[$key]["factormaximo"] = 0;
					$lista[$key]["precio"] = 0.00; $lista[$key]["preciomin"] = 0.00; $lista[$key]["preciocredito"] = 0.00;
					$lista[$key]["preciomayor"] = 0.00; $lista[$key]["preciocosto"] = 0.00; $lista[$key]["precioadicional"] = 0.00;
				}else{
					$lista[$key]["factor"] = $precio[0]["factor"]; $lista[$key]["factormaximo"] = $factormaximo[0]["factor"];
					$lista[$key]["precio"] = round($precio[0]["pventapublico"],2);
					$lista[$key]["preciomin"] = round($precio[0]["pventamin"],2);
					$lista[$key]["preciocredito"] = round($precio[0]["pventacredito"],2);
					$lista[$key]["preciomayor"] = round($precio[0]["pventaxmayor"],2);
					$lista[$key]["preciocosto"] = round($precio[0]["preciocosto"],2);
					$lista[$key]["precioadicional"] = round($precio[0]["pventaadicional"],2);
				}
			}

			echo json_encode($lista);
		}
	}

	function producto_tipopedido($codproducto){
		if ($this->input->is_ajax_request()) {
			$lista = $this->db->query("select p.codproducto,p.descripcion,u.codunidad,u.descripcion as unidad from almacen.productos as p inner join almacen.productoubicacion as pu on(p.codproducto=pu.codproducto) inner join almacen.unidades as u on(u.codunidad=pu.codunidad) where p.codproducto=".$codproducto." and pu.estado=1")->result_array();
			foreach ($lista as $key => $value) {
				$lista[$key]["stock"] = 0; $lista[$key]["control"] = 0; $lista[$key]["calcular"] = 0;

				$precio = $this->db->query("select pventapublico from almacen.productounidades where codproducto=".$value["codproducto"]." and codunidad=".$value["codunidad"]." and estado=1")->result_array();
				if (count($precio)==0) {
					$lista[$key]["precio"] = 0.00;
				}else{
					$lista[$key]["precio"] = round($precio[0]["pventapublico"],2);
				}
			}
			echo json_encode($lista);
		}
	}
}