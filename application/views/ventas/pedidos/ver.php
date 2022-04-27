<div id="netix_form">
	<div style="padding: 0px 20px;">
		<h6><b>NROPEDIDO:</b> 000<?php echo $info[0]["codpedido"];?></h6>
		<h6><b>CLIENTE:</b> <?php echo $info[0]["cliente"];?></h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>
		<h6><b>FECHA PEDIDO:</b> <?php echo $info[0]["fechapedido"];?></h6>

		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>DESCRIPCION</th>
						<th width="10px">PRECIO</th>
						<th width="10px">CANTIDAD</th>
						<th width="10px">ATENDIDO</th>
						<th width="10px">PENDIENTE</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($detalle as $key => $value) { ?>
							<tr>
								<td><?php echo $value["producto"]." - ".$value["descripcion"];?></td>
								<td><?php echo number_format($value["preciounitario"],2);?></td>
								<td><?php echo round($value["cantidad"],2);?></td>
								<td><?php echo round($value["atendido"],2);?></td>
								<td><?php echo round($value["falta"],2);?></td>
							</tr>
						<?php }
					?>
				</tbody>
				<tfoot>
					<?php 
						foreach ($totales as $key => $value) { ?>
							<tr>
								<td colspan="2" align="right"><b>TOTALES</b></td>
								<td><?php echo round($value["cantidad"],2);?></td>
								<td><?php echo round($value["atendido"],2);?></td>
								<td><?php echo round($value["cantidad"] - $value["atendido"],2);?></td>
							</tr>
						<?php }
					?>
				</tfoot>
			</table>
		</div>

		<h5 class="text-center"> <b>DETALLE DE LAS ATENCIONES DEL PEDIDO</b> </h5>
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>DESCRIPCION</th>
						<th width="10px">UNIDAD</th>
						<th width="10px">CANTIDAD</th>
						<th width="140px">FECHA Y HORA</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						foreach ($atendidos as $key => $value) { ?>
							<tr>
								<td><?php echo $value["producto"];?></td>
								<td><?php echo $value["unidad"];?></td>
								<td><?php echo round($value["cantidad"],2);?></td>
								<td style="color:#d43f3a"><?php echo $value["fecha"]." ".$value["hora"];?></td>
							</tr>
						<?php }
					?>
				</tbody>
			</table>
		</div>
		
		<div class="alert alert-success" align="center" style="padding:5px;">
			<strong style="font-size:20px">TOTAL PEDIDO: S/. <?php echo number_format(round($info[0]["importe"],2) ,2);?></strong>
		</div>
	</div>
</div>