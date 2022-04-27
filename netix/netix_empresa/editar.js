var netix_form = new Vue({
	el: "#netix_form",
	data: {estado: 0, campos: campos},
	methods: {
		netix_guardar: function(){
			this.estado= 1; const formulario = new FormData($("#formulario")[0]);
			this.$http.post(url+netix_controller+"/guardar", formulario).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_noti("EMPRESA CONFIGURADA CORRECTAMENTE","DATOS GUARDADOS EN EL SISTEMA","success");
				}else{
					netix_sistema.netix_alerta("LA CLAVE DEL CERTIFICADO ES INCORRECTA","NO SE PUEDE GENERAR LOS ARCHIVOS PARA LA FACTURACION ELECTRONICA","error");
				}
				this.netix_cerrar(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error");
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	}
});