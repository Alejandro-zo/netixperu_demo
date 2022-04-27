var netix_compras = new Vue({
	el: "#netix_compras",
	data: {
		cargando: true, registro:0, buscar: "", datos: [], fechas:{"filtro":1,"desde":"","hasta":""},
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
			this.fechas.desde = $("#fecha_desde").val(); this.fechas.hasta = $("#fecha_hasta").val();
			this.cargando = true; this.registro = 0;
			
			this.$http.post(url+netix_controller+"/lista",{"buscar":this.buscar,"fechas":this.fechas,"pagina":this.paginacion.actual}).then(function(data){
				this.datos = data.body.lista; this.paginacion = data.body.paginacion;
				this.cargando = false; netix_sistema.netix_fin();
			},function(){
				netix_sistema.netix_error(); this.cargando = false;
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
			if ($("#almacen").val()!=2) {
				netix_sistema.netix_alerta("ATENCION USUARIO","DEBES CONFIGURAR LOS COMPROBANTES DE ALMACEN","error");
			}else{
				if ($("#caja").val()==0) {
					netix_sistema.netix_alerta("ATENCION USUARIO","DEBES APERTURAR LA CAJA PARA LAS COMPRAS","error"); 
				}else{
					netix_sistema.netix_inicio();
					this.$http.post(url+netix_controller+"/nuevo").then(function(data){
						$("#netix_sistema").empty().html(data.body);
					},function(){
						netix_sistema.netix_error();
					});
				}
			}
		},
		netix_ver: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA COMPRA", "PARA VER EN EL SISTEMA LA COMPRA !!!","error");
			}else{
				$(".compose").slideToggle(); $("#netix_tituloform").text("INFORMACION DE LA COMPRA REGISTRADA"); 
				netix_sistema.netix_loader("netix_formulario",180);

				this.$http.get(url+netix_controller+"/ver/"+this.registro).then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
				});
			}
		},
		netix_editar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA COMPRA", "PARA EDITAR EN EL SISTEMA LA COMPRA !!!","error");
			}else{
				// EDITAR DE COMPRAS CON TODO DETALLE //
				if ($("#sessioncaja").val()==0) {
					netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE EDITAR COMPRAS","error"); 
				}else{
					netix_sistema.netix_inicio();
					this.$http.post(url+netix_controller+"/nuevo").then(function(data){
						$("#netix_sistema").empty().html(data.body);
					},function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
						netix_sistema.netix_fin();
					});
				}
				/* $(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
				this.$http.post(url+netix_controller+"/editar",{"codregistro":this.registro}).then(function(data){
					$("#netix_formulario").empty().html(data.body);
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
					netix_sistema.netix_fin();
				}); */
			}
		},
		netix_egresos: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA COMPRA", "PARA CARGAR LOS GASTOS DE TRASPORTE O OTROS A LA COMPRA","error");
			}else{
				if ($("#sessioncaja").val()==0) {
					netix_sistema.netix_noti("ESTIMADO USUARIO SU CAJA NO ESTA APERTURADA", "NO PUEDE REGISTRAR GASTOS","error"); 
				}else{
					$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
					this.$http.get(url+"caja/movimientos/nuevo_1/2/"+this.registro).then(function(data){
						$("#netix_formulario").empty().html(data.body);
					},function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
					});
				}
			}
		},
		netix_eliminar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UNA COMPRA", "PARA ELIMINAR EN EL SISTEMA UNA COMPRA !!!","error");
			}else{
				swal({
					title: "SEGURO ELIMINAR COMPRA ?",   
					text: "USTED ESTA POR ELIMINAR UNA COMPRA", 
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, ELIMINAR"],
					content: {
					    element: "input",
					    attributes: {
					      	placeholder: "PORQUE DESEAS ELIMINAR LA COMPRA",
					      	type: "text",
					    },
					},
				}).then((willDelete) => {
					if (willDelete) {
						this.$http.post(url+netix_controller+"/eliminar",{"codregistro":this.registro,"observaciones":$(".swal-content__input").val()}).then(function(data){
							if (data.body==1) {
								netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE", "UN REGISTRO ELIMINADO EN EL SISTEMA","success");
							}else{
								if (data.body==2) {
									netix_sistema.netix_alerta("NO PUEDE ANULAR LA COMPRA AL CREDITO", "DEBES ANULAR EL CREDITO","error");
								}else{
									netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDIÓ LA CONEXION !!! LO SENTIMOS","error");
								}
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