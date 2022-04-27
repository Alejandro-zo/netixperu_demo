var netix_form = new Vue({
	el: "#netix_form",
	data: {estado: 0, campos: campos, almacenes:[]},
	methods: {
		netix_almacenes : function(){
			if (this.campos.codsucursal!=undefined) {
				this.estado = 1;
				this.$http.get(url+netix_controller+"/almacenes/"+this.campos.codsucursal).then(function(data){
					this.almacenes = data.body; this.estado = 0;
				});
			}
		},
		netix_guardar: function(){
			this.estado= 1;
			this.$http.post(url+netix_controller+"/guardar", this.campos).then(function(data){
				if(data.body=="e"){
					netix_sistema.netix_alerta("YA EXISTE UN INVENTARIO", "EN ESTA SUCURSAL Y EN ESTE ALMACEN","error"); this.estado= 0;
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("INVENTARIO INICIADO CORRECTAMENTE", "PROCESO DE INVENTARIO INICIARIO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL CREAR INVENTARIO", "NO SE PUEDE INICIAR UN INVENTARIO AHORA","error");
					}
					netix_datos.netix_datos(); this.netix_cerrar();
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
		this.netix_almacenes();
	}
});