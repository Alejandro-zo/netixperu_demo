var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, registro:0, buscar: "", datos: [], 
		editar:{"codinventario":0}, editardetalle:[],
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
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error"); netix_sistema.netix_fin();
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
			this.$http.post(url+netix_controller+"/nuevo").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},

		netix_inventario: function(codinventario){
			this.registro = codinventario; netix_sistema.netix_inicio();
			this.$http.post(url+netix_controller+"/inventario/"+this.registro).then(function(data){
				$("#netix_sistema").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},
		netix_verinventario: function(codinventario){
			this.registro = codinventario; netix_sistema.netix_inicio();
			this.$http.post(url+netix_controller+"/verinventario/"+this.registro).then(function(data){
				$("#netix_sistema").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},

		netix_masproductos : function(){
			netix_sistema.netix_inicio();
			this.$http.get(url+netix_controller+"/mas_productos_inventario/"+this.editar.codinventario).then(function(data){
				if (data.body=="") {
					netix_sistema.netix_noti("NO HAY PRODUCTO PARA AGREGAR","PRODUCTOS ACTUALIZADOS","error");
				}else{
					netix_sistema.netix_noti("PRODUCTOS CARGADOS CORRECTAMENTE","PRODUCTOS EN EL INVENTARIO","success");
				}
				netix_sistema.netix_fin();
			});
		},
		netix_editarinventario: function(codinventario){
			this.editar.codinventario = codinventario; this.editardetalle = [];
			$("#codproducto").empty().html("<option value=''>SELECCIONE PRODUCTO</option>");
			$(".selectpicker").selectpicker("refresh"); $(".filter-option").text("SELECCIONE PRODUCTO"); 
			$("#codproducto").val(""); $("#editar_inventario").modal("show");
		},
		netix_unidades: function(){
			this.$http.get(url+netix_controller+"/productos_unidades/"+this.editar.codinventario+"/"+$("#codproducto").val()).then(function(data){
				this.editardetalle = data.body;
			});
		},
		netix_guardar_editar: function(){
			if ($("#codproducto").val()=="") {
				$("#codproducto").focus(); return false;
			}
			netix_sistema.netix_inicio_guardar("EDITANDO INVENTARIO . . .");
			this.$http.post(url+netix_controller+"/guardar_editar_inventario",{"codregistro":this.editar.codinventario,detalle:this.editardetalle}).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_alerta("INVENTARIO EDITADO CORRECTAMENTE", "EL INVENTARIO EDITADO EN EL SISTEMA","success");
				}else{
					if (data.body==0) {
						netix_sistema.netix_alerta("ERROR AL EDITAR INVENTARIO", "NO SE PUEDE EDITAR","error");
					}else{
						netix_sistema.netix_alerta("NO PUEDE ACTUALIZAR EL INVENTARIO INICIAL", data.body,"error");
					}
				}
				this.netix_datos(); $("#editar_inventario").modal("hide");
			}, function(){
				netix_sistema.netix_alerta("ERROR AL EDITAR INVENTARIO", "NO SE PUEDE EDITAR","error");
				this.netix_datos();
			});
		},

		netix_cerrarinventario: function(codinventario){
			swal({
				title: "SEGURO DESEA CERRAR INVENTARIO ?",   
				text: "NRO DE INVENTARIO A CERRAR ES EL 000"+codinventario, 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, CERRAR INVENTARIO 000"+codinventario],
			}).then((willDelete) => {
				if (willDelete) {
					netix_sistema.netix_inicio_guardar("GUARDANDO CIERRE DE INVENTARIO");
					this.$http.post(url+netix_controller+"/cerrar_inventario",{"codregistro":codinventario}).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("INVENTARIO CERRADO CORRECTAMENTE", "EL INVENTARIO DE CERRÓ EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("ERROR AL CERRAR INVENTARIO", "REVISAR CONFIGURACIONES DE LOS CMPROBANTES DE ALMACEN","error");
						}
						this.netix_datos();
					}, function(){
						netix_sistema.netix_alerta("ERROR AL CERRAR INVENTARIO", "REVISAR CONFIGURACIONES DE LOS CMPROBANTES DE ALMACEN","error");
						this.netix_datos();
					});
				}else {
					netix_sistema.netix_alerta("CIERRE DE INVENTARIO CANCELADO", "PROCESO DE INVENTARIO NO TERMINADO","error");
				}
			});
		}
	},
	created: function(){
		this.netix_datos();
	}
});