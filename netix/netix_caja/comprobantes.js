var netix_form = new Vue({
	el: "#netix_form",
	data: {estado: 0, campos: campos, caja:false, caja_alerta:false, almacen:false,almacen_alerta:false, nota:false,nota_alerta:false},
	methods: {
		netix_tipocomprobante : function(){
			if(this.campos.codcomprobantetipo>=1 && this.campos.codcomprobantetipo<=2){
				if (this.campos.codsucursal!=undefined) {
					this.caja = true; this.caja_alerta = false;
					this.$http.get(url+"administracion/comprobantes/cajas/"+this.campos.codsucursal).then(function(data){
						$("#codcaja").empty().html(data.body); 
					});
				}
			}else{
				this.caja = false; this.caja_alerta = false; this.estado= 0;
			}

			if(this.campos.codcomprobantetipo>=3 && this.campos.codcomprobantetipo<=4){
				if (this.campos.codsucursal!=undefined) {
					this.almacen = true; this.almacen_alerta = false;
					this.$http.get(url+"administracion/comprobantes/almacenes/"+this.campos.codsucursal).then(function(data){
						$("#codalmacen").empty().html(data.body); 
					});
				}
			}else{
				this.almacen = false; this.almacen_alerta = false; this.estado= 0;
			}

			if(this.campos.codcomprobantetipo>=14 && this.campos.codcomprobantetipo<=15){
				if (this.campos.codsucursal!=undefined) {
					this.nota = true; this.nota_alerta = false;
					this.$http.get(url+"administracion/comprobantes/notas/"+this.campos.codsucursal).then(function(data){
						$("#codcomprobantetipo_ref").empty().html(data.body); 
					});
				}
			}else{
				this.nota = false; this.nota_alerta = false; this.estado= 0;
			}
		},
		netix_caja: function(){
			if (this.campos.codcomprobantetipo!=undefined && this.campos.codcaja!=undefined) {
				this.$http.get(url+"administracion/comprobantes/cajas_existe/"+this.campos.codcaja+"/"+this.campos.codcomprobantetipo).then(function(data){
					if(data.body==1){
						this.caja_alerta = true; this.estado= 1;
					}else{
						this.caja_alerta = false; this.estado= 0;
					}
				});
			}
		},
		netix_almacen: function(){
			if (this.campos.codcomprobantetipo!=undefined && this.campos.codalmacen!=undefined) {
				this.$http.get(url+"administracion/comprobantes/almacen_existe/"+this.campos.codalmacen+"/"+this.campos.codcomprobantetipo).then(function(data){
					if(data.body==1){
						this.almacen_alerta = true; this.estado= 1;
					}else{
						this.almacen_alerta = false; this.estado= 0;
					}
				});
			}
		},
		netix_notas: function(){
			if (this.campos.codcomprobantetipo!=undefined && this.campos.codcomprobantetipo_ref!=undefined) {
				this.$http.get(url+"administracion/comprobantes/notas_existe/"+this.campos.codcomprobantetipo_ref+"/"+this.campos.codcomprobantetipo).then(function(data){
					if(data.body==1){
						this.nota_alerta = true; this.estado= 1;
					}else{
						this.nota_alerta = false; this.estado= 0;
					}
				});
			}
		},

		netix_impresion: function(){
			if (this.campos.impresion==1) {
				this.campos.impresion = 0;
			}else{
				this.campos.impresion = 1;
			}
		},

		netix_editarcomprobante: function(){
			$("#codsucursal").attr("disabled","true"); $("#codcomprobantetipo").attr("disabled","true");
			$("#seriecomprobante").attr("disabled","true");

			this.$http.get(url+netix_controller+"/validar_serie/"+netix_datos.registro).then(function(data){
				this.campos.seriecomprobante_editar = data.body.serie;
				if (data.body.estado==0) {
					$("#seriecomprobante").removeAttr("disabled");
				}
			});
		},
		netix_guardar: function(){
			this.estado= 1;
			this.$http.post(url+netix_controller+"/guardar", this.campos).then(function(data){
				if (data.body==1) {
					if (this.campos.codregistro=="") {
						netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "UN NUEVO REGISTRO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN REGISTRO EDITADO EN EL SISTEMA","info");
					}
				}else{
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE REGISTRAR","error");
				}
				netix_datos.netix_opcion(); this.netix_cerrar();
			}, function(){
				netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "EL SISTEMA YA TIENE REGISTRADO ESTE TIPO DOCUMENTO CON ESTA SERIE","error");
				this.estado= 0;
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	},
	created: function(){
		if (netix_datos.registro!="0") {
			this.netix_editarcomprobante();
		}
	}
});