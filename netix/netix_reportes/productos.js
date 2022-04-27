var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		registro:0, campos:campos, datos:[], existencias:[], existencias_a:[], recoger:[], compraventas:[], consultar:{"precios":0,"stock":0,"kardex":0},
		filtro:{"codalmacen":"","codproducto":"","codunidad":"","fechadesde":"","fechahasta":"","fecha":"","operacion":0,"codmoneda":0}, descripcion:""
	},
	methods: {
		netix_fecha: function(){
			this.campos.fecha = $("#fecha").val();
		},
		netix_kardex: function(producto){
			this.filtro.codalmacen = this.campos.codalmacen;
			this.filtro.codproducto = producto.codproducto;
			this.filtro.codunidad = producto.codunidad;
			this.filtro.fechadesde = $("#fechadesde_k").val();
			this.filtro.fechahasta = $("#fechahasta_k").val();

			this.$http.post(url+netix_controller+"/netix_kardex",this.filtro).then(function(data){
				this.existencias = data.body.existencias; this.existencias_a = data.body.existencias_a; 
				$("#producto_kardex").text(producto.descripcion+" | "+producto.unidad); 
				$("#modal_kardex").modal({backdrop: 'static', keyboard: false});
			});
		},
		netix_kardex_1: function(){
			this.filtro.fechadesde = $("#fechadesde_k").val();
			this.filtro.fechahasta = $("#fechahasta_k").val();

			this.$http.post(url+netix_controller+"/netix_kardex",this.filtro).then(function(data){
				this.existencias = data.body.existencias; this.existencias_a = data.body.existencias_a;
			});
		},
		netix_cambiar_fecha: function(dato){
			$("#producto_kardex_fecha").text("NRO DEL COMPROBANTE "+dato.seriecomprobante+"-"+dato.nrocomprobante);
			$("#c_fechakardex").val(dato.fechakardex); $("#c_fechacomprobante").val(dato.fechacomprobante); $("#c_codkardex").val(dato.codkardex);
			$("#modal_kardex_fecha").modal("show");
		},
		netix_cambiar_fecha_1: function(){
			this.$http.post(url+netix_controller+"/cambiar_fecha",{"codkardex":$("#c_codkardex").val(),"fechakardex":$("#c_fechakardex").val(),"fechacomprobante":$("#c_fechacomprobante").val()}).then(function(data){
				this.netix_kardex_1(); $("#modal_kardex_fecha").modal("hide");
			});
		},
		netix_kardex_pdf: function(){
			this.filtro.fechadesde = $("#fechadesde_k").val();
			this.filtro.fechahasta = $("#fechahasta_k").val();

			window.open(url+netix_controller+"/kardexproducto_pdf?datos="+JSON.stringify(this.filtro),"_blank");
		},
		netix_kardex_excel: function(){
			this.filtro.fechadesde = $("#fechadesde_k").val();
			this.filtro.fechahasta = $("#fechahasta_k").val();

			window.open(url+netix_controller+"/kardexproducto_excel?datos="+JSON.stringify(this.filtro),"_blank");
		},

		buscar_productos:function(){
			this.consultar.precios = 1; netix_sistema.netix_inicio(); 
			this.$http.post(url+netix_controller+"/buscar_productos",this.campos).then(function(data){
				this.datos = data.body; netix_sistema.netix_fin();
			});
		},
		pdf_kardexproductos: function(){
			var netix_url = url+netix_controller+"/pdf_kardexproductos?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		excel_kardexproductos: function(){
			window.open(url+netix_controller+"/excel_kardexproductos?datos="+JSON.stringify(this.campos),"_blank");
		},
		pdf_precios: function(){
			var netix_url = url+netix_controller+"/pdf_precios?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		pdf_precios_stock: function(){
			var netix_url = url+netix_controller+"/pdf_precios_stock?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		pdf_precios_stock_costo: function(){
			var netix_url = url+netix_controller+"/pdf_precios_stock_costo?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		excel_precios: function(){
			window.open(url+netix_controller+"/excel_precios?datos="+JSON.stringify(this.campos),"_blank");
		},

		compras_producto: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "SELECCIONAR UN PRODUCTO PARA VER LAS COMPRAS REALIZADAS!!!","error");
			}else{
				this.filtro.operacion = 2;
				this.filtro.fechadesde = $("#fechadesde_cv").val();
				this.filtro.fechahasta = $("#fechahasta_cv").val();
				this.filtro.codmoneda = $("#codmoneda").val();
				
				this.$http.post(url+netix_controller+"/netix_compraventas",this.filtro).then(function(data){
					this.compraventas = data.body.lista; $("#compraventas_total").text(data.body.total);
					
					$("#producto_compraventa").text("LISTA DE COMPRAS | "+this.descripcion); 
					$("#modal_comprasventas").modal({backdrop: 'static', keyboard: false});
				});
			}
		},
		ventas_producto: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "SELECCIONAR UN PRODUCTO PARA VER LAS VENTAS REALIZADAS!!!","error");
			}else{
				this.filtro.operacion = 20;
				this.filtro.fechadesde = $("#fechadesde_cv").val();
				this.filtro.fechahasta = $("#fechahasta_cv").val();
				this.filtro.codmoneda = $("#codmoneda").val();

				this.$http.post(url+netix_controller+"/netix_compraventas",this.filtro).then(function(data){
					this.compraventas = data.body.lista; $("#compraventas_total").text(data.body.total);

					$("#producto_compraventa").text("LISTA DE VENTAS | "+this.descripcion); 
					$("#modal_comprasventas").modal({backdrop: 'static', keyboard: false});
				});
			}
		},
		netix_compraventas: function() {
			this.filtro.fechadesde = $("#fechadesde_cv").val();
			this.filtro.fechahasta = $("#fechahasta_cv").val();
			this.filtro.codmoneda = $("#codmoneda").val();

			$("#producto_compraventa").text(this.descripcion);

			this.$http.post(url+netix_controller+"/netix_compraventas",this.filtro).then(function(data){
				this.compraventas = data.body.lista; $("#compraventas_total").text(data.body.total);
				
				$("#modal_comprasventas").modal({backdrop: 'static', keyboard: false});
			});
		},
		netix_compraventas_pdf: function(){
			this.filtro.fechadesde = $("#fechadesde_cv").val();
			this.filtro.fechahasta = $("#fechahasta_cv").val();
			this.filtro.codmoneda = $("#codmoneda").val();

			window.open(url+netix_controller+"/netix_compraventas_pdf?datos="+JSON.stringify(this.filtro),"_blank");
		},
		netix_compraventas_excel: function(){
			this.filtro.fechadesde = $("#fechadesde_cv").val();
			this.filtro.fechahasta = $("#fechahasta_cv").val();
			this.filtro.codmoneda = $("#codmoneda").val();

			window.open(url+netix_controller+"/netix_compraventas_excel?datos="+JSON.stringify(this.filtro),"_blank");
		},
		netix_seleccionar: function(producto){
			this.registro = producto.codproducto; this.descripcion = producto.descripcion+" | "+producto.unidad;
			this.filtro.codalmacen = this.campos.codalmacen;
			this.filtro.codproducto = producto.codproducto;
			this.filtro.codunidad = producto.codunidad;
		},

		netix_recoger: function(producto, operacion){
			this.filtro.codalmacen = this.campos.codalmacen;
			this.filtro.codproducto = producto.codproducto;
			this.filtro.codunidad = producto.codunidad;
			this.filtro.operacion = operacion;

			this.$http.post(url+netix_controller+"/netix_recoger",this.filtro).then(function(data){
				this.recoger = data.body;
				$("#producto_recoger").text(producto.descripcion+" | "+producto.unidad); 
				$("#modal_recoger").modal({backdrop: 'static', keyboard: false});
			});
		},
		stock_general: function(){
			var netix_url = url+netix_controller+"/stock_general?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		stock_valorizado: function(){
			var netix_url = url+netix_controller+"/stock_valorizado?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});