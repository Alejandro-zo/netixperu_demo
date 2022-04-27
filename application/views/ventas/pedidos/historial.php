<div id="netix_historial">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-10 col-xs-12"> 
				<h5><b>HISTORIAL DE PEDIDOS DEL CLIENTE: </b> <?php echo $persona[0]["razonsocial"];?></h5>
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
					<option value="">TODOS LOS PEDIDOS</option>
					<option value="0">ANULADOS</option>
					<option value="1">PENDIENTES</option>
					<option value="2">ATENDIDOS</option>
				</select>
			</div>
			<div class="col-md-2"> 
				<select class="form-control input-sm" v-model="campos.filtro">
					<option value="1">FECHAS FILTRO (SI)</option>
					<option value="0">FECHAS FILTRO (NO)</option>
				</select>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_pedidos()">
					<i class="fa fa-search"></i> PEDIDOS
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
						<th width="10px">PEDIDO</th>
						<th>FECHA PEDIDO</th>
						<th>CLIENTE REFERENCIA</th>
						<th>DIRECCION REFERENCIA</th>
						<th>TOTAL PEDIDO</th>
						<th width="10px">VER</th>
						<th width="10px">ATENDER</th>
						<th width="10px">ESTADO</th>
						<th width="10px">ANULAR</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="dato in pedidos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
						<td>000{{dato.codpedido}}</td>
						<td>{{dato.fechapedido}}</td>
						<td>{{dato.cliente}}</td>
						<td>{{dato.direccion}}</td>
						<td>{{dato.importe}}</td>
						<td>
							<button type="button" class="btn btn-success btn-xs" v-on:click="netix_ver(dato.codpedido)" style="margin-bottom:2px;"> <i class="fa fa-eye"></i> VER</button>
						</td>
						<td>
							<button type="button" class="btn btn-info btn-xs" v-on:click="netix_atender(dato.codpedido)" v-if="dato.estado==1" style="margin-bottom:2px;"> <i class="fa fa-file-o"></i> ATENDER</button>
						</td>
						<td>
							<span class="label label-danger" v-if="dato.estado==0">ANULADO</span>
							<span class="label label-warning" v-if="dato.estado==1">PENDIENTE</span>
							<span class="label label-success" v-if="dato.estado==2">ATENDIDO</span>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-xs" v-on:click="netix_eliminar(dato.codpedido)" style="margin-bottom:2px;" v-if="dato.estado==1">
								<i class="fa fa-trash-o"></i> ANULAR
							</button>
						</td>
					</tr>
					<tr v-for="dato in totales">
						<td colspan="4" style="text-align:right;"><b style="font-size:15px;">TOTALES</b></td>
						<td><b style="font-size:15px;">{{dato.total}}</b></td> <td colspan="2"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div> <br>
</div>

<script src="<?php echo base_url();?>netix/netix_pedidos/historial.js"> </script>
<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD'}); </script>