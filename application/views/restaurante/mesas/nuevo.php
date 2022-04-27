<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<h5 align="center"><b>SUCURSAL: <?php echo $_SESSION["netix_sucursal"];?></b></h5> <hr>

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SELECCIONAR AMBIENTE</label>
	        	<select class="form-control" name="codambiente" v-model="campos.codambiente" required>
	        		<option value="">SELECCIONE</option>
	        		<?php 
	        			foreach ($ambientes as $key => $value) { ?>
	        				<option value="<?php echo $value["codambiente"];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<h5><b>DESCRIPCION MESA: MESA NRO {{campos.nromesa}}</b></h5>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-6">
				<label>NRO MESA</label>
	        	<input type="text" name="nromesa" v-model.trim="campos.nromesa" class="form-control" required autocomplete="off" placeholder="Nro mesa . . ." maxlength="10" />
			</div>
			<div class="col-xs-6">
				<label>CAPACIDAD MESA</label>
	        	<input type="number" name="capacidad" v-model.trim="campos.capacidad" class="form-control" required autocomplete="off" placeholder="Capacidad . . ." />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codambiente:"",descripcion:"",nromesa:"",capacidad:""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>