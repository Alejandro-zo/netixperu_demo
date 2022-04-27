<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION SUCURSAL</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>DIRECCION SUCURSAL</label>
	        	<input type="text" name="direccion" v-model="campos.direccion" class="form-control" required autocomplete="off" placeholder="Direccion . . ." />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>TELEFONOS SUCURSAL</label>
	        	<input type="text" name="telefonos" v-model="campos.telefonos" class="form-control" autocomplete="off" placeholder="Telefonos . . ." maxlength="50" />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>ES SUCURSAL PRINCIPAL</label>
	        	<select name="principal" v-model="campos.principal" class="form-control" required="">
	        		<option value="0">NO ES SUCURSAL PRINCIPAL</option>
	        		<option value="1">SI ES SUCURSAL PRINCIPAL</option>
	        	</select>
			</div>
		</div>

		<hr> <h5 class="text-center"><b>CONFIGURAR COMPROBANTE DE VENTAS POR DEFECTO</b></h5> <hr>
		<div class="row form-group">
			<div class="col-md-7 col-xs-12">
				<label>TIPO COMPROBANTE</label>
	        	<select name="codcomprobantetipo" v-model="campos.codcomprobantetipo" class="form-control">
	        		<option value="0">SIN COMPROBANTE POR DEFECTO</option>
	        		<?php 
	        			foreach ($comprobantes as $key => $value) { ?>
	        				<option value="<?php echo $value['codcomprobantetipo'];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
			<div class="col-md-5 col-xs-12">
				<label>SERIE COMPROBANTE</label>
	        	<input type="text" name="seriecomprobante" v-model="campos.seriecomprobante" class="form-control" autocomplete="off" minlength="4" maxlength="4" style="text-transform:uppercase;" />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",descripcion: "",direccion: "",telefonos: "",principal: 0,codcomprobantetipo:"0",seriecomprobante:""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>