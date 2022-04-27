var netix_salidas = new Vue({
	el: "#netix_salidas",
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
		
		netix_nuevo:function(){
			netix_sistema.netix_inicio();
			this.$http.post(url+netix_controller+"/nuevo").then(function(data){
				$("#netix_sistema").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_fin();
			});
		},
		netix_seleccionar: function(registro){
			this.registro = registro;
		},
		netix_ver: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA SALIDA", "PARA VER EN EL SISTEMA LA SALIDA!!!","error");
			}else{
				$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
				this.$http.get(url+netix_controller+"/ver/"+this.registro).then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
				});
			}
		},
		netix_editar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA SALIDA", "PARA EDITAR EN EL SISTEMA LA SALIDA ALMACEN !!!","error");
			}else{
				$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
				this.$http.post(url+netix_controller+"/editar",{"codregistro":this.registro}).then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
					netix_sistema.netix_fin();
				});
			}
		},
		netix_eliminar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "PARA ELIMINAR EN EL SISTEMA UN REGISTRO!!!","error");
			}else{
				swal({
					title: "SEGURO ELIMINAR SALIDA DE ALMACEN ?",   
					text: "USTED ESTA POR ELIMINAR UNA SALIDA DE ALMACEN", 
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, ELIMINAR"],
				}).then((willDelete) => {
					if (willDelete) {
						this.$http.post(url+netix_controller+"/eliminar",{"codregistro":this.registro}).then(function(data){
							if (data.body==1) {
								netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE", "UN REGISTRO ELIMINADO EN EL SISTEMA","success");
							}else{
								netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDIÓ LA CONEXION !!! LO SENTIMOS","error");
							}
							this.netix_datos();
						}, function(){
							alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
						});
					}
				});
			}
		}
	},
	created: function(){
		this.netix_datos();
	}
});