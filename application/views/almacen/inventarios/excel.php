<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Inventario_'.date('Y-m-d').'.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="8"> 
            <b>REPORTE DE INVENTARIO</b>
        </th>
    </tr>

    <tr>
    	<th>N°</th>
    	<th>CODIGO</th>
    	<th>PRODUCTO</th>
    	<th>UNIDAD</th>
        <th>MARCA</th>
    	<th>CANTIDAD</th>
    	<th>P.COSTO</th>
    	<th>P.VENTA</th>
    	<th>IMPORTE</th>
    </tr>
    <?php 
    	$item = 0; $total = 0;$cantidad = 0;
    	foreach ($lista as $key => $value) {
    		$item = $item + 1; $cantidad = $cantidad + $value["cantidad"]; $total = $total + $value["importe"]; ?>

    		<tr>
    			<td>0<?php echo $item;?></td>
    			<td><?php echo $value["codigo"];?></td>
    			<td><?php echo utf8_decode($value["descripcion"]);?></td>
    			<td><?php echo $value["unidad"];?></td>
                <td><?php echo $value["marca"];?></td>
    			<td><?php echo number_format($value["cantidad"],2);?></td>
    			<td><?php echo number_format($value["preciocosto"],2);?></td>
    			<td><?php echo number_format($value["precioventa"],2);?></td>
    			<td><?php echo number_format($value["importe"],2);?></td>
    		</tr>
    	<?php }
    ?>
    <tr>
    	<th colspan="5">TOTALES</th>
    	<th colspan="3"><?php echo number_format($cantidad,2);?></th>
    	<th><?php echo number_format($total,2);?></th>
    </tr>
</table>
