<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Netix-Peru-Caja-'.date('Y-m-d').'.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="8"> 
            <b><?php echo utf8_decode($titulo);?></b>
        </th>
    </tr>

    <?php 
        if ($reporte==1) {
            $item = 0; $ingresos = 0; $egresos = 0;

            if ($ingresos[0]["importe"]>=$egresos[0]["importe"]) {
                $resultado_i = round($ingresos[0]["importe"] - $egresos[0]["importe"],2); $resultado_e = "";
                $ingresos = $ingresos + $resultado_i;
            }else{
                $resultado_e = round($egresos[0]["importe"] - $ingresos[0]["importe"],2); $resultado_i = "";
                $egresos = $egresos + $resultado_e;
            } ?>

            <tr>
                <th>N°</th>
                <th>N° RECIBO</th>
                <th>CONCEPTO</th>
                <th>DOC.REFEREN</th>
                <th>RAZON SOCIAL</th>
                <th>REFERENCIA</th>
                <th>INGRESOS</th>
                <th>EGRESOS</th>
            </tr>

            <tr>
                <th colspan="6"> <b>SALDO ANTERIOR</b> </th>
                <th><b><?php echo $resultado_i;?></b></th>
                <th><b><?php echo $resultado_e;?></b></th>
            </tr>
            <?php 
                foreach ($lista as $key => $value) { $item = $item + 1; ?>
                    <tr>
                        <td><?php echo "0".$item;?></td>
                        <td><?php echo $value["seriecomprobante"]."-".$value["nrocomprobante"];?></td>
                        <td><?php echo utf8_decode($value["concepto"]);?></td>
                        <td><?php echo $value["seriecomprobante_ref"]."-".$value["nrocomprobante_ref"];?></td>
                        <td><?php echo utf8_decode($value["razonsocial"]);?></td>
                        <td><?php echo utf8_decode($value["referencia"]);?></td>
                        <?php 
                            if ($value["tipomovimiento"]==1) { $ingresos = $ingresos + $value["importe_r"]; ?>
                                <td><?php echo number_format($value["importe_r"],2);?></td>
                                <td> </td>
                            <?php }else{ $egresos = $egresos + $value["importe_r"]; ?>
                                <td> </td>
                                <td><?php echo number_format($value["importe_r"],2);?></td>
                            <?php }
                        ?>
                    </tr>
                <?php }
            ?>
            <tr>
                <th colspan="6">TOTALES</th>
                <th><?php echo number_format($ingresos,2);?></th>
                <th><?php echo number_format($egresos,2);?></th>
            </tr>
            <tr>
                <th colspan="5"></th>
                <th colspan="3">SALDO (INGRESOS - EGRESOS): <?php echo number_format($ingresos - $egresos,2);?></th>
            </tr>
        <?php }else{ 
            $ingresos = 0; $egresos = 0; ?>

            <tr>
                <th>FECHA</th>
                <th>N° RECIBO</th>
                <th>CONCEPTO</th>
                <th>DOC.REFEREN</th>
                <th>RAZON SOCIAL</th>
                <th>REFERENCIA</th>
                <th>INGRESOS</th>
                <th>EGRESOS</th>
            </tr>

            <?php 
                foreach ($lista as $key => $value) { ?>
                    <tr>
                        <td><?php echo $value["fechamovimiento"];?></td>
                        <td><?php echo $value["seriecomprobante"]."-".$value["nrocomprobante"];?></td>
                        <td><?php echo utf8_decode($value["concepto"]);?></td>
                        <td><?php echo $value["seriecomprobante_ref"]."-".$value["nrocomprobante_ref"];?></td>
                        <td><?php echo utf8_decode($value["razonsocial"]);?></td>
                        <td><?php echo utf8_decode($value["referencia"]);?></td>
                        <?php 
                            if ($value["tipomovimiento"]==1) { $ingresos = $ingresos + $value["importe_r"]; ?>
                                <td><?php echo number_format($value["importe_r"],2);?></td>
                                <td> </td>
                            <?php }else{ $egresos = $egresos + $value["importe_r"]; ?>
                                <td> </td>
                                <td><?php echo number_format($value["importe_r"],2);?></td>
                            <?php }
                        ?>
                    </tr>
                <?php }
            ?>

            <tr>
                <th colspan="6">TOTALES</th>
                <th><?php echo number_format($ingresos,2);?></th>
                <th><?php echo number_format($egresos,2);?></th>
            </tr>
            <tr>
                <th colspan="5"></th>
                <th colspan="3">SALDO (INGRESOS - EGRESOS): <?php echo number_format($ingresos - $egresos,2);?></th>
            </tr>
        <?php }
    ?>
</table>