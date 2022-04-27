var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, campos: campos, estado_cuenta_socios: [], estado_cuenta_creditos: [], 
		estado_cuenta_detallado: [], saldos: []
	},
	methods: {
		netix_fecha: function(){
			this.campos.codpersona = $("#codpersona").val();
			this.campos.fecha_desde = $("#fecha_desde").val();
			this.campos.fecha_hasta = $("#fecha_hasta").val();
			this.campos.fecha_saldos = $("#fecha_saldos").val();
		},
		netix_vacio: function(){
			this.estado_cuenta_socios = []; this.estado_cuenta_creditos = []; this.estado_cuenta_detallado = [];
		},

		ver_creditos: function(){
			netix_sistema.netix_inicio(); this.netix_fecha(); this.campos.saldos = 0;
			this.$http.post(url+netix_controller+"/ver_creditos", this.campos).then(function(data){
				if (this.campos.tipo_consulta==1) {
					if (this.campos.mostrar==1) {
						this.estado_cuenta_socios = data.body;
					}else{
						this.estado_cuenta_creditos = data.body;
					}
				}else{
					this.estado_cuenta_detallado = data.body;
				}
				netix_sistema.netix_fin();
			});
		},
		saldo_creditos: function(){
			netix_sistema.netix_inicio(); this.netix_fecha(); this.netix_vacio(); this.campos.saldos = 1;
			this.$http.post(url+netix_controller+"/ver_creditos", this.campos).then(function(data){
				this.saldos = data.body; netix_sistema.netix_fin();
			});
		},

		pdf_creditos: function(){
			this.netix_fecha();
			var netix_url = url+netix_controller+"/pdf_creditos?datos="+JSON.stringify(this.campos); 
			$("#netix_pdf").attr("src",netix_url); $("#modal_reportes").modal("show");
		},
		excel_creditos: function(){
			this.netix_fecha();
			window.open(url+netix_controller+"/excel_creditos?datos="+JSON.stringify(this.campos),"_blank");
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});