var netix_ingresos = new Vue({
	el: "#netix_ingresos",
	data: {
		cargando: true, registro:0, buscar: "", datos: [], 
		paginacion: {"total":0, "actual":1, "ultima":0, "desde":0, "hasta":0}, offset: 3,
		estado_envio:0,	kardex_ref:0, transferencias:1, texto_transferencia:"", listatransferencias:[], detalletransferencia:[]
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
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN INGRESO", "PARA VER EN EL SISTEMA EL INGRESO!!!","error");
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
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN INGRESO", "PARA EDITAR EN EL SISTEMA EL INGRESO ALMACEN !!!","error");
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
					title: "SEGURO ELIMINAR INGRESO DE ALMACEN ?",   
					text: "USTED ESTA POR ELIMINAR UNA INGRESO DE ALMACEN", 
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
		},

		netix_trasferencias: function(){
			this.transferencias = 1; $("#modal_transferencias").modal("show");
			this.$http.get(url+netix_controller+"/transferencias").then(function(data){
				this.listatransferencias = data.body;
			},function(){
				netix_sistema.netix_alerta("NO SE PUEDE ABRIR LA VENTANA DE TRANSAFERENCIAS DE ALMACEN", "ERROR DE RED","error"); 
				$("#modal_transferencias").modal("hide");
			});
		},
		netix_detalle: function(campo){
			this.texto_transferencia = "ALM: "+campo.almacen + " *** REF: "+campo.seriecomprobante+" - "+campo.nrocomprobante;
			this.$http.get(url+netix_controller+"/transferencia_detalle/"+campo.codkardex).then(function(data){
				this.transferencias = 0; this.detalletransferencia = data.body; this.kardex_ref = campo.codkardex; this.estado_envio = 0;
			},function(){
				netix_sistema.netix_alerta("NO SE PUEDE ABRIR LA VENTANA DEL DETALLE DE LA TRANSAFERENCIA", "ERROR DE RED","error"); 
				this.transferencias = 1;
			});
		},
		netix_calcular: function(campo){
			this.detalletransferencia.subtotal = this.detalletransferencia.subtotal - campo.subtotal;
			campo.subtotal = campo.cantidad * campo.precio;
			this.detalletransferencia.subtotal = this.detalletransferencia.subtotal + campo.subtotal;
		},
		netix_guardartransferencia: function(){
			this.estado_envio = 1; netix_sistema.netix_inicio_guardar("GUARDANDO Y ACEPTANDO LA TRANSFERENCIA DE ALMACEN . . .");
			this.$http.post(url+netix_controller+"/guardar_transferencia", {"kardex_ref":this.kardex_ref,"detalle":this.detalletransferencia}).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SU SESION DE USUARIO A TERMINADO","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body==1) {
						netix_sistema.netix_alerta("TRANSFERENCIA DE ALMACEN REGISTRADO","INGRESO DE ALMACEN EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("ERROR AL ACEPTAR LA TRANSFERENCIA DE ALMACEN","ERROR DE RED","error");
					}
				}
				netix_sistema.netix_fin(); this.netix_datos(); $("#modal_transferencias").modal("hide");
			}, function(){
				netix_sistema.netix_alerta("ERROR AL ACEPTAR LA TRANSFERENCIA DE ALMACEN","ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		}
	},
	created: function(){
		this.netix_datos();
	}
});