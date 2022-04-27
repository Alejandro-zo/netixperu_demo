var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		sunat: {tipo:"01",serie:"",nrocomprobante:"", fdesde:"", fhasta:""}, sunatrecepcion:[],
		resumen: {codresumentipo:"", periodo:"", nrocorrelativo:0},
		facturas:[], facturas_anuladas:[], resumenes_boletas:[], resumenes_info:[], facturas_datos: [], boletas_datos: [],
		tipo_reporte: "", comprobantes_lista: [], resumenes_lista: [], notas: []
	},
	methods: {
		netix_comprobantes: function(){
			netix_sistema.netix_inicio();
			this.$http.get(url+netix_controller+"/comprobantes").then(function(data){
				this.facturas = data.body.facturas; this.notas = data.body.notas; 
				netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("NO SE PUEDEN MOSTRAR LOS COMPROBANTES PENDIENTES","","error");
				netix_sistema.netix_fin();
			});
		},
		comprobantes_enviar: function(codkardex,codoficial){
			netix_sistema.netix_inicio_guardar("ENVIANDO EL COMPROBANTE A SUNAT . . ."); $("#"+codkardex).attr("disabled","true");

			this.$http.get(url+netix_controller+"/comprobantes_enviar/"+codkardex+"/"+codoficial).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					netix_sistema.netix_noti("ATENCION USUARIO:",data.body.mensaje,data.body.alerta);
				}
				$("#"+codkardex).removeAttr("disabled"); this.netix_comprobantes(); netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("NO SE PUEDE ENVIAR EL COMPROBANTE","SIN CONEXION DE INTERNET","error");
				$("#"+codkardex).removeAttr("disabled"); netix_sistema.netix_fin(); 				 
			});
		},
		comprobantes_xml: function(codkardex,codoficial){
			window.open(url+netix_controller+"/comprobantes_xml/"+codkardex+"/"+codoficial,"_blank");
		},
		comprobantes_cdr: function(codkardex){
			window.open(url+netix_controller+"/comprobantes_cdr/"+codkardex,"_blank");
		},
		comprobantes_correo: function(data){
			this.$http.get(url+"ventas/clientes/correo/"+data.documento).then(function(correo){
				if (correo.body=="") {
					netix_sistema.netix_alerta("SIN CORREO ELECTRONICO: "+data.razonsocial,"REGISTRAR CORREO ELECTRONICO","error"); 
				}else{
					swal({
						title: "ENVIAR COMPROBANTE ELECTRONICO "+data.seriecomprobante+"-"+data.nrocomprobante+" AL CORREO "+correo.body,   
						text: "", 
						icon: "info",
						dangerMode: true,
						buttons: ["CANCELAR", "SI, ENVIAR CORREO"],
					}).then((willDelete) => {
						if (willDelete) {
							netix_sistema.netix_inicio_guardar("ENVIANDO EL COMPROBANTE AL CORREO . . .");
							this.$http.post(url+"ventas/clientes/enviar_correo",{"codkardex":data.codkardex,"email":correo.body}).then(function(info){
								if (info.body==1) {
									netix_sistema.netix_alerta("COMPROBANTE ELECTRONICO "+data.seriecomprobante+"-"+data.nrocomprobante+" ENVIANDO CORRECTAMENTE","","success");
								}else{
									netix_sistema.netix_alerta("LA CONFIGURACION DEL CORREO ES INCORRECTA","ERROR DE CONFIGURACION","error");
								}
								netix_sistema.netix_fin();
							}, function(){
								netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); netix_sistema.netix_fin();
							});
						}
					});
				}
			});
		},

		netix_resumenes: function(){
			netix_sistema.netix_inicio();
			this.$http.get(url+netix_controller+"/resumenes").then(function(data){
				this.facturas_anuladas = data.body.facturas_anuladas; 
				this.resumenes_boletas = data.body.resumenes_boletas;
				netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_alerta("NO SE PUEDE MOSTRAR LOS RESUMENES PENDIENTES","","error");
				netix_sistema.netix_fin();
			});
		},
		resumenes_generar: function(codresumentipo){
			netix_sistema.netix_inicio_guardar("GENERANDO EL RESUMEN ELECTRONICO . . .");

			this.$http.get(url+netix_controller+"/resumenes_generar/"+codresumentipo+"/"+$("#fecha").val()).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					if (data.body.estado==1) {
						netix_sistema.netix_noti("ATENCION USUARIO:",data.body.mensaje,"success");
					}else{
						netix_sistema.netix_noti("ATENCION USUARIO:",data.body.mensaje,"error");
					}
					netix_sistema.netix_fin(); this.netix_resumenes();
				}
			}, function(){
				netix_sistema.netix_alerta("NO SE PUEDE GENERAR EL RESUMEN","SIN CONEXION DE INTERNET","error");
				netix_sistema.netix_fin();
			});
		},
		resumenes_enviar: function(codresumentipo,periodo,nrocorrelativo){
			$("#"+periodo).attr("disabled","true"); netix_sistema.netix_inicio_guardar("ENVIANDO EL RESUMEN A SUNAT . . .");

			this.$http.get(url+netix_controller+"/resumenes_enviar/"+codresumentipo+"/"+periodo+"/"+nrocorrelativo).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_alerta("SESION DEL USUARIO TERMINADA","DEBE INICIAR SESION NUEVAMENTE","error");
				}else{
					// data.body.estado==0 || data.body.estado==3 || data.body.estado==4
					netix_sistema.netix_noti("ATENCION USUARIO:",data.body.mensaje,"success"); 
					$("#"+periodo).removeAttr("disabled"); netix_sistema.netix_fin(); this.netix_resumenes();
				}
			}, function(){
				netix_sistema.netix_alerta("NO SE PUEDE ENVIAR EL RESUMEN","SIN CONEXION DE INTERNET","error");
				netix_sistema.netix_fin(); $("#"+periodo).removeAttr("disabled");
			});
		},
		resumenes_xml: function(codresumentipo,periodo,nrocorrelativo){
			window.open(url+netix_controller+"/resumenes_xml/"+codresumentipo+"/"+periodo+"/"+nrocorrelativo,"_blank");
		},
		resumenes_cdr: function(codresumentipo,periodo,nrocorrelativo){
			window.open(url+netix_controller+"/resumenes_cdr/"+codresumentipo+"/"+periodo+"/"+nrocorrelativo,"_blank");
		},
		resumenes_ver: function(codresumentipo,periodo,nrocorrelativo){
			this.resumen.codresumentipo = codresumentipo; 
			this.resumen.periodo = periodo; 
			this.resumen.nrocorrelativo = nrocorrelativo;

			this.$http.get(url+netix_controller+"/resumenes_ver/"+codresumentipo+"/"+periodo+"/"+nrocorrelativo).then(function(data){
				this.resumenes_info = data.body; $("#modal_resumenes").modal("show");
			});
		},
		resumenes_eliminar_kardex(data){
			this.$http.get(url+netix_controller+"/resumenes_eliminar_kardex/"+data.codkardex+"/"+this.resumen.codresumentipo+"/"+this.resumen.periodo+"/"+this.resumen.nrocorrelativo).then(function(data){
				this.resumenes_ver(this.resumen.codresumentipo,this.resumen.periodo,this.resumen.nrocorrelativo);
			});
		},
		resumenes_siguiente_correlativo: function(){
			swal({
				title: "SEGURO ACTUALIZAR RESUMEN ELECTRONICO ?",   
				text: "SE ACTUALIZARÁ AL SIGUIENTE CORRELATIVO", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ACTUALIZAR"],
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.get(url+netix_controller+"/resumenes_siguiente_correlativo/"+this.resumen.codresumentipo+"/"+this.resumen.periodo+"/"+this.resumen.nrocorrelativo).then(function(data){
						if (data.body==1) {
							this.netix_resumenes();
							netix_sistema.netix_alerta("RESUMEN ACTUALIZADO CORRECTAMENTE","","success");
						}else{
							netix_sistema.netix_alerta("NO SE PUEDE ACTUALIZAR EL RESUMEN","","error");
						}
						$("#modal_resumenes").modal("hide");
					});
				}
			});
		},
		resumenes_actualizar: function(){
			swal({
				title: "SEGURO ACTUALIZAR RESUMEN ELECTRONICO ?",   
				text: "SE ACTUALIZARÁ COMO ENVIADO", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ACTUALIZAR"],
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.get(url+netix_controller+"/resumenes_actualizar/"+this.resumen.codresumentipo+"/"+this.resumen.periodo+"/"+this.resumen.nrocorrelativo).then(function(data){
						if (data.body==1) {
							this.netix_resumenes();
							netix_sistema.netix_alerta("RESUMEN ACTUALIZADO CORRECTAMENTE","","success");
						}else{
							netix_sistema.netix_alerta("NO SE PUEDE ACTUALIZAR EL RESUMEN","","error");
						}
						$("#modal_resumenes").modal("hide");
					});
				}
			});
		},
		resumenes_quitar_ticket: function(){
			swal({
				title: "SEGURO QUITAR EL TICKET DEL RESUMEN ELECTRONICO ?",   
				text: "", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, QUITAR"],
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.get(url+netix_controller+"/resumenes_quitar_ticket/"+this.resumen.codresumentipo+"/"+this.resumen.periodo+"/"+this.resumen.nrocorrelativo).then(function(data){
						if (data.body==1) {
							this.netix_resumenes();
							netix_sistema.netix_alerta("TICKET ELIMINADO CORRECTAMENTE","","success");
						}else{
							netix_sistema.netix_alerta("NO SE PUEDE ELIMINAR TICKET","","error");
						}
						$("#modal_resumenes").modal("hide");
					});
				}
			});
		},
		resumenes_anular: function(codresumentipo,periodo,nrocorrelativo){
			swal({
				title: "SEGURO ELIMINAR RESUMEN ELECTRONICO ?",   
				text: "USTED ESTA POR ELIMINAR UN RESUMEN", 
				icon: "warning",
				dangerMode: true,
				buttons: ["CANCELAR", "SI, ELIMINAR"],
			}).then((willDelete) => {
				if (willDelete) {
					this.$http.get(url+netix_controller+"/resumenes_anular/"+codresumentipo+"/"+periodo+"/"+nrocorrelativo).then(function(data){
						if (data.body==1) {
							this.netix_resumenes();
							netix_sistema.netix_alerta("RESUMEN ANULADO CORRECTAMENTE","","success");
						}else{
							netix_sistema.netix_alerta("NO SE PUEDE ANULAR EL RESUMEN","","error");
						}
					});
				}
			});
		},

		netix_consultasunat: function(){
			netix_sistema.netix_inicio_guardar("CONSULTANDO COMPROBANTES EN SUNAT . . .");
			this.$http.post(url+netix_controller+"/netix_consultasunat",this.sunat).then(function(data){
				$("#sunat_respuesta").empty().html(data.body.mensaje); netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_fin();
			});
		},

		netix_consultas: function(){
			$("#modal_consultas").modal("show");
			this.$http.post(url+netix_controller+"/netix_datos_cpe").then(function(data){
				this.facturas_datos = data.body.facturas; this.boletas_datos = data.body.boletas;
			});
		},
		netix_reportes_cpe: function(netix_url,tipo_reporte){
			netix_sistema.netix_inicio(); this.tipo_reporte = tipo_reporte;
			this.$http.get(url+netix_controller+"/"+netix_url+"/"+$("#fdesde").val()+"/"+$("#fhasta").val()).then(function(data){
				if (tipo_reporte=="comprobantes") {
					this.comprobantes_lista = data.body;
				}else{
					this.resumenes_lista = data.body;
				}
				netix_sistema.netix_fin();
			});
		},
		consulta_cdr: function (ticket) {
			if (ticket=="" || ticket==null) {
				netix_sistema.netix_noti("EL COMPROBANTE NO TIENE NRO DE TICKET","AUN NO ESTA ENVIADO A LA SUNAT","error");
			}else{
				window.open(url+netix_controller+"/consulta_cdr/"+ticket,"_blank");
			}
		},

		sunat_recepcion: function(){
			netix_sistema.netix_inicio_guardar("CONSULTANDO COMPROBANTES EN SUNAT . . .");
			this.sunat.fdesde = $("#fecha_desde").val(); this.sunat.fhasta = $("#fecha_hasta").val();
			this.$http.post(url+netix_controller+"/netix_bloquesunat",this.sunat).then(function(data){
				this.sunatrecepcion = data.body; $("#netix_infosunat").modal("show"); netix_sistema.netix_fin();
			}, function(){
				netix_sistema.netix_fin();
			});
		},
		sunat_automatico: function(){
			netix_sistema.netix_alerta("ALERTA SUNAT","El sistema no puede responder su solicitud. Intente nuevamente o comuniquese con su Administrador","error");
		},
		sunat_quitar_icbper: function(){
			swal({
                title: "CONFIRMAR CLAVE DE ADMINISTRADOR",   
                text: "", 
                icon: "warning",
                dangerMode: true,
                buttons: ["CANCELAR", "SI, CONFIRMAR"],
                content: {
				    element: "input",
				    attributes: {
				      	placeholder: "INGRESAR CLAVE DE ADMINISTRADOR DEL SISTEMA",
				      	type: "text",
				    },
				},
            }).then((willDelete) => {
                if (willDelete){
                	if ($(".swal-content__input").val()=="netixperu") {
                		netix_sistema.netix_inicio_guardar("REGULARIZANDO . . .");
						this.$http.post(url+netix_controller+"/kardex_sinicbper").then(function(data){
							netix_sistema.netix_alerta("REGULARIZADO CORRECTAMENTE",data.body,"success"); netix_sistema.netix_fin();
						}, function(){
							netix_sistema.netix_fin();
						});
                	}else{
                		netix_sistema.netix_alerta("LO SENTIMOS LA CLAVE INDICADA NO ES LA CORRECTA", "","error");
                	}
                }
            });
		}
	},
	created: function(){
		netix_sistema.netix_fin(); this.netix_comprobantes(); this.netix_resumenes();
	}
});