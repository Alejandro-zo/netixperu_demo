<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$info[0]["producto"].'-'.$info[0]["unidad"].'.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="13"> 
            <b><?php echo utf8_decode("KARDEX PRODUCTO | DESDE ".$info[0]["desde"]." HASTA ".$info[0]["hasta"]." | ".$info[0]["almacen"]);?></b>
        </th>
    </tr>
    <tr>
        <th colspan="13"> 
            <b><?php echo utf8_decode($info[0]["producto"]." | UNIDAD ".$info[0]["unidad"]);?></b>
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
        foreach ($existencias_a as $key => $val) { ?>
            <tr>
                <td colspan="10"><b>SALDO ANTERIOR</b></td>
                <td><b><?php echo number_format($val["existencia_cantidad"],4);?></b> </td>
                <td><b><?php echo number_format($val["existencia_precio"],4);?></b> </td>
                <td><b><?php echo number_format($val["existencia_total"],4);?></b> </td>
            </tr>
        <?php }

        foreach ($existencias as $key => $val) { ?>
            <tr>
                <td><?php echo $val["fechacomprobante"];?></td>
                <td><?php echo $val["seriecomprobante"]."-".$val["nrocomprobante"];?></td>
                <td><?php echo $val["documento"];?></td>
                <td><?php echo utf8_decode($val["razonsocial"]);?></td>
                <?php
                    if ($val["tipo"]==1) { ?>
                        <td><?php echo number_format($val["cantidad"],4);?></td>
                        <td><?php echo number_format($val["preciounitario"],4);?></td>
                        <td><?php echo number_format($val["total"],4);?></td>
                        <td></td> <td></td> <td></td>
                    <?php }else{ ?>
                        <td></td> <td></td> <td></td>
                        <td><?php echo number_format($val["cantidad"],4);?></td>
                        <td><?php echo number_format($val["preciounitario"],4);?></td>
                        <td><?php echo number_format($val["total"],4);?></td>
                    <?php }
                ?>
                <td><?php echo number_format($val["existencia_cantidad"],4);?></td>
                <td><?php echo number_format($val["existencia_precio"],4);?></td>
                <td><?php echo number_format($val["existencia_total"],4);?></td>
            </tr>
        <?php }
    ?>
</table>