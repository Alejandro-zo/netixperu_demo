var netix_despacho = new Vue({
	el: "#netix_despacho",
	data: {
		estado:0, campos:campos, detalle: [], entregados: []
	},
	methods: {
		netix_detalle: function(){
			this.$http.get(url+netix_controller+"/detalle/"+this.campos.codkardex).then(function(data){
				this.detalle = data.body.detalle; this.entregados = data.body.entregados; netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL CARGAR DETALLE DE LA OPERACION","ERROR DE RED","error");
			});
		},

		netix_guardar: function(){
			var total = 0;
			for (var i = 0; i < this.detalle.length; i++) {
				total = total + parseFloat(this.detalle[i]["recoger"]);
			}
			if (total==0) {
				netix_sistema.netix_noti("LA CANTIDAD DE LOS ITEM DEBE SER MAYOR A CERO (MINIMO DE UN ITEM)","","error"); return false;
			}

			this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO OPERACION . . .");
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("OPERACION REGISTRADA","OPERACION REGISTRADA CORRECTAMENTE","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR LA OPERACION","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REALIZAR LA OPERACION","ERROR DE RED","error"); netix_sistema.netix_modulo();
			});
		},

		netix_eliminar(datos){
			swal({
				title: "SEGURO ELIMINAR ENTREGA ?",   
				text: "USTED ESTA POR ELIMINAR UNA ENTREGA", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ELIMINAR"],
			}).then((willDelete) => {
				if (willDelete) {
					netix_sistema.netix_inicio_guardar("ELIMINANDO OPERACION ENTREGA . . .");
					this.$http.post(url+netix_controller+"/eliminar",datos).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE", "UN REGISTRO ELIMINADO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDIÓ LA CONEXION !!! LO SENTIMOS","error");
						}
						this.netix_detalle();
					}, function(){
						alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
					});
				}
			});
		},

		netix_cerrar: function(){
			netix_sistema.netix_modulo();
		}
	},
	created: function(){
		this.netix_detalle();
	}
});