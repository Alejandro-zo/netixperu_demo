var netix_unidades = new Vue({
	el: "#netix_unidades",
	data: {estado: 0,buscar:"",totales:[], productos:[], campos:[]},
	computed: {
        buscar_productos: function () {
            return this.productos.filter((dato) => dato.descripcion.includes(this.buscar.toUpperCase()));
        }
    },
	methods: {
		netix_productos : function(){
			this.$http.get(url+netix_controller+"/lista").then(function(data){
				this.productos = data.body.lista; this.totales = data.body.totales; netix_sistema.netix_fin();
			});
		},
		netix_marcar: function(producto_unidad){
			this.campos = producto_unidad;
		},

		cambiar_unidad: function(){
			if(this.campos.length==0){
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN PRODUCTO", "PARA CAMBIAR LA UNIDAD DE MEDIDA","error");
			}else{
				this.estado = 0; $("#modal_cambiar_unidad").modal("show");
			}
		},
		guardar_cambiar_unidad: function(){
			if ($("#codunidad").val()=="") {
				netix_sistema.netix_noti("DEBE SELECCIONAR LA NUEVA UNIDAD DE MEDIDA","","error");
			}else{
				swal({
					title: "SEGURO CAMBIAR LA UNIDAD DE MEDIDA",   
					text: "SE CAMBIARÁ EN COMPRAS, VENTAS, INVENTARIOS", 
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, CAMBIAR UNIDAD"],
				}).then((willDelete) => {
					if (willDelete){
						this.estado = 1; netix_sistema.netix_inicio_guardar("GUARDANDO CAMBIO DE UNIDAD . . .");
						this.$http.post(url+netix_controller+"/guardar_cambiar_unidad", {"codproducto":this.campos.codproducto,"codunidad":this.campos.codunidad,"codunidad_nueva":$("#codunidad").val()}).then(function(data){
							if (data.body==1) {
								$("#modal_cambiar_unidad").modal("hide"); this.netix_productos();
								netix_sistema.netix_noti("LA UNIDAD DE MEDIDA SE CAMBIO CORRECTAMENTE","","success");
							}else{
								netix_sistema.netix_alerta("NO SE PUEDE CAMBIAR A ESTA UNIDAD","PUEDE QUE EL PRODUCTO YA TENGA ESTA UNIDAD","error"); 
								netix_sistema.netix_fin(); this.estado = 0;
							}
						}, function(){
							netix_sistema.netix_alerta("ERROR AL CAMBIAR DE UNIDAD","SIN CONEXION","error"); netix_sistema.netix_fin();
						});
					}else{
						$("#modal_cambiar_unidad").modal("hide");
					}
				});
			}
		},
		productos_almacen: function(){
			swal({
				title: "EL SISTEMA REVISARÁ SI ALGUN PRODUCTO FALTA REGISTRAR EN ALGÚN ALMACÉN",   
				text: "", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, REVISAR Y REGISTRAR"],
			}).then((willDelete) => {
				if (willDelete){
					this.estado = 1; netix_sistema.netix_inicio_guardar("REVISANDO Y REGISTRANDO . . .");
					this.$http.post(url+netix_controller+"/productos_almacen").then(function(data){
						netix_sistema.netix_noti("REVISADO Y REGISTRADO CORRECTAMENTE","TODOS LOS PRODUCTOS EN LOS ALMACENES","success"); netix_sistema.netix_fin();
					}, function(){
						netix_sistema.netix_alerta("ERROR AL REVISAR","SIN CONEXION","error"); netix_sistema.netix_fin();
					});
				}else{
					$("#modal_cambiar_unidad").modal("hide");
				}
			});
		},
		actualizar_stock: function(){
			swal({
				title: "EL SISTEMA REVISARÁ Y ACTUALIZARÁ EL STOCK DE TODOS LOS PRODUCTOS",   
				text: "OPCION RECOMENDADA POR EL SISTEMA", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ACTUALIZAR STOCK"],
			}).then((willDelete) => {
				if (willDelete){
					this.estado = 1; netix_sistema.netix_inicio_guardar("ACTUALIZANDO STOCK . . .");
					this.$http.post(url+netix_controller+"/actualizar_stock").then(function(data){
						netix_sistema.netix_noti("STOCK ACTUALIZADO CORRECTAMENTE","OPCION RECOMENDADA POR EL SISTEMA","success"); netix_sistema.netix_fin();
					}, function(){
						netix_sistema.netix_alerta("ERROR AL ACTUALIZAR STOCK","SIN CONEXION","error"); netix_sistema.netix_fin();
					});
				}
			});
		}
	},
	created: function(){
		this.netix_productos();
	}
});