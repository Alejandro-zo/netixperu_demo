<div id="netix_historial">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-10 col-xs-12"> 
				<h5><b>HISTORIAL DE CREDITOS POR COBRAR: </b> <?php echo $persona[0]["razonsocial"];?></h5>
			</div>
			<div class="col-md-2 col-xs-12 netix_header_button">
				<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="netix_cerrar()">
					<i class="fa fa-times"></i> CERRAR
				</button>
			</div>
		</div>

		<div class="row">
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> DESDE</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechadesde" value="<?php echo date('Y-m-01');?>" autocomplete="off">
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> HASTA</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechahasta" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-2"> 
				<select class="form-control input-sm" v-model="campos.estado">
					<option value="">TODOS LOS CREDITOS</option>
					<option value="0">ANULADOS</option>
					<option value="1">PENDIENTES</option>
					<option value="2">COBRADOS</option>
				</select>
			</div>
			<div class="col-md-2"> 
				<select class="form-control input-sm" v-model="campos.filtro">
					<option value="1">FECHAS FILTRO (SI)</option>
					<option value="0">FECHAS FILTRO (NO)</option>
				</select>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_creditos()">
					<i class="fa fa-search"></i> CREDITOS
				</button>
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<input type="hidden" id="tipo" value="1">

		<div class="table-responsive" style="height: 180px;overflow-y:auto;">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="10px">CREDITO</th>
						<th>F.CREDITO</th>
						<th>NRO CUOTAS</th>
						<th>IMPORTE</th>
						<th>TASA</th>
						<th>INTERES</th>
						<th>TOTAL</th>
						<th>COBRADO</th>
						<th>SALDO</th>
						<th width="10px">ESTADO</th>
						<th width="10px">ANULAR</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="dato in creditos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
						<td>000{{dato.codcredito}}</td>
						<td>{{dato.fechacredito}}</td>
						<td>0{{dato.nrocuotas}}</td>
						<td>{{dato.importe}}</td>
						<td>{{dato.tasainteres}}</td>
						<td>{{dato.interes}}</td>
						<td>{{dato.total}}</td>
						<td><b style="font-size:15px;">{{dato.cobrado}}</b></td>
						<td><b style="font-size:15px;">{{dato.saldo}}</b></td>
						<td>
							<span class="label label-danger" v-if="dato.estado==0">ANULADO</span>
							<span class="label label-warning" v-if="dato.estado==1">PENDIENTE</span>
							<span class="label label-success" v-if="dato.estado==2">COBRADO</span>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-xs" v-on:click="netix_eliminar(dato.codcredito)" style="margin-bottom:2px;">
								<i class="fa fa-trash-o"></i> ANULAR
							</button>
						</td>
					</tr>
					<tr v-for="dato in totales">
						<td colspan="3" style="text-align:right;"><b style="font-size:15px;">TOTALES</b></td>
						<td><b style="font-size:15px;">{{dato.importe}}</b></td> <td></td>
						<td><b style="font-size:15px;">{{dato.interes}}</b></td>
						<td><b style="font-size:15px;">{{dato.total}}</b></td>
						<td><b style="font-size:15px;">{{dato.cobrado}}</b></td>
						<td><b style="font-size:15px;">{{dato.saldo}}</b></td> <td colspan="2"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div> <br>

	<div class="netix_header">
		<div class="row">
			<div class="col-md-4"> <label class="p-5">HISTORIAL DE COBRANZAS</label> </div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> DESDE</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechadesde_c" value="<?php echo date('Y-m-01');?>" autocomplete="off">
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> HASTA</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechahasta_c" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_pagos_cobros()">
					<i class="fa fa-search"></i> COBRANZAS
				</button>
			</div>
		</div>
	</div>

	<div class="netix_body" style="margin-top:5px;">
		<div class="table-responsive" style="height:calc(100vh - 75vh); overflow-y:auto;">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="10px">IMPRIMIR</th>
						<th>CUOTAS COBRADAS (CREDITO | NRO CUOTA | AMORTIZACION)</th>
						<th width="80px">FECHA</th>
						<th width="10px">IMPORTE</th>
						<th width="10px">ANULAR</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="dato in pagos_cobros">
						<td>
							<button type="button" class="btn btn-warning btn-xs" v-on:click="netix_imprimir_recibo(dato.codmovimiento,'COBRO')" style="margin-bottom:2px;">
								<i class="fa fa-print"></i> RECIBO
							</button>
						</td>
						<td>
							<span v-for="d in dato.cuotas">
								<b>CREDITO: 000{{d.codcredito}}</b> | CUOTA: {{d.nrocuota}} | AMORTIZADO: <b>{{d.importe}}</b> &nbsp; | &nbsp;
							</span>
						</td>
						<td>{{dato.fechamovimiento}}</td>
						<td>{{dato.importe}}</td>
						<td>
							<button type="button" class="btn btn-danger btn-xs" v-on:click="netix_anular_pagocobro(dato.codmovimiento,'COBRO')" style="margin-bottom:2px;">
								<i class="fa fa-trash-o"></i> ANULAR
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div style="display:none;">
		<div id="imprimir_recibo"></div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/historial.js"> </script>
<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD'}); </script>