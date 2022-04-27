var netix_form_1 = new Vue({
	el: "#netix_form_1",
	data: { estado_1: 0, agregar: {codigo:$("#codigo_extencion").val(),descripcion: ""} },
	methods: {
		netix_guardar_1: function(tabla){
			this.estado_1 = 1;
			this.$http.post(url+"almacen/extenciones/guardar/"+tabla, this.agregar).then(function(data){
				if (data.body==0) {
					netix_sistema.netix_alerta("ATENCION USUARIO","OCURRIO UN ERROR AL REGISTRAR","error");
					$("#modal_extencion").modal("hide");
				}else{
					netix_sistema.netix_noti("GUARDADO CORRECTAMENTE","UN NUEVO REGISTRO EN EL SISTEMA","success");
					netix_form.netix_extencion(tabla,data.body); $("#modal_extencion").modal("hide");
				}
			},function(){
				netix_sistema.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error"); $("#modal_extencion").modal("hide");
			});
		}
	}
});