var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		estado:0, titulo: "REGISTRO NUEVA COMPRA", igvsunat:$("#igvsunat").val(), icbpersunat:$("#icbpersunat").val(), igv:false,
		series:[], detalle: [], cuotas: [],
		campos:{
			codkardex:0, codpersona:2, codmovimientotipo:2, codcomprobantetipo:"", seriecomprobante:"", nro:"", 
			fechacomprobante:"", fechakardex:"", codconcepto:12, descripcion:"REGISTRO POR COMPRA", cliente:"", direccion:"",
			codempleado:0, codmoneda:1, tipocambio:0.00, codcentrocosto:0, nroplaca:"", retirar:true, afectacaja:true,
			condicionpago:1, nrodias:30, nrocuotas:1, codcreditoconcepto:4, tasainteres:0, interes:0, totalcredito:0, porcdescuento:0.00, codpersona_convenio:0
		},
		item:{
			producto:"", unidad:"", cantidad:0, preciobruto:0, descuento:0, porcdescuento:0, preciosinigv:0, precio:0, 
			codafectacionigv:"", igv:0, valorventa:0, conicbper:0, icbper:0, subtotal:0, descripcion:""
		},
		pagos:{
			codtipopago:1, importe:0, fechadocbanco:"", nrodocbanco:""
		},
		operaciones:{
			gravadas:0.00, exoneradas:0.00, inafectas:0.00, gratuitas:0.00
		},
		totales:{
			flete:0.00, gastos:0.00, bruto:0.00, descuentos:0.00, descglobal:0.00, valorventa:0.00, igv:0.00, isc:0.00, icbper:0.00, 
			subtotal:0.00, importe:0.00
		}
	},
	methods: {

		/* FUNCIONES GENERALES DE LA COMPRA */

		netix_compra: function(){
			swal({
				title: "SEGURO REGISTRAR NUEVA COMPRA ?",   
				text: "LOS CAMPOS SE QUEDARAN VACIOS ", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, NUEVA COMPRA"],
			}).then((willDelete) => {
				if (willDelete){
					this.netix_nueva_compra();
				}
			});
		},
		netix_nueva_compra: function(){
			netix_sistema.netix_inicio();
			netix_compras.registro = 0; this.titulo = "REGISTRO NUEVA COMPRA"; this.campos.codkardex = 0;

			this.$http.post(url+netix_controller+"/nuevo").then(function(data){
				$("#netix_sistema").empty().html(data.body);
			});
		},
		netix_atras: function(){
			netix_sistema.netix_modulo();
		},
		netix_addproveedor: function(){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180); 
			this.$http.post(url+"compras/proveedores/nuevo_1").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_error_operacion(); 
			});
		},

		/* DETALLE DE LA COMPRA Y TOTALES */

		netix_item: function(){
			$(".compose").slideToggle(); $("#netix_tituloform").text("BUSCAR PRODUCTO"); 
			netix_sistema.netix_loader("netix_formulario",180); 

			this.$http.post(url+"almacen/productos/buscar/compras").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_error(); 
			});
		},
		netix_additem: function(producto, precio){
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
				if (producto.afectoigvcompra==1) {
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

				this.detalle.push({
					codproducto: producto.codproducto, producto: producto.descripcion, codunidad: producto.codunidad,
					unidad: producto.unidad, cantidad: 1, stock:producto.stock, control:0,
					preciobrutosinigv:0, preciobruto: 0, preciosinigv: null, precio: 0,
					preciorefunitario: producto.precio, porcdescuento: 0, descuento: 0,
					codafectacionigv: producto.afectacionigv, igv: producto.igv, conicbper: producto.afectoicbper, icbper: producto.icbper,
					valorventa: 0, subtotal:0, subtotal_tem:0, descripcion:"", calcular: producto.calcular
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
			/* 
				tipoprecio: -1: DESCUENTO PRECIO, -2: DESCUENTO PORCENTAJE, 0: BRUTO SIN IGV, 1: BRUTO CON IGV, 
				2: PRECIO SIN IGV, 3: PRECIO CON IGV, 4: CALCULAR DEL SUBTOTAL
			*/
			var porcentaje = 1;
			if (item.codafectacionigv==21) {
				item.preciobrutosinigv = 0; item.preciobruto = 0; item.porcdescuento = 0; item.descuento = 0; 
				item.preciosinigv = 0; item.precio = 0; item.igv = 0; item.valorventa = 0; item.subtotal = 0; 
			}
			if (item.codafectacionigv==10) {
				var porcentaje = (1 + this.igvsunat) / 100;
			}

			if (tipoprecio==-1) {
				item.porcdescuento = Number((item.descuento / item.preciobruto * 100).toFixed(2));
				item.preciosinigv = Number((item.preciobrutosinigv - item.descuento).toFixed(4)); tipoprecio = 2;
			}
			if (tipoprecio==-2) {
				item.descuento = Number((item.preciobruto * item.porcdescuento / 100).toFixed(4));
				item.preciosinigv = Number((item.preciobrutosinigv - item.descuento).toFixed(4)); tipoprecio = 2;
			}
			if(tipoprecio==0){
				item.preciobruto = Number((item.preciobrutosinigv * porcentaje).toFixed(4));
				item.descuento = Number((item.preciobrutosinigv * item.porcdescuento / 100).toFixed(4));

				item.preciosinigv = Number((item.preciobrutosinigv - item.descuento).toFixed(4));
				item.precio = Number((item.preciosinigv * porcentaje).toFixed(4));
			}
			if(tipoprecio==1){
				item.preciobrutosinigv = Number((item.preciobruto / porcentaje).toFixed(4));
				item.descuento = Number((item.preciobrutosinigv * item.porcdescuento / 100).toFixed(4));

				item.precio = Number((item.preciobruto - item.descuento).toFixed(4));
				item.preciosinigv = Number((item.precio / porcentaje).toFixed(4));
			}

			var descuento = item.descuento;
			if (item.descuento=="") {
				var descuento = 0;
			}
			
			if (tipoprecio==2) {
				item.precio = Number((item.preciosinigv * porcentaje).toFixed(4));
				item.preciobrutosinigv = Number((parseFloat(item.preciosinigv) + parseFloat(descuento) ).toFixed(4));
				item.preciobruto = Number((item.preciobrutosinigv * porcentaje).toFixed(4));
			}
			if (tipoprecio==3) {
				item.preciosinigv = Number((item.precio / porcentaje).toFixed(4));
				item.preciobruto = Number((parseFloat(item.precio) + parseFloat(descuento) ).toFixed(4));
				item.preciobrutosinigv = Number((item.preciobruto / porcentaje).toFixed(4));
			}

			if(tipoprecio==4){
				item.preciosinigv = Number((item.valorventa / item.cantidad).toFixed(4));
				item.preciobrutosinigv = Number((parseFloat(item.preciosinigv) + parseFloat(descuento) ).toFixed(4));

				item.precio = Number((item.preciosinigv * porcentaje).toFixed(4));
				item.preciobruto = Number((parseFloat(item.precio) + parseFloat(descuento) ).toFixed(4));
			}else{
				item.valorventa = Number((item.cantidad * item.preciosinigv).toFixed(2));
			}

			item.icbper = 0;
			if (item.conicbper==1) {
				item.icbper = Number((item.cantidad * this.icbpersunat).toFixed(2));
			}

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
			var porcentaje = 0;
			if (producto.codafectacionigv==10) {
				var porcentaje = this.igvsunat / 100;
			}

			producto.precio = Number((producto.preciosinigv + (producto.preciosinigv * porcentaje)).toFixed(4));
			if (producto.preciosinigv=="") {
				producto.preciobruto = Number((producto.descuento).toFixed(4));
			}else{
				producto.preciobruto = Number((producto.precio + producto.descuento).toFixed(4));
			}

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
			if (producto.precio=="") {
				producto.preciobruto = Number((producto.descuento).toFixed(4));
			}else{
				producto.preciobruto = Number((producto.precio + producto.descuento).toFixed(4));
			}

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
					t.operaciones.gravadas = Number((t.operaciones.gravadas + parseFloat(p.subtotal) - parseFloat(p.igv) ).toFixed(2));
				}
				if (p.codafectacionigv==20) {
					t.operaciones.exoneradas = Number((t.operaciones.exoneradas + parseFloat(p.subtotal) ).toFixed(2));
				}
				if (p.codafectacionigv==30) {
					t.operaciones.inafectas = Number((t.operaciones.inafectas + parseFloat(p.subtotal) ).toFixed(2));
				}
				if (p.codafectacionigv==21) {
					t.operaciones.gratuitas = Number((t.operaciones.gratuitas + parseFloat(p.subtotal) ).toFixed(2));
				}

				t.totales.igv = Number((parseFloat(t.totales.igv) + parseFloat(p.igv)).toFixed(2));
				t.totales.icbper = Number((parseFloat(t.totales.icbper) + parseFloat(p.icbper)).toFixed(2));

				t.totales.valorventa = Number((t.totales.valorventa + parseFloat(p.valorventa) ).toFixed(2));
				t.totales.subtotal = Number((t.totales.subtotal + parseFloat(p.subtotal)).toFixed(2));
			});

			if (this.totales.flete=="" && this.totales.gastos=="") {
				var gastos = 0;
			}else{
				if (this.totales.flete=="") {
					var gastos =  this.totales.gastos;
				}else{
					if (this.totales.gastos=="") {
						var gastos = this.totales.flete;
					}else{
						var gastos = this.totales.flete + this.totales.gastos;
					}
				}
			}

			var subtotal_tem = this.operaciones.gravadas + this.operaciones.inafectas + this.operaciones.exoneradas + this.operaciones.gratuitas;
			this.totales.importe = Number((subtotal_tem + parseFloat(gastos) + parseFloat(this.totales.igv) + parseFloat(this.totales.icbper) ).toFixed(2));
			this.netix_condicionpago();
		},
		netix_igv: function () {
			this.netix_totales();
			if (this.igv == true) {
				var valorigv = (parseFloat(this.igvsunat) + 100) / 100;
				this.totales.valorventa = Number((this.totales.subtotal / valorigv).toFixed(2));
				this.totales.igv = Number((this.totales.importe - this.totales.valorventa).toFixed(2));
				alert(this.totales.valorventa);
				this.totales.subtotal = Number((this.totales.valorventa).toFixed(2));
				this.totales.importe = Number((this.totales.valorventa + this.totales.igv + this.totales.icbper).toFixed(2));
			}
			/* var igvsunat = this.igvsunat; var icbpersunat = this.icbpersunat; var itemigv = this.igv;
			var detalle = this.detalle.filter(function(producto){
				producto.codafectacionigv = 20;
				if (itemigv == true) {
					producto.codafectacionigv = 10;
				}
				var porcentaje = 1;
				if (producto.codafectacionigv==10) {
					var porcentaje = (1 + igvsunat) / 100;
				}
				producto.preciosinigv = Number((producto.precio / porcentaje).toFixed(4));
				producto.preciobruto = Number((parseFloat(producto.precio) + parseFloat(producto.descuento)).toFixed(4));

				producto.valorventa = Number((producto.cantidad * producto.preciosinigv).toFixed(2));
				producto.subtotal = Number((producto.cantidad * producto.precio).toFixed(2));
				producto.igv = Number((producto.subtotal - producto.valorventa).toFixed(2));
				producto.icbper = 0;
				if (producto.conicbper==1) {
					producto.icbper = Number((producto.cantidad * icbpersunat).toFixed(2));
				}
			});
			this.netix_totales(); */
		},
		/* DATOS GENERALES DE LA COMPRA */

		netix_tipocambio(){
			if (this.campos.codmoneda==1) {
				this.campos.tipocambio = 1;
			}else{
				this.campos.fechacomprobante = $("#fechacomprobante").val();
				this.$http.get(url+"caja/tipocambios/consulta/"+this.campos.fechacomprobante).then(function(data){
					this.campos.tipocambio = data.body;
				});
			}
		},

		netix_condicionpago: function(){
			if (this.campos.condicionpago==2) {
				this.netix_cuotas(); this.campos.codconcepto = 14;
			}else{
				this.campos.codconcepto = 12;
			}
		},
		netix_cuotas: function(){
			var importe = Number((this.totales.importe/this.campos.nrocuotas).toFixed(1));
			var interes = Number(( (this.campos.tasainteres*importe/100) ).toFixed(1));
			var total = Number((importe + interes).toFixed(1));

    		var fecha = new Date();
    		this.campos.interes = Number(( (this.campos.tasainteres * this.totales.importe/100) ).toFixed(1));
			this.campos.totalcredito = Number(( parseFloat(this.totales.importe) + parseFloat(this.campos.interes) ).toFixed(1));
    		
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

		// GUARDAR LA COMPRA //

		netix_guardar: function(){
			this.pagos.importe = this.totales.importe;
			
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("REGISTRAR UN PRODUCTO EN EL DETALLE", "REGISTRAR ITEM PARA LA COMPRA","error"); return false;
			}
			
			if (this.campos.condicionpago==1) {
				if (parseFloat(this.totales.importe)!=parseFloat(this.pagos.importe)) {
					netix_sistema.netix_noti("EL IMPORTE DEL PAGO DEBE SER IGUAL AL TOTAL DE LA COMPRA","ACTUAL S/. "+this.pagos.importe,"error"); 
					return false;
				}
			}
			
			this.campos.fechacomprobante = $("#fechacomprobante").val();
			this.campos.fechakardex = $("#fechakardex").val();
			this.pagos.fechadocbanco = $("#fechadocbanco").val();

			swal({
				title: "SEGURO REGISTRAR LA COMPRA ?",   
				text: "VERIFIQUE SUS CAMPOS QUE TODO ESTE CORRECTO", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, REGISTRAR COMPRA"],
			}).then((willDelete) => {
				if (willDelete){
					this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO COMPRA . . .");
					this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle,"cuotas":this.cuotas,"pagos":this.pagos,"totales":this.totales}).then(function(data){
						if (data.body=="e") {
							netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
						}else{
							if (data.body==1) {
								if (this.campos.codkardex == 0) {
									netix_sistema.netix_noti("COMPRA REGISTRADA CORRECTAMENTE","COMPRA REGISTRADA EN EL SISTEMA","success");
								}else{
									netix_sistema.netix_noti("COMPRA MODIFICADA CORRECTAMENTE","COMPRA MODIFICADA EN EL SISTEMA","warning");
								}
							}else{
								if (data.body==2) {
									netix_sistema.netix_alerta("NO PUEDE EDITAR LA COMPRA","PERTENECE A UNA CAJA CERRADA o TIENE AMORTIZACIONES","error");
								}else{
									netix_sistema.netix_alerta("ERROR AL REGISTRAR COMPRA","ERROR DE RED","error");
								}
							}
						}
						netix_sistema.netix_fin(); this.netix_nueva_compra();
					}, function(){
						netix_sistema.netix_alerta("ERROR AL REGISTRAR COMPRA","ERROR DE RED","error");
						netix_sistema.netix_fin();
					});
				}
			});
		},
		netix_editar: function(){
			this.titulo = "EDITAR COMPRA 000"+netix_compras.registro+" REGISTRADA"; this.campos.codkardex = netix_compras.registro;
			this.$http.post(url+netix_controller+"/editar",{"codregistro":netix_compras.registro}).then(function(data){
				var socio = eval(data.body.socio);
				$("#codpersona").empty().html("<option value='"+socio[0]["codpersona"]+"'>"+socio[0]["razonsocial"]+"</option>");

				$(".selectpicker").selectpicker("refresh"); $(".filter-option").text(socio[0]["razonsocial"]); 
				$("#codpersona").val(socio[0]["codpersona"]); this.campos.codpersona = socio[0]["codpersona"];

				/* campos:{
					"codkardex":0,"codpersona":2,"retirar":true,"afectacaja":true,"codmovimientotipo":2,"fechacomprobante":"","fechakardex":"",
					"codmoneda":1,"tipocambio":0.00,"codcomprobantetipo":"","seriecomprobante":"","nrocomprobante":"","codconcepto":12,
					"condicionpago":1,"nrodias":30,"nrocuotas":1,"codcreditoconcepto":4,"tasainteres":0,"totalcredito":0,"descripcion":"REGISTRO POR COMPRA"
				}, */
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
				
				/* this.detalle.push({
					"stock":producto.stock,"control":producto.control, "descuentototal":0,"descuento":0,
					"calcular":producto.calcular
				}); */

				this.detalle = data.body.detalle; this.netix_totales(); netix_sistema.netix_fin();
			});
		}
	},
	created: function(){
		if (parseInt(netix_compras.registro)!=0) {
			this.netix_editar();
		}else{
			this.netix_tipocambio(); netix_sistema.netix_fin();
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