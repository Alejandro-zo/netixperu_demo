var netix_form = new Vue({
	el: "#netix_form",
	data: {
		estado: 0, 
		campos: campos,
		sucursales: []
	},
	methods: {
		netix_sucursales: function(){
			this.$http.post(url+netix_controller+"/sucursales", {"codregistro":netix_datos.registro}).then(function(data){
				this.sucursales = data.body;
			});
		},
		netix_guardar: function(){
			this.estado= 1;
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"sucursales":this.sucursales}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_noti("NOMBRE DE USUARIO YA EXISTE", "CAMBIAR DE USUARIO","error"); this.estado= 0;
				}else{
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
				}
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	},
	created: function(){
		this.netix_sucursales();
	}
});