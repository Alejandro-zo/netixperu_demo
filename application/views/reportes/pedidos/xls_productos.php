<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Pedidos-Productos-'.date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="9"> 
            <b><?php echo utf8_decode("REPORTE DE PEDIDOS PRODUCTOS");?></b>
        </th>
    </tr>

    <tr>
		<th>CODIGO</th>
		<th>PRODUCTO</th>
		<th>UNIDAD</th>
		<th>RAZON SOCIAL CLIENTE</th>
		<th>CANTIDAD</th>
		<th>PRECIO DESC.</th>
		<th>TOTAL DESC.</th>
		<th>PRECIO CATALOGO</th>
		<th>TOTAL CATALOGO</th>
	</tr>
	<?php
		$cantidad = 0; $total = 0; $totalref = 0;
		foreach ($lista as $key => $value) {
			$cantidad = $cantidad + $value["cantidad"];
			$total = $total + $value["subtotal"];
			$totalref = $totalref + $value["subtotalref"]; ?>
			<tr>
				<td><?php echo $value["codigo"];?></td>
				<td><?php echo utf8_decode($value["producto"]);?></td>
				<td><?php echo $value["unidad"];?></td>
				<td><?php echo utf8_decode($value["cliente"]);?></td>
				<td><?php echo number_format($value["cantidad"],2); ?></td>
				<td><?php echo number_format($value["preciounitario"],2); ?></td>
				<td><b><?php echo number_format($value["subtotal"],2); ?></b></td>
				<td><?php echo number_format($value["preciorefunitario"],2); ?></td>
				<td><b><?php echo number_format($value["subtotalref"],2); ?></b></td>
			</tr>
		<?php }
	?>
	<tr>
		<th colspan="4">TOTAL</th>
		<th><?php echo number_format($cantidad,2); ?></th> <th></th>
		<th><?php echo number_format($total,2); ?></th> <th></th>
		<th><?php echo number_format($totalref,2); ?></th>
	</tr>
</table>