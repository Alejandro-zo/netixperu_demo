var netix_form = new Vue({
	el: "#netix_form",
	data: {
		estado: 0, campos: campos
	},
	methods: {
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
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	}
});