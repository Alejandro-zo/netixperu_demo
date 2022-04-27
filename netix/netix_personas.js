var netix_form = new Vue({
	el: "#netix_form",
	data: {
		estado: 0, campos: campos
	},
	methods: {
		netix_guardar: function(){
			this.estado= 1;
			this.$http.post(url+netix_controller+"/guardar", this.campos).then(function(data){
				if (data.body=="e") {
					netix_sistema.netix_noti("ESTE NRO DE DOCUMENTO YA EXISTE", "CAMBIAR DE NRO DOCUMENTO","error"); this.estado= 0;
				}else{
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
				}
			}, function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error");
			});
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		},

		netix_tipodocumento: function(){
			if (this.campos.coddocumentotipo==2) {
				$("#documento").attr("minlength","8"); $("#documento").attr("maxlength","8");
			}else{
				if (this.campos.coddocumentotipo==4) {
					$("#documento").attr("minlength","11"); $("#documento").attr("maxlength","11");
				}else{
					$("#documento").attr("minlength","8"); $("#documento").attr("maxlength","15");
				}
			}
		},
		netix_consultar: function(){
			if (this.campos.coddocumentotipo=="") {
				netix_sistema.netix_noti("SELECCIONE TIPO DE DOCUMENTO","DEBE SELECCIONAR . . .","error"); 
				this.$refs.coddocumentotipo.focus(); return false;
			}

			if (this.campos.coddocumentotipo==2) {
				if (this.campos.documento.length!=8) {
					this.$refs.documento.focus(); return false;
				}
			}
			if (this.campos.coddocumentotipo==4) {
				if (this.campos.documento.length!=11) {
					this.$refs.documento.focus(); return false;
				}
			}

			$(".btn-consultar").empty().html("<i class='fa fa-spinner fa-spin'></i>"); $(".btn-consultar").attr("disabled","true");
			$(".btn-consultar").empty().html("<i class='fa fa-spinner fa-spin'></i>"); $(".btn-consultar").attr("disabled","true");
			this.$http.get(url+"web/netix_buscarsocio/"+this.campos.documento).then(function(data){
				if (data.body!="") {
					var datos = eval(data.body);
					this.campos.razonsocial = datos[0]["razonsocial"];
					this.campos.nombrecomercial = datos[0]["nombrecomercial"];
					this.campos.direccion = datos[0]["direccion"];
					this.campos.email = datos[0]["email"];
					this.campos.telefono = datos[0]["telefono"];
					// this.campos.sexo = datos[0]["sexo"];
					netix_sistema.netix_noti("DOCUMENTO EXISTE EN EL SISTEMA","DOCUMENTO YA REGISTRADO","warning");
					$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
				}else{
					if (this.campos.coddocumentotipo==2) {
						/* this.$http.get(url+"web/netix_dni/"+this.campos.documento).then(function(data){
							if(data.body.success==true){
								if(data.body.source=="essalud"){
									this.campos.razonsocial = data.body.result.ApellidoPaterno+" "+data.body.result.ApellidoMaterno+" "+data.body.result.Nombres;
								}else{
									if (data.body.source=="jne") {
										this.campos.razonsocial = data.body.result.apellidoPaterno+" "+data.body.result.apellidoMaterno+" "+data.body.result.nombres;
									}else{
										this.campos.razonsocial = data.body.result.apellidos+" "+data.body.result.Nombres;
									}
								}
								this.campos.direccion = "-";
							}else{
								netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
							}
							$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
						}); */
						this.$http.get(url+"web/netix_dni/"+this.campos.documento).then(function(data){
							if(data.body.persona){
								this.campos.razonsocial = data.body.persona.razonSocial;
								this.campos.direccion = "-";
							}else{
								netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
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
								}else{
									netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
								}
								$(".btn-consultar").empty().html("<i class='fa fa-search'></i>"); $(".btn-consultar").removeAttr("disabled");
							});
						}else{
							netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","DOCUMENTO NO EXISTE","error");
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
		netix_editarpersona: function(){
			this.$http.post(url+netix_controller+"/ubigeo", {"codregistro":netix_datos.registro}).then(function(data){
				if (this.campos.codpatrocinador==undefined) {
					var patrocinador = eval(data.body.patrocinador);
					$("#codpatrocinador").empty().html("<option value='"+patrocinador[0]["codpersona"]+"'>"+patrocinador[0]["razonsocial"]+"</option>");

					$(".selectpicker").selectpicker("refresh"); $(".filter-option").text(patrocinador[0]["razonsocial"]); 
					$("#codpatrocinador").val(patrocinador[0]["codpersona"]); this.campos.codpatrocinador = patrocinador[0]["codpersona"];
				}

				var datos = eval(data.body.ubigeo); this.campos.departamento = datos[0]["ubidepartamento"];
				this.campos.provincia = datos[0]["ubiprovincia"]; this.campos.codubigeo = datos[0]["codubigeo"];

				this.$http.get(url+"ventas/clientes/provincias/"+this.campos.departamento).then(function(data){
					$("#provincia").empty().html(data.body); $("#codubigeo").empty().html('<option value="">SELECCIONE</option>');
					this.campos.provincia = datos[0]["ubiprovincia"];

					this.$http.get(url+"ventas/clientes/distritos/"+this.campos.departamento+"/"+this.campos.provincia).then(function(data){
						$("#codubigeo").empty().html(data.body); this.campos.codubigeo = datos[0]["codubigeo"];
					});
				});
			});
		},
		netix_editarctacte:function(){
			this.$http.post(url+netix_controller+"/socio", {"codregistro":netix_datos.registro}).then(function(data){
				var socio = eval(data.body);
				$("#codpersona").empty().html("<option value='"+socio[0]["codpersona"]+"'>"+socio[0]["razonsocial"]+"</option>");

				$(".selectpicker").selectpicker("refresh"); $(".filter-option").text(socio[0]["razonsocial"]); 
				$("#codpersona").val(socio[0]["codpersona"]); this.campos.codpersona = socio[0]["codpersona"];
			});
		}
	},
	mounted: function(){
		if (netix_datos.registro>0) {
			if (netix_controller=="ventas/clientes" || netix_controller=="compras/proveedores") {
				this.netix_editarpersona();
			}
			if (netix_controller=="caja/ctasctes") {
				this.netix_editarctacte();
			}
		}
	}
});