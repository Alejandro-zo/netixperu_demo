var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		estado:0, kardex_id:0, titulo: "REGISTRO NUEVA GUIA", igvsunat:$("#igvsunat").val(), detalle:[], comprobantes: [],
		campos:{
			codguia:0, codkardex_ref:0, coddestinatario:"", fechaemision:"", seriecomprobante:"", codmodalidadtraslado:"", modalidadtraslado:"",
			punto_partida:"", ubigeo_partida:"", punto_llegada:"", ubigeo_llegada:"", tipo_trasporte:"", nroplaca:"", fechatraslado:"", 
			codempresa_traslado:"", dniconductor:"", conductor:"", licencia:"", descripcion:"GUIA DE REMISIÓN", destinatario: "", transportista:"",
			pesototal:0.00, valorguia:0.00, igv:0.00, subtotal:0.00, importe:0.00
		},
		item:{
			producto:"", unidad:"", cantidad:0, preciosinigv:0, precio:0, codafectacionigv:"", igv:0, 
			valorguia:0, subtotal:0, descripcion:""
		}
	},
	methods: {
		netix_atras: function(){
			netix_sistema.netix_modulo();
		},
		netix_addcliente: function(){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180); 
			this.$http.post(url+"ventas/clientes/nuevo_1").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){ 
				netix_sistema.netix_error_operacion(); 
			});
		},
        netix_comprobantes: function(){
        	this.comprobantes = [];
        	if (this.campos.coddestinatario != "" || this.campos.coddestinatario != 0) {
        		this.$http.get(url+"ventas/guias/comprobantes/"+this.campos.coddestinatario+"/"+$("#fecha_desde").val()+"/"+$("#fecha_hasta").val()).then(function(data){
					this.comprobantes = data.body;
				});
        	}
        },
        netix_detalle: function(datos){
        	if (datos.codkardex != this.kardex_id) {
        		$("#"+this.kardex_id).css({"background-color":"#fff","color":"#000"}); this.kardex_id = datos.codkardex;
				$("#"+datos.codkardex).css({"background-color":"#13a89e","color":"#fff"});

				this.$http.get(url+"ventas/guias/detalle/"+datos.codkardex).then(function(data){
					this.detalle = data.body;
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				});
        	}else{
        		$("#"+this.kardex_id).css({"background-color":"#fff","color":"#000"}); this.kardex_id = 0;
				$("#0").css({"background-color":"#13a89e","color":"#fff"});
        	}
		},

		netix_item: function(){
			$(".compose").slideToggle(); $("#netix_tituloform").text("BUSCAR PRODUCTO"); 
			netix_sistema.netix_loader("netix_formulario",180); 

			this.$http.post(url+"almacen/productos/buscar/ventas").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_error(); 
			});
		},
		netix_additem: function(producto,precio){
			var existe_item = [];
			if ($("#itemrepetir").val()==0) {
				var existe_item = this.detalle.filter(function(p){
				    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
				    	p.cantidad = p.cantidad + 1; return p;
				    };
				});
			}

		    if (existe_item.length==0 || $("#itemrepetir").val()==1) {
		    	producto.preciosinigv = producto.precio; producto.precio = precio; 
		    	producto.valorguia = producto.precio; producto.subtotal = producto.precio;
		    	
		    	producto.afectacionigv = 20; producto.igv = 0; var porcentaje = 1;
				if (producto.afectoigvventa==1) {
					var porcentaje = (1 + this.igvsunat) / 100;

					producto.afectacionigv = 10;
					producto.preciosinigv = Number((producto.precio / porcentaje).toFixed(4));
					producto.valorguia = Number((producto.precio / porcentaje).toFixed(2));
					producto.igv = Number((producto.subtotal - producto.valorguia).toFixed(2));
				}

				this.detalle.push({
					codproducto: producto.codproducto, producto: producto.descripcion, codunidad: producto.codunidad,
					unidad: producto.unidad, cantidad: 1, preciosinigv: producto.preciosinigv, precio: producto.precio,
					preciorefunitario: producto.precio, codafectacionigv: producto.afectacionigv, igv: producto.igv,
					valorguia: producto.valorguia, subtotal:producto.subtotal, descripcion:"", pesokg:0, pesototal:0
				});
				this.netix_totales();
		    }else{
		    	this.netix_calcular(existe_item[0]);
		    }
		},
		netix_deleteitem: function(index,producto){
			this.detalle.splice(index,1); this.netix_totales();
		},
		netix_itemdetalle: function(index,producto){
			this.item = producto; $("#modal_itemdetalle").modal({backdrop: 'static', keyboard: false});
		},
		netix_itemcalcular: function (item,tipoprecio) {
			var porcentaje = 1;
			if (item.codafectacionigv==21) {
				item.preciosinigv = 0; item.precio = 0; item.igv = 0; item.valorguia = 0; item.subtotal = 0; 
			}
			if (item.codafectacionigv==10) {
				var porcentaje = (1 + this.igvsunat) / 100;
			}
			
			if (tipoprecio==1) {
				item.precio = Number((item.preciosinigv * porcentaje).toFixed(4));
			}
			if (tipoprecio==2) {
				item.preciosinigv = Number((item.precio / porcentaje).toFixed(4));
			}

			item.valorguia = Number((item.cantidad * item.preciosinigv).toFixed(2));
			item.subtotal = Number((item.cantidad * item.precio).toFixed(2));
			item.igv = Number((item.subtotal - item.valorguia).toFixed(2));
			this.netix_totales();
		},
		netix_itemcalcular_cerrar: function (item) {
			if (parseFloat(item.subtotal) < 0) {
				netix_sistema.netix_noti("EL SUBTOTAL DEBE SER MAYOR A CERO","REVISAR LOS CAMPOS DEL ITEM","error"); return false;
			}
			$("#modal_itemdetalle").modal("hide");
		},
		netix_calcular: function(producto){
			var porcentaje = 1;
			if (producto.codafectacionigv==10) {
				var porcentaje = (1 + this.igvsunat) / 100;
			}
			producto.preciosinigv = Number((producto.precio / porcentaje).toFixed(4));

			producto.valorguia = Number((producto.cantidad * producto.preciosinigv).toFixed(2));
			producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
			producto.igv = Number((producto.subtotal - producto.valorguia).toFixed(2));
			this.netix_totales();
		},
		netix_peso: function(producto){
			producto.pesototal = Number((producto.cantidad * producto.pesokg).toFixed(2));
			this.netix_totales();
		},
		netix_totales: function () {
			this.campos.igv = 0.00; this.campos.valorguia = 0.00; this.campos.subtotal = 0.00; this.campos.importe = 0.00; 
			this.campos.pesototal = 0.00; t = this;
			var detalle = this.detalle.filter(function(p){
				t.campos.igv = Number((t.campos.igv + parseFloat(p.igv) ).toFixed(2));
				t.campos.valorguia = Number((t.campos.valorguia + parseFloat(p.valorguia) ).toFixed(2));
				t.campos.subtotal = Number((t.campos.subtotal + parseFloat(p.subtotal) ).toFixed(2));
				t.campos.pesototal = Number((t.campos.pesototal + parseFloat(p.pesototal) ).toFixed(2));
			});
			this.campos.importe = Number((this.campos.valorguia + this.campos.igv).toFixed(2));
		},

		netix_guardar: function(){
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE", "REGISTRAR ITEM PARA LA GUIA","error"); return false;
			}
			this.campos.fechaemision = $("#fechaemision").val(); this.campos.fechatraslado = $("#fechatraslado").val();
			this.campos.modalidadtraslado = $("#codmodalidadtraslado option:selected").text();
			this.campos.destinatario = $("#coddestinatario option:selected").text(); 
			this.campos.transportista = $("#codempresa_traslado option:selected").text();

			if ($("#coddestinatario").val() == "") {
				netix_sistema.netix_noti("SELECCIONE DESTINATARIO O CLIENTE", "","error"); return false;
			}
			if ($("#ubigeo_partida").val() == "") {
				netix_sistema.netix_noti("SELECCIONE UBIGEO DE PARTIDA", "","error"); return false;
			}
			if ($("#ubigeo_llegada").val() == "") {
				netix_sistema.netix_noti("SELECCIONE UBIGEO DE LLEGADA", "","error"); return false;
			}

			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO GUIA . . .");
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						swal({
							title: "DESEA IMPRIMIR LA GUIA ?",   
							text: "DESEA IMPRIMIR EL COMPROBANTE REGISTRADO", 
							icon: "warning",
							dangerMode: true,
							buttons: ["CANCELAR", "SI, IMPRIMIR"],
						}).then((willDelete) => {
							if (willDelete){
								this.netix_imprimir(data.body.codguia);
							}
						});
						if (this.campos.codguia == 0) {
							netix_sistema.netix_noti("GUIA REGISTRADA CORRECTAMENTE","GUIA REGISTRADA EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_noti("GUIA MODIFICADA CORRECTAMENTE","GUIA MODIFICADA EN EL SISTEMA","success");
						}
					}else{
						if (data.body.estado==2) {
							netix_sistema.netix_alerta("NO PUEDE EDITAR LA GUIA","NO TIENE PERMISOS EN EL SISTEMA","error");
						}else{
							netix_sistema.netix_alerta("ERROR AL REGISTRAR GUIA","ERROR DE RED","error");
						}
					}
				}
				netix_sistema.netix_fin(); // this.netix_atras();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR GUIA","ERROR DE RED","error");
				netix_sistema.netix_fin(); this.netix_atras();
			});
		},

		netix_imprimir: function(codguia){
			var netix_url = url+"facturacion/formato/guia/"+codguia;
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
        },

        netix_editar: function(){
			this.titulo = "EDITAR GUIA 000"+netix_ventas.registro+" REGISTRADA"; this.campos.codkardex = netix_ventas.registro;
			this.$http.post(url+netix_controller+"/editar",{"codregistro":netix_ventas.registro}).then(function(data){
				var socio = eval(data.body.socio);
				$("#codpersona").empty().html("<option value='"+socio[0]["codpersona"]+"'>"+socio[0]["razonsocial"]+"</option>");

				$(".selectpicker").selectpicker("refresh"); $(".filter-option").text(socio[0]["razonsocial"]); 
				$("#codpersona").val(socio[0]["codpersona"]); this.campos.codpersona = socio[0]["codpersona"];

				this.campos.codkardex = data.body.campos[0].codkardex;
				this.campos.retirar = data.body.campos[0].retirar;
				this.campos.afectacaja = data.body.campos[0].afectacaja;
				$("#fechacomprobante").val(data.body.campos[0].fechacomprobante); $("#fechakardex").val(data.body.campos[0].fechakardex);
				this.campos.codmoneda = data.body.campos[0].codmoneda;
				this.campos.tipocambio = data.body.campos[0].tipocambio;
				this.campos.codcomprobantetipo = data.body.campos[0].codcomprobantetipo;
				this.campos.seriecomprobante = data.body.campos[0].seriecomprobante;
				this.campos.nro = data.body.campos[0].nrocomprobante;
				this.campos.condicionpago = data.body.campos[0].condicionpago;
				this.campos.descripcion = data.body.campos[0].descripcion;

				this.totales.flete = data.body.campos[0].flete;
				this.totales.gastos = data.body.campos[0].gastos;
				this.totales.subtotal = data.body.campos[0].subtotal;
				this.totales.descuentoglobal = data.body.campos[0].descuentoglobal;
				this.totales.igv = data.body.campos[0].igv;
				this.totales.importe = data.body.campos[0].importe;

				this.detalle = data.body.detalle; this.netix_totales();

				this.correlativo = this.campos.nro;
				this.netix_series(); netix_sistema.netix_fin();
				$("#codcomprobantetipo").attr("disabled", "disabled"); $("#seriecomprobante").attr("disabled", "disabled");
			});
		}
	},
	created: function(){
		if (parseInt(netix_guias.registro)!=0) {
			this.netix_editar();
		}else{
			netix_sistema.netix_fin();
		}
	}
});