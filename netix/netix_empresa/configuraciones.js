var netix_datos = new Vue({
	el: "#netix_datos",
	data: {cargando: true, campos: campos},
	methods: {
		netix_consultar: function(){
			$(".btn-consultar").empty().html("<i class='fa fa-spinner fa-spin'></i> CONSULTANDO"); $(".btn-consultar").attr("disabled","true");
			this.$http.get(url+"web/netix_ruc/"+this.campos.documento).then(function(data){
				if(data.body.success==true){
					this.campos.razonsocial = data.body.result.RazonSocial;
					this.campos.direccion = data.body.result.Direccion;
				}else{
					netix_sistema.netix_noti("NO SE ENCONTRARON DATOS","RUC NO EXISTE","error");
				}
				$(".btn-consultar").empty().html("<i class='fa fa-undo'></i> CONSULTAR SUNAT"); $(".btn-consultar").removeAttr("disabled");
			});
		},
		netix_itemrepetir: function(){
			if (this.campos.itemrepetircomprobante==1) {
				this.campos.itemrepetircomprobante = 0;
			}else{
				this.campos.itemrepetircomprobante = 1;
			}
		},
		netix_guardar: function(){
			this.estado = 1; const formulario = new FormData($("#formulario")[0]);
			this.$http.post(url+netix_controller+"/guardar", formulario).then(function(data){
				if (data.body==1) {
					netix_sistema.netix_noti("CONFIGURACION REGISTRADA CORRECTAMENTE","DATOS GUARDADOS EN EL SISTEMA","success");
				}else{
					netix_sistema.netix_alerta("ATENCION USUARIO","OCURRIO UN ERROR AL GUARDAR LA CONFIGURACION","error");
				}
			}, function(){
				netix_sistema.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error");
			});
		}
	},
	created: function(){
		this.campos.agradecimiento = $("#agradecimiento_texto").val();
		this.campos.publicidad = $("#publicidad_texto").val();
		netix_sistema.netix_fin();
	}
});