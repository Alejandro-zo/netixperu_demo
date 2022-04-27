var netix_historial = new Vue({
	el: "#netix_historial",
	data: {
		estado:0, pedidos: [], totales: [],
		campos:{"codpersona":0,"fechadesde":"","fechahasta":"","estado":1,"filtro":1},
	},
	methods: {
		netix_fechas: function(){
			this.campos.codpersona = netix_pedidos.registro;
			this.campos.fechadesde = $("#fechadesde").val();
			this.campos.fechahasta = $("#fechahasta").val();
		},
		netix_pedidos: function(){
			this.netix_fechas(); netix_sistema.netix_inicio();
			this.$http.post(url+netix_controller+"/filtro_pedidos",this.campos).then(function(data){
				this.pedidos = data.body.pedidos; this.totales = data.body.totales; netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
				netix_sistema.netix_fin();
			});
		},
		netix_atender: function(codpedido){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
			this.$http.post(url+netix_controller+"/atender/"+codpedido).then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},
		netix_ver: function(codpedido){
			$(".compose").slideToggle(); netix_sistema.netix_loader("netix_formulario",180);
			this.$http.post(url+netix_controller+"/ver/"+codpedido).then(function(data){
				$("#netix_formulario").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
			});
		},
		netix_eliminar: function(codpedido){
			swal({
				title: "SEGURO ELIMINAR PEDIDO ?",   
				text: "USTED ESTA POR ELIMINAR UN PEDIDO DEL SISTEMA", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ELIMINAR"],
			}).then((willDelete) => {
				if (willDelete) {
					netix_sistema.netix_inicio_guardar("ANULANDO PEDIDO . . .");
					this.$http.post(url+netix_controller+"/eliminar",{"codregistro":codpedido}).then(function(data){
						if (data.body.estado==1) {
							netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE","UN PEDIDO ELIMINADO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("NO PUEDE ELIMINAR EL PEDIDO","TIENE ENTREGAS REALIZADAS","error");
						}
						netix_sistema.netix_fin(); this.netix_pedidos();
					}, function(){
						netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
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
		netix_sistema.netix_fin(); this.netix_pedidos();
	}
});