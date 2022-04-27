<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SELECCIONAR SUCURSAL</label>
	        	<select class="form-control" name="codsucursal" v-model="campos.codsucursal" required v-on:change="netix_almacenes()">
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
				<label>SELECCIONAR ALMACEN</label>
	        	<select class="form-control" name="codalmacen" v-model="campos.codalmacen" required>
	        		<option value="">SELECCIONE</option>
	        		<option v-for="dato in almacenes" v-bind:value="dato.codalmacen"> {{dato.descripcion}} </option>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>TIPO INVENTARIO</label>
	        	<select name="descripcion" class="form-control" v-model="campos.tipoinventario" required>
	        		<option value="0">INVENTARIO INICIAL</option>
	        		<option value="1">INVENTARIO SEMANAL</option>
	        		<option value="2">INVENTARIO MENSUAL</option>
	        		<option value="3">INVENTARIO TRIMESTRAL</option>
	        		<option value="4">INVENTARIO ANUAL</option>
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

<script> var campos = {codregistro:"",codsucursal:"<?php echo $_SESSION['netix_codsucursal'];?>",codalmacen:"",tipoinventario:"2",codmovimientotipo:"9"}; </script>
<script src="<?php echo base_url();?>netix/netix_inventarios/nuevo.js"></script>