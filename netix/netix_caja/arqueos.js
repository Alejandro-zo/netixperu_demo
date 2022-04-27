var netix_datos = new Vue({
	el: "#netix_datos",
	data: {
		cargando: true, registro:0, filtro:{buscar:"", desde:"", hasta:""}, datos: [],
		paginacion: {"total":0, "actual":1, "ultima":0, "desde":0, "hasta":0}, offset: 3
	},
	computed: {
		netix_actual: function(){
			return this.paginacion.actual;
		},
		netix_paginas: function(){
			if (!this.paginacion.hasta) {
				return [];
			}
			var desde = this.paginacion.actual - this.offset;
			if (desde < 1) {
				desde = 1;
			}
			var hasta = desde + (this.offset * 2);
			if (hasta >= this.paginacion.ultima) {
				hasta = this.paginacion.ultima;
			}

			var paginas = [];
			while(desde <= hasta){
				paginas.push(desde); desde++;
			}
			return paginas;
		}
	},
	methods: {
		netix_datos: function(){
			this.cargando = true; this.filtro.desde = $("#desde").val(); this.filtro.hasta = $("#hasta").val();
			this.$http.post(url+netix_controller+"/lista",{"filtro":this.filtro, "pagina":this.paginacion.actual}).then(function(data){
				this.datos = data.body.lista; this.paginacion = data.body.paginacion;
				this.cargando = false; netix_sistema.netix_fin();
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS", "ERROR DE RED","error"); 
				this.cargando = false; netix_sistema.netix_fin();
			});
		},
		netix_buscar: function(){
			this.paginacion.actual = 1; this.netix_datos();
		},
		netix_paginacion: function(pagina){
			this.paginacion.actual = pagina; this.netix_datos();
		},
		
		pdf_anfitrionas: function(dato){
            var netix_url = url+"restaurante/caja/pdf_vendedores_caja/"+dato.codcontroldiario;
            window.open(netix_url,"_blank");
        },
        pdf_anfitrionas_general: function(dato){
        	$("#modal_empleados").modal("show");
			this.$http.get(url+"restaurante/caja/pdf_vendedores_caja_directo/"+dato.codcontroldiario).then(function(data){
				$("#modal_empleados_contenido").empty().html(data.body);
			},function(){
				netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS LO SENTIMOS", "ERROR DE RED","error"); 
			});
        },
        pdf_venta: function(dato){
            var netix_url = url+"restaurante/caja/venta_diaria/"+dato.codcontroldiario;
            window.open(netix_url,"_blank");
        },
        pdf_balance: function(dato){
            var netix_url = url+"restaurante/caja/balance_caja/"+dato.codcontroldiario;
            window.open(netix_url,"_blank");
        },

		pdf_arqueo_caja: function(dato){
            var netix_url = url+"caja/controlcajas/pdf_arqueo_caja/"+dato.codcontroldiario;
            window.open(netix_url,"_blank");
        },
        pdf_arqueo_excel: function(dato){
            var netix_url = url+"caja/controlcajas/pdf_arqueo_excel/"+dato.codcontroldiario;
            window.open(netix_url,"_blank");
        },
        reaperturar_caja: function(dato) {
        	swal({
                title: "SEGURO RE-APERTURAR CAJA "+dato.fechaapertura+" ?",   
                text: "", 
                icon: "warning",
                dangerMode: true,
                buttons: ["CANCELAR", "SI, RE-APERTURAR CAJA"],
                content: {
				    element: "input",
				    attributes: {
				      	placeholder: "CLAVE DE ADMINISTRADOR",
				      	type: "password",
				    },
				},
            }).then((willDelete) => {
                if (willDelete){
                    this.estado = 1; netix_sistema.netix_inicio_guardar("RE-APERTURANDO CAJA . . .");
                    this.$http.post(url+"caja/controlcajas/netix_reaperturar",{"codcontroldiario":dato.codcontroldiario, "clave":$(".swal-content__input").val()}).then(function(data){
                        if (data.body == "e") {
                        	netix_sistema.netix_alerta("CONTRASEÑA INCORRECTA", "NO SE PUEDE RE-APERTURAR CAJA","error");
                        }else{
                        	if (data.body==1) {
	                            netix_sistema.netix_alerta("CAJA RE-APERTURADA CORRECTAMENTE", "CAJA RE-APERTURADA","success");
	                        }else{
	                            netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE RE-APERTURAR CAJA","error");
	                        }
                        }
                        netix_sistema.netix_fin(); this.netix_datos();
                    }, function(){
                        netix_sistema.netix_alerta("OCURRIO UN ERROR", "NO SE PUEDE RE-APERTURAR CAJA","error"); netix_sistema.netix_fin();
                    });
                }
            });
        }
	},
	created: function(){
		this.netix_datos();
	}
});