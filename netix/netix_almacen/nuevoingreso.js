var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		campos:{
			"codmovimientotipo":"","codcomprobantetipo":"","seriecomprobante":"","codalmacen_ref":"",
			"codcomprobantetipo_ref":0,"seriecomprobante_ref":"","nrocomprobante_ref":"","descripcion":"","fechakardex":""
		},
		estado:0, porcigv:$("#porcigv").val(), detalle: [], totales: {"valorventa":0.00,"igv":0.00,"importe":0.00},
	},
	methods: {
		netix_item: function(){
			$(".compose").slideToggle(); $("#netix_tituloform").text("BUSCAR PRODUCTO"); 
			netix_sistema.netix_loader("netix_formulario",180); 

			this.$http.post(url+"almacen/productos/buscar/compras").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_modulo();
			});
		},
		netix_additem: function(producto){
			var existeproducto = this.detalle.filter(function(p){
			    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
			    	p.cantidad = p.cantidad + 1; return p;
			    };
			});

		    if (existeproducto.length==0) {
				this.detalle.push({
					"codproducto":producto.codproducto,"producto":producto.descripcion,"codunidad":producto.codunidad,
					"unidad":producto.unidad,"cantidad":1,"stock":producto.stock,"control":producto.controlstock,"precio":producto.precio,
					"preciorefunitario":producto.precio,"subtotal":producto.precio
				});
				this.netix_calcular(producto,1);
		    }else{
		    	this.netix_calcular(existeproducto[0],3);
		    }
		},
		netix_deleteitem: function(index,producto){
			this.netix_calcular(producto,2); this.detalle.splice(index,1);
		},
		netix_calcular: function(producto,tipo){
			if (tipo==1) {
				this.totales.valorventa = Number((this.totales.valorventa + parseFloat(producto.precio) ).toFixed(2));
			}else{
				if (tipo==2) {
					this.totales.valorventa = Number((this.totales.valorventa - producto.subtotal).toFixed(2));
				}else{
					this.totales.valorventa = Number((this.totales.valorventa - producto.subtotal).toFixed(2));
					producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
					this.totales.valorventa = Number((this.totales.valorventa + producto.subtotal).toFixed(2));
				}
			}
			this.totales.importe = Number((this.totales.valorventa + this.totales.igv).toFixed(2));
		},
		netix_guardar: function(){
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE","REGISTRAR ITEM PARA EL INGRESO","error"); 
				return false;
			}

			this.campos.fechakardex = $("#fechakardex").val();
			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO INGRESO DE ALMACEN . . .");
			
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle,"totales":this.totales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("INGRESO DE ALMACEN REGISTRADO","INGRESO DE ALMACEN EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR INGRESO DE ALMACEN","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR INGRESO DE ALMACEN","ERROR DE RED","error");
			});
		},
		netix_cerrar: function(){
			netix_sistema.netix_modulo();
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});