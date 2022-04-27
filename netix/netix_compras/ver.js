var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {estado:0},
	methods: {
		netix_valorizar_precios: function(codkardex, fechakardex){
			swal({
				title: "SEGURO VALORIZAR PRECIOS ?",   
				text: "EL PRECIO DE COSTO SE ACTUALIZARÁ HASTA ESTA COMPRA", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, VALORIZAR"],
			}).then((willDelete) => {
				if (willDelete){
					this.estado = 1; netix_sistema.netix_inicio_guardar("RECALCULANDO PRECIOS . . .");
					this.$http.post(url+netix_controller+"/valorizar_precios/"+codkardex+"/"+fechakardex).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("RECALCULANDO CORRECTAMENTE","-","success");
						}else{
							netix_sistema.netix_alerta("ERROR AL RECALCULAR","ERROR DE RED","error");
						}
						netix_sistema.netix_fin();
					}, function(){
						netix_sistema.netix_alerta("ERROR AL RECALCULAR","ERROR DE RED","error"); netix_sistema.netix_fin();
					});
				}
			});
		}
	}
});