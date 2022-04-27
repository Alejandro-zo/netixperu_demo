<div id="netix_form">
	<div style="padding: 0px 20px;">
		<h6><b>CODIGO NOTA CREDITO:</b> 000<?php echo $info[0]["codkardex"];?></h6>
		<h6><b>FECHA NOTA:</b> <?php echo $info[0]["fechacomprobante"];?></h6>
		<h6><b>PROVEEDOR RAZON SOCIAL:</b> <?php echo $info[0]["cliente"];?></h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>
		<h6><b>COMPROBANTE:</b> <?php echo $info[0]["seriecomprobante"]."-".$info[0]["nrocomprobante"];?></h6>
		<h6><b>COMPROBANTE REFERENCIA:</b> <?php echo $info[0]["seriecomprobante_ref"]."-".$info[0]["nrocomprobante_ref"];?></h6>

		<h5 class="text-center"> <b>DETALLE DE LA NOTA DE CREDITO</b> </h5>
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
			<strong style="font-size:25px">TOTAL NOTA CREDITO: S/. <?php echo round($info[0]["importe"],2);?></strong>
		</div>
	</div>
</div>