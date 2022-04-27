var netix_administrar = new Vue({
	el: "#netix_administrar",
	data: {netix_almacen : "1", netix_caja : "1"},
	methods: {
		netix_noti: function(titulo,mensaje,tipo){
			new PNotify({
				title: titulo,
				text: mensaje,
				type: tipo,
				styling: 'bootstrap3'
			});
		},
		administrar: function(netix_sucursal){
			if ($("#netix_almacen_"+netix_sucursal).val()=="" || $("#netix_almacen_"+netix_sucursal).val()==null) {
				this.netix_noti("DEBE SELECCIONAR UN ALMACEN", "REGISTRAR ALMACEN","error"); return 0;
			}
			if ($("#netix_caja_"+netix_sucursal).val()=="" || $("#netix_caja_"+netix_sucursal).val()==null) {
				this.netix_noti("DEBE SELECCIONAR UNA CAJA", "REGISTRAR CAJA","error"); return 0;
			}
			
			this.$http.post(url+"netix/netix_web",{"sucursal":netix_sucursal,"almacen":$("#netix_almacen_"+netix_sucursal).val(),"caja":$("#netix_caja_"+netix_sucursal).val()}).then(function(data){
				window.location.href = url+"netix/w";
			}, function(){
				netix_sistema.alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
			});
		}
	}
});