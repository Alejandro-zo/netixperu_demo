<div id="netix_salidas">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE SALIDAS ALMACEN</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO SALIDA</button>
		    	<button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER SALIDA </button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>
	
	<div class="netix_body">
		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th>TIPO MOVIMIENTO</th>
							<th>FECHA</th>
							<th>COMPROBANTE</th>
							<th>COMPROBANTE REF.</th>
							<th>IMPORTE</th>
							<th>ESTADO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
							<td> <input type="radio" v-if="dato.estado!=0" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex)"> </td>
							<td>{{dato.tipomovimiento}}</td>
							<td>{{dato.fechakardex}}</td>
							<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
							<td>{{dato.tipo}} ({{dato.seriecomprobante_ref}} - {{dato.nrocomprobante_ref}})</td>
							<td>S/. {{dato.importe}}</td>
							<td>
								<span class="label label-danger" v-if="dato.estado==0">ANULADO</span>
								<span class="label label-warning" v-if="dato.estado==1">ACTIVO</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_almacen/salidas.js"> </script>