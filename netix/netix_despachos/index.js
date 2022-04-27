var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, registro:0, operacion:0, buscar: "", datos: [],
		filtro:{"codpersona":0,"seriecomprobante":"","nrocomprobante":""}, filtros: [],
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
		netix_seleccionar: function(registro,operacion){
			this.registro = registro; this.operacion = operacion;
		},
		
		netix_operacion: function(tipo){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA OPERACION","SELECCIONAR CON UN CHECK","error");
			}else{
				if(tipo==this.operacion){
					netix_sistema.netix_inicio();
					this.$http.get(url+netix_controller+"/nuevo/"+this.registro).then(function(data){
						$("#netix_sistema").empty().html(data.body);
					},function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
					});
				}else{
					if (tipo==20) {
						netix_sistema.netix_alerta("DEBE SELECCIONAR UNA VENTA","PARA REGISTRAR UN DESPACHO","error");
					}else{
						netix_sistema.netix_alerta("DEBE SELECCIONAR UNA COMPRA","PARA REGISTRAR UNA ENTREGA","error");
					}
				}
			}
		},
		netix_operacion_1: function(){
			this.$http.get(url+netix_controller+"/nuevo/"+this.registro).then(function(data){
				$("#netix_sistema").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_fin();
			});
		},

		netix_buscarkardex: function(){
			$("#modal_buscarkardex").modal("show");
		},
		netix_filtrar: function(){
			$filtros = []; netix_sistema.netix_inicio_guardar("CONSULTANDO Y BUSCANDO . . .");
			this.$http.post(url+netix_controller+"/filtrar", this.filtro).then(function(data){
				this.filtros = data.body; netix_sistema.netix_fin();
			});
		},
		netix_seleccionar_1: function(registro){
			this.registro = registro; $("#modal_buscarkardex").modal("hide"); netix_sistema.netix_inicio(); 
			var self = this;
			setTimeout(function(){
  				self.netix_operacion_1();
			},300);
		}
	},
	created: function(){
		this.netix_datos();
	}
});