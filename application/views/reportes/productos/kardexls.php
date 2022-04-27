<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Netix-Peru-Productos-'.date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="13"> 
            <b><?php echo utf8_decode($titulo);?></b>
        </th>
    </tr>

    <tr>
        <th colspan="4">DATOS DE LA OPERACION</th>
        <th colspan="3">ENTRADAS</th>
        <th colspan="3">SALIDAS</th>
        <th colspan="3">EXISTENCIAS</th>
    </tr>
    <tr>
        <th>FECHA</th>
        <th>COMPROBANTE</th>
        <th>DOCUMENTO</th>
        <th>RAZON SOCIAL</th>

        <th>CANTIDAD</th>
        <th>P.U</th>
        <th>TOTAL</th>
        <th>CANTIDAD</th>
        <th>P.U</th>
        <th>TOTAL</th>
        <th>CANTIDAD</th>
        <th>PRECIO</th>
        <th>TOTAL</th>
    </tr>
    <?php
        foreach ($lista as $key => $value) {
            if (count($value["existencias"])>0) { ?>
                <tr>
                    <th colspan="13"> <b><?php echo utf8_decode($value["descripcion"]." | UNIDAD: ".$value["unidad"]);?></b> </th>
                </tr>
                <?php 
                    foreach ($value["existencias"] as $val) { ?>
                        <tr>
                            <td><?php echo $val["fechacomprobante"];?></td>
                            <td><?php echo $val["seriecomprobante"]."-".$val["nrocomprobante"];?></td>
                            <td><?php echo $val["documento"];?></td>
                            <td><?php echo utf8_decode($val["razonsocial"]);?></td>
                            <?php
                                if ($val["tipo"]==1) { ?>
                                    <td><?php echo number_format($val["cantidad"],2);?></td>
                                    <td><?php echo number_format($val["preciounitario"],2);?></td>
                                    <td><?php echo number_format($val["total"],2);?></td>
                                    <td></td> <td></td> <td></td>
                                <?php }else{ ?>
                                    <td></td> <td></td> <td></td>
                                    <td><?php echo number_format($val["cantidad"],2);?></td>
                                    <td><?php echo number_format($val["preciounitario"],2);?></td>
                                    <td><?php echo number_format($val["total"],2);?></td>
                                <?php }
                            ?>
                            <td><?php echo number_format($val["existencia_cantidad"],2);?></td>
                            <td><?php echo number_format($val["existencia_precio"],2);?></td>
                            <td><?php echo number_format($val["existencia_total"],2);?></td>
                        </tr>
                <?php }
            }
        }
    ?>
</table>