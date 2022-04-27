<div id="netix_compras">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-4 col-xs-12">
				<input type="hidden" id="caja" value="<?php echo $caja;?>"> <input type="hidden" id="almacen" value="<?php echo $almacen;?>">
				<h5>LISTA DE COMPRAS REGISTRADAS</h5> 
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
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVA COMPRA </button>
			    <button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER </button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_egresos()"> <i class="fa fa-plus-circle"></i> GASTO </button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
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
							<th>DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th>FECHA</th>
							<th>TIPO</th>
							<th>COMPROBANTE</th>
							<th width="130px">IMPORTE</th>
							<th>PAGO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
							<td> <input type="radio" v-if="dato.estado!=0" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex)"> </td>
							<td>{{dato.documento}}</td>
							<td>{{dato.razonsocial}}</td>
							<td>{{dato.fechacomprobante}}</td>
							<td>{{dato.tipo}}</td>
							<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
							<td>
								<b v-if="dato.codmoneda==1" style="font-size:17px;">S/.</b> 
								<b v-if="dato.codmoneda!=1" style="font-size:17px;">$</b> 
								<b style="font-size:17px;">{{dato.importe}}</b>
							</td>
							<td>
								<span class="label label-danger" v-if="dato.condicionpago==1">AL CONTADO</span>
								<span class="label label-warning" v-else="dato.condicionpago==2">AL CREDITO</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_compras/index.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>