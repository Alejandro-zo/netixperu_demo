var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando:true, campos:campos, cajas:[], almacenes:[], comprobantes: []
	},
	methods: {
		netix_fecha: function(){
			this.campos.fechadesde = $("#fechadesde").val(); 
			this.campos.fechahasta = $("#fechahasta").val(); 
		},
		netix_cajas: function(){
			this.campos.cajas = [];
			if (this.campos.codsucursal==0) {
				this.campos.codcaja = 0; this.ver_grafico();
			}else{
				this.$http.get(url+"caja/controlcajas/netix_cajas/"+this.campos.codsucursal).then(function(data){
					this.cajas = data.body; this.ver_grafico();
				});
				this.$http.get(url+"caja/controlcajas/netix_almacenes/"+this.campos.codsucursal).then(function(data){
					this.almacenes = data.body; this.ver_grafico();
				});
			}
		},
		ver_grafico: function(){
			this.netix_fecha();
			if (this.campos.fechadesde>this.campos.fechahasta) {
				netix_sistema.netix_noti("LA FECHA DESDE DEBE SER MAYOR","QUE LA FECHA HASTA","error"); return false;
			}
			ver_grafico(JSON.stringify(this.campos));
		},
		mas_reportes: function(){
			$("#fechadesde_mas").val($("#fechadesde").val()); 
			$("#fechahasta_mas").val($("#fechahasta").val()); 

			$("#modal_reportes").modal("show");
		},

		pdf_productos_vendidos: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/pdf_productos_vendidos?datos="+encodeURIComponent(JSON.stringify(this.campos)),"_blank");
		},
		pdf_ventas_vendedor: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/pdf_ventas_vendedor?datos="+encodeURIComponent(JSON.stringify(this.campos)),"_blank");
		},
		pdf_ventas_vendedor_resumen: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/pdf_ventas_vendedor?tipo='resumen'&datos="+encodeURIComponent(JSON.stringify(this.campos)),"_blank");
		},
		pdf_ventas_cliente: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/pdf_ventas_cliente?datos="+encodeURIComponent(JSON.stringify(this.campos)),"_blank");
		},
		pdf_ventas_cliente_detallado: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/pdf_ventas_cliente_detallado?datos="+encodeURIComponent(JSON.stringify(this.campos)),"_blank");
		},

		netix_comprobantes: function(){
			this.comprobantes = []; list = this;
			$("input[name='comprobantes']:checked").each(function() {
				list.comprobantes.push({"codcomprobantetipo":$(this).val()});
	        });
		},
		pdf_reporte_ventas: function(estado){
			this.netix_comprobantes(); this.campos.estado = estado;
			if (this.comprobantes.length==0) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UN TIPO DE COMPROBANTE","PARA EL REPORTE DE VENTAS","error"); return false;
			}
			this.campos.fechadesde = $("#fechadesde_mas").val(); 
			this.campos.fechahasta = $("#fechahasta_mas").val(); 

			var datos = "datos="+encodeURIComponent(JSON.stringify(this.campos))+"&tipos="+JSON.stringify(this.comprobantes);
			window.open(url+netix_controller+"/pdf_reporte_ventas?"+datos,"_blank");
		},
		pdf_reporte_ventas_det: function(estado){
			this.netix_comprobantes(); this.campos.estado = estado;
			if (this.comprobantes.length==0) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UN TIPO DE COMPROBANTE","PARA EL REPORTE DE VENTAS DETALLADO","error"); return false;
			}
			this.campos.fechadesde = $("#fechadesde_mas").val(); 
			this.campos.fechahasta = $("#fechahasta_mas").val(); 

			var datos = "datos="+encodeURIComponent(JSON.stringify(this.campos))+"&tipos="+JSON.stringify(this.comprobantes);
			window.open(url+netix_controller+"/pdf_reporte_ventas_det?"+datos,"_blank");
		},
		pdf_contable_ventas: function(){
			this.netix_comprobantes();
			if (this.comprobantes.length==0) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UN TIPO DE COMPROBANTE","PARA EL REPORTE DE VENTAS","error"); return false;
			}
			this.campos.fechadesde = $("#fechadesde_mas").val(); 
			this.campos.fechahasta = $("#fechahasta_mas").val(); 

			var datos = "datos="+encodeURIComponent(JSON.stringify(this.campos))+"&tipos="+JSON.stringify(this.comprobantes);
			window.open(url+netix_controller+"/pdf_contable_ventas?"+datos,"_blank");
		},
		excel_contable_ventas: function(){
			this.netix_comprobantes();
			if (this.comprobantes.length==0) {
				netix_sistema.netix_noti("DEBE SELECCIONAR UN TIPO DE COMPROBANTE","PARA EL REPORTE DE VENTAS","error"); return false;
			}
			this.campos.fechadesde = $("#fechadesde_mas").val(); 
			this.campos.fechahasta = $("#fechahasta_mas").val(); 

			var datos = "datos="+encodeURIComponent(JSON.stringify(this.campos))+"&tipos="+JSON.stringify(this.comprobantes);
			window.open(url+netix_controller+"/excel_contable_ventas?"+datos,"_blank");
		}
	},
	created: function(){
		this.netix_cajas(); netix_sistema.netix_fin();
	}
});

function ver_grafico(datos){
	$.getJSON(url+netix_controller+"/ver_grafico?datos="+datos, function(data) {
		Highcharts.chart("reporte_ventas", {
		    title: {text: "REPORTE GRAFICO DE VENTAS"},
		    subtitle: {text: "REPORTE DESDE "+$("#fechadesde").val()+" HASTA "+$("#fechahasta").val() },
		    xAxis: {
		        categories: data.categorias
		    },
		    yAxis: {
		        title: { text: "S/. TOTAL EN SOLES" }
		    },
		    series: [{
		        type: "column",
		        name: "S/ ",
		        colorByPoint: true,
		        data: data.totales,
		        showInLegend: false
		    }]
		});
	});
}