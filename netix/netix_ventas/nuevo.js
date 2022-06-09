var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		estado:0, titulo: "REGISTRO NUEVA VENTA", codigobarra: "", correlativo: "",
		stockalmacen: $("#stockalmacen").val(), igvsunat:$("#igvsunat").val(), icbpersunat:$("#icbpersunat").val(), rubro:0, series:[], detalle:[], cuotas:[],
		campos:{
			codkardex:0, codpersona:2, codmovimientotipo:20, codcomprobantetipo:$("#comprobante").val(),seriecomprobante:$("#serie").val(), nro:"",
			fechacomprobante:"", fechakardex:"", codconcepto:13, descripcion:"REGISTRO POR VENTA", cliente:"CLIENTES VARIOS", direccion:"-",
			codempleado:0, codmoneda:1, tipocambio:0.00, codcentrocosto:0, nroplaca:"", retirar:true, afectacaja:true,
			condicionpago:1, nrodias:30, nrocuotas:1, codcreditoconcepto:3, tasainteres:0, interes:0, totalcredito:0, porcdescuento:0.00, codpersona_convenio:1
		},
		item:{
			producto:"", unidad:"", cantidad:0, preciobruto:0, descuento:0, porcdescuento:0, preciosinigv:0, precio:0, 
			codafectacionigv:"", igv:0, valorventa:0, conicbper: 0, icbper:0, subtotal:0, descripcion:""
		},
		pagos:{
			codtipopago_efectivo:1, monto_efectivo:"", vuelto_efectivo:"", codtipopago_tarjeta:0, monto_tarjeta:0, nrovoucher:""
		},
		operaciones:{
			gravadas:0.00, exoneradas:0.00, inafectas:0.00, gratuitas:0.00
		},
		totales:{
			flete:0.00, gastos:0.00, bruto:0.00, descuentos:0.00, descglobal:0.00, valorventa:0.00, igv:0.00, isc:0.00, icbper:0.00, 
			subtotal:0.00, importe:0.00
		},
		cuot:{
			nrocuotas:"",fechavence:"",importe:"",interes:"",total:""
		}
	},
	methods: {

		/* FUNCIONES GENERALES DE LA VENTA */

		netix_venta: function(){
			swal({
				title: "SEGURO REGISTRAR NUEVA VENTA?",   
				text: "LOS CAMPOS SE QUEDARAN VACIOS ", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, NUEVA VENTA"],
			}).then((willDelete) => {
				if (willDelete){
					this.netix_nueva_venta();
				}
			});
		},
		netix_nueva_venta: function(){
			netix_sistema.netix_inicio(); $(".in").remove();
			netix_ventas.registro = 0; this.titulo = "REGISTRO NUEVA VENTA"; this.campos.codkardex = 0;

			this.$http.post(url+netix_controller+"/nuevo").then(function(data){
				$("#netix_sistema").empty().html(data.body);
			});
		},
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
		netix_infocliente: function(){
			this.campos.cliente = $("#codpersona option:selected").text();
			this.$http.get(url+"ventas/clientes/infocliente/"+this.campos.codpersona).then(function(data){
				if (this.campos.codpersona==2) {
					$("#cliente").removeAttr("readonly"); $("#direccion").removeAttr("readonly");
				}else{
					$("#cliente").attr("readonly","true"); $("#direccion").attr("readonly","true");
				}
				this.codtipodocumento = data.body[0].coddocumentotipo; this.campos.direccion = data.body[0].direccion;
			});
        },

		/* DETALLE DE LA VENTA Y TOTALES */

		netix_codigobarra: function(){
			if (this.codigobarra!="") {
				this.$http.get(url+"almacen/productos/buscar_codigobarra/"+this.codigobarra).then(function(data){
					if (data.body.cantidad==0) {
						netix_sistema.netix_alerta("NO EXISTE CODIGO DE BARRA", "REGISTRA EL CODIGO DE BARRA", "error");
					}else{
						if (data.body.cantidad==1) {
							this.netix_additem(data.body.info[0],data.body.precio); this.codigobarra = "";
						}else{
							netix_sistema.netix_alerta("EL CODIGO DE BARRA EXISTE EN MÁS DE UN PRODUCTO", "REGISTRADO MAS DE UNA VEZ", "error");
						}
					}
				});
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
					descripcion:"", calcular: producto.calcular
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
				item.preciobruto = 0; item.porcdescuento = 0; item.descuento = 0; item.preciosinigv = 0; item.precio = 0; 
				item.igv = 0; item.valorventa = 0; item.subtotal = 0; 
			}
			if (item.codafectacionigv==10) {
				var porcentaje = (1 + this.igvsunat) / 100;
			}

			if (tipoprecio==-1) {
				item.porcdescuento = Number((item.descuento / item.preciobruto * 100).toFixed(2));
				item.precio = Number((item.preciobruto - item.descuento).toFixed(4)); tipoprecio = 2;
			}
			if (tipoprecio==-2) {
				item.descuento = Number((item.preciobruto * item.porcdescuento / 100).toFixed(4));
				item.precio = Number((item.preciobruto - item.descuento).toFixed(4)); tipoprecio = 2;
			}
			if(tipoprecio==0){
				item.precio = Number((item.preciobruto - item.descuento).toFixed(4));
			}
			
			var descuento = item.descuento;
			if (item.descuento=="") {
				var descuento = 0;
			}
			
			if (tipoprecio==1) {
				item.precio = Number((item.preciosinigv * porcentaje).toFixed(4));
				item.preciobruto = Number((item.precio + descuento).toFixed(4));
			}
			if (tipoprecio==2) {
				item.preciosinigv = Number((item.precio / porcentaje).toFixed(4));
				item.preciobruto = Number((item.precio + descuento).toFixed(4));
			}

			item.icbper = 0;
			if (item.conicbper==1) {
				item.icbper = Number((item.cantidad * this.icbpersunat).toFixed(2));
			}

			item.valorventa = Number((item.cantidad * item.preciosinigv).toFixed(2));
			item.subtotal = Number((item.cantidad * item.precio).toFixed(2));
			item.igv = Number((item.subtotal - item.valorventa).toFixed(2));
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
			producto.preciobruto = Number((producto.precio + producto.descuento).toFixed(4));

			producto.valorventa = Number((producto.cantidad * producto.preciosinigv).toFixed(2));
			producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
			producto.igv = Number((producto.subtotal - producto.valorventa).toFixed(2));
			producto.icbper = 0;
			if (producto.conicbper==1) {
				producto.icbper = Number((producto.cantidad * this.icbpersunat).toFixed(2));
			}
			this.netix_totales();
		},
		netix_subtotal: function(producto){
			// SI producto.calcular = 1 calcula cantidad, producto.calcular = 2 calcula precio //
			if (producto.calcular==1) {
				if (producto.precio!=0) {
					producto.cantidad = Number((producto.subtotal / producto.precio).toFixed(4));
				}
			}else{
				if (producto.cantidad!=0) {
					producto.precio = Number((producto.subtotal / producto.cantidad).toFixed(4));
				}
			}

			var porcentaje = 1;
			if (producto.codafectacionigv==10) {
				var porcentaje = (1 + this.igvsunat) / 100;
			}
			producto.preciosinigv = Number((producto.precio / porcentaje).toFixed(4));
			producto.preciobruto = Number((producto.precio + producto.descuento).toFixed(4));

			producto.valorventa = Number((producto.cantidad * producto.preciosinigv).toFixed(2));
			producto.igv = Number((producto.subtotal - producto.valorventa).toFixed(2));
			producto.icbper = 0;
			if (producto.conicbper==1) {
				producto.icbper = Number((producto.cantidad * this.icbpersunat).toFixed(2));
			}
			this.netix_totales();
		},
		netix_totales: function () {
			this.totales.bruto = 0.00; this.totales.descuentos = 0.00; this.totales.descglobal = 0.00;
			this.operaciones.gravadas = 0.00; this.operaciones.inafectas = 0.00; 
			this.operaciones.exoneradas = 0.00; this.operaciones.gratuitas = 0.00;
			this.totales.igv = 0.00; this.totales.isc = 0.00; this.totales.icbper = 0.00;
			this.totales.valorventa = 0.00; this.totales.subtotal = 0.00; this.totales.importe = 0.00;
			t = this;
			var detalle = this.detalle.filter(function(p){
				t.totales.bruto = Number((t.totales.bruto + (p.cantidad * p.preciobruto) ).toFixed(2));
				t.totales.descuentos = Number((t.totales.descuentos + (p.cantidad * p.descuento) ).toFixed(2));

				if (p.codafectacionigv==10) {
					t.operaciones.gravadas = Number((t.operaciones.gravadas + p.subtotal - p.igv).toFixed(2));
				}
				if (p.codafectacionigv==20) {
					t.operaciones.exoneradas = Number((t.operaciones.exoneradas + p.subtotal).toFixed(2));
				}
				if (p.codafectacionigv==30) {
					t.operaciones.inafectas = Number((t.operaciones.inafectas + p.subtotal).toFixed(2));
				}
				if (p.codafectacionigv==21) {
					t.operaciones.gratuitas = Number((t.operaciones.gratuitas + p.subtotal).toFixed(2));
				}

				t.totales.igv = Number((t.totales.igv + p.igv).toFixed(2));
				t.totales.icbper = Number((t.totales.icbper + p.icbper).toFixed(2));

				t.totales.valorventa = Number((t.totales.valorventa + parseFloat(p.valorventa) ).toFixed(2));
				t.totales.subtotal = Number((t.totales.subtotal + parseFloat(p.subtotal) ).toFixed(2));
			});

			var subtotal_tem = this.operaciones.gravadas + this.operaciones.inafectas + this.operaciones.exoneradas + this.operaciones.gratuitas;
			this.totales.importe = Number((subtotal_tem + this.totales.igv + this.totales.icbper).toFixed(2));
		},

		/* DATOS GENERALES DE LA VENTA */

		netix_guardar: function(){
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE", "REGISTRAR ITEM PARA LA VENTA","error"); return false;
			}
			this.campos.fechacomprobante = $("#fechacomprobante").val();
			this.campos.fechakardex = $("#fechakardex").val();
			this.pagos.monto_efectivo = this.totales.importe;

			this.netix_vuelto(); this.netix_condicionpago(); this.campos.nro = this.correlativo
			$("#modal_pago").modal({backdrop: 'static', keyboard: false});
		},

		/* PAGO DE LA VENTA */

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
				this.pagos.monto_tarjeta = 0; this.pago.nrovoucher = "";
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
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle,"cuotas":this.cuotas,"pagos":this.pagos,"totales":this.totales}).then(function(data){
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
								this.netix_imprimir(data.body.codkardex);
							}
						});
						if (this.campos.codkardex == 0) {
							netix_sistema.netix_noti("VENTA REGISTRADA CORRECTAMENTE","VENTA REGISTRADA EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_noti("VENTA MODIFICADA CORRECTAMENTE","VENTA MODIFICADA EN EL SISTEMA","success");
						}
					}else{
						if (data.body.estado==2) {
							netix_sistema.netix_alerta("NO PUEDE EDITAR LA VENTA","PERTENECE A UNA CAJA CERRADA o TIENE AMORTIZACIONES","error");
						}else{
							netix_sistema.netix_alerta("ERROR AL REGISTRAR VENTA","ERROR DE RED","error");
						}
					}
				}
				netix_sistema.netix_fin(); this.netix_nueva_venta();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR VENTA","ERROR DE RED","error");
				netix_sistema.netix_fin(); this.netix_nueva_venta();
			});
		},
		netix_imprimir: function(codkardex){
			if ($("#formato").val()=="ticket") {
				window.open(url+"facturacion/formato/ticket/"+codkardex,"_blank");
			}else{
				var netix_url = url+"facturacion/formato/"+$("#formato").val()+"/"+codkardex;
				$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
			}

			/* if ($("#netix_formato").val()==0) {
				var netix_url = url+"facturacion/formato/a4/"+codkardex;
            	$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
			}else{
				if ($("#netix_formato").val()==1) {
					var netix_url = url+"facturacion/formato/a5/"+codkardex;
            		$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
				}else{
					window.open(url+"facturacion/formato/ticket/"+codkardex,"_blank");
				}
			} */
        },

        netix_editar: function(){
			this.titulo = "EDITAR VENTA 000"+netix_ventas.registro+" REGISTRADA"; this.campos.codkardex = netix_ventas.registro;
			this.$http.post(url+netix_controller+"/editar",{"codregistro":netix_ventas.registro}).then(function(data){
				var socio = eval(data.body.socio);
				$("#codpersona").empty().html("<option value='"+socio[0]["codpersona"]+"'>"+socio[0]["razonsocial"]+"</option>");

				$(".selectpicker").selectpicker("refresh"); $(".filter-option").text(socio[0]["razonsocial"]); 
				$("#codpersona").val(socio[0]["codpersona"]); this.campos.codpersona = socio[0]["codpersona"];

				this.campos.codkardex = data.body.campos[0].codkardex;
				this.campos.retirar = data.body.campos[0].retirar;
				this.campos.afectacaja = data.body.campos[0].afectacaja;
				$("#fechacomprobante").val(data.body.campos[0].fechacomprobante);
				$("#fechakardex").val(data.body.campos[0].fechakardex);
				this.campos.codmoneda = data.body.campos[0].codmoneda;
				this.campos.tipocambio = data.body.campos[0].tipocambio;
				this.campos.codcomprobantetipo = data.body.campos[0].codcomprobantetipo;
				this.campos.seriecomprobante = data.body.campos[0].seriecomprobante;
				this.campos.nro = data.body.campos[0].nrocomprobante;
				this.campos.condicionpago = data.body.campos[0].condicionpago;
				this.campos.descripcion = data.body.campos[0].descripcion;
				this.campos.direccion = data.body.campos[0].direccion;
				this.campos.cliente = data.body.campos[0].cliente;
				this.campos.codempleado = data.body.campos[0].codempleado;

				this.pagos.monto_efectivo = data.body.campos[0].monto_efectivo;
				this.pagos.vuelto_efectivo = data.body.campos[0].vuelto_efectivo;

				this.totales.flete = data.body.campos[0].flete;
				this.totales.gastos = data.body.campos[0].gastos;
				this.totales.subtotal = data.body.campos[0].subtotal;
				this.totales.descuentoglobal = data.body.campos[0].descuentoglobal;
				this.totales.igv = data.body.campos[0].igv;
				this.totales.importe = data.body.campos[0].importe;

				this.detalle = data.body.detalle; this.netix_totales();

				this.correlativo = this.campos.nro;

				this.campos.nrocuotas=data.body.cuot[0].nrocuotas;this.netix_series(); netix_sistema.netix_fin();
				$("#codcomprobantetipo").attr("disabled", "disabled"); $("#seriecomprobante").attr("disabled", "disabled");
			});
		}
	},
	created: function(){
		if (parseInt(netix_ventas.registro)!=0) {
			this.netix_editar();
		}else{
			this.netix_series(); netix_sistema.netix_fin();
		}
	}
});

document.addEventListener("keyup", buscar_f11, false);
function buscar_f11(e){
    var keyCode = e.keyCode;
    if(keyCode==122){
    	netix_operacion.netix_item();
    }
}