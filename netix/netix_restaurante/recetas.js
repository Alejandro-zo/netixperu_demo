var netix_operacion = new Vue({
	el: "#netix_operacion",
	data: {estado: 0,buscar:"", filtro: {"fechadesde":"","fechahasta":""}, productos:[], campos:[], detalle: []},
	computed: {
        buscar_productos: function () {
            return this.productos.filter((dato) => dato.descripcion.includes(this.buscar.toUpperCase()));
        }
    },
	methods: {
		netix_productos : function(){
			this.$http.get(url+netix_controller+"/lista").then(function(data){
				this.productos = data.body; netix_sistema.netix_fin();
			});
		},
		netix_item: function(){
			netix_sistema.netix_loader("lista_productos",180);
			this.$http.post(url+"almacen/productos/buscar/ventas").then(function(data){
				$("#lista_productos").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
				netix_sistema.netix_modulo();
			});
		},
		netix_additem: function(producto){
			var existeproducto = this.detalle.filter(function(p){
			    if(p.codproducto == producto.codproducto && p.codunidad == producto.codunidad ){
			    	p.cantidad = parseFloat(p.cantidad) + 1; return p;
			    };
			});

		    if (existeproducto.length==0) {
				this.detalle.push({
					"codproducto":producto.codproducto,"producto":producto.descripcion,"codunidad":producto.codunidad,
					"unidad":producto.unidad,"cantidad":1
				});
		    }
		},
		netix_deleteitem: function(index,producto){
			this.detalle.splice(index,1);
		},
		netix_receta: function(producto){
			this.campos = producto; this.estado = 0;
			$("#titulo_receta").text("RECETA DE: "+producto.descripcion+" - UNIDAD: "+producto.unidad); this.netix_item();
			this.$http.get(url+netix_controller+"/detalle_receta/"+producto.codproducto+"/"+producto.codunidad).then(function(data){
				this.detalle = data.body; $("#modal_receta").modal("show");
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
			});
		},
		netix_guardar: function(){
			this.estado = 1; $("#modal_receta").modal("hide"); netix_sistema.netix_inicio_guardar("GUARDANDO RECETA . . .");
			this.$http.post(url+netix_controller+"/guardar", {"campos":this.campos,"detalle":this.detalle}).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_noti("RECETA REGISTRADA CORRECTAMENTE","RECETA REGISTRADA EN EL SISTEMA","success");
				}else{
					netix_sistema.netix_alerta("ERROR AL REGISTRAR RECETA","ERROR DE RED","error");
				}
				this.netix_productos();
			}, function(){
				netix_sistema.netix_alerta("ERROR AL REGISTRAR RECETA","ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},

		consumo_total: function(){
			this.filtro.fechadesde = $("#fechadesde").val(); this.filtro.fechahasta = $("#fechahasta").val();

			var netix_url = url+netix_controller+"/consumo_total_pdf?datos="+JSON.stringify(this.filtro); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		consumo_fechas: function(){
			this.filtro.fechadesde = $("#fechadesde").val(); this.filtro.fechahasta = $("#fechahasta").val();

			var netix_url = url+netix_controller+"/consumo_fechas_pdf?datos="+JSON.stringify(this.filtro); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		}
	},
	created: function(){
		this.netix_productos();
	}
});