var netix_form = new Vue({
	el: "#netix_form",
	data: {
		estado: 0,
		campos: {
			codperfil: 0, 
			modulos: permisos
		}
	},
	methods: {
		netix_marcar: function(){
			if ($("#marcar").is(":checked")) {
				var marcados = [];
				$('input[name^="lista"]').each(function() {
					marcados.push($(this).val());
				});
				this.campos.modulos = marcados;
		    }else{
		    	this.campos.modulos = [];
		    }
		},
		netix_guardar: function(){
			this.estado= 1;
			this.$http.post(url+netix_controller+"/guardar_permisos", this.campos).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_alerta("PERMISOS GUARDADOS", "PERMISOS REGISTRADOS EN EL SISTEMA","success");
				}else{
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE REGISTRAR","error");
				}
				this.netix_cerrar();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	}
});