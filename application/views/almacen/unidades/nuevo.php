<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION UNIDAD</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>UNIDAD OFICIAL SUNAT</label>
				<select class="form-control" name="oficial" v-model="campos.oficial" required>
					<option value="">SELECCIONE</option>
					<option value="NIU">UNIDAD (NIU)</option>
					<option value="ZZ">SERVICIO (ZZ)</option>
					<option value="BG">BOLSA (BG)</option>
					<option value="BJ">BALDE (BJ)</option>
					<option value="CY">CILINDRO (CY)</option>
					<option value="GLI">GALON (GLI)</option>
					<option value="CT">CARTONES (CT)</option>
					<option value="BX">CAJA (BX)</option>
					<option value="BO">BOTELLAS (BO)</option>
					<option value="CA">LATAS (CA)</option>
					<option value="KGM">KILOGRAMO (KGM)</option>
					<option value="GRM">GRAMO (GRM)</option>
				</select>
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()"> CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",descripcion: "",oficial: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>