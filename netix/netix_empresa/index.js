var netix_datos = new Vue({
	el: "#netix_datos",
	data: { cargando: true, registro:$("#codempresa").val() },
	methods: {
		netix_editar: function(){
			$("#netix_tituloform").text("CONFIGURAR FACTURACION - EMPRESA");
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
			this.$http.post(url+netix_controller+"/editar",{"codregistro":this.registro}).then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error"); netix_sistema.netix_fin();
			});
		},
		netix_copia: function(){
			netix_sistema.netix_alerta("ATENCION USUARIO","LA GENERACION DE COPIAS NO ESTÁ HABILITADO","error"); 
		}
	},
	created: function(){
		netix_sistema.netix_fin();
	}
});