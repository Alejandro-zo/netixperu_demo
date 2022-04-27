<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." maxlength="100" />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>CENTRO COSTO</label>
	        	<input type="text" name="centrocosto" v-model.trim="campos.centrocosto" class="form-control" required autocomplete="off" placeholder="Centro costo . . ." maxlength="10" />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>CTA ABONO</label>
	        	<input type="text" name="ctacontableabono" v-model.trim="campos.ctacontableabono" class="form-control" required autocomplete="off" placeholder="Cuenta abono . . ." maxlength="20" />
			</div>
			<div class="col-md-6 col-xs-12">
				<label>CTA CARGO</label>
	        	<input type="text" name="ctacontablecargo" v-model.trim="campos.ctacontablecargo" class="form-control" required autocomplete="off" placeholder="Cuenta cargo . . ." maxlength="20" />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>CTA DEBE</label>
	        	<input type="text" name="ctacontabledebe" v-model.trim="campos.ctacontabledebe" class="form-control" required autocomplete="off" placeholder="Cuenta debe . . ." maxlength="20" />
			</div>
			<div class="col-md-6 col-xs-12">
				<label>CTA HABER</label>
	        	<input type="text" name="ctacontablehaber" v-model.trim="campos.ctacontablehaber" class="form-control" required autocomplete="off" placeholder="Cuenta haber . . ." maxlength="20" />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",descripcion: "",centrocosto: "",ctacontableabono: "",ctacontablecargo: "",ctacontabledebe: "",ctacontablehaber: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>