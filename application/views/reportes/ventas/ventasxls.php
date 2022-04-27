<?php
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="ReporteVentas' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');
?>

<table border="1">
    <tr>
        <th colspan="13"> 
            <b>REPORTE DE VENTAS <?php echo utf8_decode($_SESSION["netix_empresa"]);?></b>
        </th>
    </tr>
    <tr>
        <th colspan="13">
            <b style="font-size:9px">DEL <?php echo $fechadesde;?> AL <?php echo $fechahasta;?></b>
        </th>
    </tr>
    <tr>
        <th colspan="3">RUC: <?php echo $empresa[0]["documento"];?></th>
        <th colspan="7"></th>
        <th colspan="3"> MONEDA: SOLES </th>
    </tr>
    <?php 
        $valorventa_general = 0; $igv_general = 0; $icbper_general = 0; $total_general = 0;
        foreach ($sucursales as $key => $value) { ?>
            <tr>
                <th colspan="12"> <b>SUCURSAL: <?php echo utf8_decode($value["descripcion"]);?></b> </th>
            </tr>

            <tr>
                <td rowspan="2">FECHA</td>
                <td rowspan="2">T.DOC</td>
                <td rowspan="2"><?php echo utf8_decode("N°DOC")?></td>
                <td rowspan="2">DOC. IDEN</td>
                <td rowspan="2">RAZON SOCIAL</td>
                <td rowspan="2">VALOR VENTA</td>
                <td rowspan="2">IGV</td>
                <td rowspan="2">ICBPER</td>
                <td rowspan="2">TOTAL</td>
                <td rowspan="2">IMPORTE N.C</td>
                <td colspan="3">DOCUMENTO ORIGINAL QUE SE MODIFICA</td>
            </tr>
            <tr>
                <td>FECHA</td>
                <td>T.DOC</td>
                <td><?php echo utf8_decode("N°DOC")?></td>
            </tr>

            <?php 
                $valorventa = 0; $igv = 0; $icbper = 0; $total = 0;
                foreach ($value["lista"] as $val) { 
                    $color = "";
                    if ((int)$val["estado"]==0) {
                        $color = "color:red !important";
                    } ?>
                    
                    <tr style="<?php echo $color;?>">
                        <td><?php echo $val["fechacomprobante"];?></td>
                        <td><?php echo $val["tipo"];?></td>
                        <td><?php echo $val["seriecomprobante"].'-'.$val["nrocomprobante"];?></td>
                        <?php 
                            if ($val["coddocumentotipo"]==1) {
                                echo '<td> </td>';
                            }else{
                                echo '<td>'.$val["documento"].'</td>';
                            }

                            if ((int)$val["estado"]==0) {
                                echo '<td>ANULADO</td>';

                                echo '<td>'.number_format(0.00,2).'</td>';
                                echo '<td>'.number_format(0.00,2).'</td>';
                                echo '<td>'.number_format(0.00,2).'</td>';
                                echo '<td>'.number_format(0.00,2).'</td>';
                            }else{
                                $valorventa = $valorventa + $val["valorventa"]; 
                                $igv = $igv + $val["igv"]; 
                                $icbper = $icbper + $val["icbper"]; 
                                $total = $total + $val["importe"];

                                echo '<td>'.$val["razonsocial"].'</td>';

                                echo '<td>'.number_format($val["valorventa"],2).'</td>';
                                echo '<td>'.number_format($val["igv"],2).'</td>';
                                echo '<td>'.number_format($val["icbper"],2).'</td>';
                                echo '<td>'.number_format($val["importe"],2).'</td>';
                                echo '<td>'.number_format(0.00,2).'</td>';
                            }
                        ?>

                        <td> </td>
                        <td> </td>
                        <td> </td>
                    </tr>
                <?php }
            ?>

            <tr>
                <td style="color:#d9534f;text-align:right" colspan="5"> <b><?php echo utf8_decode($empresa[0]["direccion"]);?></b> </td>
                <td style="color:#d9534f"><?php echo number_format($valorventa,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($igv,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($icbper,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($total,2)?></td>
                <td style="color:#d9534f"><?php echo number_format(0.00,2)?></td>
                <td colspan="3"> </td>
            </tr>
            <tr>
                <td style="color:#d9534f;text-align:right" colspan="5"> <b>TOTAL GENERAL S/:</b> </td>
                <td style="color:#d9534f"><?php echo number_format($valorventa,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($igv,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($icbper,2)?></td>
                <td style="color:#d9534f"><?php echo number_format($total,2)?></td>
                <td style="color:#d9534f"><?php echo number_format(0.00,2)?></td>
                <td colspan="3"> </td>
            </tr>
            <tr>
                <td style="color:#d9534f;text-align:right" colspan="5"> <b>TOTAL NETO SUCURSAL S/:</td>
                <td style="color:#d9534f"><?php echo number_format($total,2)?></td>
                <td style="color:#d9534f"><?php echo number_format(0.00,2)?></td>
                <td colspan="5"> </td>
            </tr>
        <?php 
            $valorventa_general = $valorventa_general + $valorventa; 
            $igv_general = $igv_general + $igv; 
            $icbper_general = $icbper_general + $icbper; 
            $total_general = $total_general + $total;
        }
    ?>
    <tr>
        <td style="color:#d9534f;text-align:right" colspan="5"> <b>TOTAL GENERAL S/:</b> </td>
        <td style="color:#d9534f"><?php echo number_format($valorventa_general,2)?></td>
        <td style="color:#d9534f"><?php echo number_format($igv_general,2)?></td>
        <td style="color:#d9534f"><?php echo number_format($icbper_general,2)?></td>
        <td style="color:#d9534f"><?php echo number_format($total_general,2)?></td>
        <td style="color:#d9534f"><?php echo number_format(0.00,2)?></td>
        <td colspan="3"> </td>
    </tr>
    <tr>
        <td style="color:#d9534f;text-align:right" colspan="5"> <b>TOTAL NETO GENERAL S/:</td>
        <td style="color:#d9534f"><?php echo number_format($total_general,2)?></td>
        <td style="color:#d9534f"><?php echo number_format(0.00,2)?></td>
        <td colspan="5"> </td>
    </tr>
</table>