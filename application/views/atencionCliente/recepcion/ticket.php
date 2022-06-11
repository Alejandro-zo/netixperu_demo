<!DOCTYPE html>
<html>
	<!-- <link href="http://allfont.es/allfont.css?fonts=agency-fb" rel="stylesheet" type="text/css" /> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>public/css/ticket/ticket.css">
	<script language="javascript">
	    function printThis() {
	        window.print(); return false;
	    }
	</script>

	<body onLoad="printThis();">
		<?php $linea = '--------------------------------------------------------------------'; ?>
		<table  width="260" border="0" align="center">
			<tr>
	            <td colspan="3" align="center" class="Cabecera0"> <img src="<?php echo base_url();?>public/img/<?php echo $_SESSION['netix_logo'];?>" style="height:80px;"> </td>
	        </tr>
	        <tr>
	            <td colspan="3" align="center" class="Cabecera0"> <?php echo $empresa[0]['razonsocial'];?> </td>
	        </tr>
	        <tr>
	            <td colspan="3" align="center" class="Cabecera2"><?php echo utf8_decode($sucursal[0]['direccion']);?> </td>
	        </tr>
	        <tr>
	            <td colspan="3" align="center" class="Cabecera2-numeros1">RUC: <?php echo $empresa[0]['documento'];?></td>
	        </tr>
	        <tr>
	            <td colspan="3" align="center" class="Cabecera2-numeros1"><?php echo $sucursal[0]['telefonos'];?></td>
	        </tr>
	        <tr class="Linea"><td colspan="3" align="center"><?php echo $linea ?> </td></tr>
            <tr>
                <td colspan="3" align="center" class="InfoVer"><b> PROFORMA DE VENTA </b> </td>
            </tr>
	       <!-- <tr>
	            <td align="left" class="Cabecera2" colspan="2"> FECHA: <?php /*echo $venta[0]['fechacomprobante'];*/?></td>
	            <td align="right" class="Cabecera2"> <?php /*echo date("H:i:s");*/?></td>
	        </tr>-->

            <tr>
                <td align="left" class="InfoVer" colspan="3"> EMPLEADO: <?php echo $recepcion[0]['nombreempleado'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> CLIENTE: <?php echo $recepcion[0]['nombrepersona'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> PRODUCTO: <?php echo $recepcion[0]['producto'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> MARCA: <?php echo $recepcion[0]['marca'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> MODELO: <?php echo $recepcion[0]['modelo'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> DESCRIPCION: <?php echo $recepcion[0]['descripcion'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> FECHA: <?php echo $recepcion[0]['fecharecepcion'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> TIPO PAGO: <?php echo $recepcion[0]['tipopago'];?> </td>
            </tr>
            <tr>
                <td align="left" class="InfoVer" colspan="3"> IMPORTE: <?php echo $recepcion[0]['importe'];?> </td>
            </tr>


            <tr class="Linea"><td colspan="4" align="center"> <?php echo $linea ?> </td></tr>
	        <tr class="footer">
	            <td colspan="3" align="center"> <b>GRACIAS POR SU PREFERENCIA !</b> </td>
	        </tr>
	        <tr>
	            <td colspan="3">&nbsp;</td>
	        </tr>
	        <tr>
	            <td colspan="3">&nbsp;</td>
	        </tr>
	        <tr>
	            <td colspan="3">&nbsp;</td>
	        </tr>
	    </table>
	</body>
</html>