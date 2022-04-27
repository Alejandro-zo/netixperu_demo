<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>DESCRIPCION LINEA</label>
	        	<input type="text" name="descripcion" v-model.trim="campos.descripcion" class="form-control" required autocomplete="off" placeholder="Descripcion . . ." />
			</div>
		</div>

		<?php 
			if ($_SESSION["netix_rubro"]==3) { ?>
				<div class="row form-group">
					<div class="col-xs-12">
						<label>COLOR DE LETRA</label>
			        	<input type="color" name="color" v-model="campos.color" class="form-control" autocomplete="off" placeholder="Color . . ." />
					</div>
				</div>

				<div class="row form-group">
					<div class="col-xs-12">
						<label>COLOR DE FONDO</label>
			        	<input type="color" name="background" v-model="campos.background" class="form-control" autocomplete="off" placeholder="Background . . ." />
					</div>
				</div>
			<?php }
		?>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",descripcion: "",color: "",background: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_form.js"></script>