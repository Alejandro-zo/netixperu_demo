var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, registro:0, buscar: "", datos: [], transferencias:[], 
		campos_t:{"codmovimiento":"","codpersona":"","codcaja":0,"codtipopago":0,"importe":0,"codcomprobantetipo":0,
		"seriecomprobante":"","nrocomprobante":"","fechadocbanco":"","nrodocbanco":""},
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
		
		netix_nuevo: function(){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
			$(".netix_radio").removeAttr('checked'); this.registro = 0;
			this.$http.post(url+netix_controller+"/nuevo").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},
		netix_seleccionar: function(registro){
			this.registro = registro;
		},
		netix_ver: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "PARA VER EN EL SISTEMA EL REGISTRO!!!","error");
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
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "PARA EDITAR EN EL SISTEMA UN REGISTRO!!!","error");
			}else{
				$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
				this.$http.post(url+netix_controller+"/nuevo").then(function(data){
					this.$http.post(url+netix_controller+"/editar",{"codregistro":this.registro}).then(function(info){
						$("#netix_formulario").empty().html(data.body); var datos = eval(info.body);
						
						$.each(campos, function(key, value){
							campos[key] = datos[0][key];
						});
						netix_form.campos = campos;
					});
				},function(){
					netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
				});
			}
		},
		netix_eliminar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "PARA ELIMINAR EN EL SISTEMA UN REGISTRO!!!","error");
			}else{
				swal({
					title: "SEGURO ELIMINAR REGISTRO ?",   
					text: "USTED ESTA POR ELIMINAR UN REGISTRO", 
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, ELIMINAR"],
				}).then((willDelete) => {
					if (willDelete) {
						this.$http.post(url+netix_controller+"/eliminar",{"codregistro":this.registro}).then(function(data){
							if (data.body==1) {
								netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE", "UN REGISTRO ELIMINADO EN EL SISTEMA","success");
							}else{
								netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDI?? LA CONEXION !!! LO SENTIMOS","error");
							}
							this.netix_datos();
						}, function(){
							alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
						});
					}
				});
			}
		},
		netix_transferencias: function(){
			this.$http.post(url+netix_controller+"/transferencias").then(function(data){
				this.transferencias = data.body; $("#modal_transferencias").modal("show");
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},
		netix_aceptar_transferencia(datos){
			this.campos_t.codmovimiento = datos.codmovimiento;
			this.campos_t.codpersona = datos.codpersona;
			this.campos_t.codcaja = datos.codcaja;
			this.campos_t.codtipopago = datos.codtipopago;
			this.campos_t.importe = datos.importe;
			this.campos_t.codcomprobantetipo = datos.codcomprobantetipo;
			this.campos_t.seriecomprobante = datos.seriecomprobante;
			this.campos_t.nrocomprobante = datos.nrocomprobante;
			this.campos_t.fechadocbanco = datos.fechadocbanco;
			this.campos_t.nrodocbanco = datos.nrodocbanco;

			$("#modal_transferencias").modal("hide");
			swal({
				title: "SEGURO ACEPTAR ESTA TRANSFERENCIA DE CAJA ?",   
				text: "", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ACEPTAR"],
				content: {
				    element: "input",
				    attributes: {
				      	placeholder: "ESCRIBIR UNA REFERENCIA",
				      	type: "text",
				    },
				},
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.post(url+netix_controller+"/aceptar_transferencia",{"campos":this.campos_t,"referencia":$(".swal-content__input").val()}).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("TRANSFERENCIA ACEPTADA CORRECTAMENTE", "","success");
						}else{
							netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDI?? LA CONEXION !!! LO SENTIMOS","error");
						}
						$("#modal_transferencias").modal("hide"); this.netix_datos();
					}, function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
					});
				}else{
					$("#modal_transferencias").modal("show");
				}
			});
		}
	},
	created: function(){
		this.netix_datos();
	}
});