var netix_recepcion = new Vue({
	el: "#netix_form",
	data: {
		estado: 0, campos: campos, fecha:{fecha:""}
	},
	methods: {
		netix_guardar: function(){
			this.estado= 1;
			this.fecha.fecha = $("#fechacomprobante").val();
			this.$http.post(url+netix_controller+"/guardar",{fecha:this.fecha, campos:this.campos}).then(function(data){
				if (data.body==1) {
					if (this.campos.codregistro=="") {
						netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "UN NUEVO REGISTRO EN EL SISTEMA","success");
					}else{
						netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN REGISTRO EDITADO EN EL SISTEMA","info");
					}
				}else{
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE REGISTRAR","error");
				}
				netix_datos.netix_opcion(); this.netix_cerrar();
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},

		netix_eliminar: function(){
			if (this.registro==0) {
				netix_sistema.netix_alerta("DEBE SELECCIONAR UN REGISTRO", "PARA ELIMINAR EN EL SISTEMA UN REGISTRO!!!","error");
			}else{
				swal({
					title: "SEGURO ELIMINAR REGISTRO ?",
					text: "USTED ESTA POR ELIMINAR UN REGISTRO",
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, ELIMINAR"],
				}).then((willDelete) => {
					if (willDelete) {
						this.$http.post(url+netix_controller+"/eliminar",{"codregistro":this.registro}).then(function(data){
							if (data.body==1) {
								netix_sistema.netix_alerta("ELIMINADO CORRECTAMENTE", "UN REGISTRO ELIMINADO EN EL SISTEMA","success");
							}else{
								netix_sistema.netix_alerta("OCURRIO UN ERROR !!!", "SE PERDIÓ LA CONEXION !!! LO SENTIMOS","error");
							}
							this.netix_opcion();
						}, function(){
							netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error");
						});
					}
				});
			}
		},


		netix_cerrar: function(){
			$(".compose").slideToggle();
		},

		netix_consultar: function(){
			var err=$("#fechacomprobante").val();
			if (this.campos.coddocumentotipo=="") {
				netix_sistema.netix_noti(err,"DEBE SELECCIONAR . . .","error");
				this.$refs.coddocumentotipo.focus(); return false;
			}

			if (this.campos.coddocumentotipo==2) {
				if (this.campos.documento.length!=8) {
					netix_sistema.netix_noti("TIPO DE DOCUMENTO DNI","DEBE TENER 8 DÍGITOS . . .","warning");
					this.$refs.documento.focus(); return false;
				}
			}
			if (this.campos.coddocumentotipo==4) {
				if (this.campos.documento.length!=11) {
					netix_sistema.netix_noti("TIPO DE DOCUMENTO RUC","DEBE TENER 11 DÍGITOS . . .","warning");
					this.$refs.documento.focus(); return false;
				}
			}

			$(".btn-consultar").empty().html("<i class='fa fa-spinner fa-spin'></i>"); $(".btn-consultar").attr("disabled","true");
			$(".btn-consultar").empty().html("<i class='fa fa-spinner fa-spin'></i>"); $(".btn-consultar").attr("disabled","true");
			this.$http.get(url+"web/netix_buscarsocio/"+this.campos.documento).then(function(data){
				if (data.body!="") {
					var datos = eval(data.body);
					this.campos.nombrepersona = datos[0]["razonsocial"];
					this.campos.nombrecomercial = datos[0]["nombrecomercial"];
					this.campos.direccion = datos[0]["direccion"];
					this.campos.email = datos[0]["email"];
					this.campos.telefono = datos[0]["telefono"];
					$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
					netix_sistema.netix_noti("CLIENTE ENCONTRADO","","success");
					$("#cliente").hide();
				}
				else{
					if (this.campos.coddocumentotipo==2) {
						this.$http.get(url+"web/netix_dni/"+this.campos.documento).then(function(data){
							if(data.body.persona){
								this.campos.nombrepersona = data.body.persona.razonSocial;
								this.campos.nombrecomercial = data.body.persona.razonSocial;
								this.campos.direccion = "-";
								this.campos.direccion = "";
								this.campos.email = "";
								this.campos.telefono = "";
								//netix_sistema.netix_alerta("Cliente no regitrado","¿Registar?","info");

								swal({
									title: "CLIENTE NO REGISTADO",
									text: "'¿REGISTAR?'",
									icon: "warning",
									dangerMode: true,
									buttons: ["CANCELAR", "SI, REGISTRAR"],
								}).then((willDelete) => {
									if (willDelete) {
										this.campos.newCustomer=1;
										$("#cliente").show();
										this.$http.get(url+"almacen/extenciones/nuevo/"+tabla).then(function(data){
											$("#extencion_modal").empty().html(data.body); $("#modal_extencion").modal("show");
										});
									}
								});
								$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
							}else{
								netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
								//this.noData();
								this.campos.razonsocial = "";
								this.campos.nombrecomercial = "";
								this.campos.direccion = "";
								this.campos.email = "";
								this.campos.telefono = "";
							}
							$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
						});
					}else{
						if (this.campos.coddocumentotipo==4) {
							/* this.$http.get(url+"web/netix_ruc/"+this.campos.documento).then(function(data){
								if(data.body.success==true){
									this.campos.razonsocial = data.body.result.RazonSocial;
									this.campos.direccion = data.body.result.Direccion;
								}else{
									netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
								}
								$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
							}); */
							this.$http.get(url+"web/netix_ruc/"+this.campos.documento).then(function(data){

								if(data.body.persona){
									this.campos.razonsocial = data.body.persona.razonSocial;
									this.campos.direccion = data.body.persona.direccion;
									netix_sistema.netix_noti("Cliente no regitrado","¿Registar?","info");
								}else{
									netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
									//this.noData();
									this.campos.razonsocial = "";
									this.campos.nombrecomercial = "";
									this.campos.direccion = "";
									this.campos.email = "";
									this.campos.telefono = "";
								}
								$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
							});
						}else{
							netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
							//this.noData();
							this.campos.razonsocial = "";
							this.campos.nombrecomercial = "";
							this.campos.direccion = "";
							this.campos.email = "";
							this.campos.telefono = "";
							$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
						}
					}
				}
			});
		},
		netix_provincias: function(){
			if (this.campos.departamento!=undefined) {
				this.$http.get(url+"ventas/clientes/provincias/"+this.campos.departamento).then(function(data){
					$("#provincia").empty().html(data.body); $("#codubigeo").empty().html('<option value="">SELECCIONE</option>');
				});
			}
		},
		netix_distritos: function(){
			if (this.campos.provincia!=undefined) {
				this.$http.get(url+"ventas/clientes/distritos/"+this.campos.departamento+"/"+this.campos.provincia).then(function(data){
					$("#codubigeo").empty().html(data.body);
				});
			}
		},
	}
});