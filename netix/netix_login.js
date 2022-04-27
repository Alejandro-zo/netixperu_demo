/* var netix_login = new Vue({
	el: "#netix_login",
	data: {netix_usuario : "", netix_clave : ""},
	methods: {
		login: function(){
			$("#netix_cargando").css("display","block"); $("#netix_mensaje").css("display","none");
			if (this.netix_usuario!="") {
				this.$http.post(url+"netix/netix_login",{"usuario":this.netix_usuario,"clave":this.netix_clave}).then(function(data){
					if (data.body==1) {
						location.href = url+"netix";
					}else{
						$("#netix_cargando").css("display","none"); $("#netix_mensaje").css("display","block");
					}
				}, function(){
					$("#netix_cargando").css("display","none"); $("#netix_mensaje").css("display","block");
				});
			}
		}
	}
}); */

function netix_login(){
	$("#netix_cargando").css("display","block"); $("#netix_mensaje").css("display","none");
	
	$.post(url+"netix/netix_login/",{"usuario":$("#netix_usuario").val(),"clave":$("#netix_clave").val()},function(data){
		if (data==1) {
			window.location.href = url;
		}else{
			$("#netix_cargando").css("display","none"); $("#netix_mensaje").css("display","block");
		}
	},"json");
	return false;
}