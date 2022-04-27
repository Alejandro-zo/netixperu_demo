<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SOCIO DE LA CUENTA</label>
	        	<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true"> </select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
		        <label>BANCO DE LA CUENTA</label>
		        <select class="form-control" name="codbanco" v-model="campos.codbanco" required>
		        	<option value="">SELECCIONE BANCO</option>
		        	<?php 
		        		foreach ($bancos as $key => $value) { ?>
		        			<option value="<?php echo $value['codbanco'];?>"><?php echo $value["descripcion"];?></option>
		        		<?php }
		        	?>
		        </select>
		    </div>
	    </div>
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
				<label>NRO CTA CTE</label>
	        	<input type="text" name="nroctacte" v-model.trim="campos.nroctacte" class="form-control" required autocomplete="off" placeholder="Nro cuenta . . ." maxlength="50" />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>CODIGO INTERBANCARIO (CCI)</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" autocomplete="off" placeholder="Descripcion . . ." maxlength="50" />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codpersona: "",codbanco: "",codmoneda: "1",nroctacte: "",descripcion: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_personas.js"></script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>