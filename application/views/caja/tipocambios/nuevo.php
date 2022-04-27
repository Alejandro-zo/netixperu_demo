<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>MONEDA</label>
	        	<select class="form-control" name="codmoneda" v-model="campos.codmoneda" required>
		        	<?php 
		        		foreach ($monedas as $key => $value) { ?>
		        			<option value="<?php echo $value['codmoneda'];?>"><?php echo $value["descripcion"];?></option>
		        		<?php }
		        	?>
		        </select>
		    </div>
			<div class="col-md-6 col-xs-12">
				<label>FECHA</label>
				<input type="hidden" id="fecha_ref" value="<?php echo date('Y-m-d');?>">
		    	<input type="text" class="form-control datepicker" name="fecha" id="fecha" v-model="campos.fecha" autocomplete="off" required v-on:blur="netix_fecha()">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>CAMBIO COMPRA</label>
	        	<input type="number" step="0.01" name="compra" v-model.number="campos.compra" class="form-control" required autocomplete="off" />
			</div>
			<div class="col-md-6 col-xs-12">
				<label>CAMBIO VENTA</label>
	        	<input type="number" step="0.01" name="venta" v-model.number="campos.venta" class="form-control" required autocomplete="off" />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codmoneda: "2",fecha:$("#fecha_ref").val(),compra: "",venta: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_caja/tipocambios.js"></script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>