var netix_notas = new Vue({
	el: "#netix_notas",
	data: {
		cargando: true, registro:0, buscar: "", datos: [],
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
		netix_nuevo:function(){
			if ($("#sessioncaja").val()==0) {
				netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE REALIZAR NOTAS ELECTRONICAS","error"); 
			}else{
				netix_sistema.netix_inicio();
				this.$http.post(url+netix_controller+"/nuevo").then(function(data){
					$("#netix_sistema").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
					netix_sistema.netix_fin();
				});
			}
		},
		netix_ver: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA NOTA", "PARA VER EN EL SISTEMA LA NOTA ELECTRONICA !!!","error");
			}else{
				$(".compose").slideToggle(); $("#netix_tituloform").text("INFORMACION DE LA NOTA REGISTRADA"); 
				netix_sistema.netix_loader("netix_formulario",180);

				this.$http.get(url+netix_controller+"/ver/"+this.registro).then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
				});
			}
		},
		netix_imprimir: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA VENTA", "PARA IMPRIMIR EN EL SISTEMA LA VENTA !!!","error");
			}else{
				if ($("#formato").val()=="ticket") {
					window.open(url+"facturacion/formato/ticket/"+this.registro,"_blank");
				}else{
					var netix_url = url+"facturacion/formato/"+$("#formato").val()+"/"+this.registro;
					$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
				}
			}
        }
	},
	created: function(){
		this.netix_datos();
	}
});