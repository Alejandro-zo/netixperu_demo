<div id="netix_cobranza">
	<div class="row netix_header" style="margin-bottom:5px;">
		<div class="col-md-8"> 
			<h5 style="letter-spacing:1px;"> <b>COBRANZA DEL CREDITO</b> </h5> 
		</div>
	</div>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<div class="col-md-7 col-xs-12">
			<div class="x_panel">
				<div class="cuotas" style="height:200px;">
					<table class="table table-bordered">
						<thead>
							<tr align="center" >
								<th width="3%">N°.C</th>
								<th width="10%">COMPROBANTE</th>
								<th width="5%">CUOTA</th>
								<th width="20%">F.&nbsp;&nbsp;INICIO</th>
								<th width="20%">F.&nbsp;VENCE</th>
								<th width="12%">TOTAL</th>
								<th width="12%">COBRADO</th>
								<th width="13%">SALDO</th>
								<th width="5%"> </th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(dato,index) in cuotas">
								<td>000{{dato.codcredito}}</td>
								<td>{{dato.comprobante}}</td>
								<td>000{{dato.nrocuota}} </td>
								<td>{{dato.fecha}} </td>
								<td>{{dato.fechavence}} </td>
								<td>{{dato.total}} </td>
								<td>{{dato.cobrado}} </td>
								<td>{{dato.saldo}} </td>
								<td> 
									<input type="checkbox" class="netix_radio" v-bind:id="index" v-on:click="netix_cobrar(index,dato)"> 
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<div style="border-bottom:2px solid #f3f3f3;margin-bottom:10px;" align="center"> <b>CUOTAS A COBRAR</b> </div>

				<div class="cobros" style="height:200px;">
					<table class="table table-bordered">
						<thead>
							<tr align="center" >
								<th width="15%">CREDITO</th>
								<th width="20%">CUOTA</th>
								<th width="20%">TOTAL</th>
								<th width="20%">SALDO</th>
								<th width="20%">COBRAR</th>
								<th width="5%"> </th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(dato,index) in cuotascobrar">
								<td>000{{dato.codcredito}}</td>
								<td>000{{dato.nrocuota}} </td>
								<td> <b>S/. {{dato.total}}</b> </td>
								<td style="color:#d43f3a"> <b>S/. {{dato.saldo}}</b> </td>
								<td> 
									<input type="number" step="0.01" class="netix-input number" v-model.number="dato.cobrar" v-on:keyup="netix_calcular(dato)" min="1" v-bind:max="dato.importe" required style="border:2px solid #13a89e;">
								</td>
								<td style="padding-top:2px;">
									<button type="button" class="btn btn-danger btn-xs" v-on:click="netix_anularcuota(index,dato)">
										<i class="fa fa-trash-o"></i>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="col-md-5 col-xs-12">
	        <div class="x_panel">
	        	<a class="btn btn-success btn-block"> <b style="font-size:25px;">
		    		TOTAL COBRANZA S/. {{campos.total}}</b> 
		    	</a> <br>

		    	<div class="row form-group">
			    	<div class="col-xs-12">
				    	<label>SOCIO DEL CREDITO</label>
				    	<select class="form-control" name="codpersona" v-model="campos.codpersona" required>
				    		<option value="<?php echo $persona[0]['codpersona'];?>"><?php echo $persona[0]['razonsocial'];?></option>
				    	</select>
				    </div>
			    </div>

			    <div class="row form-group">
			    	<div class="col-md-8 col-xs-12">
				    	<label>CONCEPTO CREDITO</label>
				    	<select class="form-control" name="codconcepto" v-model="campos.codconcepto" required>
				    		<option value="19">COBRANZA DE CUOTAS</option>
				    	</select>
				    </div>
				    <div class="col-md-4 col-xs-12">
				    	<label>TIPO DE PAGO</label>
				    	<select class="form-control" name="codtipopago" v-model="campos.codtipopago" v-on:change="netix_vuelto()" required>
				    		<?php 
				    			foreach ($tipopagos as $key => $value) { ?>
				    				<option value="<?php echo $value["codtipopago"];?>"><?php echo $value["descripcion"];?></option>
				    			<?php }
				    		?>
				    	</select>
				    </div>
			    </div>
			    <div class="row form-group">
    				<div class="col-xs-12">
	    				<label>DESCRIPCION COBRANZA</label>
	    				<input type="text" class="form-control" v-model="campos.descripcion" required maxlength="250">
	    			</div>
	    		</div>

			    <div class="row form-group" v-show="campos.codtipopago==1">
    				<div class="col-xs-6">
	    				<label style="font-size:15px;">S/. RECIBIDO</label>
	    				<input type="number" step="0.01" class="form-control number netix-money-success" v-model="campos.importe" min="1" required v-on:keyup="netix_vuelto()" placeholder="S/. 0.00">
	    			</div>
	    			<div class="col-xs-6">
	    				<label style="font-size:15px;">VUELTO</label>
	    				<input type="number" class="form-control netix-money-error" v-model="campos.vuelto" readonly>
	    			</div>
			    </div>

			    <div class="row form-group" v-show="campos.codtipopago!=1">
	    			<div class="col-md-6 col-xs-12">
				    	<label>FECHA DOC. BANCO</label>
				    	<input type="text" class="form-control datepicker" name="fechadocbanco" id="fechadocbanco" autocomplete="off" required value="<?php echo date('Y-m-d');?>">
				    </div>
				    <div class="col-md-6 col-xs-12">
				    	<label>NRO DOCUMENTO BANCO</label>
			        	<input type="text" class="form-control" name="nrodocbanco" id="nrodocbanco" v-model="campos.nrodocbanco" placeholder="Nro documento banco" autocomplete="off">
				    </div>

				    <div class="col-xs-3"></div>
    				<div class="col-xs-6"> <br>
	    				<label style="font-size:15px;">S/. IMPORTE</label>
	    				<input type="number" step="0.01" class="form-control number netix-money-success" v-model="campos.importe" min="1" required placeholder="S/. 0.00">
	    			</div>
			    </div>
	            
			    <div class="row form-group" align="center">
					<div class="col-md-12" align="center"> <br>
						<button type="submit" class="btn btn-success btn-lg" v-bind:disabled="estado==1"> <b>GUARDAR COBRANZA</b> </button>
						<button type="button" class="btn btn-danger btn-lg" v-on:click="netix_cerrar()"> <b>CANCELAR</b> </button>
					</div>
				</div>
	        </div>
		</div>
	</form>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/cobranza.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'}); 

	var div_altura = jQuery(document).height(); var cobros = div_altura - 420;
	$(".cuotas").slimScroll({position:'right',size:"5px", color:'#98a6ad',wheelStep:10,height:"250px"});
	$(".cobros").slimScroll({position:'right',size:"5px", color:'#98a6ad',wheelStep:10,height:cobros+"px"});
</script>