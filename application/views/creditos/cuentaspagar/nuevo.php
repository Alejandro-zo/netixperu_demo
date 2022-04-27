<div id="netix_nuevocredito">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12"> <h5>REGISTRO NUEVO CREDITO POR PAGAR</h5> </div>
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<div class="col-md-6 col-xs-12">
	        <div class="x_panel">
	        	<h5 align="center" style="border-bottom:2px solid #f3f3f3;padding-bottom:10px;">
	        		<b>DATOS DEL CREDITO A REALIZAR</b>
	        	</h5>
	        	<div class="row form-group">
			    	<div class="col-md-9 col-xs-12">
				    	<label>SOCIO DEL CREDITO</label>
				    	<select class="form-control" name="codpersona" v-model="campos.codpersona" required>
				    		<option value="<?php echo $persona[0]['codpersona'];?>"><?php echo $persona[0]['razonsocial'];?></option>
				    	</select>
				    </div>
				    <div class="col-md-3 col-xs-12">
				    	<label>AFECTA CAJA</label>
				    	<input type="checkbox" style="height:18px;width:18px;" v-model="campos.afectacaja">
				    </div>
			    </div>
			    <div class="row form-group">
				    <div class="col-md-4 col-xs-12">
				    	<label>FECHA</label>
				    	<input type="hidden" id="fecha" value="<?php echo $_SESSION["netix_fechaproceso"];?>">
			        	<input type="text" class="form-control datepicker" id="fechacredito" v-model="campos.fechacredito" autocomplete="off" required v-on:blur="netix_fecha()">
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<label>FECHA INICIO</label>
			        	<input type="text" class="form-control datepicker" id="fechainicio" v-model="campos.fechainicio" autocomplete="off" required v-on:blur="netix_fecha()">
				    </div>
				    <div class="col-md-2 col-xs-12">
				    	<label>DIAS</label>
			        	<input type="number" class="form-control number" name="nrodias" v-model="campos.nrodias" min="1" autocomplete="off" v-on:keyup="netix_calcular()">
				    </div>
				    <div class="col-md-2 col-xs-12">
				    	<label>CUOTAS</label>
			        	<input type="number" class="form-control number" name="nrocuotas" v-model="campos.nrocuotas" min="1" autocomplete="off" v-on:keyup="netix_calcular()">
				    </div>
			    </div>
			    <div class="row form-group">
			    	<div class="col-md-8 col-xs-12">
				    	<label>CONCEPTO CREDITO</label>
				    	<select class="form-control" name="codcreditoconcepto" v-model="campos.codcreditoconcepto" required>
				    		<option value="2">CREDITOS RECIBIDOS</option>
				    	</select>
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<label>TIPO DE PAGO</label>
				    	<select class="form-control" name="codtipopago" v-model="campos.codtipopago" required>
				    		<?php 
				    			foreach ($tipopagos as $key => $value) { ?>
				    				<option value="<?php echo $value["codtipopago"];?>"><?php echo $value["descripcion"];?></option>
				    			<?php }
				    		?>
				    	</select>
				    </div>
			    </div>

			    <div class="row form-group" v-show="campos.codtipopago!=1">
			    	<div class="col-md-4 col-xs-12">
				    	<label>FECHA DOC. BANCO</label>
				    	<input type="hidden" id="fechadocbanco_ref" value="<?php echo date('Y-m-d');?>">
				    	<input type="text" class="form-control datepicker" name="fechadocbanco" id="fechadocbanco" v-model="campos.fechadocbanco" autocomplete="off" required v-on:blur="netix_fechamovimiento()">
				    </div>
				    <div class="col-md-8 col-xs-12">
				    	<label>NRO DOCUMENTO BANCO (VOUCHER)</label>
			        	<input type="text" class="form-control" name="nrodocbanco" id="nrodocbanco" v-model="campos.nrodocbanco" placeholder="Nro documento banco" autocomplete="off">
				    </div>
			    </div>

			    <div class="row form-group">
			    	<div class="col-md-4 col-xs-12">
				    	<label>IMPORTE</label>
				    	<input type="number" class="form-control number" name="importe" step="0.01" v-model="campos.importe" min="1" required style="border: 2px solid #d43f3a;" autocomplete="off" placeholder="0.00" v-on:keyup="netix_calcular()">
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<label>TASA INTERES %</label>
				    	<input type="number" class="form-control number" name="tasainteres" step="0.01" v-model="campos.tasainteres" min="0" required autocomplete="off" placeholder="0.00" v-on:keyup="netix_calcular()">
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<label>INTERES</label>
				    	<input type="number" class="form-control number" name="interes" v-model="campos.interes" min="0" readonly>
				    </div>
			    </div>
			    <div class="row form-group">
			    	<div class="col-xs-12">
				    	<label>DESCRIPCION CREDITO</label>
				    	<input type="text" class="form-control" name="referencia" v-model="campos.referencia" maxlength="255">
				    </div>
				</div>

			    <div class="row form-group" align="center"> <br>
			    	<a class="btn btn-warning btn-block"> <b style="font-size:25px;">
			    		TOTAL CREDITO S/. {{campos.total}}</b> 
			    	</a>
				</div>
	        </div>
		</div>

		<div class="col-md-6 col-xs-12">
			<div class="x_panel">
				<div class="row form-group slimscroll-detalle" style="height:250px;">
					<div class="col-xs-12">
						<table class="table table-striped">
							<thead>
								<tr align="center" >
									<th width="15%">CUOTA</th>
									<th width="25%">FECHA VENCE</th>
									<th width="15%">IMPORTE</th>
									<th width="15%">TASA %</th>
									<th width="15%">INTERES</th>
									<th width="15%">TOTAL</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="dato in cuotas">
									<th>{{dato.nrocuota}}</th>
									<th>{{dato.fechavence}}</th>
									<th>{{dato.importe}}</th>
									<th>{{dato.tasa}}</th>
									<th>{{dato.interes}}</th>
									<th>{{dato.total}}</th>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
				<div align="center">
					<button type="button" class="btn btn-warning btn-sm"> <b>IMPORTE: {{campos.importe}}</b> </button>
					<button type="button" class="btn btn-danger btn-sm"> <b>INTERES: {{campos.interes}}</b> </button>
					<button type="button" class="btn btn-success btn-sm"> <b>TOTAL: S/. {{campos.total}}</b> </button>
				</div>

				<div class="col-md-12" align="center"> <br>
					<button type="submit" class="btn btn-success btn-lg" v-bind:disabled="estado==1"> <b>GUARDAR CREDITO</b> </button>
					<button type="button" class="btn btn-danger btn-lg" v-on:click="netix_cerrar()"> <b>CANCELAR</b> </button>
				</div>
			</div>
		</div>
	</form>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/nuevopagar.js"> </script>
<script>
	var div_altura = jQuery(document).height(); var detalle = div_altura - 260;
	$(".slimscroll-detalle").slimScroll({position:'right',size:"5px", color:'#98a6ad',wheelStep:10,height:detalle+"px"});

	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>