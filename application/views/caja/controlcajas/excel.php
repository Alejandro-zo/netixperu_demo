<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Netix-Caja.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="6"> 
            <b><?php echo utf8_decode("REPORTE DE LA CAJA APERTURADA");?></b>
        </th>
    </tr>

    <tr>
        <th colspan="6"> 
            <b><?php echo utf8_decode("LISTA DE INGRESOS");?></b>
        </th>
    </tr>

    <tr>
		<th> <b>N RECIBO</b> </th>
		<th> <b>CONCEPTO CAJA</b> </th>
		<th> <b>DOC. REF.</b> </th>
		<th> <b>RAZON SOCIAL</b> </th>
		<th> <b>REFERENCIA</b> </th>
		<th> <b>S/. IMPORTE</b> </th>
	</tr>
	<?php 
		$ingresos_total = 0;
		foreach ($ingresos as $key => $value) { $ingresos_total = $ingresos_total + $value["importe_r"]; ?>
			<tr>
				<td> <?php echo $value["seriecomprobante"].'-'.$value["nrocomprobante"];?></td>
				<td> <?php echo utf8_decode($value["concepto"]);?></td>
				<td> <?php echo $value["seriecomprobante_ref"].'-'.$value["nrocomprobante_ref"];?></td>
				<td> <?php echo utf8_decode($value["razonsocial"]);?></td>
				<td> <?php echo utf8_decode($value["referencia"]);?></td>
				<td> S/. <?php echo $value["importe_r"];?></td>
			</tr>
		<?php }
	?>
	<tr>
        <th colspan="6"> 
            <b>TOTAL INGRESOS S/. <?php echo number_format($ingresos_total,2) ?></b>
        </th>
    </tr>

	<tr>
        <th colspan="6"> 
            <b><?php echo utf8_decode("LISTA DE EGRESOS");?></b>
        </th>
    </tr>
    <tr>
        <th colspan="6"> </th>
    </tr>

    <tr>
		<th> <b>N RECIBO</b> </th>
		<th> <b>CONCEPTO CAJA</b> </th>
		<th> <b>DOC. REF.</b> </th>
		<th> <b>RAZON SOCIAL</b> </th>
		<th> <b>REFERENCIA</b> </th>
		<th> <b>S/. IMPORTE</b> </th>
	</tr>

	<?php 
		$egresos_total = 0;
		foreach ($egresos as $key => $value) { $egresos_total = $egresos_total + $value["importe_r"]; ?>
			<tr>
				<td> <?php echo $value["seriecomprobante"].'-'.$value["nrocomprobante"];?></td>
				<td> <?php echo utf8_decode($value["concepto"]);?></td>
				<td> <?php echo $value["seriecomprobante_ref"].'-'.$value["nrocomprobante_ref"];?></td>
				<td> <?php echo utf8_decode($value["razonsocial"]);?></td>
				<td> <?php echo utf8_decode($value["referencia"]);?></td>
				<td> S/. <?php echo $value["importe_r"];?></td>
			</tr>
		<?php }
	?>
	<tr>
        <th colspan="6"> 
            <b>TOTAL INGRESOS S/. <?php echo number_format($egresos_total,2) ?></b>
        </th>
    </tr>

</table>