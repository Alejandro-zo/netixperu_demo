<div id="netix_form">
	<div style="padding: 0px 20px;">
		<h6><b>VENTA:</b> 000<?php echo $info[0]["codkardex"];?> | <b>FECHA VENTA:</b> <?php echo $info[0]["fechacomprobante"];?> | <b>KARDEX:</b> <?php echo $info[0]["fechakardex"];?> </h6>
		<h6><b>CLIENTE:</b> <?php echo $info[0]["cliente"];?></h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>
		<h6><b>FECHA VENTA:</b> <?php echo $info[0]["fechacomprobante"];?></h6>

		<h5 class="text-center"> <b>DETALLE DE LA VENTA</b> </h5>
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
						<th>VALORVENTA</th>
						<th>SUBTOTAL</th>
						<th>ICBPER</th>
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
								<td><?php echo number_format($value["valorventa"],2);?></td>
								<td><?php echo number_format($value["subtotal"],2);?></td>
								<td><?php echo number_format($value["icbper"],2);?></td>
							</tr>
						<?php }
					?>
				</thead>
			</table>
		</div>
		
		<h5 class="text-center"> <b>DETALLE DE LOS PAGOS</b> </h5>
		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>TIPO</th>
						<th>ENTREGADO</th>
						<th>IMPORTE</th>
						<th>VUELTO</th>
						<th>NRO DOC</th>
					</tr>
				</thead>
				<thead>
					<?php 
						foreach ($pagos as $key => $value) { ?>
							<tr>
								<td><?php echo $value["tipopago"];?></td>
								<td><?php echo round($value["importeentregado"],2);?></td>
								<td><?php echo round($value["importe"],2);?></td>
								<td><?php echo round($value["vuelto"],2);?></td>
								<td><?php echo $value["nrodocbanco"];?></td>
							</tr>
						<?php }
					?>
				</thead>
			</table>
		</div>

		<h4 align="center">
			<span class="label label-danger">DESC.: S/. <?php echo number_format(round($info[0]["descglobal"],2) ,2);?> </span> &nbsp;
			<span class="label label-success">VALOR VENTA: S/. <?php echo number_format(round($info[0]["valorventa"],2) ,2);?> </span>
		</h4>
		<h4 align="center">
			<span class="label label-warning">I.G.V: S/. <?php echo number_format(round($info[0]["igv"],2) ,2);?> </span>
		</h4> <br>
		<div class="alert alert-success" align="center" style="padding:5px;">
			<strong style="font-size:25px">TOTAL VENTA: S/. <?php echo number_format(round($info[0]["importe"],2) ,2);?></strong>
		</div>
	</div>
</div>