var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, campos:campos, estado_detallado: 0, estado_movimientos:0,
		saldocaja:{"ingresos":"","egresos":"","totalingresos":0,"totalegresos":0,"total":0}, detallado:[], movimientos:[]
	},
	methods: {
		netix_fecha: function(){
			this.campos.codpersona = $("#codpersona").val();
			this.campos.fecha_desde = $("#fecha_desde").val();
			this.campos.fecha_hasta = $("#fecha_hasta").val();
			this.campos.fecha_detallado = $("#fecha_detallado").val();
		},
		caja_detallado: function(){
			netix_sistema.netix_inicio(); this.netix_fecha(); this.estado_detallado = 1; this.estado_movimientos = 0;
			this.$http.post(url+netix_controller+"/caja_detallado",this.campos).then(function(data){
				this.detallado = data.body.lista; netix_sistema.netix_fin();
				this.saldocaja.ingresos = data.body.ingresos; this.saldocaja.egresos = data.body.egresos;
				this.saldocaja.totalingresos = data.body.totalingresos; this.saldocaja.totalegresos = data.body.totalegresos;
				this.saldocaja.total = data.body.total;
			});
		},
		reporte_movimientos: function(){
			netix_sistema.netix_inicio(); this.netix_fecha(); this.estado_detallado = 0; this.estado_movimientos = 1;
			this.$http.post(url+netix_controller+"/reporte_movimientos",this.campos).then(function(data){
				this.movimientos = data.body.lista; netix_sistema.netix_fin();
				this.saldocaja.totalingresos = data.body.ingresos; this.saldocaja.totalegresos = data.body.egresos;
				this.saldocaja.total = data.body.total;
			});
		},
		pdf_caja: function(){
			if (this.estado_detallado==0 && this.estado_movimientos==0) {
				netix_sistema.netix_noti("SELECCIONA UN REPORTE","CAJA DETALLADO O REPORTE DE MOVIMIENTOS","error");
			}else{
				if (this.estado_detallado==1) {
					this.campos.reporte = 1;
				}else{
					this.campos.reporte = 2;
				}
				var netix_url = url+netix_controller+"/pdf_caja?datos="+JSON.stringify(this.campos); 
            	$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
			}
		},
		excel_caja(){
			window.open(url+netix_controller+"/excel_caja?datos="+JSON.stringify(this.campos),"_blank");
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});