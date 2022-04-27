<div id="netix_operacion">
	<div style="padding: 0px 20px;">
		<h6><b>COMPRA:</b> 000<?php echo $info[0]["codkardex"];?> | <b>F.COMPRA:</b> <?php echo $info[0]["fechacomprobante"];?> | <b>KARDEX:</b> <?php echo $info[0]["fechakardex"];?> </h6>
		<h6><b>PROVEEDOR:</b> <?php echo $info[0]["razonsocial"];?></h6>
		<h6><b>NOMBRE COMERCIAL:</b> <?php echo $info[0]["nombrecomercial"];?></h6>
		<h6><b>DIRECCION:</b> <?php echo $info[0]["direccion"];?></h6>

		<?php 
			if ($info[0]["codmoneda"]!=1) {
				$simbolo = "$"; ?>
				<h6>
					<b>MONEDA:</b> DOLAR | 
					<b>TIPO CAMBIO: <?php echo $info[0]["tipocambio"];?></b>
				</h6>
			<?php }else{
				$simbolo = "S/.";
			}
		?>

		<h5 class="text-center"> <b>DETALLE DE LA COMPRA</b> </h5>
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
							<td><?php echo round($value["cantidad"],3);?></td>
							<td><?php echo round($value["preciounitario"],4);?></td>
							<td><?php echo round($value["subtotal"],2);?></td>
						</tr>
					<?php }
				?>
			</thead>
		</table>
		<button type="button" class="btn btn-success btn-block" v-on:click="netix_valorizar_precios(<?php echo $info[0]["codkardex"];?>,'<?php echo $info[0]["fechakardex"];?>')"><i class="fa fa-cog"></i> VALORIZAR PRECIOS</button>

		<table class="table table-bordered">
			<?php
				if (count($otros)>0) {
					echo '<h5 class="text-center"> <b>DETALLE OTROS GASTOS DE LA COMPRA</b> </h5>';
				}
				foreach ($otros as $key => $value) { ?>
					<tr>
						<td><?php echo $value["razonsocial"];?></td>
						<td><b><?php echo number_format($value["importe"],2);?></b></td>
					</tr>
				<?php }
			?>
		</table>

		<h5 class="text-center"> <b>DETALLE DE LOS PAGOS</b> </h5>
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

		<h4 class="text-center">
			<span class="label label-danger">DESCUENTOS: <?php echo $simbolo." ".number_format(round($info[0]["descuentos"],2) ,2);?> </span> &nbsp;
			<span class="label label-success">VALOR COMPRA: <?php echo $simbolo." ".number_format(round($info[0]["valorventa"],2) ,2);?> </span>
		</h4>
		<h4 class="text-center">
			<span class="label label-warning">I.G.V: <?php echo $simbolo." ".number_format(round($info[0]["igv"],2) ,2);?> </span> &nbsp;
			<span class="label label-warning">ICBPER: <?php echo $simbolo." ".number_format(round($info[0]["icbper"],2) ,2);?> </span>
		</h4>
		<h4 class="text-center">
			<span class="label label-info">FLETE: <?php echo $simbolo." ".number_format(round($info[0]["flete"],2) ,2);?> </span> &nbsp;
			<span class="label label-primary">GASTOS: <?php echo $simbolo." ".number_format(round($info[0]["gastos"],2) ,2);?> </span>
		</h4> <br>
		<div class="alert alert-success" align="center" style="padding:5px;">
			<strong style="font-size:25px">TOTAL COMPRA: <?php echo $simbolo." ".number_format(round($info[0]["importe"],2) ,2);?></strong>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_compras/ver.js"> </script>