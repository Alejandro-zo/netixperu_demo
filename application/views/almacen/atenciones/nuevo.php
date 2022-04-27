<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION AREA ATENCION</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>TICKETERA IMPRESIÓN</label>
	        	<input type="text" name="impresora" v-model.trim="campos.impresora" class="form-control" required autocomplete="off" placeholder="Impresora . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>COPIAS</label>
	        	<input type="number" name="copias" v-model.trim="campos.copias" class="form-control" required autocomplete="off" placeholder="Copias . . ." />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",descripcion:"",impresora: "",copias: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>