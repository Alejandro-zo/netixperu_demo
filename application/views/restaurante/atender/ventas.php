<div class="table-responsive">
	<table class="table table-bordered" style="font-size:10px;">
		<thead>
			<tr>
				<th rowspan="2">ANFITRIONA - VENDEDOR</th>
				<?php 
					foreach ($productos as $key => $value) { ?>
						<th colspan="5"><?php echo $value["producto"];?></th>
					<?php }
				?>
				<th rowspan="2">S/. DIA</th>
			</tr>
			<tr>
				<?php 
					foreach ($productos as $key => $value) { ?>
						<td>CANT.</td>
						<td>CANT-2</td>
						<td>PRECIO</td>
						<td>IMPORTE</td>
						<td>S/. DIA</td>
					<?php }
				?>
			</tr>
		</thead>
		<tbody>
			<?php $dia_total = 0;
				foreach ($vendedores as $key => $value) { $dia_total = $dia_total + $value["total"]; ?>
					<tr>
						<td><?php echo $value["razonsocial"];?></td>
						<?php
							foreach ($value["detalle"] as $v) { ?>
								<td><?php echo $v["cantidad"];?></td>
								<td><?php echo $v["primeros"];?></td>
								<td><?php echo $v["preciounitario"];?></td>
								<td><b><?php echo $v["importe"];?></b></td>
								<td><?php echo $v["dia"];?></td>
							<?php }
						?>
						<td><b><?php echo $value["total"];?></b></td>
					</tr>
				<?php }
			?>
		</tbody>
		<tfoot>
			<tr>
				<td><b>TOTALES</b></td>
				<td colspan="<?php echo count($productos) * 5;?>"></td>
				<td><b><?php echo number_format($dia_total,2);?></b></td>
			</tr>
		</tfoot>
	</table>
</div>