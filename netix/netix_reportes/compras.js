var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando:true, campos:campos, cajas:[]
	},
	methods: {
		netix_fecha: function(){
			this.campos.fechadesde = $("#fechadesde").val(); 
			this.campos.fechahasta = $("#fechahasta").val(); 
		},
		netix_cajas: function(){
			this.campos.cajas = [];
			if (this.campos.codsucursal==0) {
				this.campos.codcaja = 0;
			}else{
				this.$http.get(url+"caja/controlcajas/netix_cajas/"+this.campos.codsucursal).then(function(data){
					this.cajas = data.body; this.ver_grafico();
				});
			}
		},
		ver_grafico: function(){
			if (this.campos.fechadesde>this.campos.fechahasta) {
				netix_sistema.netix_noti("LA FECHA DESDE DEBE SER MAYOR","QUE LA FECHA HASTA","error"); return false;
			}
			ver_grafico(JSON.stringify(this.campos));
		},
		pdf_compras: function(){
			// window.open(url+netix_controller+"/pdf_compras?datos="+JSON.stringify(this.campos), "_blank"); //
			var netix_url = url+netix_controller+"/pdf_compras?datos="+JSON.stringify(this.campos); 
            $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		mas_reportes: function(){
			netix_sistema.netix_noti("OPCION PARA GENERAR REPORTES","MAS PERSONALIZADOS","success");
		}
	},
	created: function(){
		this.netix_cajas(); netix_sistema.netix_fin();
	}
});

function ver_grafico(datos){
	$.getJSON(url+netix_controller+"/ver_grafico?datos="+datos, function(data) {
		Highcharts.chart("reporte_compras", {
		    title: {text: "REPORTE GRAFICO DE COMPRAS"},
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