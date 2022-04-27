<div id="netix_form">
	<div style="padding: 0px 20px;">
		<h6><b>GUIA:</b> 000<?php echo $info[0]["codguia"];?> | <b>FECHA EMSIÓN:</b> <?php echo $info[0]["fechaemision"];?></h6>
		<h6><b>DNI / RUC:</b> <?php echo $info[0]["documento"];?> | <b>DESTINATARIO CLIENTE:</b> <?php echo $info[0]["destinatario"];?></h6>
		<h6><b>COMPROBANTE:</b> <?php echo $info[0]["seriecomprobante"]." - ".$info[0]["nrocomprobante"];?></h6>
		<h6><b>CONCEPTO:</b> <?php echo $info[0]["modalidadtraslado"];?> | <b>FECHA TRASLADO:</b> <?php echo $info[0]["fechatraslado"];?></h6> <hr>

		<h6><b>PUNTO PARTIDA:</b> <?php echo $info[0]["punto_partida"];?></h6>
		<h6><b>UBIGEO PARTIDA:</b> <?php echo $info[0]["ubi_partida"];?></h6>
		<h6><b>PUNTO LLEGADA:</b> <?php echo $info[0]["punto_llegada"];?></h6>
		<h6><b>UBIGEO LLEGADA:</b> <?php echo $info[0]["ubi_llegada"];?></h6> <hr>

		<h6><b>EMPRESA TRASPORTISTA:</b> <?php echo $info[0]["transportista"];?></h6>
		<h6><b>DNI:</b> <?php echo $info[0]["dniconductor"];?> | <b>CONDUCTOR:</b> <?php echo $info[0]["conductor"];?></h6>
		<h6><b>LICENCIA:</b> <?php echo $info[0]["licencia"];?> | <b>NRO PLACA:</b> <?php echo $info[0]["nroplaca"];?></h6>

		<h5 class="text-center"> <b>DETALLE DE LA GUIA</b> </h5>
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="5px">ID</th>
						<th>PRODUCTO</th>
						<th>UNIDAD</th>
						<th>CANT.</th>
						<th>PRECIO</th>
						<th>IGV</th>
						<th>VALORGUIA</th>
						<th>SUBTOTAL</th>
						<th>PESO KG</th>
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
								<td><?php echo number_format($value["preciounitario"],2);?></td>
								<td><?php echo number_format($value["igv"],2);?></td>
								<td><?php echo number_format($value["valorguia"],2);?></td>
								<td><?php echo number_format($value["subtotal"],2);?></td>
								<td><?php echo number_format($value["pesokg"],2);?></td>
							</tr>
						<?php }
					?>
				</thead>
			</table>
		</div>
		
		<h4 align="center">
			<span class="label label-success">VALOR GUIA: S/. <?php echo number_format(round($info[0]["valorguia"],2) ,2);?> </span> &nbsp;
			<span class="label label-warning">I.G.V: S/. <?php echo number_format(round($info[0]["igv"],2) ,2);?> </span> &nbsp; 
			<span class="label label-info">PESO TOTAL: S/. <?php echo number_format(round($info[0]["pesototal"],2) ,2);?> </span>
		</h4> <br>
		<div class="alert alert-success" align="center" style="padding:5px;">
			<strong style="font-size:25px">TOTAL GUIA: S/. <?php echo number_format(round($info[0]["importe"],2) ,2);?></strong>
		</div>
	</div>
</div>