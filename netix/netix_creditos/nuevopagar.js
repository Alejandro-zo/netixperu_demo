var netix_nuevocredito = new Vue({
	el: "#netix_nuevocredito",
	data: {
		campos:{
			"codregistro":"","codpersona":netix_creditos.registro,"fechacredito":$("#fecha").val(),"fechainicio":$("#fecha").val(),
			"nrodias":30,"nrocuotas":1,"codcreditoconcepto":2,"codcajaconcepto":8,"codtipopago":1,"fechadocbanco":$("#fechadocbanco_ref").val(),
			"nrodocbanco":"","importe":"","tasainteres":0,"interes":0,"total":0,"afectacaja":true,"referencia":"","codpersona_convenio":1
		},
		estado:0, cuotas: []
	},
	methods: {
		netix_fecha: function(){
			this.campos.fechainicio = $("#fechainicio").val();
			this.campos.fechacredito = $("#fechacredito").val();
		},
		netix_fechamovimiento: function(){
			this.campos.fechadocbanco = $("#fechadocbanco").val();
		},

		netix_calcular: function(){
			var importe = Number((this.campos.importe/this.campos.nrocuotas).toFixed(1));
			var interes = Number(( (this.campos.tasainteres*importe/100) ).toFixed(1));
			var total = Number((importe + interes).toFixed(1));
			
			var fechainicio = String(this.campos.fechainicio).split("-");
    		var fecha = new Date(fechainicio[0]+"/"+fechainicio[1]+"/"+fechainicio[2]);

    		this.campos.interes = Number(( (this.campos.tasainteres * this.campos.importe/100) ).toFixed(1));
    		if (this.campos.importe=="") {
    			this.campos.total = 0;
    		}else{
    			this.campos.total = Number(( parseFloat(this.campos.importe) + parseFloat(this.campos.interes) ).toFixed(1));
    		}

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
					importe = Number(( this.campos.importe - parseFloat(suma_importe) ).toFixed(1));
					total = Number(( this.campos.total - parseFloat(suma_total) ).toFixed(1));
				}else{
					suma_importe = Number(( parseFloat(suma_importe) + parseFloat(importe) ).toFixed(1));
					suma_total = Number(( parseFloat(suma_total) + parseFloat(total) ).toFixed(1));
				}

				this.cuotas.push({
					"nrocuota":i,"fechavence":fechavence,"importe":importe,"tasa":this.campos.tasainteres,
					"interes":interes,"total":total
				});
			}
		},
		netix_guardar: function(){
			if (this.cuotas.length==0) {
				netix_sistema.netix_noti("DEBE INGRESAR UN MONTO","NO SE ENCONTRARON CUOTAS","error"); 
				return false;
			}

			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO CREDITO POR PAGAR . . .");
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"cuotas":this.cuotas}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("CREDITO POR PAGAR REGISTRADO","CREDITO REGISTRADO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR CREDITO","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL CUENTA POR PAGAR","ERROR DE RED","error");
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