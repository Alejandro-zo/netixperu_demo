var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		totales:{"estado":"CERRADA","caja":0,"banco":0,"general":0}
	},
	methods: {
		netix_totales: function(){
			this.$http.get(url+netix_controller+"/netix_totales").then(function(data){
				this.totales.estado = data.body.estado; this.totales.caja = data.body.caja;
				this.totales.banco = data.body.banco; this.totales.general = data.body.general;
				netix_sistema.netix_fin(); // netix_pagos();
			});
		}
	},
	created: function(){
		this.netix_totales();
	}
});

function netix_pagos(){
	$.getJSON(url+netix_controller+"/netix_pagos", function(data) {
        Highcharts.chart("netix_ingresos", {
		    chart: {
		        plotBackgroundColor: null, plotBorderWidth: null, plotShadow: false, type: "pie"
		    },
		    title: { text: '' },
		    tooltip: { pointFormat: '{series.name}: <b>{point.y:.1f}</b>' },
		    plotOptions: {
		        pie: {
		            allowPointSelect: true,
		            cursor: "pointer",
		            dataLabels: {
		                enabled: false
		            },
		            showInLegend: true
		        }
		    },
		    series: [{
		        name: "S/. ",
		        colorByPoint: true,
		        data: data.ingresos
		    }]
		});

		Highcharts.chart("netix_egresos", {
		    chart: {
		        plotBackgroundColor: null, plotBorderWidth: null, plotShadow: false, type: "pie"
		    },
		    title: { text: '' },
		    tooltip: { pointFormat: '{series.name}: <b>{point.y:.1f}</b>' },
		    plotOptions: {
		        pie: {
		            allowPointSelect: true,
		            cursor: "pointer",
		            dataLabels: {
		                enabled: false
		            },
		            showInLegend: true
		        }
		    },
		    series: [{
		        name: "S/. ",
		        colorByPoint: true,
		        data: data.egresos
		    }]
		});
    });
}