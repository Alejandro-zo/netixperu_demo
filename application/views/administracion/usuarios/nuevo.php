<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SELECCIONAR EMPLEADO</label>
	        	<select class="form-control" name="codempleado" v-model="campos.codempleado" required>
		    		<option value="">SELECCIONE</option>
		            <?php 
		                foreach ($empleados as $key => $value) { ?>
		                    <option value="<?php echo $value['codpersona'];?>"><?php echo $value["razonsocial"];?></option>
		                <?php }
		            ?>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>SELECCIONAR PERFIL</label>
	        	<select class="form-control" name="codperfil" v-model="campos.codperfil" required>
		    		<option value="">SELECCIONE</option>
		            <?php 
		                foreach ($perfiles as $key => $value) { ?>
		                    <option value="<?php echo $value['codperfil'];?>"><?php echo $value["descripcion"];?></option>
		                <?php }
		            ?>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>NOMBRE DE USUARIO</label>
	        	<input type="text" name="usuario" v-model.trim="campos.usuario" class="form-control" required autocomplete="off" placeholder="Usuario . . ." />
			</div>
			<div class="col-md-6 col-xs-12">
				<label>CLAVE DE USUARIO</label>
	        	<input type="password" name="clave" v-model="campos.clave" class="form-control" required placeholder="Clave . . ." />
			</div>
		</div>

		<h5 align="center">SUCURSALES DEL USUARIO</h5>
		<div class="row form-group">
			<div class="col-xs-12">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>SUCURSAL</th>
							<th>PERMISO</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach ($sucursales as $key => $value) { ?>
								<tr>
									<td><?php echo $value["descripcion"];?></td>
									<td>
										<input type="hidden" name="sucursales[]" value="<?php echo $value["codsucursal"];?>">
										<input type="checkbox" class="check-netix" id="sucursal_<?php echo $value["codsucursal"];?>" value="<?php echo $value["codsucursal"];?>" v-model="sucursales">
									</td>
								</tr>
							<?php }
						?>
					</tbody>
				</table>
			</div>
		</div>

		<!-- <h5 style="text-align:center;">CONFIGURAR OPCIONES EN EL SISTEMA</h5> <hr>
		<div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
	    		<label>EDITAR PRECIO EN VENTA</label>
		        <select class="form-control" name="editar_pventa" v-model="campos.editar_pventa">
		        	<option value="1">SI EDITAR</option>
		        	<option value="0">NO EDITAR</option>
		        </select>
		    </div>
	    </div> -->

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codempleado:"",codperfil: "",usuario: "",clave: "",editar_pventa:"1"};</script>
<script src="<?php echo base_url();?>netix/netix_usuarios.js"></script>