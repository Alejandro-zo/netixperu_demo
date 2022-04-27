<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Netix-Peru-Productos-'.date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="8"> 
            <b><?php echo utf8_decode($titulo);?></b>
        </th>
    </tr>
    <tr>
        <th colspan="8"> 
            <b><?php echo utf8_decode($subtitulo);?></b>
        </th>
    </tr>
    <tr>
        <th>N°</th>
        <th>FECHA</th>
        <th>RAZON SOCIAL</th>
        <th>COMPROBANTE</th>
        <th>CANTIDAD</th>
        <th>P.U.</th>
        <th>TOTAL</th>
        <th>MONEDA</th>
    </tr>
    <?php $subtotal = 0; $item = 0;
        foreach ($lista as $key => $value) { $subtotal = $subtotal + $value["subtotal"]; $item = 0; ?>
            <tr>
                <td><?php echo $item;?></td>
                <td><?php echo $value["fechacomprobante"];?></td>
                <td><?php echo utf8_decode($value["razonsocial"]);?></td>
                <td><?php echo $value["seriecomprobante"]."-".$value["nrocomprobante"];?></td>
                <td><?php echo number_format($value["cantidad"],3);?></td>
                <td><?php echo number_format($value["preciounitario"],3);?></td>
                <td><?php echo number_format($value["subtotal"],3);?></td>
                <td><?php echo $value["moneda"];?></td>
            </tr>
        <?php }
    ?>
    <tr>
        <th colspan="6">TOTALES</th>
        <th colspan="2"><?php echo number_format($subtotal,3);?></th>
    </tr>
</table>