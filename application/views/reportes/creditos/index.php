<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-3" style="margin-top:3px;"> <h5> <b>REPORTE GENERAL DE CREDITOS</b> </h5> </div>
			<div class="col-md-4" style="margin-top:3.5px;">
				<select class="form-control selectpicker ajax" id="codpersona" required data-live-search="true">
					<option value="0">LISTA GENERAL - TODAS LAS PERSONAS</option>
				</select>
			</div>
			<div class="col-md-1" style="margin-top:2.5px;"> <label class="padding-9"> <i class="fa fa-calendar"></i> FECHA</label> </div>
			<div class="col-md-2 padding-3" style="margin-top:2.5px;">
				<input type="text" class="form-control datepicker" id="fecha_saldos" value="<?php echo date('Y-m-d');?>">
			</div>
			<div class="col-md-2 padding-3" style="margin-top:2.5px;">
				<button type="button" class="btn btn-warning btn-block" v-on:click="saldo_creditos()">
					<i class="fa fa-print"></i> SALDOS
				</button>
			</div>
		</div>
		<div class="row netix_header" style="padding-bottom:4px">
			<div class="col-md-1"> <label class="padding-9"><i class="fa fa-calendar"></i> DESDE</label> </div>
			<div class="col-md-2 padding-3">
				<input type="text" class="form-control input-sm datepicker" id="fecha_desde" value="<?php echo date('Y-m-01');?>" v-on:blur="netix_vacio()">
			</div>
			<div class="col-md-1"> <label class="padding-9"><i class="fa fa-calendar"></i> HASTA</label> </div>
			<div class="col-md-2 padding-3">
				<input type="text" class="form-control input-sm datepicker" id="fecha_hasta" value="<?php echo date('Y-m-d');?>" v-on:blur="netix_vacio()">
			</div>
			<div class="col-md-2"> <label class="padding-9">TIPO REPORTE</label> </div>
			<div class="col-md-4 padding-3">
				<select class="form-control input-sm" id="tipo_consulta" v-model="campos.tipo_consulta" v-on:change="netix_vacio()">
					<option value="1">ESTADO DE CUENTA</option>
					<option value="2">ESTADO DE CUENTA DETALLADO</option>
				</select>
			</div>
		</div>
		<div class="row netix_header">
			<div class="col-md-1"> <label class="padding-9">CREDITO</label> </div>
			<div class="col-md-2 padding-3">
				<select class="form-control input-sm" id="tipo" v-model="campos.tipo" v-on:change="netix_vacio()">
					<option value="1">POR COBRAR</option>
					<option value="2">POR PAGAR</option>
				</select>
			</div>
			<div class="col-md-1"> <label class="padding-9">MOSTRAR</label> </div>
			<div class="col-md-2 padding-3">
				<select class="form-control input-sm" id="mostrar" v-model="campos.mostrar" v-on:change="netix_vacio()">
					<option value="1" v-if="campos.tipo==1">POR CLIENTE</option>
					<option value="1" v-if="campos.tipo!=1">POR PROVEEDOR</option>
					<option value="2" v-if="campos.tipo_consulta==1">POR CREDITO</option>
				</select>
			</div>
			<div class="col-md-6 padding-3">
				<button type="button" class="btn btn-success btn-sm" v-on:click="ver_creditos()">
					<i class="fa fa-print"></i> VER REPORTE
				</button>
				<button type="button" class="btn btn-danger btn-sm" v-on:click="pdf_creditos()">
					<i class="fa fa-print"></i> PDF REPORTE
				</button>
				<!-- <button type="button" class="btn btn-warning btn-sm" v-on:click="excel_creditos()">
					<i class="fa fa-print"></i> EXCEL REPORTE
				</button> -->
			</div>
		</div> <br>
	</div>

	<div class="col-md-12" id="netix_creditos" style="height:400px;overflow-y:auto;">
		<div v-if="campos.tipo_consulta==1">
			<div v-if="campos.mostrar==1">
				<div v-for="dato in estado_cuenta_socios">
					<table class="table table-bordered">
						<tr style="background:#f2f2f2">
							<th colspan="6">
								<b v-if="campos.tipo==1">CLIENTE:</b> 
								<b v-if="campos.tipo!=1">PROVEEDOR:</b> {{dato.razonsocial}} </b> |
								<b>DIRECCION:</b> {{dato.direccion}}
							</th>
						</tr>
						<tr>
							<th style="width:10%;"><b>FECHA</b></th>
							<th style="width:10%;"><b>COMPROBANTE</b></th>
							<th style="width:50%;"><b>DESCRIPCION</b></th>
							<th style="width:10%;"><b>CARGO</b></th>
							<th style="width:10%;"><b>ABONO</b></th>
							<th style="width:10%;"><b>SALDO</b></th>
						</tr>
						<tr>
							<td colspan="5" align="right"><b>SALDO ANTERIOR</b></td>
							<td align="right">{{dato.anterior}}</td>
						</tr>
						<tr v-for="c in dato.movimientos">
							<td style="width:10%;">{{c.fecha}}</td>
							<td style="width:10%;">{{c.comprobante}}</td>
							<td style="width:50%;">{{c.referencia}}</td>
							<td style="width:10%;" align="right">{{c.cargo}} </td>
							<td style="width:10%;" align="right">{{c.abono}} </td>
							<td style="width:10%;" align="right">{{c.saldo}} </td>
						</tr>
						<tr>
							<td colspan="3" align="right"><b>TOTALES</b></td>
							<td align="right"><b>{{dato.cargo}}</b></td>
							<td align="right"><b>{{dato.abono}}</b></td>
							<td align="right"><b>{{dato.saldo}}</b></td>
						</tr>
					</table>
				</div>
			</div>

			<div v-if="campos.mostrar==2">
				<div v-for="dato in estado_cuenta_creditos">
					<table class="table table-bordered">
						<tr style="background:#f2f2f2;">
							<th colspan="8">
								<b v-if="campos.tipo==1">CLIENTE:</b> 
								<b v-if="campos.tipo!=1">PROVEEDOR:</b> {{dato.razonsocial}} </b> |
								<b>DIRECCION:</b> {{dato.direccion}}
							</th>
						</tr>
						<tr>
							<th style="width:10%;"><b>FECHA</b></th>
							<th style="width:10%;"><b>COMPROBANTE</b></th>
							<th style="width:40%;"><b>DESCRIPCION</b></th>
							<th style="width:10%"><b>IMPORTE</b></th>
							<th style="width:10%"><b>INTERES</b></th>
							<th style="width:10%"><b>TOTAL</b></th>
							<th style="width:10%"><b v-if="campos.tipo==1">COBRANZA</b> <b v-if="campos.tipo!=1">PAGO</b></th>
							<th style="width:10%"><b>SALDO</b></th>
						</tr>
						<tr v-for="c in dato.creditos">
							<td style="width:10%;">{{c.fecha}}</td>
							<td style="width:10%;">{{c.comprobante}}</td>
							<td style="width:40%;">{{c.referencia}}</td>
							<td style="width:10%;" align="right">{{c.importe}} </td>
							<td style="width:10%;" align="right">{{c.interes}} </td>
							<td style="width:10%;" align="right"><b>{{c.total}}</b></td>
							<td style="width:10%;" align="right">{{c.cobranza}} </td>
							<td style="width:10%;" align="right"><b>{{c.saldo}}</b></td>
						</tr>
					</table>
				</div>
			</div>
		</div>

		<div v-if="campos.tipo_consulta==2">
			<div v-for="dato in estado_cuenta_detallado">
				<table class="table table-bordered">
					<tr style="background:#f2f2f2">
						<th colspan="7">
							<b v-if="campos.tipo==1">CLIENTE:</b> 
							<b v-if="campos.tipo!=1">PROVEEDOR:</b> {{dato.razonsocial}} </b> |
							<b>DIRECCION:</b> {{dato.direccion}}
						</th>
					</tr>
					<tr>
						<th style="width:10%;"><b>FECHA</b></th>
						<th style="width:40%;"><b>DESCRIPCION</b></th>
						<th style="width:10%;"><b>CANTIDAD</b></th>
						<th style="width:10%;"><b>P.UNITARIO</b></th>
						<th style="width:10%;"><b>CARGO</b></th>
						<th style="width:10%;"><b>ABONO</b></th>
						<th style="width:10%;"><b>SALDO</b></th>
					</tr>
					<tr>
						<td colspan="6" align="right"><b>SALDO ANTERIOR</b></td>
						<td align="right">{{dato.anterior}}</td>
					</tr>
					<tr v-for="c in dato.movimientos">
						<td style="width:10%;">{{c.fecha}}</td>
						<td style="width:40%;">{{c.referencia}}</td>
						<td style="width:10%;">{{c.cantidad}}</td>
						<td style="width:10%;">{{c.preciounitario}}</td>
						<td style="width:10%;" align="right">{{c.cargo}} </td>
						<td style="width:10%;" align="right">{{c.abono}} </td>
						<td style="width:10%;" align="right">{{c.saldo}} </td>
					</tr>
					<tr>
						<td colspan="4" align="right"><b>TOTALES</b></td>
						<td align="right"><b>{{dato.cargo}}</b></td>
						<td align="right"><b>{{dato.abono}}</b></td>
						<td align="right"><b>{{dato.saldo}}</b></td>
					</tr>
				</table>
			</div>
		</div>

		<div v-if="this.campos.saldos==1">
			<div v-for="dato in saldos">
				<table class="table table-bordered">
					<tr style="background:#f2f2f2">
						<th colspan="8">
							<b v-if="campos.tipo==1">CLIENTE:</b> 
							<b v-if="campos.tipo!=1">PROVEEDOR:</b> {{dato.razonsocial}} </b> |
							<b>DIRECCION:</b> {{dato.direccion}}
						</th>
					</tr>

					<tr>
						<th style="width:10%;">COMPROBANTE</th>
						<th style="width:15%;">FECHA CREDITO</th>
						<th style="width:15%;">FECHA VENCE</th>
						<th style="width:20%;">ESTADO</th>
						<th style="width:10%;" align="right">IMPORTE</th>
						<th style="width:10%;" align="right">INTERES</th>
						<th style="width:10%;" align="right">TOTAL</th>
						<th style="width:10%;" align="right">SALDO</th>
					</tr>
					<tr v-for="c in dato.creditos">
						<td style="width:10%;">{{c.seriecomprobante_ref}} - {{c.nrocomprobante_ref}}</td>
						<td style="width:15%;">{{c.fechacredito}}</td>
						<td style="width:15%;">{{c.fechavencimiento}}</td>
						<td style="width:20%;">{{c.estado}}</td>
						<td style="width:10%;" align="right">{{c.importe}} </td>
						<td style="width:10%;" align="right">{{c.interes}} </td>
						<td style="width:10%;" align="right">{{c.total}} </td>
						<td style="width:10%;" align="right">{{c.saldo}} </td>
					</tr>
					<tr>
						<td colspan="4" align="right"><b>TOTALES</b></td>
						<td align="right"><b>{{dato.importe}}</b></td>
						<td align="right"><b>{{dato.interes}}</b></td>
						<td align="right"><b>{{dato.total}}</b></td>
						<td align="right"><b>{{dato.saldo}}</b></td>
					</tr>
				</table>
			</div>
		</div>
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
	var campos = {"codpersona":0,"fecha_desde":"","fecha_hasta":"","fecha_saldos":"","tipo_consulta":1,"tipo":1,"mostrar":1,"saldos":0};
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>

<script src="<?php echo base_url();?>netix/netix_reportes/creditos.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
	var pantalla = jQuery(document).height(); $("#netix_creditos").css({height: pantalla - 230});
</script>