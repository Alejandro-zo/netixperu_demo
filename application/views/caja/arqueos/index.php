<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE ARQUEOS DE CAJA</h5> </div>
		</div>
	    <div class="row">
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> DESDE</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control datepicker" id="desde" value="<?php echo date('Y-m-01');?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> HASTA</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control datepicker" id="hasta" value="<?php echo date('Y-m-d');?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>

	    	<div class="col-md-3 netix_header_button">
			    <button type="button" class="btn btn-success btn-block" v-on:click="netix_buscar()"> <i class="fa fa-search"></i> BUSCAR ARQUEOS </button>
		    </div>
		    <div class="col-xs-3 netix_header_button">
				<a href="<?php echo base_url();?>netix/w/caja/controlcajas" class="btn btn-primary btn-block"><i class="fa fa-undo"></i> ANTERIOR</a>
        	</div>
	    </div>
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
							<th>F. APERTURA</th>
							<th>F. CIERRE</th>
							<th>CODIGO</th>
							<th>S/. TOTAL APERTURA</th>
							<th>S/. TOTAL CIERRE</th>
							<!-- <th width="10px" colspan="2">S/.ANFITRIONAS</th>
							<th width="10px">V.DIARIA</th>
							<th width="10px">BALANCE</th> -->
							<th width="10px">ARQUEO</th>
							<th width="10px">EXCEL</th>
							<th width="10px">REAPERTURAR CAJA</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td>{{dato.codcontroldiario}}</td>
							<td>{{dato.fechaapertura}}</td>
							<td>
								<span v-if="dato.fechacierre!=null">{{dato.fechacierre}}</span>
								<span class="badge badge-danger" v-else="dato.fechacierre==''">SIN CERRAR</span>
							</td>
							<td>{{dato.codigodiario}}</td>
							<td>S/. {{dato.saldoinicialcaja}}</td>
							<td>
								<b v-if="dato.cerrado==0">S/. {{dato.cierre}}</b>
								<span class="badge badge-danger" v-else="dato.cerrado!=0">CAJA APERTURADA</span>
							</td>
							<!-- <td style="padding-top:5px;">
								<button type="button" class="btn btn-success btn-sm btn-table" v-on:click="pdf_anfitrionas(dato)">GRAL</button>
							</td>
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-success btn-sm btn-table" v-on:click="pdf_anfitrionas_general(dato)">RES</button>
							</td>
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-primary btn-sm btn-table" v-on:click="pdf_venta(dato)">V.DIARIA</button>
							</td>
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-info btn-sm btn-table" v-on:click="pdf_balance(dato)">B.CAJA</button>
							</td> -->
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-success btn-sm btn-table" v-on:click="pdf_arqueo_caja(dato)"><i class="fa fa-print"></i> PDF</button>
							</td>
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-warning btn-sm btn-table" v-on:click="pdf_arqueo_excel(dato)"><i class="fa fa-download"></i> EXCEL</button>
							</td>
							<td style="padding-top:5px;">
								<button type="button" class="btn btn-primary btn-sm btn-table" v-if="dato.cerrado==0" v-on:click="reaperturar_caja(dato)"><i class="fa fa-undo"></i> REAPERTURAR CAJA</button>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="modal_empleados" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title">REPORTE DE ANFITRIONAS</h4>
				</div>
				<div class="modal-body" id="modal_empleados_contenido">

				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_caja/arqueos.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>