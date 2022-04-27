var netix_sistema = new Vue({
	el: "#netix_sistema",
	methods: {
		netix_inicio: function(){
			var html = "<div id='ajax-overlay'>";
		    	html += "<div id='ajax-overlay-body'>";
			        html += '<img src="'+url+'public/img/netix_loading.gif">';
			    html += "</div>";
			html += "</div>";

			$("body").prepend(html); $("#ajax-overlay").fadeIn(50);
		},
		netix_inicio_guardar: function(mensaje){
			var html = "<div id='ajax-overlay'>";
		    	html += "<div id='ajax-overlay-body'>";
			        html += '<img src="'+url+'public/img/netix_loading.gif"> <br> <span>'+mensaje+'</span>';
			    html += "</div>";
			html += "</div>";

			$("body").prepend(html); $("#ajax-overlay").fadeIn(50);
		},
		netix_fin: function(){
			$("#ajax-overlay").fadeOut(100, function(){
				$("#ajax-overlay").remove();
			});
		},
		netix_loader: function(contenido, top){
			var html = '<center> <img src="'+url+'public/img/netix_loading.gif" style="padding-top:'+top+'px;"> <center>';
			$("#"+contenido).empty().html(html);
		},
		netix_alerta: function(titulo,mensaje,tipo){
			swal({title: titulo, text: mensaje, icon: tipo, closeOnClickOutside: false });
		},
		netix_noti: function(titulo,mensaje,tipo){
			new PNotify({
				title: titulo,
				text: mensaje,
				type: tipo,
				delay: 1500,
				styling: 'bootstrap3'
			});
		},
		netix_modulo: function(){
			this.netix_inicio();
			this.$http.post(url+netix_controller).then(function(data){
				$("#netix_sistema").empty().html(data.body);
			},function(){
				this.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error"); this.netix_fin();
			});
		},
		netix_error: function(){
			this.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error"); this.netix_fin();
		},
		netix_error_operacion: function(){
			this.netix_alerta("ATENCION USUARIO","ERROR DE RED (INTERNET)","error"); this.netix_modulo();
		}
	},
	created: function(){
		this.netix_modulo();
	}
});