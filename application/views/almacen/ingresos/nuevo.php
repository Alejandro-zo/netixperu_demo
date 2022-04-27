<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12"> <h5>REGISTRO NUEVA INGRESO ALMACEN</h5> </div>
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="igvsunat" value="<?php echo $_SESSION["netix_igv"];?>">

		<div class="netix_body_row">
        	<div class="row form-group">
		    	<div class="col-md-3 col-xs-12">
			    	<label>TIPO MOVIMIENTO</label>
			    	<select class="form-control" name="codmovimientotipo" v-model="campos.codmovimientotipo" required>
			    		<option value="">SELECCIONE  . . .</option>
			    		<?php
			    			foreach ($movimientos as $key => $value) { ?>
			    				<option value="<?php echo $value["codmovimientotipo"];?>">
			    					<?php echo $value["descripcion"];?>
			    				</option>
			    			<?php }
			    		?>
			    	</select>
			    </div>
		    	<div class="col-md-4 col-xs-12">
			    	<label>ALMACEN ORIGEN</label>
			    	<input type="text" class="form-control" value="<?php echo $_SESSION['netix_almacen']?>" readonly>
			    </div>
		    	<div class="col-md-2 col-xs-12">
			    	<label>COMPROBANTE</label>
			    	<?php 
			    		if (count($serie)==0) { ?>
			    			<input type="text" class="form-control" readonly value="NO TIENE" style="border:2px solid #d43f3a"> 
			    			<span style="display:none">{{estado = 1}}</span>
			    		<?php }else{ ?>
			    			<input type="text" class="form-control" readonly value="<?php echo $serie[0]["comprobante"];?>">
			    		<?php }
			    	?>
			    </div>
			    <div class="col-md-1 col-xs-12">
			    	<label>SERIE</label>
			    	<input type="text" class="form-control" name="seriecomprobante" v-model="campos.seriecomprobante" readonly>
			    	
			    	<?php 
			    		if (count($serie)>0) { ?>
			    			<span style="display:none;">
					    		{{campos.codcomprobantetipo = '<?php echo $serie[0]["codcomprobantetipo"];?>'}}
					    		{{campos.seriecomprobante = '<?php echo $serie[0]["seriecomprobante"];?>'}}
					    	</span>
			    		<?php }
			    	?>
			    </div>
			    <div class="col-md-2 col-xs-12">
			    	<label>FECHA INGRESO</label>
			    	<input type="text" class="form-control datepicker" name="fechakardex" id="fechakardex" value="<?php echo $_SESSION["netix_fechaproceso"];?>" autocomplete="off" required>
			    </div>
			</div>
		    <div class="row form-group">
		    	<div class="col-md-3 col-xs-12">
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
			    <div class="col-md-2 col-xs-12">
			    	<label>SERIE REF.</label>
		        	<input type="text" class="form-control" name="seriecomprobante_ref" v-model="campos.seriecomprobante_ref" maxlength="4" autocomplete="off">
			    </div>
			    <div class="col-md-2 col-xs-12">
			    	<label>N° DOC. REFERENCIA</label>
		        	<input type="text" class="form-control" name="nrocomprobante_ref" v-model="campos.nrocomprobante_ref" maxlength="10" autocomplete="off">
			    </div>

		    	<div class="col-md-5 col-xs-12">
			    	<label>DESCRIPCION DEL INGRESO</label>
			    	<input class="form-control" name="descripcion" v-model="campos.descripcion" required autocomplete="off">
			    </div>
		    </div>
		</div> <br>

		<div class="netix_body_row table-responsive scroll-netix-view" style="height:calc(100vh - 414px);padding:0px; overflow:auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr align="center" >
						<th width="55%">PRODUCTO</th>
						<th width="10%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">PRECIO</th>
						<th width="10%">SUBTOTAL</th>
						<th width="5%"> <i class="fa fa-trash-o"></i> </th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td>{{dato.producto}}</td>
						<td> <input type="hidden" v-model="dato.codunidad">{{dato.unidad}} </td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato,3)" min="0.0001" required>
						</td>
						<td> 
							<input type="number" step="0.0001" class="netix-input number" v-model.number="dato.precio" v-on:keyup="netix_calcular(dato,3)" min="0" required>
						</td>
						<td> 
							<input type="number" step="0.01" class="netix-input number" v-model.number="dato.subtotal" readonly> 
						</td>
						<td> 
							<button type="button" class="btn btn-danger btn-xs" style="margin-bottom:-1px;" v-on:click="netix_deleteitem(index,dato)">
								<i class="fa fa-trash-o"></i> 
							</button> 
						</td>
					</tr>
				</tbody>
			</table>
		</div> <br>

		<div class="netix_body_row" style="padding-bottom: 0px;">
			<div class="row">
				<div class="col-md-2">
					<button type="button" class="btn btn-primary btn-lg btn-block" v-on:click="netix_item()"> <b>AGREGAR ITEM</b> </button>
				</div>
				<div class="col-md-5">
					<a class="btn btn-warning btn-block"> <b style="font-size:25px;"> TOTAL INGRESO S/. {{totales.importe}}</b> </a>
				</div>
				<div class="col-md-3">
					<button type="submit" class="btn btn-success btn-lg btn-block" v-bind:disabled="estado==1"> <b>GUARDAR INGRESO</b> </button>
				</div>
				<div class="col-md-2">
					<button type="button" class="btn btn-danger btn-lg btn-block" v-on:click="netix_cerrar()"> <b>CANCELAR</b> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<script src="<?php echo base_url();?>netix/netix_almacen/nuevoingreso.js"> </script>
<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true"); </script>