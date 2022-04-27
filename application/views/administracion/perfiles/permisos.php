<div id="netix_form" style="padding: 0px 20px;"> <br>
	<table class="table table-bordered">
		<tbody>
			<tr>
				<td style="width: 350px;"><b>MARCAR TODOS LOS MODULOS</b></td>
				<td class="text-center"> <input type="checkbox" class="check-netix" id="marcar" v-on:change="netix_marcar()"> </td>
			</tr>
		</tbody>
	</table>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" name="codperfil" v-model="campos.codperfil">
		<?php 
			foreach ($modulos as $value) { ?>
				<h5><b><i class="<?php echo $value["icono"]; ?>"></i> MÓDULO <?php echo $value["descripcion"]; ?></b></h5>

				<table class="table table-bordered">
					<thead>
						<tr>
							<th style="width: 350px;"> <i class="fa fa-book"></i> SUB MODULO</th>
							<th> <center> <i class="fa fa-file-text-o"></i> VER </center> </th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach ($value["submodulos"] as $val) {
			                   	foreach ($permisos as $v) {
		                        	if ($v["codmodulo"]==$val["codmodulo"]) {
	                             		echo '<input type="hidden" name="modulos[]" value="'.$v["codmodulo"].'">'; break;
		                    		}
	              				} ?>
								<tr>
									<td><?php echo $val["descripcion"]; ?></td>
									<td class="text-center">
										<input type="checkbox" class="check-netix" id="modulos_<?php echo $val["codmodulo"];?>" value="<?php echo $val["codmodulo"];?>" v-model="campos.modulos">
										<input type="hidden" name="lista[]" value="<?php echo $val["codmodulo"];?>">
									</td>
								</tr>
							<?php }
						?>
					</tbody>
				</table>
			<?php }
		?>
		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> 
	var permisos = [];
	$('input[name^="modulos"]').each(function() {
	    permisos.push($(this).val());
	});
</script>
<script src="<?php echo base_url();?>netix/netix_permisos.js"></script>