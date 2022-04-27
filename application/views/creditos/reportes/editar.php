<div id="netix_form">
	<div style="padding:0px 10px;">
		<h6><b>NROCREDITO:</b> 000<?php echo $info[0]["codcredito"] ?> | <b>FECHA CREDITO:</b> <?php echo $info[0]["fechacredito"] ?></h6>
		<h6><b>FECHA VENCIMIENTO:</b> <?php echo $info[0]["fechavencimiento"] ?> | <b>NRO CUOTAS:</b> | 0<?php echo $info[0]["nrocuotas"] ?> </h6>
		<h6><b><?php echo $tipo?>:</b> <?php echo $info[0]["documento"]." - ".$info[0]["razonsocial"];?> </h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?> </h6>
		<h6><b>REFERENCIA:</b> <?php echo $info[0]["referencia"];?> </h6>

		<h5>
			<span class="label label-success">CREDITO: <?php echo number_format($info[0]["importe"],2);?></span>
			<span class="label label-warning">INTERES: <?php echo number_format($info[0]["interes"],2);?></span>
			<span class="label label-info">TOTAL: <?php echo number_format($info[0]["total"],2);?></span>
			<span class="label label-danger">SALDO: <?php echo number_format($info[0]["saldo"],2);?></span>
		</h5>
	</div>

	<hr> <h5 class="text-center"><b>CAMBIAR <?php echo $tipo;?> DEL CREDITO REGISTRADO</b></h5> <hr>
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">
		<div class="row form-group">
			<div class="col-xs-12">
				<label>CAMBIAR <?php echo $tipo;?> DEL CREDITO</label>
	        	<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true"> 
	        		<option value="<?php echo $info[0]["codpersona"];?>"><?php echo $info[0]["razonsocial"];?></option>
	        	</select>
			</div>
		</div>

		<div class="text-center"> <span class="label label-danger">NOTA* NECESITAS LA CLAVE DE ADMINISTRADOR</span> </div>
		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<div class="alert alert-danger" v-if="sunat==1">EL CREDITO LE PERTENECE A UN COMPROBANTE ELECTRONICO QUE YA FUE DECLARADO A SUNAT - NO PUEDES EDITAR LO SENTIMOS</div>
			<button type="submit" class="btn btn-success" v-if="sunat==0" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script>
	var netix_form = new Vue({
		el: "#netix_form",
		data: {
			estado: 0, sunat:"<?php echo $sunat;?>", tipo: "<?php echo $tipo;?>",
			campos: {codcredito:"<?php echo $info[0]["codcredito"];?>",codkardex:"<?php echo $info[0]["codkardex"];?>",codpersona:"<?php echo $info[0]["codpersona"];?>"}
		},
		methods: {
			netix_guardar: function(){
				swal({
					title: "SEGURO CAMBIAR EL "+this.tipo+" DEL CREDITO ?",   
					text: "NOTA: SE MODIFICARA LOS PAGOS Y KARDEX AL NUEVO "+this.tipo, 
					icon: "warning",
					dangerMode: true,
					buttons: ["CANCELAR", "SI, GUARDAR"],
					content: {
					    element: "input",
					    attributes: {
					      	placeholder: "CLAVE DE PERMISO PARA EDITAR CREDITO",
					      	type: "password",
					    },
					},
				}).then((willDelete) => {
					if (willDelete) {
						this.estado= 1;
						this.$http.post(url+netix_controller+"/editar_guardar",{"campos":this.campos,"clave":$(".swal-content__input").val()}).then(function(data){
							if (data.body==1) {
								netix_sistema.netix_alerta("EDITADO CORRECTAMENTE", "UN CREDITO CAMBIADO EN EL SISTEMA","info");
							}else{
								netix_sistema.netix_alerta("LO SENTIMOS LA CLAVE NO ES LA CORRECTA", "NO PUEDES EDITAR EL CREDITO","error");
							}
							netix_sistema.netix_modulo(); this.netix_cerrar();
						}, function(){
							netix_sistema.netix_alerta("ESTAMOS TENIENDO PROBLEMAS","ERROR DE RED","error");
							netix_sistema.netix_modulo(); this.netix_cerrar();
						});
					}
				});
			},
			netix_cerrar: function(){
				$(".compose").slideToggle();
			}
		}
	});

	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>

<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>