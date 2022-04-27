<div id="netix_movimiento">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" name="codregistro" v-model="campos.codregistro">
		<input type="hidden" name="codkardex" v-model="campos.codkardex">
		<input type="hidden" name="tipomovimiento" v-model="campos.tipomovimiento">
		<input type="hidden" name="codcomprobantetipo" v-model="campos.codcomprobantetipo">
		<input type="hidden" name="seriecomprobante" v-model="campos.seriecomprobante">

		<h5 class="text-center" style="font-size:18px;color:#06B8AC;letter-spacing:1px;padding-bottom:5px;border-bottom:1px solid #f2f2f2" v-if="campos.tipomovimiento==1">
			<b>REGISTRAR INGRESO A CAJA</b>
		</h5>
		<h5 class="text-center" style="font-size:18px;color:#d9534f;letter-spacing:1px;padding-bottom:5px;border-bottom:1px solid #f2f2f2" v-if="campos.tipomovimiento==2">
			<b>REGISTRAR EGRESO A CAJA</b>
		</h5>

		<div class="row form-group" v-if="campos.codkardex!=0">
	    	<div class="col-xs-12">
		        <label>SELECCIONAR SERVICIO</label>
		        <select class="form-control" name="codproducto" v-model="campos.codproducto" required>
		    		<option value="">SELECCIONE</option>
		    		<?php 
		    			foreach ($productos as $key => $value) { ?>
		    				<option value="<?php echo $value["codproducto"];?>"><?php echo $value["descripcion"];?></option>	
		    			<?php }
		    		?>
		    	</select>
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>CONCEPTO CAJA</label>
		        <select class="form-control" name="codconcepto" v-model="campos.codconcepto" required>
		    		<option value="">SELECCIONE</option>
		    		<?php 
		    			foreach ($conceptos as $key => $value) { ?>
		    				<option value="<?php echo $value["codconcepto"];?>"><?php echo $value["descripcion"];?></option>	
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
		    		<?php 
		    			foreach ($tipopagos as $key => $value) { ?>
		    				<option value="<?php echo $value["codtipopago"];?>"><?php echo $value["descripcion"];?></option>	
		    			<?php }
		    		?>
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
		    	<input type="text" class="form-control datepicker" name="fechadocbanco" id="fechadocbanco" autocomplete="off" required value="<?php echo date('Y-m-d');?>">
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
	        	<input type="text" class="form-control" name="seriecomprobante_ref" v-model="campos.seriecomprobante_ref" maxlength="4" autocomplete="off">
		    </div>
		    <div class="col-md-8 col-xs-12">
		    	<label>N° DOC. REFERENCIA</label>
	        	<input type="text" class="form-control" name="nrocomprobante_ref" v-model="campos.nrocomprobante_ref" maxlength="10" autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>DESCRIPCION DEL MOVIMIENTO</label>
		        <input type="text" class="form-control" name="referencia" v-model="campos.referencia" placeholder="Descripcion del movimiento ..." maxlength="200" required>
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
	var campos = {codkardex:"<?php echo $codkardex;?>",codproducto:"",codregistro:"",tipomovimiento: "<?php echo $tipomovimiento;?>",codcomprobantetipo:"<?php echo $comprobante_caja[0]['codcomprobantetipo'];?>",seriecomprobante:"<?php echo $series[0]['seriecomprobante'];?>",codconcepto: "",codpersona: "",codcomprobantetipo_ref:"0",seriecomprobante_ref: "",nrocomprobante_ref: "",codtipopago: "",importe:"",fechadocbanco:"",nrodocbanco:"",referencia:"",codcaja_ref:""};
</script>
<script src="<?php echo base_url();?>netix/netix_caja/movi_nuevo_1.js"></script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>