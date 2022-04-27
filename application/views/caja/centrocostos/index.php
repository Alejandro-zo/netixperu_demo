<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE CENTROS DE COSTOS</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO </button>
			    <button type="button" class="btn btn-info" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>

	<div class="netix_body">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<i class="fa fa-spinner fa-spin"></i> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th>DESCRIPCION</th>
							<th>CENTRO COSTO</th>
							<th>CTA ABONO</th>
							<th>CTA CARGO</th>
							<th>CTA DEBE</th>
							<th>CTA HABER</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td> <input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codcentrocosto)"> </td>
							<td>{{dato.descripcion}}</td>
							<td>{{dato.centrocosto}}</td>
							<td>{{dato.ctacontableabono}}</td>
							<td>{{dato.ctacontablecargo}}</td>
							<td>{{dato.ctacontabledebe}}</td>
							<td>{{dato.ctacontablehaber}}</td>
						</tr>
					</tbody>
				</table>
			</div> <hr>

			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_datos.js"> </script>