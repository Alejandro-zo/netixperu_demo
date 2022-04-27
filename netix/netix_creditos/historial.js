var netix_historial = new Vue({
	el: "#netix_historial",
	data: {
		estado:0, creditos: [], totales: [], pagos_cobros: [],
		campos:{"codpersona":0,"fechadesde":"","fechahasta":"","estado":1,"filtro":1,"tipo":$("#tipo").val()},
	},
	methods: {
		netix_fechas: function(){
			this.campos.codpersona = netix_creditos.registro;
			this.campos.fechadesde = $("#fechadesde").val();
			this.campos.fechahasta = $("#fechahasta").val();
		},
		netix_creditos: function(){
			this.netix_fechas(); netix_sistema.netix_inicio();
			this.$http.post(url+"creditos/cuentascobrar/filtro_creditos",this.campos).then(function(data){
				this.creditos = data.body.creditos; this.totales = data.body.totales; netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				netix_sistema.netix_fin();
			});
		},
		netix_pagos_cobros: function(){
			this.campos.fechadesde = $("#fechadesde_c").val(); this.campos.fechahasta = $("#fechahasta_c").val();
			netix_sistema.netix_inicio();
			this.$http.post(url+"creditos/cuentascobrar/filtro_pagos_cobros",this.campos).then(function(data){
				this.pagos_cobros = data.body; netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				netix_sistema.netix_fin();
			});
		},

		netix_eliminar: function(codcredito){
			swal({
				title: "SEGURO ELIMINAR CREDITO ?",   
				text: "USTED ESTA POR ELIMINAR UN CREDITO DEL SISTEMA", 
				icon: "warning",
				dangerMode: true,
				content: {
				    element: "input",
				    attributes: {
				      	placeholder: "PORQUE DESEAS ELIMINAR EL CREDITO",
				      	type: "text",
				    },
				},
				buttons: ["CANCELAR", "SI, ELIMINAR"],
			}).then((willDelete) => {
				if (willDelete) {
					netix_sistema.netix_inicio_guardar("ANULANDO CREDITO . . .");
					this.$http.post(url+netix_controller+"/eliminar",{"codregistro":codcredito,"observaciones":$(".swal-content__input").val()}).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE","UN CREDITO ELIMINADO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("NO PUEDE ELIMINAR EL CREDITO","TIENE PAGOS REGISTRADOS LO SENTIMOS","error");
						}
						netix_sistema.netix_fin(); this.netix_creditos();
					}, function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
						netix_sistema.netix_fin();
					});
				}
			});
		},
		netix_anular_pagocobro: function(codmovimiento,tipo){
			if (tipo=="COBRO") {
				urlanular = "anularcobro";
			}else{
				urlanular = "anularpago";
			}
			swal({
				title: "SEGURO ANULAR "+tipo+" ?",   
				text: "USTED ESTA POR ANULAR UN "+tipo+" DEL SISTEMA", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ANULAR"],
			}).then((willDelete) => {
				if (willDelete) {
					netix_sistema.netix_inicio_guardar("ANULANDO "+tipo+" DEL CREDITO . . .");
					this.$http.post(url+netix_controller+"/"+urlanular,{"codmovimiento":codmovimiento}).then(function(data){
						if (data.body==1) {
							netix_sistema.netix_alerta(tipo+" ELIMINADO CORRECTAMENTE","UN "+tipo+" ELIMINADO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("NO PUEDE ANULAR EL "+tipo,"ERROR DE CONEXION INTERNET","error");
						}
						netix_sistema.netix_fin(); this.netix_pagos_cobros();
					}, function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE CONEXION INTERNET","error");
						netix_sistema.netix_fin();
					});
				}
			});
		},
		netix_imprimir_recibo: function(codmovimiento,tipo){
			swal("IMPRIMIR RECIBO DE PAGO ?", {
				buttons: {
					cancel: "CANCELAR",
					catch: {
						text: "IMPRIMIR A5",
						value: "a5",
					},
					defeat: {
						text: "TICKET",
						value: "ticket",
					},
				},
			}).then((value) => {
				switch (value) {
					case "ticket":
						this.$http.get(url+"creditos/historial/imprimir_recibo/ticket/"+codmovimiento+"/"+tipo).then(function(data){
							$("#imprimir_recibo").empty().html(data.body);
							var id = "imprimir_recibo";
							var data = document.getElementById(id).innerHTML;
					        var modal = window.open('', 'IMPRIMIENDO', 'height=400,width=800');
					        modal.document.write('<html><head> <meta charset="utf-8"><title>RECIBO CREDITO</title>');
					        modal.document.write('</head><body >'+data+'</body></html>');
					        modal.document.close();

					        modal.focus(); modal.print(); modal.close();
						}); break;
					case "a5":
						window.open(url+"creditos/historial/imprimir_recibo/a5/"+codmovimiento+"/"+tipo,"_target"); break;
					default:
						console.log("CANCELAR - IMPRESION");
				}
			});
		},

		netix_cerrar: function(){
			netix_sistema.netix_modulo();
		}
	},
	created: function(){
		netix_sistema.netix_fin(); this.netix_creditos();
	}
});