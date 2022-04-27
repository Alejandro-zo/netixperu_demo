<div id="netix_form">
	<div style="padding: 0px 20px;">
		<h6><b>NROSALIDA:</b> 000<?php echo $info[0]["codkardex"];?> | <b>FECHA:</b> <?php echo $info[0]["fechakardex"];?> </h6>
		<h6><b>PROVEEDOR:</b> <?php echo $info[0]["razonsocial"];?></h6>
		<h6><b>NOMBRE COMERCIAL:</b> <?php echo $info[0]["nombrecomercial"];?></h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>
		<?php $simbolo = "S/."; ?>

		<h5 class="text-center"> <b>DETALLE DE LA SALIDA</b> </h5>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th width="5px">ID</th>
					<th>PRODUCTO</th>
					<th>UNIDAD</th>
					<th>CANTIDAD</th>
					<th>PRECIO</th>
					<th>SUBTOTAL</th>
				</tr>
			</thead>
			<thead>
				<?php 
					foreach ($detalle as $key => $value) { ?>
						<tr>
							<td><?php echo $value["codproducto"];?></td>
							<td><?php echo $value["producto"];?></td>
							<td><?php echo $value["unidad"];?></td>
							<td><?php echo round($value["cantidad"],2);?></td>
							<td><?php echo round($value["preciounitario"],2);?></td>
							<td><?php echo round($value["subtotal"],2);?></td>
						</tr>
					<?php }
				?>
			</thead>
		</table>

		<div class="alert alert-success" align="center" style="padding:5px;">
			<strong style="font-size:25px">TOTAL SALIDA: <?php echo $simbolo." ".number_format(round($info[0]["importe"],2) ,2);?></strong>
		</div>
	</div>
</div>