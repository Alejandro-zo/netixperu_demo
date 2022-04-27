var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {
		campos:{
			"codmotivonota":1,"codpersona":2,"codmovimientotipo":26,"codkardex_ref":0,"seriecomprobante":"","nrocomprobante":"","codcomprobantetipo_ref":0,"seriecomprobante_ref":"",
			"nrocomprobante_ref":"","descripcion":"","cliente":"","direccion":"","codmoneda":1, "tipocambio":0
		},
		estado:0, kardex_id:0, comprobantes:[], detalle: [], totales: {"valorventa":0.00,"igv":0.00,"importe":0.00},
	},
	methods: {
		netix_motivos: function(){
			// Motivos de las Notas de Credito //
		},
		netix_series: function(){
			if (this.campos.codcomprobantetipo_ref!=undefined) {
				this.estado = 1;
				this.$http.get(url+"caja/controlcajas/netix_seriescaja/"+this.campos.codcomprobantetipo_ref).then(function(data){
					this.series_ref = data.body.series; this.estado = 0;
				});
			}
		},
		netix_comprobantes: function(){
			if (this.campos.codpersona!="") {
				this.estado = 1;
				this.$http.get(url+netix_controller+"/comprobantes/"+this.campos.codpersona+"/"+$("#fechacomprobante_ref").val()).then(function(data){
					this.comprobantes = data.body.comprobantes; this.estado = 0;
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				});
			}else{
				this.comprobantes = []; netix_sistema.netix_noti("SELECCIONAR PROVEEDOR DE LA COMPRA", "PARA FILTRAR LOS COMPROBANTES","error");
			}
		},
		netix_detalle: function(datos){
			if (datos.codmotivonota==0) {
				$("#"+this.kardex_id).css({"background-color":"#fff","color":"#000"}); this.kardex_id = datos.codkardex;
				$("#"+datos.codkardex).css({"background-color":"#13a89e","color":"#fff"});

				this.campos.codkardex_ref = datos.codkardex; this.campos.codcomprobantetipo_ref = datos.codcomprobantetipo;
				this.campos.seriecomprobante_ref = datos.seriecomprobante; this.campos.nrocomprobante_ref = datos.nrocomprobante;
				this.campos.cliente = datos.cliente; this.campos.direccion = datos.direccion; 
				this.campos.codmoneda = datos.codmoneda; this.campos.tipocambio = datos.tipocambio;

				this.$http.get(url+netix_controller+"/detalle/"+datos.codkardex).then(function(data){
					this.detalle = data.body.detalle; var datos = eval(data.body.totales);
					this.totales.valorventa = datos[0]["valorventa"]; this.totales.igv = datos[0]["igv"]; this.totales.importe = datos[0]["importe"];
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				});
			}else{
				netix_sistema.netix_noti("NOTA DE CREDITO GENERADA CON EL MOTIVO: "+datos.motivo,"","error");
			}
		},
		
		netix_calcular: function(dato){
			this.totales.valorventa = Number((this.totales.valorventa - dato.valorventa ).toFixed(2));
			this.totales.igv = Number((this.totales.igv - dato.igv ).toFixed(2));
			this.totales.importe = Number((this.totales.importe - dato.subtotal ).toFixed(2));

			dato.subtotal = Number((dato.cantidad * dato.precio ).toFixed(2));
			if (dato.codafectacionigv=="10") {
				dato.igv = Number(( dato.subtotal - (dato.subtotal / 1.18) ).toFixed(2));
			}
			dato.valorventa = Number((dato.subtotal - dato.igv ).toFixed(2));
			
			this.totales.valorventa = Number((this.totales.valorventa + dato.valorventa ).toFixed(2));
			this.totales.igv = Number((this.totales.igv + dato.igv ).toFixed(2));
			this.totales.importe = Number((this.totales.importe + dato.subtotal ).toFixed(2));
		},
		netix_quitardetalle: function(index, dato){
			this.detalle.splice(index,1); this.netix_calcular(dato);
		},

		netix_guardar: function(){
			if (this.detalle.length==0) {
				netix_sistema.netix_noti("DEBE TENER MINIMO UN ITEM LA NOTA","PARA REGISTRAR LA NOTA ELECTRONICA","error"); 
				return false;
			}

			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO NOTA ELECTRONICA . . .");
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle,"totales":this.totales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						netix_sistema.netix_noti("NOTA REGISTRADA CORRECTAMENTE","NOTA REGISTRADA EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR NOTA ELECTRONICA","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR NOTA ELECTRONICA","ERROR DE RED","error"); netix_sistema.netix_fin();
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