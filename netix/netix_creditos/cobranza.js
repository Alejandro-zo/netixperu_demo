var netix_cobranza = new Vue({
	el: "#netix_cobranza",
	data: {
		campos:{
			"codpersona":netix_creditos.registro,"codconcepto":19,"codcomprobantetipo":1,"codtipopago":1,"importe":0,"vuelto":0,
			"fechadocbanco":$("#fechadocbanco").val(),"nrodocbanco":"","total":0,"descripcion":"COBRO DE CUOTAS"
		},
		estado:0, cuotas: [], cuotascobrar: []
	},
	methods: {
		netix_cuotas: function(){
			this.$http.get(url+netix_controller+"/cuotas/"+netix_creditos.registro).then(function(data){
				if (data.body=="") {
					this.estado = 1;
				}
				this.cuotas = data.body; netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL CARGAR CUOTAS","ERROR DE RED","error");
			});
		},
		netix_cobrar: function(index,cuota){
			if ($("#"+index).is(":checked")){
				$("#"+index).attr("disabled","true");
				this.cuotascobrar.push({
					"item":index,"codcredito":cuota.codcredito,"nrocuota":cuota.nrocuota,"total":parseFloat(cuota.total),
					"importe":parseFloat(cuota.saldo),"saldo":0.00,"cobrar":parseFloat(cuota.saldo),"cobrartem":parseFloat(cuota.saldo)
				});
				this.campos.total = Number(( (this.campos.total + parseFloat(cuota.saldo)) ).toFixed(1)); this.netix_vuelto();
			}
		},
		netix_anularcuota:function(index,cuota){
			$("#"+cuota.item).removeAttr("disabled"); $("#"+cuota.item).removeAttr("checked");

			this.campos.total = this.campos.total - cuota.cobrar;
			this.cuotascobrar.splice(index,1); this.netix_vuelto();
		},
		netix_calcular: function(cuota){
			this.campos.total = this.campos.total - cuota.cobrartem;
			cuota.saldo = cuota.importe - cuota.cobrar; cuota.cobrartem = cuota.cobrar;
			this.campos.total = this.campos.total + cuota.cobrartem; this.netix_vuelto();
		},
		netix_vuelto: function(){
			if (this.campos.codtipopago==1) {
				this.campos.vuelto = Number((this.campos.importe - this.campos.total).toFixed(2));
				if (this.campos.vuelto < 0) {
					this.campos.vuelto = 0; this.estado = 1;
				}else{
					this.estado = 0;
				}
			}else{
				this.estado = 0;
			}
		},

		netix_guardar: function(){
			if (this.cuotascobrar.length==0) {
				netix_sistema.netix_noti("DEBE SELECCIONAR MINIMO UNA CUOTA PARA GUARDAR EL PAGO","","error"); 
				return false;
			}

			if (this.campos.codtipopago==1) {
				if (this.campos.importe<this.campos.total) {
					netix_sistema.netix_noti("EL IMPORTE ENTREGADO","DEBE SER MAYOR O IGUAL AL TOTAL","error"); 
					return false;
				}
			}else{
				if(this.campos.importe!=this.campos.total){
					netix_sistema.netix_noti("EL IMPORTE DEBE SER S/. "+this.campos.total,"LOS IMPORTE NO COINCIDEN","error"); 
					return false;
				}
			}
			this.campos.fechadocbanco = $("#fechadocbanco").val();
			
			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO COBRO DEL CREDITO . . .");
			this.$http.post(url+netix_controller+"/pagar", {"campos":this.campos,"cuotas":this.cuotascobrar}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("COBRANZA REGISTRADA","CUOTA DE CREDITO COBRADO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR COBRANZA","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR COBRANZA","ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},
		netix_cerrar: function(){
			netix_sistema.netix_modulo();
		}
	},
	created: function(){
		this.netix_cuotas();
	}
});