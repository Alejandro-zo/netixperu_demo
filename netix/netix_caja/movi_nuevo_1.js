var netix_movimiento = new Vue({
	el: "#netix_movimiento",
	data: {estado: 0, campos: campos, movimientobanco:0},
	methods: {
		netix_cajabanco: function(){
			if (this.campos.codtipopago==1) {
				this.movimientobanco = 0; $("#nrodocbanco").removeAttr("required");
			}else{
				this.movimientobanco = 1; $("#nrodocbanco").attr("required","true");
			}
		},
		netix_guardar: function(){
			if (netix_controller=="compras/compras") {
				var url_movimiento = "compras/compras/guardar_gasto";
			}else{
				var url_movimiento = "caja/movimientos/guardar";
			}
			this.campos.fechadocbanco = $("#fechadocbanco").val(); this.estado= 1; 
			this.$http.post(url+url_movimiento, this.campos).then(function(data){
				if (data.body==1) {
					if (this.campos.codregistro=="") {
						netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "UN NUEVO REGISTRO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN REGISTRO EDITADO EN EL SISTEMA","info");
					}
				}else{
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE REGISTRAR","error");
				}

				if (netix_controller=="compras/compras") {
					netix_compras.netix_datos();
				}else{
					// netix_datos.netix_datos();
				}
				this.netix_cerrar();
			}, function(){
				netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE GUARDAR EL MOVIMIENTO DE CAJA","error");
				this.estado= 0;
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	}
});