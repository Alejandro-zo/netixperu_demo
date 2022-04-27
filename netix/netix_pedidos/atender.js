var netix_atender = new Vue({
	el: "#netix_atender",
	data: {estado:0, atender:[], totales:[]},
	methods: {
		netix_atender_pedido: function(){
			this.$http.post(url+netix_controller+"/netix_atenciones",{"codpedido":$("#codpedido").val()}).then(function(data){
				this.atender = data.body.detalle; this.totales = data.body.totales;
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
			});
		},
		netix_mas_menos: function(pedido,tipo){
			if (tipo==1) {
				if (pedido.falta!=pedido.atender) {
					pedido.atender = pedido.atender + 1;
				}
			}else{
				if (pedido.atender>0) {
					pedido.atender = pedido.atender - 1;
				}
			}
		},
		netix_atender: function(){
			var atender = 0;
			for (var i = 0; i < this.atender.length; i++) {
				if (this.atender[i]["atender"]!="") {
					atender = atender + parseFloat(this.atender[i]["atender"]);
				}
			}
			if (atender==0) {
				netix_sistema.netix_noti("NO HAY PEDIDOS PARA ATENDER","MINIMO DEBE HABER UNA CANTIDAD ATENDIDA","error");
			}else{
				this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO ATENCION . . .");
				this.$http.post(url+netix_controller+"/guardar_atencion",{"atender":this.atender}).then(function(data){
					if (data.body==1) {
						netix_sistema.netix_noti("ATENCION REGISTRADA CORRECTAMENTE","PEDIDO ATENDIDO","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL REGISTRAR ATENCION","ERROR DE RED","error");
					}
					netix_sistema.netix_fin(); netix_historial.netix_pedidos(); this.netix_cerrar();
				}, function(){
					netix_sistema.netix_alerta("ERROR AL REGISTRAR ATENCION","ERROR DE RED","error");
					netix_sistema.netix_fin(); this.netix_cerrar();
				});
			}
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	},
	created: function(){
		this.netix_atender_pedido();
	}
});