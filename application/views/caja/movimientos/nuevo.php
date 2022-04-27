<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">

	    <div class="row form-group">
	    	<div class="col-md-4 col-xs-12">
		    	<label>TIPO MOVIMIENTO</label>
		    	<select class="form-control" name="tipomovimiento" id="tipomovimiento" v-model="campos.tipomovimiento" required v-on:change="netix_tipomovimiento()">
		    		<option value="1">INGRESO</option>
		    		<option value="2">EGRESO</option>
		    	</select>
		    </div>
		    <div class="col-md-8 col-xs-12">
		    	<label>COMPROBANTE</label>
	        	<select class="form-control" name="codcomprobantetipo" id="codcomprobantetipo" v-model="campos.codcomprobantetipo" required v-on:change="netix_tipopagos()">
		    		<option value="">SELECCIONE</option>
		    		<option v-for="dato in comprobantes" v-bind:value="dato.codcomprobantetipo"> {{dato.descripcion}} </option>
		    	</select>
		    	<input type="hidden" name="seriecomprobante" v-model="campos.seriecomprobante">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>CONCEPTO CAJA</label>
		        <select class="form-control" name="codconcepto" v-model="campos.codconcepto" v-on:change="netix_conceptos()" required>
		    		<option value="">SELECCIONE</option>
		    		<option v-for="dato in conceptos" v-bind:value="dato.codconcepto"> {{dato.descripcion}} </option>
		    	</select>
		    </div>
	    </div>
	    <div class="row form-group" v-if="transferencia==1">
	    	<div class="col-xs-12">
		        <label>CAJA DESTINO DE LA TRANSFERENCIA</label>
		        <select class="form-control" name="codcaja_ref" v-model="campos.codcaja_ref">
		    		<option value="">SELECCIONE CAJA</option>
		    		<?php 
		    			foreach ($cajas as $key => $value) { ?>
		    				<option value="<?php echo $value["codcaja"];?>"> <?php echo "SUCURSAL:".$value["sucursal"]." CAJA:".$value["descripcion"];?> </option>
		    			<?php }
		    		?>
		    	</select>
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
				<label>SOCIO DEL MOVIMIENTO</label>
	        	<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true"> </select>
			</div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		    	<label>TIPO PAGO</label>
		    	<select class="form-control" name="codtipopago" v-model="campos.codtipopago" required v-on:change="netix_cajabanco()">
		    		<option value="">SELECCIONE</option>
		    		<option v-for="dato in tipopagos" v-bind:value="dato.codtipopago"> {{dato.descripcion}} </option>
		    	</select>
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>IMPORTE</label>
	        	<input type="number" step="0.01" class="form-control" name="importe" id="importe" v-model="campos.importe" placeholder="S/. 0.00" style="border:2px solid #d43f3a;" required>
		    </div>
	    </div>
	    <div class="row form-group" v-show="movimientobanco==1">
	    	<div class="col-md-6 col-xs-12">
		    	<label>FECHA DOC. BANCO</label>
		    	<input type="hidden" id="fechadocbanco_ref" value="<?php echo date('Y-m-d');?>">
		    	<input type="text" class="form-control datepicker" name="fechadocbanco" id="fechadocbanco" v-model="campos.fechadocbanco" autocomplete="off" required v-on:blur="netix_fechamovimiento()">
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>NRO DOCUMENTO BANCO</label>
	        	<input type="text" class="form-control" name="nrodocbanco" id="nrodocbanco" v-model="campos.nrodocbanco" placeholder="Nro documento banco" autocomplete="off">
		    </div>
	    </div>

	   	<div class="row form-group">
	    	<div class="col-xs-12">
		    	<label>COMPROBANTE REFERENCIA</label>
		    	<select class="form-control" name="codcomprobantetipo_ref" v-model="campos.codcomprobantetipo_ref">
		    		<option value="0">SIN COMPROBANTE DE REFERENCIA</option>
		    		<?php 
		    			foreach ($tipocomprobantes as $key => $value) { ?>
		    				<option value="<?php echo $value["codcomprobantetipo"];?>"><?php echo $value["descripcion"];?></option>	
		    			<?php }
		    		?>
		    	</select>
		    </div>
	    </div>
	    <div class="row form-group">
		    <div class="col-md-4 col-xs-12">
		    	<label>SERIE REF.</label>
	        	<input type="text" class="form-control" name="seriecomprobante_ref" v-model="campos.seriecomprobante_ref" maxlength="4">
		    </div>
		    <div class="col-md-8 col-xs-12">
		    	<label>N° DOC. REFERENCIA</label>
	        	<input type="text" class="form-control" name="nrocomprobante_ref" v-model="campos.nrocomprobante_ref" maxlength="10">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>DESCRIPCION DEL MOVIMIENTO</label>
		        <input type="text" class="form-control" name="referencia" v-model="campos.referencia" placeholder="Descripcion del movimiento ..." maxlength="200">
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
	var campos = {codregistro:"",tipomovimiento: "1",codcomprobantetipo:"",seriecomprobante:"",codconcepto: "",codpersona: "",codcomprobantetipo_ref:"0",seriecomprobante_ref: "",nrocomprobante_ref: "",codtipopago: "",importe:"",fechadocbanco:"",nrodocbanco:"",referencia:"",codcaja_ref:""};
</script>
<script src="<?php echo base_url();?>netix/netix_caja/movi_nuevo.js"></script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>