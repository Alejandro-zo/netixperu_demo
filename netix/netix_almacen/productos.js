var netix_form = new Vue({
	el: "#netix_form",
	data: { estado: 0, editar:0, factor:0, campos: campos, familias:[], lineas:[], marcas:[], unidades:[], campos_1 : campos_1},
	methods: {
		netix_datos: function(){
			this.netix_extencion("almacen/familias",""); this.netix_extencion("almacen/lineas",""); this.netix_extencion("almacen/marcas","");
		},
		netix_extencion: function(tabla,codigo){
			this.$http.get(url+"almacen/extenciones/lista/"+tabla).then(function(data){
				if (tabla=="almacen/familias") {
					this.familias = data.body; this.campos.codfamilia = codigo;
				}
				if (tabla=="almacen/lineas") {
					this.lineas = data.body; this.campos.codlinea = codigo;
				}
				if (tabla=="almacen/marcas") {
					this.marcas = data.body; this.campos.codmarca = codigo;
				}
			});
		},
		netix_nuevo_extencion: function(tabla){
			this.$http.get(url+"almacen/extenciones/nuevo/"+tabla).then(function(data){
				$("#extencion_modal").empty().html(data.body); $("#modal_extencion").modal("show");
			});
		},

		netix_addunidad: function(){
			if (this.campos_1.codunidad=="") {
		        netix_sistema.netix_noti("SELECCIONAR UNIDAD", "INGRESAR LOS CAMPOS Y LUEGO AGREGAR LA UNIDAD","error"); return false;
		    }else{
		    	this.campos_1.unidad = String($("#codunidad option:selected").text());
		    }

			if (this.campos_1.factor=="") {
		        netix_sistema.netix_noti("INGRESAR FACTOR", "EL FACTOR DEBE SER VALIDO","error"); return false;
		    }

		    if (parseFloat(this.campos_1.pventapublico)<=0) {
		        netix_sistema.netix_noti("PRECIO DE VENTA", "DEBE SER MAYOR A CERO","error"); return false;
		    }else{
		    	if (parseFloat(this.campos_1.pventapublico)<parseFloat(this.campos_1.pventamin)) {
		    		netix_sistema.netix_noti("PRECIO DE VENTA", "DEBE SER MAYOR A P.VENTA MINIMO","error"); return false;
		    	}
		    }

		    var existe = this.unidades.filter(function(uni){
			    if(uni.codunidad == this.campos_1.codunidad){
			    	return uni.codunidad;
			    };
			});

		    if (existe.length==0) {
		    	if (this.editar == 1) {
			    	this.editar = 0; var factorminimo = [];
			    }else{
			    	var factorminimo = this.unidades.filter(function(uni){
					    if(this.campos_1.factor <= uni.factor){
					    	return uni.factor;
					    };
					});
			    }

		    	if (factorminimo.length==0) {
		    		this.unidades.push({
			    		codunidad:this.campos_1.codunidad,
			    		unidad:this.campos_1.unidad,
			    		factor:this.campos_1.factor,
			    		preciocompra:this.campos_1.preciocompra,
			    		pventapublico:this.campos_1.pventapublico,
			    		pventamin:this.campos_1.pventamin,
			    		pventacredito:this.campos_1.pventacredito,
			    		pventaxmayor:this.campos_1.pventaxmayor,
			    		pventaadicional:this.campos_1.pventaadicional,
			    		codigobarra:this.campos_1.codigobarra
			    	}); 
			    	this.campos_1.codunidad = ""; this.campos_1.factor = ""; this.campos_1.preciocompra = "0.00";
			    	this.campos_1.pventapublico = "0.00"; this.campos_1.pventamin = "0.00"; this.campos_1.pventacredito = "0.00";
			    	this.campos_1.pventaxmayor = "0.00"; this.campos_1.pventaadicional = "0.00"; this.campos_1.codigobarra = "";
		    	}else{
		    		netix_sistema.netix_noti("EL FACTOR DEBE SER MAYOR", "FACTOR NUEVO MAYOR A "+this.campos_1.factor,"error");
		    		return false;
		    	}
		    }else{
		    	netix_sistema.netix_noti("ESTA UNIDAD YA EXISTE", "CAMBIAR UNIDAD DE MEDIDA","error");
		    	return false;
		    }
		},
		netix_ediunidad: function(index,unidad){
			this.campos_1.codunidad = unidad.codunidad; this.campos_1.factor = unidad.factor;
			this.campos_1.preciocompra = unidad.preciocompra; this.campos_1.pventapublico = unidad.pventapublico;
			this.campos_1.pventamin = unidad.pventamin; this.campos_1.pventacredito = unidad.pventacredito;
			this.campos_1.pventaxmayor = unidad.pventaxmayor; this.campos_1.pventaadicional = unidad.pventaadicional;
			this.campos_1.codigobarra = unidad.codigobarra;

			this.editar = 1; this.factor = unidad.factor; this.unidades.splice(index,1);
		},
		netix_deleteunidad: function(index,unidad){
			this.unidades.splice(index,1);
		},
		netix_unidades: function(unidad){
			this.$http.post(url+netix_controller+"/unidades", {"codregistro":netix_datos.registro}).then(function(data){
				this.unidades = data.body.unidades;
				
				if (this.campos.afectoigvcompra==1) { 
					$("#afectoigvcompra_check").attr("checked","true"); 
				}
				if (this.campos.afectoigvventa==1) { 
					$("#afectoigvventa_check").attr("checked","true"); 
				}
				if (this.campos.afectoicbper==1) { 
					$("#afectoicbper_check").attr("checked","true");
				}else{
					$("#afectoicbper_check").removeAttr("checked");
				}
				var datos = eval(data.body.campos); this.netix_extencion("almacen/familias",datos[0]["codfamilia"]); 
				this.netix_extencion("almacen/lineas",datos[0]["codlinea"]); this.netix_extencion("almacen/marcas",datos[0]["codmarca"]);
			});
		},
		netix_guardar: function(){
			if (this.unidades.length==0) {
				netix_sistema.netix_noti("REGISTRAR MINIMO UNA UNIDAD", "CON SUS PRECIOS DEL PRODUCTO","error"); return false;
			}

		    this.campos.afectoigvcompra = 0;
		    if ($("#afectoigvcompra_check").is(":checked")) {
		    	this.campos.afectoigvcompra = 1;
		    }
		    this.campos.afectoigvventa = 0;
		    if ($("#afectoigvventa_check").is(":checked")) {
		    	this.campos.afectoigvventa = 1;
		    }
		    this.campos.afectoicbper = 0;
			if ($("#afectoicbper_check").is(":checked")) {
		    	this.campos.afectoicbper = 1;
		    }

			this.estado= 1;
			this.$http.post(url+"almacen/productos/guardar", {"campos":this.campos,"unidades":this.unidades}).then(function(data){
				if (data.body==0) {
					netix_sistema.netix_alerta("OCURRIO UN ERROR AL REGISTRAR", "NO SE PUEDE REGISTRAR","error");
					netix_datos.netix_opcion(); this.netix_cerrar();
				}else{
					if ($("#foto").val()=="") {
						if (this.campos.codregistro=="") {
							netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "UN NUEVO PRODUCTO EN EL SISTEMA","success");
						}else{
							netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN REGISTRO PRODUCTO EN EL SISTEMA","info");
						}

						if (netix_controller=="almacen/productos") {
							netix_datos.netix_opcion(); 
						}

						if (netix_controller=="compras/compras" || netix_controller=="almacen/ingresos") {
							$("#netix_tituloform").text("BUSCAR PRODUCTO"); netix_sistema.netix_loader("netix_formulario",180); 
							this.$http.post(url+"almacen/productos/buscar/compras").then(function(data){
								$("#netix_formulario").empty().html(data.body);
							});
						}else{
							this.netix_cerrar();
						}
					}else{
						$("#codproducto").val(data.body); const formulario = new FormData($("#formulario")[0]);
						
						this.$http.post(url+"almacen/productos/guardar_foto", formulario).then(function(info){
							if (info.body==0) {
								netix_sistema.netix_alerta("OCURRIO UN ERROR AL SUBIR LA IMAGEN", "PRODUCTO REGISTRADO","error");
							}else{
				                if (this.campos.codregistro=="") {
									netix_sistema.netix_alerta("GUARDADO CORRECTAMENTE", "UN NUEVO PRODUCTO EN EL SISTEMA","success");
								}else{
									netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN REGISTRO PRODUCTO EN EL SISTEMA","info");
								}
							}
							if (netix_controller=="almacen/productos") {
								netix_datos.netix_opcion();
							}

							if (netix_controller=="compras/compras" || netix_controller=="almacen/ingresos") {
								$("#netix_tituloform").text("BUSCAR PRODUCTO"); netix_sistema.netix_loader("netix_formulario",180); 
								this.$http.post(url+"almacen/productos/buscar/compras").then(function(data){
									$("#netix_formulario").empty().html(data.body);
								});
							}else{
								this.netix_cerrar();
							}
			            }, function(){
							netix_sistema.netix_alerta("OCURRIO UN ERROR AL SUBIR LA IMAGEN", "ERROR DE RED","error");
						});
					}
				}
			}, function(){
				netix_sistema.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error");
			});
		},
		netix_codigobarra: function(e){
			if (e.keyCode == 13) {               
			    e.preventDefault(); return false;
			}
		},
		netix_cerrar: function(){
			$(".compose").slideToggle();
		}
	},
	created: function(){
		if (netix_controller=="almacen/productos") {
			if (netix_datos.registro!=0) {
				this.netix_unidades();
			}else{
				this.netix_datos();
			}
		}else{
			this.netix_datos();
		}
	}
});