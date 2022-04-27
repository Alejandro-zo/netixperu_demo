<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-2" style="margin-top:3px;"> <h5> <b>REPORTE DE CAJA</b> </h5> </div>
			<div class="col-md-3" style="margin-top:3.5px;">
				<select class="form-control selectpicker ajax" id="codpersona" required data-live-search="true">
					<option value="0">LISTA GENERAL - TODAS LAS PERSONAS</option>
				</select>
			</div>
			<div class="col-md-2">
				<select class="form-control input-sm" v-model="campos.codusuario">
					<option value="0">TODOS LOS USUARIOS</option>
					<?php 
						foreach ($usuarios as $key => $value) { ?>
							<option value="<?php echo $value["codusuario"];?>"><?php echo $value["usuario"];?></option>
						<?php }
					?>
				</select>
			</div>
			<div class="col-md-1" style="margin-top:2.5px;"> <label class="padding-9"> <i class="fa fa-calendar"></i> CAJA AL</label> </div>
			<div class="col-md-2 padding-3" style="margin-top:2.5px;">
				<input type="text" class="form-control input-sm datepicker" id="fecha_detallado" value="<?php echo date('Y-m-d');?>">
			</div>
			<div class="col-md-2 padding-3" style="margin-top:2.5px;">
				<button type="button" class="btn btn-warning btn-sm btn-block" v-on:click="caja_detallado()">
					<i class="fa fa-print"></i> CAJA DETALLADO
				</button>
			</div>
		</div>
		<div class="row netix_header">
			<div class="col-md-1"> <label class="padding-9"><i class="fa fa-calendar"></i> DESDE</label> </div>
			<div class="col-md-2 padding-3">
				<input type="text" class="form-control input-sm datepicker" id="fecha_desde" value="<?php echo date('Y-m-d');?>">
			</div>
			<div class="col-md-1"> <label class="padding-9"><i class="fa fa-calendar"></i> HASTA</label> </div>
			<div class="col-md-2 padding-3">
				<input type="text" class="form-control input-sm datepicker" id="fecha_hasta" value="<?php echo date('Y-m-d');?>">
			</div>
			
			<div class="col-md-1">
				<label class="padding-9">CAJA</label>
				<input type="checkbox" style="height:20px;width:20px;" v-model="campos.caja"> 
			</div>
			<div class="col-md-1">
				<label class="padding-9">BANCO</label>
				<input type="checkbox" style="height:20px;width:20px;" v-model="campos.banco">
			</div>
			<div class="col-md-4 padding-3" style="text-align:right;">
				<button type="button" class="btn btn-success btn-sm" v-on:click="reporte_movimientos()">
					<i class="fa fa-print"></i> REPORTE MOVIMIENTOS
				</button>
				<button type="button" class="btn btn-danger btn-sm" v-on:click="pdf_caja()">
					<i class="fa fa-print"></i> PDF
				</button>
				<button type="button" class="btn btn-danger btn-sm" v-on:click="excel_caja()">
					<i class="fa fa-print"></i> EXCEL
				</button>
			</div>
		</div> <br>
	</div>

	<div class="col-md-12" id="netix_cajabancos" style="height:400px;overflow-y:auto;" >
		<table class="table table-bordered" v-if="estado_detallado==1">
			<thead>
				<tr>
					<th width="120px">N° RECIBO</th>
					<th>CONCEPTO</th>
					<th>DOC.&nbsp;REFERENCIA</th>
					<th>RAZON SOCIAL</th>
					<th>REFERENCIA</th>
					<th>INGRESOS&nbsp;S/.</th>
					<th>EGRESOS&nbsp;S/.</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td colspan="5" align="right"> <b>SALDO ANTERIOR</b> </td>
					<td>{{saldocaja.ingresos}}</td>
					<td>{{saldocaja.egresos}}</td>
				</tr>
				<tr v-for="dato in detallado">
					<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
					<td>{{dato.concepto}}</td>
					<td>{{dato.seriecomprobante_ref}} - {{dato.nrocomprobante_ref}}</td>
					<td>{{dato.razonsocial}}</td>
					<td>{{dato.referencia}}</td>
					<td> <b v-if="dato.tipomovimiento==1">{{dato.importe_r}}</b> </td>
					<td> <b v-if="dato.tipomovimiento==2">{{dato.importe_r}}</b> </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="5" align="right"> <b>TOTALES</b> </td>
					<td>{{saldocaja.totalingresos}}</td>
					<td>{{saldocaja.totalegresos}}</td>
				</tr>
				<tr>
					<td colspan="7" align="right"> <b>SALDO (INGRESOS - EGRESOS): {{saldocaja.total}}</b> </td>
				</tr>
			</tfoot>
		</table>

		<table class="table table-bordered" v-if="estado_movimientos==1">
			<thead>
				<tr>
					<th width="100px">FECHA</th>
					<th width="120px">N° RECIBO</th>
					<th>CONCEPTO</th>
					<th>DOC.&nbsp;REFERENCIA</th>
					<th>RAZON SOCIAL</th>
					<th>REFERENCIA</th>
					<th>INGRESOS&nbsp;S/.</th>
					<th>EGRESOS&nbsp;S/.</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="dato in movimientos">
					<td>{{dato.fechamovimiento}}</td>
					<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
					<td>{{dato.concepto}}</td>
					<td>{{dato.seriecomprobante_ref}} - {{dato.nrocomprobante_ref}}</td>
					<td>{{dato.razonsocial}}</td>
					<td>{{dato.referencia}}</td>
					<td> <b v-if="dato.tipomovimiento==1">{{dato.importe_r}}</b> </td>
					<td> <b v-if="dato.tipomovimiento==2">{{dato.importe_r}}</b> </td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6" align="right"> <b>TOTALES</b> </td>
					<td>{{saldocaja.totalingresos}}</td>
					<td>{{saldocaja.totalegresos}}</td>
				</tr>
				<tr>
					<td colspan="8" align="right"> <b>SALDO (INGRESOS - EGRESOS): {{saldocaja.total}}</b> </td>
				</tr>
			</tfoot>
		</table>
	</div>

	<div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:100%;margin:0px;">
			<div class="modal-content" align="center" style="border-radius:0px">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title">
						<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"];?> </b>
					</h4>
				</div>
				<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
					<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
				</div>
			</div>
		</div>
	</div>
</div>

<script> 
	var campos = {"codpersona":0,"codusuario":0,"fecha_detallado":$("#fecharef").val(),"fecha_desde":$("#fecharef").val(),"fecha_hasta":$("#fecharef").val(),"caja":1,"banco":0,"reporte":0};

	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>
<script src="<?php echo base_url();?>netix/netix_reportes/cajabancos.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
	var pantalla = jQuery(document).height(); $("#netix_cajabancos").css({height: pantalla - 200});
</script>