<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SUCURSAL ALMACEN</label>
	        	<select class="form-control" name="codsucursal" v-model="campos.codsucursal" required>
	        		<option value="">SELECCIONE</option>
	        		<?php 
	        			foreach ($sucursales as $key => $value) { ?>
	        				<option value="<?php echo $value["codsucursal"];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION ALMACEN</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>DIRECCION ALMACEN</label>
	        	<input type="text" name="direccion" v-model="campos.direccion" class="form-control" required autocomplete="off" placeholder="Direccion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>TELEFONOS ALMACEN</label>
	        	<input type="number" name="telefonos" v-model.number="campos.telefonos" class="form-control" autocomplete="off" placeholder="Telefonos . . ." />
			</div>
		</div>

		<div class="row form-group">
			<div class="col-xs-12">
				<label>ALMACEN CONTROLA STOCK</label>
	        	<select name="controlstock" v-model.number="campos.controlstock" class="form-control">
	        		<option value="1">SI CONTROLA STOCK</option>
	        		<option value="0">NO CONTROLA STOCK</option>
	        	</select>
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codsucursal:"",descripcion: "",direccion: "",telefonos: "",controlstock: 1}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>