<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-xs-12">
				<label>SELECCIONAR SUCURSAL</label>
	        	<select class="form-control" id="codsucursal" v-model="campos.codsucursal" required v-on:change="netix_tipocomprobante()">
	        		<option value="">SELECCIONE</option>
	        		<?php 
	        			foreach ($sucursales as $key => $value) { ?>
	        				<option value="<?php echo $value["codsucursal"];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-xs-12">
				<label>TIPO COMPROBANTE</label>
	        	<select class="form-control" id="codcomprobantetipo" v-model="campos.codcomprobantetipo" required v-on:change="netix_tipocomprobante()">
	        		<option value="">SELECCIONE</option>
	        		<?php 
	        			foreach ($tipos as $key => $value) { ?>
	        				<option value="<?php echo $value["codcomprobantetipo"];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
		</div>
		<div class="row form-group" v-if="caja">
			<div class="col-xs-12">
				<label>SELECCIONE CAJA</label>
	        	<select class="form-control" name="codcaja" v-model="campos.codcaja" id="codcaja" required v-on:change="netix_caja()">
	        		<option value="">SELECCIONE</option>
	        	</select> <br>
	        	<div class="alert alert-danger" v-if="caja_alerta" align="center"> 
	        		<strong>YA TIENE REGISTRADO UNA SERIE PARA ESTA CAJA <br> <b style="font-size:16px;">CAMBIAR DE CAJA O TIPO COMPROBANTE</b></strong> 
	        	</div>
			</div>
		</div>
		<div class="row form-group" v-if="almacen">
			<div class="col-xs-12">
				<label>SELECCIONE ALMACEN</label>
	        	<select class="form-control" name="codalmacen" v-model="campos.codalmacen" id="codalmacen" required v-on:change="netix_almacen()">
	        		<option value="">SELECCIONE</option>
	        	</select> <br>
	        	<div class="alert alert-danger" v-if="almacen_alerta" align="center"> 
	        		<strong>YA TIENE REGISTRADO UNA SERIE PARA ESTE ALMACEN <br> <b style="font-size:16px;">CAMBIAR DE ALMACEN O TIPO DE COMPROBANTE</b></strong> 
	        	</div>
			</div>
		</div>
		<div class="row form-group" v-if="nota">
			<div class="col-xs-12">
				<label>SELECCIONE COMPROBANTE Y SERIE REFERENCIA</label>
	        	<select class="form-control" name="codcomprobantetipo_ref" v-model="campos.codcomprobantetipo_ref" id="codcomprobantetipo_ref" required v-on:change="netix_notas()">
	        		<option value="">SELECCIONE</option>
	        	</select> <br>
	        	<div class="alert alert-danger" v-if="nota_alerta" align="center"> 
	        		<strong>YA TIENE REGISTRADO UNA SERIE PARA ESTA NOTA ELECTRONICA <br> <b style="font-size:16px;">CAMBIAR DE TIPO DE COMPROBANTE O SERIE</b></strong> 
	        	</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-4 col-xs-12">
				<label>SERIE</label>
	        	<input type="text" id="seriecomprobante" v-model.trim="campos.seriecomprobante" class="form-control" required autocomplete="off" minlength="4" maxlength="4" style="text-transform:uppercase;" />
			</div>
			<div class="col-md-4 col-xs-12">
				<label>NRO INICIAL</label>
	        	<input type="number" name="nroinicial" v-model.number="campos.nroinicial" class="form-control" required autocomplete="off" placeholder="Nro inicial . . ." />
			</div>
			<div class="col-md-4 col-xs-12">
				<label>CORRELATIVO</label>
	        	<input type="number" name="nrocorrelativo" v-model.number="campos.nrocorrelativo" class="form-control" required autocomplete="off" placeholder="Nro Correlativo . . ." />
			</div>
		</div>

		<div class="row form-group text-center"> <br>
			<div class="col-md-12 col-xs-12">
				<div class="">
					<label v-if="campos.impresion==1" >
					  	CONFIGURAR IMPRESION DEL COMPROBANTE &nbsp; &nbsp; <input type="checkbox" class="js-switch" v-on:click="netix_impresion()" checked/>
					</label>
					<label v-else="campos.impresion!=1" >
					  	CONFIGURAR IMPRESION DEL COMPROBANTE &nbsp; &nbsp; <input type="checkbox" class="js-switch" v-on:click="netix_impresion()"/>
					</label>
				</div>
			</div>
		</div>
		<div class="row form-group" v-if="campos.impresion==1">
			<div class="col-md-6 col-xs-12">
				<label>FORMATO</label>
	        	<select v-model="campos.formato" class="form-control" required>
	        		<option value="a4">A4</option>
	        		<option value="a5">A5</option>
	        		<option value="ticket">TICKET</option>
	        	</select>
			</div>
			<div class="col-md-6 col-xs-12">
				<label>ORIENTACIÓN</label>
				<select v-model="campos.orientacion" class="form-control" required>
	        		<option value="h">HORIZONTAL</option>
	        		<option value="p">VERTICAL</option>
	        	</select>
			</div>
		</div>
		<div class="row form-group" v-if="campos.impresion==1">
			<div class="col-md-12 col-xs-12">
				<label>IMPRESORA</label>
	        	<input type="text" name="impresora" v-model.trim="campos.impresora" class="form-control" autocomplete="off" placeholder="Impresora descripcion . . ." maxlength="100" />
			</div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script>
	if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) { var switchery = new Switchery(html, { color: '#26B99A' }); });
    }

	var campos = {codregistro:"",codsucursal:"",codcomprobantetipo:"",codcaja:"",codalmacen:"",codcomprobantetipo_ref:"",seriecomprobante:"",seriecomprobante_editar:"",nroinicial:"",nrocorrelativo:"",impresion:0,formato:"a4",orientacion:"p",impresora:""}; 
</script>
<script src="<?php echo base_url();?>netix/netix_caja/comprobantes.js"></script>