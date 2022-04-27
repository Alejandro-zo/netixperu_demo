var netix_inventario = new Vue({
	el: "#netix_inventario",
	data: {estado: 0,buscar:"",tiporeporte:0,campos:{"codregistro":0,"importe":0},productos:[]},
	computed: {
        buscar_productos: function () {
            return this.productos.filter((dato) => dato.descripcion.includes(this.buscar.toUpperCase()));
        }
    },
	methods: {
		netix_productos : function(){
			this.campos.codregistro = netix_datos.registro;
			this.$http.get(url+netix_controller+"/productos_inventario/"+netix_datos.registro).then(function(data){
				this.productos = data.body.productos; this.campos.importe = data.body.importe; netix_sistema.netix_fin();
			});
		},
		netix_masproductos : function(){
			netix_sistema.netix_inicio();
			this.$http.get(url+netix_controller+"/mas_productos_inventario/"+netix_datos.registro).then(function(data){
				if (data.body=="") {
					netix_sistema.netix_noti("NO HAY PRODUCTO PARA AGREGAR","PRODUCTOS ACTUALIZADOS","error");
				}else{
					for(i in data.body){
						this.productos.push({
							"codproducto":data.body[i]["codproducto"],"codunidad":data.body[i]["codunidad"],
							"unidad":data.body[i]["unidad"],"codigo":data.body[i]["codigo"],"descripcion":data.body[i]["descripcion"],
							"cantidad":data.body[i]["cantidad"],"preciocosto":data.body[i]["preciocosto"],
							"precioventa":data.body[i]["precioventa"],"importe":data.body[i]["importe"]
						}); 
					}
					netix_sistema.netix_noti("PRODUCTOS CARGADOS CORRECTAMENTE","PRODUCTOS EN EL INVENTARIO","success");
				}
				netix_sistema.netix_fin();
			});
		},
		netix_nuevoproducto : function(){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
			this.$http.post(url+"almacen/productos/nuevo").then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_fin();
			});
		},
		netix_itemquitar: function(index, campo){
			swal({
				title: "SEGURO QUITAR DE INVENTARIO?",   
				text: "QUITAR EL PRODUCTO DEL INVENTARIO", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ELIMINAR"],
			}).then((willDelete) => {
				if (willDelete){
					this.$http.get(url+netix_controller+"/productos_quitaritem/"+this.campos.codregistro+"/"+campo.codproducto+"/"+campo.codunidad).then(function(data){
						this.campos.importe = this.campos.importe - campo.importe;
						this.productos.splice(index,1);
					});
				}
			});
		},
		netix_calcular: function(campo){
			this.campos.importe = this.campos.importe - campo.importe;
			campo.importe = campo.cantidad * campo.preciocosto;
			this.campos.importe = this.campos.importe + campo.importe;
		},

		netix_guardar: function(){
			this.estado= 1; netix_sistema.netix_inicio_guardar("GUARDANDO CAMBIOS INVENTARIO");
			this.$http.post(url+netix_controller+"/guardar_inventario", {"campos":this.campos,"productos":this.productos}).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "CAMBIOS DEL INVENTARIO REGISTRADO","success");
				}else{
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL GUARDAR CAMBIOS", "ERROR DE RED","error");
				}
				netix_sistema.netix_fin(); netix_sistema.netix_modulo();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},

		netix_pdf: function(){
			var netix_url = url+netix_controller+"/netix_pdf/"+this.campos.codregistro+"/"+this.tiporeporte;
            $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		netix_excel: function(){
			window.open(url+netix_controller+"/netix_excel/"+this.campos.codregistro+"/"+this.tiporeporte,"_blank");
		},
		netix_cerrar: function(){
			netix_sistema.netix_modulo();
		}
	},
	created: function(){
		this.netix_productos();
	}
});