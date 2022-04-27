<div id="netix_sunat">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-3 col-xs-12"><h5>COMPROBANTES ELECTRÓNICOS</h5></div>
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
							<th>RAZON SOCIAL CLIENTE</th>
							<th>FECHA</th>
							<th width="10px">COMPROBANTE</th>
							<th width="10px">IMPORTE</th>
							<th>DESCRIPCION</th>
							<th>SUNAT</th>
							<th width="10px">XML</th>
							<th width="10px">CDR</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td> <span class="label label-success">{{dato.tipo}}</span> </td>
							<td>{{dato.documento}}-{{dato.cliente}}</td>
							<td>{{dato.fechacomprobante}}</td>
							<td>{{dato.seriecomprobante}}-{{dato.nrocomprobante}}</td>
							<td>{{dato.importe}}</td>
							<td>{{dato.descripcion_cdr}}</td>
							<td>
								<span class="label label-danger" v-if="dato.estado==0">PENDIENTE</span>
								<span class="label label-success" v-else="dato.estado!=0">ENVIADO</span>
							</td>
							<td>
								<button type="button" class="btn btn-info btn-xs btn-table" style="margin:1px;" v-on:click="netix_xml(dato.codkardex)"><i class="fa fa-download"></i> XML</button>
							</td>
							<td>
								<button type="button" class="btn btn-warning btn-xs btn-table" style="margin:1px;" v-on:click="netix_cdr(dato.codkardex)"><i class="fa fa-download"></i> CDR</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_facturacion/comprobantes.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>