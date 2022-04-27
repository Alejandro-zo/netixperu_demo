var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, registro:0, buscar: "", codpersona:0, datos: [],
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
			this.$http.post(url+netix_controller+"/lista",{"buscar":this.buscar, "codpersona":this.codpersona, "pagina":this.paginacion.actual}).then(function(data){
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
		
		netix_resumen: function(){
			if (this.codpersona == 0) {
				netix_sistema.netix_alerta("SELECCIONE EMPRESA CONVENIO", "SELECCIONE","error");
			}else{
				window.open(url+netix_controller+"/pdf_resumen/"+this.codpersona,"_blank");
			}
		},
		netix_detallado: function() {
			if (this.codpersona == 0) {
				netix_sistema.netix_alerta("SELECCIONE EMPRESA CONVENIO", "SELECCIONE","error");
			}else{
				window.open(url+netix_controller+"/pdf_detallado/"+this.codpersona,"_blank");
			}
		}
	},
	created: function(){
		this.netix_datos();
	}
});