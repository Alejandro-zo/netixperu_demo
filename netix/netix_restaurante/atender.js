var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		estado:0, stockalmacen: $("#stockalmacen").val(), igvsunat:$("#igvsunat").val(), icbpersunat:$("#icbpersunat").val(), 
		rubro:0, series:[], cuotas: [], mesas:[], detalle: [], atender: [], atendidos: [],
		campos:{
			codambiente: $("#codambiente").val(), codmesa: "", mesa:"...", codpedido:0, pedidonuevo:1, tipopedido:0, 
			codcomprobante:$("#comprobante").val(), fechapedido: $("#fechapedido").val(),
			codkardex:0, codpersona:2, codmovimientotipo:20, codcomprobantetipo:$("#comprobante").val(),seriecomprobante:$("#serie").val(), nro:"",
			fechacomprobante:$("#fechapedido").val(), fechakardex:$("#fechapedido").val(), codconcepto:13, descripcion:"REGISTRO POR VENTA", cliente:"CLIENTES VARIOS", direccion:"-",
			codempleado:0, codmoneda:1, tipocambio:0.00, codcentrocosto:0, nroplaca:"", retirar:true, afectacaja:true,
			condicionpago:1, nrodias:30, nrocuotas:1, codcreditoconcepto:3, tasainteres:0, interes:0, totalcredito:0, porcdescuento:0.00
		},
		pagos:{
			codtipopago_efectivo:1, monto_efectivo:0, vuelto_efectivo:0, codtipopago_tarjeta:0, monto_tarjeta:0, nrovoucher:""
		},
		operaciones:{
			gravadas:0.00, exoneradas:0.00, inafectas:0.00, gratuitas:0.00
		},
		item:{
			producto:"", unidad:"", cantidad:0, preciobruto:0, descuento:0, porcdescuento:0, preciosinigv:0, precio:0, 
			codafectacionigv:"", igv:0, valorventa:0, conicbper: 0, icbper:0, subtotal:0, descripcion:""
		},
		totales:{
			flete:0.00, gastos:0.00, bruto:0.00, descuentos:0.00, descglobal:0.00, valorventa:0.00, igv:0.00, isc:0.00, icbper:0.00, 
			subtotal:0.00, importe:0.00
		}
	},
	methods: {
		netix_mesas: function(){
			this.$http.post(url+"restaurante/mesas/mesas_ambiente/"+this.campos.codambiente).then(function(data){
				this.mesas = data.body;
			});
		},
		netix_pedido: function(mesa){
			$("#"+this.campos.codmesa).removeClass("mesa-activa");
			this.campos.codmesa = mesa.codmesa; this.campos.mesa = mesa.nromesa;
			$("#"+this.campos.codmesa).addClass("mesa-activa");

			this.$http.post(url+"ventas/pedidos/netix_pedido",{"codmesa":this.campos.codmesa}).then(function(data){
				this.campos.codpedido = data.body.codpedido; this.campos.pedidonuevo = data.body.pedidonuevo;
				// data.body.pedidonuevo=1 		SIN DETALLE - PEDIDO NUEVO //
				if (data.body.pedidonuevo==0) {
					this.totales.valorventa = Number((parseFloat(data.body.pedido[0].valorventa) ).toFixed(2));
					this.totales.descglobal = Number((parseFloat(data.body.pedido[0].descglobal) ).toFixed(2));
					this.totales.igv = Number((parseFloat(data.body.pedido[0].igv) ).toFixed(2));
					this.totales.importe = Number((parseFloat(data.body.pedido[0].importe) ).toFixed(2));

					this.campos.codcomprobante = parseInt(data.body.pedido[0].codcomprobante);
					this.campos.codempleado = parseInt(data.body.pedido[0].codempleado);
				}
				this.detalle = data.body.detalle;
			},function(){
				netix_sistema.netix_alerta("SIN CONEXION DE INTERNET", "ERROR DE RED","error");
			});
		},
		cambiar_mesa: function(){
			if (this.campos.pedidonuevo==1) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UNA MESA CON PEDIDO","PARA CAMBIAR DE MESA","error");
			}else{
				alert();
			}
		},

		netix_producto: function(codlinea){
			// netix_sistema.netix_loader("netix_restaurante",180);

			this.$http.post(url+"almacen/productos/restobar/"+codlinea).then(function(data){
				$("#netix_restaurante").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
			});
		},
		netix_additem: function(producto, precio){
			var existe_item = [];
			if ($("#itemrepetir").val()==0) {
				var existe_item = this.detalle.filter(function(p){
				    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
				    	p.cantidad = parseFloat(p.cantidad) + 1; return p;
				    };
				});
			}

		    if (existe_item.length==0 || $("#itemrepetir").val()==1) {
		    	producto.preciosinigv = producto.precio; producto.precio = precio; 
		    	producto.valorventa = producto.precio; producto.subtotal = producto.precio;
		    	
		    	producto.afectacionigv = 20; producto.igv = 0; var porcentaje = 1;
				if (producto.afectoigvventa==1) {
					var porcentaje = (1 + this.igvsunat) / 100;

					producto.afectacionigv = 10;
					producto.preciosinigv = Number((producto.precio / porcentaje).toFixed(4));
					producto.valorventa = Number((producto.precio / porcentaje).toFixed(2));
					producto.igv = Number((producto.subtotal - producto.valorventa).toFixed(2));
				}
				
				producto.icbper = 0; producto.isc = 0;
				if (producto.afectoicbper==1) {
					producto.icbper = Number((1 * this.icbpersunat).toFixed(2));;
				}

				producto.control = 0;
				if (this.stockalmacen==1) {
					if (producto.controlstock==1) {
						producto.control = 1;
					}
				}

		    	this.detalle.push({
					codproducto: producto.codproducto, producto: producto.descripcion, codunidad: producto.codunidad,
					unidad: producto.unidad, cantidad: 1, stock:producto.stock, control:producto.control,
					preciobruto: producto.preciosinigv, preciosinigv: producto.preciosinigv, precio: producto.precio,
					preciorefunitario: producto.precio, porcdescuento: 0, descuento: 0,
					codafectacionigv: producto.afectacionigv, igv: producto.igv, conicbper: producto.afectoicbper, icbper: producto.icbper,
					valorventa: producto.valorventa, subtotal:producto.subtotal, subtotal_tem:producto.subtotal, 
					descripcion:"", calcular: producto.calcular, atendido:0, item:0,
				});
				this.netix_calcular(producto,1);
		    }else{
		    	this.netix_calcular(existe_item[0],3);
		    }
		},
		netix_itemdetalle: function(index,producto){
			this.item = producto; $("#modal_itemdetalle").modal("show");
		},
		netix_cerrar_itemdetalle: function(){
			$("#modal_itemdetalle").modal("hide");
		},
		netix_deleteitem: function(index,producto){
			this.netix_calcular(producto,2); this.detalle.splice(index,1);
		},
		netix_calcular: function(producto,tipo){
			if (tipo==1) {
				this.totales.valorventa = Number((this.totales.valorventa + parseFloat(producto.precio)).toFixed(2));
			}else{
				if (tipo==2) {
					this.totales.valorventa = Number((this.totales.valorventa - producto.subtotal).toFixed(2));
				}else{
					this.totales.valorventa = Number((this.totales.valorventa - producto.subtotal).toFixed(2));

					if (producto.cantidad=="") {
						producto.subtotal = 0;
					}else{
						producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
					}
					this.totales.valorventa = Number((this.totales.valorventa + producto.subtotal).toFixed(2));
				}
			}
			this.totales.importe = Number((this.totales.valorventa + this.totales.igv).toFixed(2));
		},

		netix_guardar_pedido: function(){
			if ($("#sessioncaja").val()==0) {
				netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE REALIZAR VENTAS","error"); return false;
			}

			if(this.campos.codmesa==""){
				netix_sistema.netix_noti("DEBE SELECCIONAR LA MESA DEL PEDIDO PARA PODER REGISTRAR","","error"); return false;
			}

			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE", "REGISTRAR ITEM PARA EL PEDIDO","error"); return false;
			}

			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO PEDIDO . . .");
			this.$http.post(url+"ventas/pedidos/guardar_pedido", {"campos":this.campos,"detalle":this.detalle,"totales":this.totales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						netix_sistema.netix_noti("PEDIDO REGISTRADO CORRECTAMENTE","PEDIDO REGISTRADO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR PEDIDO","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR PEDIDO","ERROR DE RED","error");
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			});
		},

		netix_atender_pedido: function(){
			if (this.campos.pedidonuevo==1) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UNA MESA CON PEDIDO","PARA ATENDER UN PEDIDO","error");
				return false;
			}else{
				$("#modal_atender").modal("show");
				this.$http.post(url+"ventas/pedidos/netix_atenciones",{"codpedido":this.campos.codpedido}).then(function(data){
					this.atender = data.body.detalle; this.atendidos = data.body.atendidos; this.totales = data.body.totales;
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				});
			}
		},
		netix_mas_menos: function(pedido,tipo){
			if (tipo==1) {
				if (pedido.falta!=pedido.atender) {
					pedido.atender = pedido.atender + 1;
				}
			}else{
				if (pedido.atender>0) {
					pedido.atender = pedido.atender - 1;
				}
			}
		},
		netix_atender: function(){
			var atender = 0;
			for (var i = 0; i < this.atender.length; i++) {
				if (this.atender[i]["atender"]!="") {
					atender = atender + parseFloat(this.atender[i]["atender"]);
				}
			}
			if (atender==0) {
				netix_sistema.netix_noti("NO HAY PEDIDOS PARA ATENDER","MINIMO DEBE HABER UNA CANTIDAD ATENDIDA","error");
			}else{
				this.estado = 1; $("#modal_atender").modal("hide"); netix_sistema.netix_inicio_guardar("GUARDANDO ATENCION . . .");
				this.$http.post(url+"ventas/pedidos/guardar_atencion",{"atender":this.atender}).then(function(data){
					if (data.body==1) {
						netix_sistema.netix_noti("ATENCION REGISTRADA CORRECTAMENTE","PEDIDO 000"+this.campos.codpedido+" ATENDIDO","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR ATENCION","ERROR DE RED","error");
					}
					netix_sistema.netix_fin(); netix_sistema.netix_modulo();
				}, function(){
					netix_sistema.netix_alerta("ERROR AL REGISTRAR ATENCION","ERROR DE RED","error");
					netix_sistema.netix_fin(); netix_sistema.netix_modulo();
				});
			}
		},

		netix_anular_pedido: function(){
			if (this.campos.pedidonuevo==1) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UNA MESA CON PEDIDO","PARA ANULAR EL PEDIDO","error");
				return false;
			}

			swal({
				title: "SEGURO ANULAR PEDIDO ?",   
				text: "USTED ESTA POR ANULAR EL PEDIDO 00"+this.campos.codpedido, 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ANULAR"],
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.post(url+"ventas/pedidos/anular_pedido",{"codregistro":this.campos.codpedido,"codmesa":this.campos.codmesa}).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("ANULADO CORRECTAMENTE", "UN PEDIDO ANULADO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDIÓ LA CONEXION !!! LO SENTIMOS","error");
						}
						netix_sistema.netix_modulo();
					}, function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
					});
				}
			});
		},

		netix_avance_pedido: function(){
			if (this.campos.pedidonuevo==1) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UNA MESA CON PEDIDO","PARA IMPRIMIR EL AVANCE DE CUENTE","error");
				return false;
			}else{
				this.$http.get(url+"restaurante/caja/avance_pedido/"+this.campos.codpedido).then(function(data){
					$("#imprimir_pedido").empty().html(data.body); var id = "imprimir_pedido";
					var data = document.getElementById(id).innerHTML;
			        var myWindow = window.open('', 'IMPRIMIENDO', 'height=400,width=800');
			        myWindow.document.write('<html><head><title>TICKET</title>');
			        // myWindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
			        myWindow.document.write('</head><body >');
			        myWindow.document.write(data);
			        myWindow.document.write('</body></html>');
			        myWindow.document.close();

			        myWindow.onload=function(){
			            myWindow.focus(); myWindow.print(); myWindow.close();
			        };
				});
				// $("#netix_pdf").attr("src",url+"restaurante/caja/avance_pedido/"+this.campos.codpedido);
			}
		},

		netix_cobrar_pedido: function(){
			if (this.campos.pedidonuevo==1) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UNA MESA CON PEDIDO","PARA COBRAR EL PEDIDO","error");
				return false;
			}

			if ($("#sessioncaja").val()==0) {
				netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE COBRAR PEDIDO","error"); 
			}else{
				this.campos.codcomprobantetipo = this.campos.codcomprobante; this.netix_series();
				this.pagos.monto_efectivo = this.totales.importe;
				$("#modal_pago").modal({backdrop: 'static', keyboard: false});
			}
		},

		// GUARDAR EL PEDIDO COMO VENTA //

		netix_addcliente: function(){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180); 
			this.$http.post(url+"ventas/clientes/nuevo_1").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_modulo();
			});
		},

		netix_infocliente: function(){
			this.campos.cliente = $("#codpersona option:selected").text();
			this.$http.get(url+"ventas/clientes/infocliente/"+this.campos.codpersona).then(function(data){
				/* if (this.campos.codpersona==2) {
					$("#cliente").removeAttr("readonly"); $("#direccion").removeAttr("readonly");
				}else{
					$("#cliente").attr("readonly","true"); $("#direccion").attr("readonly","true");
				} */
				this.codtipodocumento = data.body[0].coddocumentotipo; this.campos.direccion = data.body[0].direccion;
			});
        },

		netix_series: function(){
			if (this.campos.codcomprobantetipo!=undefined) {
				this.estado = 1;
				this.$http.get(url+"caja/controlcajas/netix_seriescaja/"+this.campos.codcomprobantetipo).then(function(data){
					this.series = data.body.series; this.estado = 0;
					// this.campos.seriecomprobante = $("#serie").val(); this.netix_correlativo();
					this.campos.seriecomprobante = data.body.serie; this.netix_correlativo();
				});

				if (this.campos.codcomprobantetipo==10) {
					this.$http.get(url+"ventas/clientes/infocliente/"+this.campos.codpersona).then(function(data){
						this.codtipodocumento = data.body[0].coddocumentotipo;
					});
				}
			}
		},
		netix_correlativo: function(){
			if (this.campos.codcomprobantetipo!=undefined) {
				if (this.campos.seriecomprobante!="") {
					this.$http.get(url+"caja/controlcajas/netix_correlativo/"+this.campos.codcomprobantetipo+"/"+this.campos.seriecomprobante).then(function(data){
						this.campos.nro = data.body;
					});
				}
			}
		},

		netix_pagotarjeta: function(){
			if (this.pagos.codtipopago_tarjeta==0) {
				this.pagos.monto_tarjeta = 0; this.pagos.nrovoucher = "";
				$("#monto_tarjeta").attr("readonly","true"); $("#monto_tarjeta").removeAttr("required");
				$("#nrovoucher").attr("readonly","true"); $("#nrovoucher").removeAttr("required");
			}else{
				$("#monto_tarjeta").removeAttr("readonly"); $("#monto_tarjeta").attr("required","true");
				$("#nrovoucher").removeAttr("readonly"); $("#nrovoucher").attr("required","true");
			}
		},
		netix_vuelto: function(){
			this.pagos.vuelto_efectivo = Number((this.pagos.monto_efectivo - this.totales.importe).toFixed(2));
			if (this.pagos.vuelto_efectivo<=0) {
				this.pagos.vuelto_efectivo = 0;
			}
		},

		netix_condicionpago: function(){
			if (this.campos.condicionpago==2) {
				this.netix_cuotas(); this.campos.codconcepto = 15;
			}else{
				this.campos.codconcepto = 13;
			}
		},
		netix_cuotas: function(){
			var importe = Number((this.totales.importe/this.campos.nrocuotas).toFixed(1));
			var interes = Number(( (this.campos.tasainteres*importe/100) ).toFixed(1));
			var total = Number((importe + interes).toFixed(1));

    		var fecha = new Date();
    		this.totales.interes = Number(( (this.campos.tasainteres * this.totales.importe/100) ).toFixed(1));
			this.campos.totalcredito = Number(( parseFloat(this.totales.importe) + parseFloat(this.totales.interes) ).toFixed(1));
    		
			this.cuotas = []; var suma_importe = 0; var suma_total = 0;
			for (var i = 1; i <= this.campos.nrocuotas; i++) {
				if (this.campos.nrodias=="") {
					fecha.setDate(fecha.getDate() + 0);
				}else{
					fecha.setDate(fecha.getDate() + parseInt(this.campos.nrodias));
				}

				year = fecha.getFullYear(); month = String(fecha.getMonth() + 1); day = String(fecha.getDate());
				if (month.length < 2) month = "0"+month;
				if (day.length < 2) day = "0"+day;

				fechavence = year+"-"+month+"-"+day;

				if (this.campos.nrocuotas==i) {
					importe = Number(( this.totales.importe - parseFloat(suma_importe) ).toFixed(1));
					total = Number(( this.campos.totalcredito - parseFloat(suma_total) ).toFixed(1));
				}else{
					suma_importe = Number(( parseFloat(suma_importe) + parseFloat(importe) ).toFixed(1));
					suma_total = Number(( parseFloat(suma_total) + parseFloat(total) ).toFixed(1));
				}

				this.cuotas.push({
					"nrocuota":i,"fechavence":fechavence,"importe":importe,"interes":interes,"total":total
				});
			}
		},

		netix_pagar: function(){
			if ((this.campos.codcomprobantetipo==10 || this.campos.codcomprobantetipo==25) && this.codtipodocumento!=4) {
				netix_sistema.netix_noti("PARA EMITIR UNA FACTURA", "DEBE SELECCIONAR UN CLIENTE CON RUC","error"); return false;
			}

			if (parseFloat(this.totales.importe)>=700) {
				if ((this.campos.codcomprobantetipo==12 || this.campos.codcomprobantetipo==26) && this.codtipodocumento==0) {
					netix_sistema.netix_noti("PARA EMITIR UNA BOLETA CON MONTO MAYOR A 700.00 SOLES","DEBE SELECCIONAR UN CLIENTE CON DNI o RUC","error");
					return false;
				}
			}

			if (this.campos.condicionpago==1) {
				if (this.pagos.codtipopago_tarjeta==0) {
					if (parseFloat(this.pagos.monto_efectivo) < parseFloat(this.totales.importe)) {
						netix_sistema.netix_noti("EL IMPORTE DEBE SER MAYOR O IGUAL AL TOTAL DE LA VENTA","FALTAN S/. "+
						Number(( parseFloat(this.totales.importe - this.pagos.monto_efectivo) ).toFixed(2)),"error"); return false;
					}
				}else{
					var suma_importe = parseFloat(this.pagos.monto_efectivo) + parseFloat(this.pagos.monto_tarjeta);
					if (parseFloat(suma_importe)!=parseFloat(this.totales.importe)) {
						netix_sistema.netix_noti("LA SUMA DE LOS IMPORTES DEBE SER IGUAL AL TOTAL DE LA VENTA","DIFERENCIA S/. "+
						Number(( parseFloat(this.totales.importe - suma_importe) ).toFixed(2)),"error"); return false;
					}
				}
			}else{
				if (this.campos.codpersona==2) {
					netix_sistema.netix_noti("ATENCION USUARIO: EL SISTEMA NO PERMITE REGISTRAR UN CREDITO A CLIENTES VARIOS","","error");
					return false;
				}
			}
			
			this.estado = 1; $("#modal_pago").modal("hide"); netix_sistema.netix_inicio_guardar("GUARDANDO VENTA . . .");

			this.$http.post(url+"ventas/pedidos/cobrar_pedido", {"campos":this.campos,"detalle":this.detalle,"cuotas":this.cuotas,"pagos":this.pagos,"totales":this.totales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						swal({
							title: "DESEA IMPRIMIR LA VENTA ?",   
							text: "DESEA IMPRIMIR EL COMPROBANTE REGISTRADO", 
							icon: "warning",
							dangerMode: true,
							buttons: ["CANCELAR", "SI, IMPRIMIR"],
						}).then((willDelete) => {
							if (willDelete){
								window.open(url+"facturacion/formato/ticket/"+data.body.codkardex,"_blank");

								// $("#netix_pdf").attr("src",url+"restaurante/caja/cobrar_pedido/"+data.body.codkardex);
							}
						});
						netix_sistema.netix_noti("VENTA REGISTRADA CORRECTAMENTE","VENTA REGISTRADA EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR VENTA","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR VENTA","ERROR DE RED","error");
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			});
		},
		
		netix_cocina: function(){
			/* this.$http.get("http://localhost/netixperu_tickets/netix_cocina.php").then(function(data){
				if (data.body!="") {
					netix_sistema.netix_alerta(data.body,"","error");
				}
			},function(){
				netix_sistema.netix_alerta("LA IMPRESORA DE COCINA NO ESTA CONFIGURADA","ERROR DE IMPRESION","error");
			}); */
			netix_sistema.netix_alerta("LA IMPRESORA DE COCINA NO ESTA CONFIGURADA","ERROR DE IMPRESION","error");
		},

		netix_movimientos: function(tipomovimiento){
			if ($("#sessioncaja").val()==0) {
				netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE REGISTRAR GASTOS","error"); 
			}else{
				$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);

				this.$http.get(url+"caja/movimientos/nuevo_1/"+tipomovimiento+"/0").then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				});
			}
		},
		netix_ventadiaria: function(){
			$("#modal_reportes").modal("show");
	        $("#netix_pdf").attr("src",url+"restaurante/caja/venta_diaria");
		},
		netix_balancecaja: function(){
			if ($("#sessioncaja").val()==0) {
				netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE VER EL BALANCE DE CAJA","error"); 
			}else{
				$("#modal_reportes").modal("show");
				$("#netix_pdf").attr("src",url+"restaurante/caja/balance_caja");
			}
		},
		netix_vendedores_caja: function(){
			$("#modal_empleados").modal("show");
			this.$http.get(url+"restaurante/caja/pdf_vendedores_caja_directo").then(function(data){
				$("#modal_empleados_contenido").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
			});
		},
		netix_tipopedido: function(){
			if (this.campos.tipopedido > 0) {
				this.$http.post(url+"almacen/productos/producto_tipopedido/"+this.campos.tipopedido).then(function(data){
					var producto = {"codproducto":data.body[0].codproducto,"descripcion":data.body[0].descripcion,"codunidad":data.body[0].codunidad,
					"unidad":data.body[0].unidad,"stockdisponible":0,"control":0,"precio":data.body[0].precio,"preciorefunitario":data.body[0].precio,
					"calcular":0,"subtotal":data.body[0].precio};
					this.netix_additem(producto);
				});
			}
		}
	},
	created: function(){
		this.netix_mesas(); this.netix_producto(0); netix_sistema.netix_fin(); $(".in").remove();
	}
});

document.addEventListener("keyup", buscar_tecla, false);
function buscar_tecla(e){
    var keyCode = e.keyCode;

    // TECHA CTROL + F11 //
    if(keyCode==122){ 
    	netix_operacion.netix_producto();
    }
}