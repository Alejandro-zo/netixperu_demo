<div id="netix_ventas">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-2 col-xs-12">
				<input type="hidden" id="caja" value="<?php echo $caja;?>"> 
				<input type="hidden" id="almacen" value="<?php echo $almacen;?>">
				<input type="hidden" id="formato" value="<?php echo $_SESSION['netix_formato'];?>">
				<h5>LISTA DE VENTAS</h5> 
			</div>
			<div class="col-md-2 col-xs-12 p-5 hidden-xs">
				<label class="netix_checkbox"> CON RANGO <input type="checkbox" v-model="fechas.filtro"> <span class="check"></span> </label>
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> DESDE</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control input-sm datepicker" id="fecha_desde" value="<?php echo $_SESSION["netix_fechaproceso"];?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>
			<div class="col-md-1"> <label class="p-5"><i class="fa fa-calendar"></i> HASTA</label> </div>
			<div class="col-md-2 col-xs-12">
				<input type="text" class="form-control input-sm datepicker" id="fecha_hasta" value="<?php echo $_SESSION["netix_fechaproceso"];?>" v-on:blur="netix_buscar()" autocomplete="off">
			</div>
			<div class="col-md-2 col-xs-12 hidden-xs">
				<select class="form-control input-sm" v-model="formato_impresion" v-on:change="netix_formato()">
					<option value="a4">A4 IMPRESION</option>
	        		<option value="a5">A5 IMPRESION</option>
	        		<option value="ticket">TICKET IMPRESION</option>
				</select>
			</div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVA VENTA</button>
		    	<button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER</button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR</button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_imprimir()"> <i class="fa fa-print"></i> IMPRIMIR</button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR</button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>

	<div class="netix_body">
		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th width="5px"><i class="fa fa-file-o"></i></th>
							<th>DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th width="80px">FECHA</th>
							<th>TIPO</th>
							<th>COMPROBANTE</th>
							<th width="120px">IMPORTE</th>
							<th>PAGO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
							<td v-if="dato.estado!=0"> 
								<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex)"> 
							</td>
							<td v-if="dato.estado==0" style="padding:15px;"></td>
							<td>{{dato.codkardex}}</td>
							<td>{{dato.documento}}</td>
							<td>{{dato.cliente}}</td>
							<td>{{dato.fechacomprobante}}</td>
							<td>{{dato.tipo}}</td>
							<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
							<td> <b style="font-size:17px;">S/. {{dato.importe}}</b> </td>
							<td>
								<span class="label label-success" v-if="dato.condicionpago==1">AL CONTADO</span>
								<span class="label label-warning" v-else="dato.condicionpago==2">AL CREDITO</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>

		    <div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="modal-dialog" style="width:100%;margin:0px;">
					<div class="modal-content" align="center" style="border-radius:0px">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
								<i class="fa fa-times-circle"></i> 
							</button>
							<h4 class="modal-title">
								<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"];?> </b>
							</h4>
						</div>
						<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
							<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>
<script src="<?php echo base_url();?>netix/netix_ventas/index.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>