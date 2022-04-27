<div id="netix_buscar">
	<div class="x_header" style="margin-bottom:10px;">
		<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR PRODUCTO, PLATO O BEBIDA . . ." autocomplete="off" style="border:2px solid #d43f3a !important;">
	</div>

	<div class="netix_cargando" v-if="cargando">
		<i class="fa fa-spinner fa-spin"></i> <h5>CARGANDO DATOS</h5>
	</div>
	<div class="row" v-if="!cargando">
		<div class="col-md-4" v-for="dato in productos" v-on:click="netix_seleccionado(dato)" style="margin-top:5px;">
			<div v-bind:style="{background: dato.background,color: dato.color}" v-bind:title="dato.mostrarstock">
				<div style="padding:4px;text-align:center;">
					<p style="height:30px;font-weight:bold;font-size:10px;">{{dato.descripcion.substring(0,30)}} - {{dato.marca}}</p>
					<b style="font-size:20px;">S/. {{dato.precio}}</b>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_precios" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" align="center">
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;">MAS PRECIOS DEL PRODUCTO</b> </h4> 
				</div>
				<div class="modal-body" style="height: 380px;">
					<h4 align="center">
						{{masprecios.producto}} <br> <br> <span class="label label-warning">UNIDAD: {{masprecios.unidad}}</span> 
					</h4> <hr>
					
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA PUBLICO</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.precio)"> 
								<b style="font-size:18px;">S/. {{masprecios.precio}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA MINIMO</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.preciomin)">
								<b style="font-size:18px;">S/. {{masprecios.preciomin}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA CREDITO</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.preciocredito)">
								<b style="font-size:18px;">S/. {{masprecios.preciocredito}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO VENTA X MAYOR</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.preciomayor)">
								<b style="font-size:18px;">S/. {{masprecios.preciomayor}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO DE COSTO</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.preciocosto)">
								<b style="font-size:18px;">S/. {{masprecios.preciocosto}}</b> 
							</button>
						</div>
					</div>
					<div class="col-md-4">
						<div class="x_panel">
							<h4> <b>PRECIO ADICIONAL</b> </h4> 
							<button type="button" class="btn btn-success btn-sm" v-on:click="netix_seleccionado_1(masprecios.precioadicional)">
								<b style="font-size:18px;">S/. {{masprecios.precioadicional}}</b> 
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var netix_buscar = new Vue({
		el: "#netix_buscar",
		data: {
			cargando: true, buscar: "", buscando: "buscando_restobar", codlinea:"<?php echo $codlinea;?>", verprecios:1, productos:[], productoprecio:{},
			masprecios: {"producto":"", "unidad":"", "precio":0, "preciomin":0, "preciocredito":0, "preciomayor":0, "preciocosto":0, "precioadicional":0},
			salida: {"producto":"","unidad":"","codproducto":0,"codunidad":0,"factor":0,"preciocosto":0,"stock":0,"cantidad":1}
		},
		methods: {
			netix_productos: function(){
				this.cargando = true;
				this.$http.post(url+"almacen/productos/"+this.buscando,{"buscar":this.buscar,"codlinea":this.codlinea}).then(function(data){
					this.productos = data.body; this.codlinea = 0; this.cargando = false;
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error"); 
					this.cargando = false;
				});
			},
			netix_buscar: function(){
				this.netix_productos();
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
				netix_operacion.netix_additem(this.productoprecio,precio, precio); $("#modal_precios").modal("hide");
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