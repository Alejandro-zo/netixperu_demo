<div id="netix_sunat">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-3 col-xs-12"><h5>LISTA RESUMENES DIARIOS</h5></div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> DESDE</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control input-sm datepicker" id="fecha_desde" value="<?php echo date('Y-m-d');?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> HASTA</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control input-sm datepicker" id="fecha_hasta" value="<?php echo date('Y-m-d');?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>
			<div class="col-md-3 col-xs-12">
		    	<input type="text" class="form-control input-sm" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
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
							<th width="10px">TIPO</th>
							<th>RESUMEN DIARIO</th>
							<th>ENVIO</th>
							<th width="10px">PERIODO</th>
							<th width="10px">TICKET</th>
							<th>DESCRIPCION</th>
							<th>SUNAT</th>
							<th width="10px">ACCIONES</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td> <span class="label label-success">{{dato.tiporesumen}}</span> </td>
							<td>{{dato.nombre_xml}}</td>
							<td>{{dato.fechaenvio}}</td>
							<td>{{dato.periodo}}</td>
							<td>{{dato.ticket}}</td>
							<td>{{dato.descripcion_cdr}}</td>
							<td>
								<span class="label label-danger" v-if="dato.estado==0">PENDIENTE</span>
								<span class="label label-success" v-else="dato.estado!=0">ENVIADO</span>
							</td>
							<td>
								<button type="button" class="btn btn-info btn-xs btn-table" style="margin:1px;" v-on:click="netix_verresumen(dato)">VER RESUMEN</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="modal_resumenes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"> <i class="fa fa-times-circle"></i> </button>
					<h4 class="modal-title" align="center"> <b style="letter-spacing:1px;">INFORMACION DEL RESUMEN</b> </h4>
				</div>

				<div class="modal-body" style="height:350px;overflow-y:auto;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>RAZON SOCIAL</th>
								<th>COMPROBANTE</th>
								<th>F.COMPROBANTE</th>
								<th>F.ANULADO</th>
								<th width="100px">MOTIVO</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in resumenes_info">
								<td>{{dato.cliente}}</td>
								<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
								<td>{{dato.fechacomprobante}}</td>
								<td>{{dato.fechaanulacion}}</td>
								<td>{{dato.motivobaja}}</td>
								<td>{{dato.importe}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_facturacion/resumenes.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>