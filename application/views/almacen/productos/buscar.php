<div id="netix_buscar">
	<div style="padding:10px 0px; height:53px; border-bottom: 2px solid #f3f3f3;">
		<div class="col-md-10 col-xs-10">
			<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR PRODUCTO . . ." v-bind:autofocus="true">
		</div>
		<div class="col-md-2 col-xs-2">
			<button type="button" class="btn btn-block btn-success" v-on:click="netix_nuevoproducto()">
				<i class="fa fa-shopping-cart"></i> <i class="fa fa-plus-circle"></i>
			</button>
		</div>
	</div>

	<div class="col-xs-12">
		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>
		<div class="row" v-if="!cargando">
			<table class="table table-striped projects">
				<tbody>
					<tr v-for="dato in productos">
						<td style="width:20%;cursor:pointer;" v-on:click="netix_seleccionado(dato)">
							<ul class="list-inline">
								<li> <img v-bind:src="`<?php echo base_url();?>public/img/productos/${dato.foto}`" style="height:40px;width:100%"> </li>
							</ul>
						</td>
						<td style="width:60%;cursor:pointer;" v-on:click="netix_seleccionado(dato)">
							<a>{{dato.descripcion}}</a> <br> 
							<b style="color:#13a89e" v-if="dato.stock>0">STOCK {{dato.stock}} {{dato.unidad}}</b>
							<b style="color:#d43f3a" v-if="dato.stock<=0">STOCK {{dato.stock}} {{dato.unidad}}</b> 
							<span class="label label-warning">C: {{dato.codigo}}</span> <br> 
							<small>MARCA: {{dato.marca}} CARACT. {{dato.caracteristicas}}</small>
						</td>
						<td style="width:20%;" align="center">
							<b style="font-size:20px;" v-if="rubro==4">S/. {{dato.preciocosto}}</b>
							<b style="font-size:20px;" v-else="rubro!=4">S/. {{dato.precio}}</b>

							<button type="button" v-if="verprecios==1" v-on:click="netix_masprecios(dato)" class="btn btn-success btn-xs"> <b>MAS PRECIOS</b> </button>
							<div v-if="rubro==2">
								<button type="button" v-if="dato.factor>1" v-on:click="netix_salida(dato)" class="btn btn-danger btn-xs"> <b>DAR SALIDA</b> </button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div class="col-md-12 col-xs-12" align="center">
		<ul class="pagination">
			<li class="page-item disabled" v-if="paginacion.actual <= 1">
		    	<a class="page-link"> <i class="fa fa-angle-left"></i> ATRAS </a> 
		    </li>
		    <li class="page-item" v-if="paginacion.actual > 1">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(paginacion.actual - 1)"> 
		    		<i class="fa fa-angle-left"></i> ATRAS 
		    	</a> 
		    </li>

		    <li class="page-item" v-for="pag in netix_paginas" v-bind:class="[pag==netix_actual ? 'active':'']">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(pag)">{{pag}}</a> 
		    </li>

		    <li class="page-item" v-if="paginacion.actual < paginacion.ultima">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(paginacion.actual + 1)"> 
		    		SIGUE <i class="fa fa-angle-right"></i> 
		    	</a> 
		    </li>
		    <li class="page-item disabled" v-if="paginacion.actual >= paginacion.ultima">
		    	<a class="page-link"> SIGUE <i class="fa fa-angle-right"></i> </a> 
		    </li>
		</ul>
	</div>

	<div id="modal_precios" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header modal-netix-titulo"> 
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;">MAS PRECIOS DEL PRODUCTO</b> </h4> 
				</div>
				<div class="modal-body text-center" style="height:350px;">
					<h5>
						<b>PRODUCTO: {{masprecios.producto}} &nbsp; <span class="label label-warning">U.M. {{masprecios.unidad}}</span></b>
					</h5> <hr>
					
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA PUBLICO</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.precio)"> 
								<b style="font-size:18px;">S/. {{masprecios.precio}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA MINIMO</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.preciomin)">
								<b style="font-size:18px;">S/. {{masprecios.preciomin}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA CREDITO</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.preciocredito)">
								<b style="font-size:18px;">S/. {{masprecios.preciocredito}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA X MAYOR</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.preciomayor)">
								<b style="font-size:18px;">S/. {{masprecios.preciomayor}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO DE COSTO</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.preciocosto)">
								<b style="font-size:18px;">S/. {{masprecios.preciocosto}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO ADICIONAL</b> </h4> 
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_seleccionado_1(masprecios.precioadicional)">
								<b style="font-size:18px;">S/. {{masprecios.precioadicional}}</b> 
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_salidas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;">SALIDA DE STOCK</b> </h4> 
				</div>
				<div class="modal-body" style="height: 410px;">
					<h4 align="center">
						{{salida.producto}} <br> <br> <span class="label label-warning">STOCK: {{salida.stock}} {{salida.unidad}} </span> 
					</h4> <hr>

					<div class="row">
						<div class="col-md-6"> <label align="center">FECHA KARDEX Y COMPROBANTE</label> </div>
						<div class="col-md-6"> <input type="text" class="form-control input-sm datepicker" id="fechakardex_salida" value="<?php echo date('Y-m-d');?>"> </div>
					</div> <br>

					<div class="row">
						<div class="col-md-6"> <label align="center">CANTIDAD SALIDA {{salida.unidad}}</label> </div>
						<div class="col-md-6"> <input type="number" class="form-control number" min="0" step="0.01" v-model="salida.cantidad" v-on:keyup="netix_unidadingreso()"> </div>
					</div> <hr>

					<div class="row">
						<div class="col-md-6"> <label align="center">UNIDAD A CONVERTIR</label> </div>
						<div class="col-md-6"> 
							<select class="form-control number" id="codunidad_ingreso" v-model="salida.codunidad_ingreso" v-on:change="netix_unidadingreso()">
								<option value="0">SELECCIONE</option>
								<option v-for="dato in unidades" v-bind:value="dato.codunidad"> {{dato.descripcion}} </option>
							</select>
						</div>
					</div>

					<h5 class="text-center"> <b>TOTAL INGRESO: {{salida.cantidadingreso}}</b> </h5>
					<button type="button" class="btn btn-success btn-block btn-salida" v-on:click="netix_guardarsalida()">GUARDAR OPERACION DE STOCK</button>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var netix_buscar = new Vue({
		el: "#netix_buscar",
		data: {
			cargando: true, buscar: "", rubro:"<?php echo $_SESSION['netix_rubro'];?>", verprecios:1, 
			productos:[], unidades:[], productoprecio:{},
			masprecios: {
				producto:"", unidad:"", precio:0, preciomin:0, preciocredito:0, preciomayor:0, preciocosto:0, precioadicional:0
			},
			salida: {
				producto:"", unidad:"", codproducto:0, codunidad:0, factor:0, preciocosto:0, stock:0, cantidad:1, fechakardex:"", codunidad_ingreso:0, factor_ingreso:0, cantidadingreso:0
			},
			paginacion: {"total":0, "actual":1, "ultima":0, "desde":0, "hasta":0}, offset: 3
		},
		computed: {
			netix_actual: function(){
				return this.paginacion.actual;
			},
			netix_paginas: function(){
				if (!this.paginacion.hasta) {
					return [];
				}
				var desde = this.paginacion.actual - this.offset;
				if (desde < 1) {
					desde = 1;
				}
				var hasta = desde + (this.offset * 2);
				if (hasta >= this.paginacion.ultima) {
					hasta = this.paginacion.ultima;
				}

				var paginas = [];
				while(desde <= hasta){
					paginas.push(desde); desde++;
				}
				return paginas;
			}
		},
		methods: {
			netix_nuevoproducto : function(){
				netix_sistema.netix_loader("netix_formulario",180);
				this.$http.post(url+"almacen/productos/nuevo").then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_error();
				});
			},
			netix_productos: function(){
				var buscar = "buscar_salidas";
				if (netix_controller=="almacen/ingresos" || netix_controller=="almacen/salidas" || netix_controller=="compras/compras") {
					var buscar = "buscar_ingresos"; this.verprecios = 0;
				}

				this.cargando = true;
				this.$http.post(url+"almacen/productos/"+buscar,{"buscar":this.buscar,"pagina":this.paginacion.actual}).then(function(data){
					this.productos = data.body.lista; this.paginacion = data.body.paginacion; this.cargando = false;
				},function(){
					netix_sistema.netix_error(); this.cargando = false;
				});
			},
			netix_buscar: function(){
				this.paginacion.actual = 1; this.netix_productos();
			},
			netix_paginacion: function(pagina){
				this.paginacion.actual = pagina; this.netix_productos();
			},
			netix_seleccionado: function(producto){
				netix_operacion.netix_additem(producto, producto.precio);
			},
			netix_masprecios:function(producto){
				this.masprecios.producto = producto.descripcion; this.masprecios.unidad = producto.unidad;
				this.masprecios.precio = producto.precio; this.masprecios.preciomin = producto.preciomin; 
				this.masprecios.preciocredito = producto.preciocredito; this.masprecios.preciomayor = producto.preciomayor;
				this.masprecios.preciocosto = producto.preciocosto; this.masprecios.precioadicional = producto.precioadicional;

				this.productoprecio = producto; $("#modal_precios").modal("show");
			},
			netix_seleccionado_1: function(precio){
				netix_operacion.netix_additem(this.productoprecio,precio); $("#modal_precios").modal("hide");
			},
			netix_salida:function(producto){
				this.salida.producto = producto.descripcion; this.salida.unidad = producto.unidad;
				this.salida.codproducto = producto.codproducto;
				this.salida.codunidad = producto.codunidad;
				this.salida.factor = producto.factor;
				this.salida.preciocosto = producto.preciocosto;
				this.salida.stock = producto.stock;
				this.salida.cantidad = 1;
				this.salida.codunidad_ingreso = 0;
				this.salida.factor_ingreso = 0;
				this.salida.cantidadingreso = 0;

				this.$http.get(url+"almacen/productos/unidades_venta/"+producto.codproducto+"/"+producto.factor).then(function(data){
					this.unidades = data.body;

					$(".btn-salida").html("GUARDAR OPERACION DE STOCK").removeAttr("disabled"); 
					$("#modal_salidas").modal({backdrop: 'static', keyboard: false});
				});
			},
			netix_unidadingreso: function(){
				that = this;
				var existe_factor = this.unidades.filter(function(u){
				    if(u.codunidad == that.salida.codunidad_ingreso){
				    	that.salida.factor_ingreso = u.factor; return u;
				    };
				});
				this.salida.cantidadingreso = 0;
				if (this.salida.factor_ingreso>0) {
					this.salida.cantidadingreso = this.salida.cantidad * this.salida.factor / this.salida.factor_ingreso;
				}
			},
			netix_guardarsalida: function(){
				if ($("#codunidad_ingreso").val()==0 || $("#codunidad_ingreso").val()=="") {
					netix_sistema.netix_alerta("SELECCIONE UNIDAD MEDIDA A CONVERTIR","","error"); return false;
				}
				if (this.salida.cantidad=="" || this.salida.cantidad<1) {
					netix_sistema.netix_alerta("INGRESAR LA CANTIDAD A DAR SALIDA","","error"); return false;
				}
				if (parseFloat(this.salida.stock)<parseFloat(this.salida.cantidad)) {
					netix_sistema.netix_alerta("LA CANTIDAD EN STOCK SOLO ES "+this.salida.stock+" "+this.salida.unidad,"","error");
				}else{
					this.salida.fechakardex = $("#fechakardex_salida").val();
					$(".btn-salida").html("<i class='fa fa-spinner fa-spin'></i> GUARDANDO OPERACION").attr("disabled","true");
					this.$http.post(url+"almacen/salidas/guardar_operacionstock",this.salida).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("OPERACION GUARDADA CORRECTAMENTE","","success");
							this.netix_productos();
						}else{
							netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
						}
						$("#modal_salidas").modal("hide");
					},function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
						$("#modal_salidas").modal("hide");
					});
				}
			},
			netix_cerrar: function(){
				$(".compose").slideToggle();
			}
		},
		created: function(){
			this.netix_productos();
		}
	});
</script>

<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true"); </script>