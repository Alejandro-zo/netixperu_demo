var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		estado:0, sinstockventa:0, itemrepetirventa:0, porcigv:0, detalle: [],
		campos:{
			"codpersona":$("#codpersona").val(),"retirar":true,"fechapedido":"","codempleado":0,"porcdescuento":0.00,
			"descripcion":"REGISTRO POR PEDIDO","cliente":$("#persona").val(),"direccion":"-",
			"afectastock":$("#stockalmacen").val(),"afectacaja":1,"descripcion":"PEDIDO DE VENTA"
		},
		itemdetalle:{"producto":"","unidad":"","descripcion":""},
		totales: {"subtotal":0.00,"descuentos":0.00,"descuentoglobal":0.00,"igv":0.00,"interes":0,"importe":0.00},
	},
	methods: {
		netix_atras: function(){
			netix_sistema.netix_modulo();
		},

		// DETALLE DEL PEDIDO Y TOTALES //

		netix_item: function(){
			$(".compose").slideToggle(); $("#netix_tituloform").text("BUSCAR PRODUCTO"); 
			netix_sistema.netix_loader("netix_formulario",180); 

			this.$http.post(url+"almacen/productos/buscar/ventas").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_modulo();
			});
		},
		netix_additem: function(producto){
			if (this.itemrepetirventa==0) {
				var existeproducto = this.detalle.filter(function(p){
				    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
				    	p.cantidad = p.cantidad + 1; return p;
				    };
				});
			}else{
				var existeproducto = [];
			}

			var preciodescuento = producto.preciocosto; var preciocatalogo = producto.precio;

		    if (existeproducto.length==0 || this.itemrepetirventa==1) {
		    	var afectacionigv = 20; var igv = 0;
				if (producto.igvventa==1) {
					afectacionigv = 10; igv = preciodescuento * this.porcigv / 100; igv = Number((igv).toFixed(2));
				}

				this.detalle.push({
					"codproducto":producto.codproducto,"producto":producto.descripcion,"codunidad":producto.codunidad,
					"unidad":producto.unidad,"cantidad":1,"stock":producto.stock,"control":producto.control,"precio":preciodescuento,
					"preciorefunitario":preciocatalogo,"descuentototal":0,"descuento":0,"codafectacionigv":afectacionigv,
					"calcular":producto.calcular,"igv":igv,"subtotal":preciodescuento,"subtotal_tem":preciodescuento,"descripcion":""
				});
				producto.precio = preciodescuento;
				this.netix_calcular(producto,1);
		    }else{
		    	this.netix_calcular(existeproducto[0],3);
		    }
		},
		netix_additem_precio: function(producto,precio){
			if (this.itemrepetirventa==0) {
				var existeproducto = this.detalle.filter(function(p){
				    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
				    	p.precio = precio; return p;
				    };
				});
			}else{
				var existeproducto = [];
			}

		    if (existeproducto.length==0 || this.itemrepetirventa==1) {
		    	var afectacionigv = 20; var igv = 0;
				if (producto.igvventa==1) {
					afectacionigv = 10; igv = producto.precio * this.porcigv / 100; igv = Number((igv).toFixed(2));
				}

				this.detalle.push({
					"codproducto":producto.codproducto,"producto":producto.descripcion,"codunidad":producto.codunidad,
					"unidad":producto.unidad,"cantidad":1,"stock":producto.stock,"control":producto.control,"precio":precio,
					"preciorefunitario":preciocosto,"descuentototal":0,"descuento":0,"codafectacionigv":afectacionigv,
					"calcular":producto.calcular,"igv":igv,"subtotal":precio,"subtotal_tem":precio,"descripcion":""
				});
				this.netix_calcular(producto,1);
		    }else{
		    	this.netix_calcular(existeproducto[0],3);
		    }
		},
		netix_itemdetalle: function(index,producto){
			this.itemdetalle = producto; $("#modal_itemdetalle").modal("show");
		},
		netix_cerrar_itemdetalle: function(){
			$("#modal_itemdetalle").modal("hide");
		},
		netix_deleteitem: function(index,producto){
			this.netix_calcular(producto,2); this.detalle.splice(index,1);
		},
		netix_calcular: function(producto,tipo){
			var igv = 0;
			if (tipo==1) {
				if (producto.codafectacionigv==10) {
					igv = producto.precio * this.porcigv / 100; igv = Number((igv).toFixed(2));
				}
				
				this.totales.subtotal = Number((this.totales.subtotal + producto.precio).toFixed(2));
				this.totales.igv = Number((this.totales.igv + igv).toFixed(2));
			}else{
				if (producto.codafectacionigv==10) {
					igv = producto.cantidad * producto.precio * this.porcigv / 100; igv = Number((igv).toFixed(2));
				}

				if (tipo==2) {
					this.totales.subtotal = Number((this.totales.subtotal - producto.subtotal).toFixed(2));
					this.totales.igv = Number((this.totales.igv - producto.igv).toFixed(2));
				}else{
					this.totales.descuentos = Number((this.totales.descuentos - producto.descuentototal).toFixed(2));
					producto.descuentototal = producto.descuento;
					this.totales.descuentos = Number((this.totales.descuentos + producto.descuentototal).toFixed(2));

					this.totales.igv = Number((this.totales.igv - producto.igv).toFixed(2));
					producto.igv = Number((igv).toFixed(2));
					this.totales.igv = Number((this.totales.igv + producto.igv).toFixed(2));

					this.totales.subtotal = Number((this.totales.subtotal - producto.subtotal).toFixed(2));
					producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
					this.totales.subtotal = Number((this.totales.subtotal + producto.subtotal).toFixed(2));

					producto.subtotal_tem = producto.subtotal;
				}
			}

			this.totales.importe = Number((this.totales.subtotal + this.totales.igv - this.totales.descuentos).toFixed(2));
		},
		netix_subtotal: function(producto){
			this.totales.subtotal = Number((this.totales.subtotal - producto.subtotal_tem).toFixed(2));

			// SI producto.calcular = 1 calcula cantidad, producto.calcular = 2 calcula precio //
			if (producto.calcular==1) {
				if (producto.precio!=0) {
					producto.cantidad = Number((producto.subtotal / producto.precio).toFixed(3));
				}
			}else{
				if (producto.cantidad!=0) {
					producto.precio = Number((producto.subtotal / producto.cantidad).toFixed(2));
				}
			}
			
			if (producto.subtotal=="") {
				producto.subtotal_tem = 0;
			}else{
				producto.subtotal_tem = producto.subtotal;
			}

			var igv = 0;
			if (producto.codafectacionigv==10) {
				igv = producto.cantidad * producto.precio * this.porcigv / 100; igv = Number((igv).toFixed(2));
			}
			this.totales.igv = Number((this.totales.igv - producto.igv).toFixed(2));
			producto.igv = Number((igv).toFixed(2));
			this.totales.igv = Number((this.totales.igv + producto.igv).toFixed(2));

			this.totales.subtotal = Number((this.totales.subtotal + producto.subtotal_tem).toFixed(2));
			this.totales.importe = Number((this.totales.subtotal + this.totales.igv - this.totales.descuentos).toFixed(2));
		},

		// DATOS GENERALES DE LA VENTA //

		netix_guardar: function(){
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE", "REGISTRAR ITEM PARA EL PEDIDO","error"); return false;
			}

			netix_sistema.netix_inicio_guardar("GUARDANDO PEDIDO . . ."); this.campos.fechapedido = $("#fechapedido").val();
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle,"totales":this.totales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						swal({
							title: "DESEA IMPRIMIR EL PEDIDO ?",   
							text: "DESEA IMPRIMIR EL PEDIDO REGISTRADO", 
							icon: "warning",
							dangerMode: true,
							buttons: ["CANCELAR", "SI, IMPRIMIR"],
						}).then((willDelete) => {
							if (willDelete){
								// this.netix_imprimir(data.body.codpedido);
								netix_sistema.netix_noti("FALTA CONFIGURAR LA TICKETERA","PEDIDO REGISTRADO EN EL SISTEMA","success");
							}
						});
						netix_sistema.netix_noti("PEDIDO REGISTRADO CORRECTAMENTE","PEDIDO REGISTRADO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR PEDIDO","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); this.netix_atras();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR PEDIDO","ERROR DE RED","error");
				netix_sistema.netix_fin(); this.netix_atras();
			});
		},

		netix_imprimir: function(codpedido){
			window.open(url+"facturacion/formato/ticket/"+codpedido,"_blank");
            // var netix_url = url+netix_controller+"/imprimir/"+codpedido;
            // $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
        }
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});