var netix_pedidos = new Vue({
	el: "#netix_pedidos",
	data: {
		cargando: true, registro:0, buscar: "", datos: [],  sessioncaja:0, 
		paginacion: {"total":0, "actual":1, "ultima":0, "desde":0, "hasta":0}, offset: 3
	},
	computed: {
		netix_actual: function(){
			return this.paginacion.actual;
		},
		netix_paginas: function(){
			if (!this.paginacion.hasta) {
				return [];
			}
			var desde = this.paginacion.actual - this.offset;
			if (desde < 1) {
				desde = 1;
			}
			var hasta = desde + (this.offset * 2);
			if (hasta >= this.paginacion.ultima) {
				hasta = this.paginacion.ultima;
			}

			var paginas = [];
			while(desde <= hasta){
				paginas.push(desde); desde++;
			}
			return paginas;
		}
	},
	methods: {
		netix_datos: function(){
			this.cargando = true; this.registro = 0;
			this.$http.post(url+netix_controller+"/lista",{"buscar":this.buscar, "pagina":this.paginacion.actual}).then(function(data){
				this.datos = data.body.lista; this.paginacion = data.body.paginacion;
				this.cargando = false; netix_sistema.netix_fin();
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error"); this.cargando = false;
			});
		},
		netix_buscar: function(){
			this.paginacion.actual = 1; this.netix_datos();
		},
		netix_paginacion: function(pagina){
			this.paginacion.actual = pagina; this.netix_datos();
		},
		
		netix_seleccionar: function(registro){
			this.registro = registro;
		},
		netix_nuevo: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN CLIENTE", "PARA REGISTRAR UN NUEVO PEDIDO !!!","error");
			}else{
				netix_sistema.netix_inicio();
				this.$http.get(url+netix_controller+"/nuevo/"+this.registro).then(function(data){
					$("#netix_sistema").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
					netix_sistema.netix_fin();
				});
			}
		},
		netix_historial: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN CLIENTE", "PARA VER EL HISTORIAL DE PEDIDOS !!!","error");
			}else{
				netix_sistema.netix_inicio();
				this.$http.get(url+netix_controller+"/historial/"+this.registro).then(function(data){
					$("#netix_sistema").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
					netix_sistema.netix_fin();
				});
			}
		}
	},
	created: function(){
		this.netix_datos();
	}
});