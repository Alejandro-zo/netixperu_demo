var netix_datos = new Vue({
	el: "#netix_datos",
	data: {campos:{buscar:"", codpersona:0}, productos:[], datos:[], totales_productos:[] },
	methods: {
		buscar_producto_pedidos: function(){
			netix_sistema.netix_inicio(); 
			this.$http.post(url+netix_controller+"/buscar_producto_pedidos",this.campos).then(function(data){
				this.productos = data.body.lista; this.totales_productos = data.body.totales; netix_sistema.netix_fin();
			});
		},
		pdf_producto_pedidos: function() {
			window.open(url+netix_controller+"/pdf_producto_pedidos?datos="+JSON.stringify(this.campos),"_blank");
		},
		excel_producto_pedidos: function() {
			window.open(url+netix_controller+"/excel_producto_pedidos?datos="+JSON.stringify(this.campos),"_blank");
		},

		buscar_cliente_pedidos: function(){
			netix_sistema.netix_inicio(); this.campos.codpersona = $("#codpersona").val();
			this.$http.post(url+netix_controller+"/buscar_cliente_pedidos",this.campos).then(function(data){
				this.datos = data.body; netix_sistema.netix_fin();
			});
		},
		pdf_cliente_pedidos: function() {
			this.campos.codpersona = $("#codpersona").val();
			window.open(url+netix_controller+"/pdf_cliente_pedidos?datos="+JSON.stringify(this.campos),"_blank");
		},
		excel_cliente_pedidos: function() {
			this.campos.codpersona = $("#codpersona").val();
			window.open(url+netix_controller+"/excel_cliente_pedidos?datos="+JSON.stringify(this.campos),"_blank");
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});