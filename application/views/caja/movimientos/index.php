<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> 
				<h5>
					<?php 
			    		if ($_SESSION['netix_codcontroldiario']==0) {
			    			echo '<span class="label label-danger">CAJA CERRADA</span>';
			    		}
			    	?>
					<b>LISTA DE MOVIMIENTOS DE CAJA</b> 
				</h5> 
			</div>
		</div>
		<?php 
			if ($_SESSION['netix_codcontroldiario']!=0) { ?>
			    <div class="row">
			    	<div class="col-md-8 col-xs-12 netix_header_button">
				    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()">
					        <i class="fa fa-plus-square"></i> NUEVO
					    </button>
					    <button type="button" class="btn btn-info" v-on:click="netix_transferencias()">
					        <i class="fa fa-exchange"></i> TRANSFERENCIAS 
					        <span class="label label-danger" style="color:#fff;"><?php echo $transferencias[0]["cantidad"];?></span>
					    </button>
					    <button type="button" class="btn btn-warning" v-on:click="netix_editar()">
					        <i class="fa fa-edit"></i> EDITAR 
					    </button>
					    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()">
					        <i class="fa fa-trash-o"></i> ELIMINAR
					    </button>
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
				    </div>
			    </div>
			<?php }
		?>
	</div> <br>

	<div class="netix_body">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th width="80px">FECHA</th>
							<th width="110px">N° RECIBO</th>
							<th>CONCEPTO</th>
							<th>RAZÓN SOCIAL</th>
							<th>REFERENCIA</th>
							<th>TIPO</th>
							<th width="100px">S/ IMPORTE</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
							<td v-if="dato.estado!=0">
								<input type="radio"  class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codmovimiento)">
							</td>
							<td v-if="dato.estado==0" style="height:25px;"></td>
							<td>{{dato.fechamovimiento}}</td>
							<td>{{dato.seriecomprobante+"-"+dato.nrocomprobante}}</td>
							<td>{{dato.concepto}}</td>
							<td>{{dato.razonsocial}}</td>
							<td>{{dato.referencia}}</td>
							<td>
								<span class="label label-danger" v-if="dato.tipomovimiento==2">EGRESO</span>
								<span class="label label-warning" v-if="dato.tipomovimiento==1">INGRESO</span>
							</td>
							<td>S/. {{dato.importe_r}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="modal_transferencias" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" align="center">
				<div class="modal-header">
					<h4 class="modal-title" style="letter-spacing:1px;">
						<i class="fa fa-exchange" style="font-size:80px"></i> <br> <b>LISTA DE TRANFERENCIAS A ESTA CAJA</b> 
					</h4>
				</div>
				<div class="modal-body" style="height:270px;overflow-y: auto;">
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th width="80px">FECHA</th>
								<th width="110px">N° RECIBO</th>
								<th>CAJA</th>
								<th>CONCEPTO</th>
								<th>RAZÓN SOCIAL</th>
								<th width="100px">S/ IMPORTE</th>
								<th width="100px">ACEPTAR</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in transferencias">
								<td>{{dato.fechamovimiento}}</td>
								<td>{{dato.seriecomprobante+"-"+dato.nrocomprobante}}</td>
								<td>{{dato.caja}}</td>
								<td>{{dato.concepto}}</td>
								<td>{{dato.razonsocial}}</td>
								<td>S/. {{dato.importe_r}}</td>
								<td>
									<button type="button" class="btn btn-success btn-sm" v-on:click="netix_aceptar_transferencia(dato)">ACEPTAR</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
				</div>
			</div>
		</div>
	</div>

</div>

<script src="<?php echo base_url();?>netix/netix_caja/movi_index.js"> </script>