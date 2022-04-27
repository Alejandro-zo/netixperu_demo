<div id="netix_atender" style="padding: 0px 20px;">
	<input type="hidden" id="codpedido" value="<?php echo $info[0]["codpedido"];?>">

	<h6><b>NROPEDIDO:</b> 000<?php echo $info[0]["codpedido"];?></h6>
	<h6><b>CLIENTE:</b> <?php echo $info[0]["cliente"];?></h6>
	<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>
	<h6><b>FECHA PEDIDO:</b> <?php echo $info[0]["fechapedido"];?></h6> <hr>
	<h5 class="text-center"><b>DETALLE DEL PEDIDO</b></h5>

	<div class="table-responsive"> 
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>DESCRIPCION</th>
					<th width="10px">UNIDAD</th>
					<th width="10px">CANTIDAD</th>
					<th width="10px">ATENDIDO</th>
					<th width="10px">ATENDER</th>
					<th width="10px" colspan="2">AGREGAR</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="(dato,index) in atender">
					<td>{{dato.producto}} - {{dato.descripcion}}</td>
					<td>{{dato.unidad}}</td>
					<td>{{dato.cantidad}}</td>
					<td>{{dato.atendido}}</td>
					<td>
						<input type="number" step="0.1" class="netix-input number line-success" v-model.number="dato.atender" min="0" max="dato.cantidad" readonly>
					</td>
					<td v-if="dato.cantidad!=dato.atendido">
						<button class="btn btn-info btn-xs btn-block" style="margin-bottom:-1px;" v-on:click="netix_mas_menos(dato,1)">
							<i class="fa fa-plus"></i>
						</button>
					</td>
					<td v-if="dato.cantidad!=dato.atendido">
						<button class="btn btn-warning btn-xs btn-block" style="margin-bottom:-1px;" v-on:click="netix_mas_menos(dato,2)">
							<i class="fa fa-minus"></i>
						</button>
					</td>
					<td v-if="dato.cantidad==dato.atendido" colspan="2">
						<button type="button" class="btn btn-danger btn-xs btn-block" style="margin-bottom:-1px;">ATENDIDO</button>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr v-for="dato in totales">
					<td colspan="2" align="right"><b>TOTALES</b></td>
					<td><b>{{dato.cantidad}}</b></td>
					<td><b>{{dato.atendido}}</b></td>
					<td colspan="3">
						<button type="button" class="btn btn-success btn-block btn-sm" style="margin-bottom:-1px;" v-on:click="netix_atender()" v-bind:disabled="estado==1">GUARDAR ATENCION</button>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_pedidos/atender.js"></script>