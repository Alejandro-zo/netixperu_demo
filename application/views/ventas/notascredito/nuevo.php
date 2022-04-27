<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12"> <h5>REGISTRO NUEVO NOTA DE CREDITO</h5> </div>
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="formato" value="<?php echo $_SESSION['netix_formato'];?>">
		
		<div class="netix_body_row">
        	<div class="row form-group">
		    	<div class="col-md-3">
			    	<label>MOTIVO DE LA NOTA</label>
			    	<select class="form-control" name="codmotivonota" v-model="campos.codmotivonota" v-on:change="netix_motivos()" required>
			    		<?php
			    			foreach ($motivos as $key => $value) { ?>
			    				<option value="<?php echo $value["codmotivonota"];?>">
			    					<?php echo $value["descripcion"];?>
			    				</option>
			    			<?php }
			    		?>
			    	</select>
			    </div>
		    	<div class="col-md-4">
			    	<label>CLIENTE DE LA VENTA</label>
	    			<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true" v-on:change="netix_comprobantes()">
	    				<option value="2">CLIENTES VARIOS</option>
	    			</select>
			    </div>
			    <div class="col-md-5">
			    	<label>DESCRIPCION DE LA NOTA DE CREDITO</label>
			    	<input class="form-control" name="descripcion" v-model.trim="campos.descripcion" required autocomplete="off">
			    </div>
		    </div>

		    <div class="row form-group">
	    		<div class="col-md-6">
	    			<div class="col-md-6" style="padding-left: 0px;">
				    	<label class="text-center">COMPROBANTE DE REFERENCIA</label>
				    	<select class="form-control" name="codcomprobantetipo_ref" v-model="campos.codcomprobantetipo_ref" v-on:change="netix_series()">
				    		<option value="0">SELECCIONE</option>
				    		<?php 
				    			foreach ($tipocomprobantes as $key => $value) { ?>
				    				<option value="<?php echo $value["codcomprobantetipo"];?>"><?php echo $value["descripcion"];?></option>	
				    			<?php }
				    		?>
				    	</select>
				    </div>
				    <div class="col-md-6" style="padding-right: 0px;">
				    	<label class="text-center">FECHA COMPROBANTE REF.</label>
				    	<input type="text" class="form-control datepicker" id="fechacomprobante_ref" value="<?php echo date('Y-m-d');?>" autocomplete="off">
				    </div>

			    	<div class="col-md-6" style="padding-left: 0px;">
			    		<label><br>SERIE COMPROBANTE</label>
				    	<select class="form-control" name="seriecomprobante_ref" v-model="campos.seriecomprobante_ref" v-on:change="netix_comprobantes()">
				    		<option value="">SELECCIONE</option>
				    		<option v-for="dato in series_ref" v-bind:value="dato.seriecomprobante">{{dato.seriecomprobante}}</option>
				    	</select>
			    	</div>
			    	<div class="col-md-4" style="padding-right: 0px;">
			    		<label><br>SERIE NOTA CREDITO</label>
				    	<select class="form-control" name="seriecomprobante" v-model="campos.seriecomprobante" required="true">
				    		<option value="">SELECCIONE</option>
				    		<option v-for="dato in series" v-bind:value="dato.seriecomprobante">{{dato.seriecomprobante}}</option>
				    	</select>
			    	</div>
			    	<div class="col-md-2" style="padding-right: 0px;">
			    		<label><br>&nbsp;</label>
			    		<button type="button" class="btn btn-success btn-block" v-on:click="netix_comprobantes()"><i class="fa fa-search"></i></button>
			    	</div>
			    </div>
		    	<div class="col-md-6" style="height:145px;overflow-y:auto;">
				    <table class="table table-bordered">
				    	<thead>
				    		<tr>
				    			<th>RAZON SOCIAL</th>
				    			<th width="10px">COMPROBANTE</th>
				    			<th width="80px">FECHA</th>
				    			<th width="10px">IMPORTE</th>
				    			<th width="10px">SELECCIONAR</th>
				    		</tr>
				    	</thead>
				    	<thead>
				    		<tr v-for="dato in comprobantes" style="cursor:pointer;" v-bind:id="dato.codkardex">
				    			<td>{{dato.cliente}}</td>
				    			<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
				    			<td>{{dato.fechacomprobante}}</td>
				    			<td>{{dato.importe}}</td>
				    			<td v-if="dato.codmotivonota!=0">
				    				<button type="button" class="btn btn-danger btn-block btn-xs" style="margin-bottom:-1px;">CON NOTA</button> 
				    			</td>
				    			<td v-if="dato.codmotivonota==0">
				    				<button type="button" class="btn btn-success btn-block btn-xs" style="margin-bottom:-1px;" v-on:click="netix_detalle(dato)">
										<i class="fa fa-check"></i> SELECCIONAR
									</button> 
				    			</td>
				    		</tr>
				    	</thead>
				    </table>
				</div>
		    </div>
        </div>

        <div class="netix_body_row table-responsive scroll-netix-view" style="height:calc(100vh - 450px);padding:0px; overflow:auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr align="center" >
						<th width="40%">PRODUCTO</th>
						<th width="15%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">P.&nbsp;UNITARIO</th>
						<th width="10%">I.G.V.</th>
						<th width="15%">SUBTOTAL</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td>{{dato.producto}}</td>
						<td>{{dato.unidad}}</td>
						<td>{{dato.cantidad}}</td>
						<td>{{dato.precio}}</td>
						<td>{{dato.igv}}</td>
						<td>{{dato.subtotal}}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="netix_body_row">
			<div class="row">
				<div class="col-md-6">
					<button type="button" class="btn btn-warning btn-block"> <b>TOTAL NOTA DE CREDITO S/. {{totales.importe}}</b> </button>
				</div>
				<div class="col-md-3">
					<button type="submit" class="btn btn-success btn-block" v-bind:disabled="estado==1"> <b><i class="fa fa-save"></i> GUARDAR NOTA</b> </button>
				</div>
				<div class="col-md-3">
					<button type="button" class="btn btn-danger btn-block" v-on:click="netix_cerrar()"> <b>ATRAS - CANCELAR</b> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<script src="<?php echo base_url();?>netix/netix_notas/nuevo.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>