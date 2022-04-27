var netix_datos = new Vue({
	el: "#netix_datos",
	data: {estado:0,cargando: true},
	methods: {
		netix_controlcaja: function(){
			netix_sistema.netix_fin();
		},
		netix_aperturar: function(){
            swal({
                title: "SEGURO APERTURAR CAJA ?",   
                text: "", 
                icon: "warning",
                dangerMode: true,
                buttons: ["CANCELAR", "SI, APERTURAR CAJA"],
            }).then((willDelete) => {
                if (willDelete){
                    this.estado = 1; netix_sistema.netix_inicio_guardar("APERTURANDO CAJA . . .");
                    this.$http.post(url+netix_controller+"/netix_aperturar", {"fecha":$("#fecha").val()}).then(function(data){
                        if (data.body==1) {
                            netix_sistema.netix_alerta("CAJA APERTURADA CORRECTAMENTE", "CAJA INICIADA","success");
                        }else{
                            netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE APERTURAR CAJA","error");
                        }
                        netix_sistema.netix_fin(); netix_sistema.netix_modulo();
                    }, function(){
                        netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE APERTURAR CAJA","error"); netix_sistema.netix_fin();
                    });
                }
            });
		},
		netix_cerrarcaja: function(){
            netix_sistema.netix_inicio();
            this.$http.post(url+netix_controller).then(function(data){
                $("#netix_sistema").empty().html(data.body);
                swal({
                    title: "CERRAR CAJA CON "+$("#saldo_actual").text(),   
                    text: "SEGURO DESEA CERRAR LA CAJA APERTURADA ?", 
                    icon: "warning",
                    dangerMode: true,
                    buttons: ["CANCELAR", "SI, CERRAR CAJA"],
                }).then((willDelete) => {
                    if (willDelete){
                        this.$http.post(url+netix_controller+"/netix_cerrar").then(function(data){
                            if (data=="e") {
                                netix_sistema.netix_alerta("ESTIMADO USUARIO, DEBE INGRESAR NUEVAMENTE","SU SESION A VENCIDO EN EL SISTEMA","error");
                            }else{
                                if (data.body==1) {
                                    netix_sistema.netix_alerta("CAJA CERRADA CORRECTAMENTE", "CAJA CERRADA","success"); netix_sistema.netix_modulo();
                                }else{
                                    netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE CERRAR CAJA","error");
                                }
                            }
                        }, function(){
                            netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE CERRAR CAJA","error");
                        });
                    }
                });
            },function(){
                this.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); this.netix_fin();
            });
		},

        // REPORTES PDF DE CAJA //
        pdf_movimientos: function(){
            var netix_url = url+netix_controller+"/pdf_movimientos/"+$("#f_desde").val()+"/"+$("#f_hasta").val();
            $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
        },
        pdf_arqueo: function(){
            var netix_url = url+netix_controller+"/pdf_arqueo/"+$("#f_arqueo").val();
            $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
        },
        pdf_arqueo_caja: function(){
            var netix_url = url+netix_controller+"/pdf_arqueo_caja/"+$("#estadocaja").val();
            $("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
        },
        pdf_arqueo_excel: function(){
            var netix_url = url+netix_controller+"/pdf_arqueo_excel/"+$("#estadocaja").val();
            window.open(netix_url,"_blank");
        }
	},
	created: function(){
		this.netix_controlcaja();
	}
});

$(document).ready(function () {
    if ($("#estadocaja").val()==0) {
        netix_graficocaja();
    }
});

function netix_graficocaja(){
    $.getJSON(url+netix_controller+"/netix_graficocaja", function(data) {
        Highcharts.chart("netix_graficocaja", {
            chart: { type: "column" },
            title: { text: "GRAFICOS DE CAJA" },
            subtitle: { text: 'Sistema comercial: webnetix.com' },
            xAxis: {
                categories: ["CAJA","BANCOS"],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {text: "SOLES (S/.)"}
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>S/. {point.y:.1f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: "INGRESOS",
                data: data.ingresos
            }, {
                name: "EGRESOS",
                data: data.egresos
            }]
        });
    });
}