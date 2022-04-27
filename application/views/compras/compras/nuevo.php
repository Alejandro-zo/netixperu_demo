<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title"> <div class="col-md-12 col-xs-12"> <h5>{{titulo}}</h5> </div> </div>
	</div> <br>
	
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="itemrepetir" value="<?php echo $_SESSION["netix_itemrepetir"];?>">
		<input type="hidden" id="igvsunat" value="<?php echo $_SESSION["netix_igv"];?>">
		<input type="hidden" id="icbpersunat" value="<?php echo $_SESSION["netix_icbper"];?>">

		<div class="netix_body_row">
			<div class="row form-group">
				<div class="col-md-4 col-xs-12">
					<label>PROVEEDOR DE LA COMPRA</label>
	    			<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true">
	    				<option value="2">PROVEEDORES VARIOS</option>
	    			</select>
				</div>
				<div class="col-md-1 col-xs-2">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-success btn-block" v-on:click="netix_addproveedor()" title="AGREGAR PROVEEDOR"> 
						<i class="fa fa-user-plus"></i>
					</button>
				</div>
				<div class="col-md-2 col-xs-6">
					<label>FECHA COMPRA</label>
	    			<input type="text" class="form-control datepicker" name="fechacomprobante" id="fechacomprobante" autocomplete="off" v-on:blur="netix_tipocambio()" required value="<?php echo $_SESSION["netix_fechaproceso"];?>">
				</div>
				<div class="col-md-2 col-xs-6">
					<label>FECHA KARDEX</label>
					<input type="text" class="form-control datepicker" name="fechakardex" id="fechakardex" autocomplete="off" required value="<?php echo $_SESSION["netix_fechaproceso"];?>">
				</div>
				<div class="col-md-2 col-xs-6">
					<label>MONEDA</label>
	    			<select class="form-control" name="codmoneda" v-model="campos.codmoneda" v-on:change="netix_tipocambio()" required>
	    				<?php 
	    					foreach ($monedas as $key => $value) {?>
	    						<option value="<?php echo $value["codmoneda"];?>"><?php echo $value["simbolo"]." ".$value["descripcion"];?></option>
	    					<?php }
	    				?>
	    			</select>
				</div>
				<div class="col-md-1 col-xs-6">
					<label>CAMBIO</label>
	    			<input type="number" step="0.001" class="form-control number" name="tipocambio" v-model.number="campos.tipocambio" autocomplete="off" min="1" v-bind:disabled="campos.codmoneda==1" required>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-3 col-xs-12">
			    	<label>TIPO COMPROBANTE</label>
			    	<select class="form-control" name="codcomprobantetipo" v-model="campos.codcomprobantetipo" required>
			    		<option value="">SELECCIONE</option>
			    		<?php
			    			foreach ($comprobantes as $key => $value) { ?>
			    				<option value="<?php echo $value["codcomprobantetipo"];?>">
			    					<?php echo $value["descripcion"];?>
			    				</option>
			    			<?php }
			    		?>
			    	</select>
			    </div>
			    <div class="col-md-1 col-xs-12">
			    	<label>SERIE</label>
		        	<input class="form-control" name="seriecomprobante" v-model.trim="campos.seriecomprobante" maxlength="4" required autocomplete="off">
			    </div>
			    <div class="col-md-2 col-xs-12">
			    	<label>NRO COMPROBANTE</label>
		        	<input class="form-control" name="nro" v-model.trim="campos.nro" maxlength="8" required autocomplete="off">
		    	</div>
		    	<div class="col-md-1" align="center">
					<label style="font-size:10px;">AFECTACAJA</label> <br>
					<input type="checkbox" style="height:20px;width:20px;" v-model="campos.afectacaja"> 
				</div>
				<div class="col-md-1" align="center">
					<label style="font-size:10px;">RECEPCION</label> <br>
					<input type="checkbox" style="height:20px;width:20px;" v-model="campos.retirar"> 
				</div>
				<div class="col-md-1" align="center">
					<label style="font-size:10px;">CON IGV</label> <br>
					<input type="checkbox" style="height:20px;width:20px;" v-model="igv" v-on:change="netix_igv()"> 
				</div>
		    	<div class="col-md-3">
					<label>GLOSA DE LA COMPRA</label>
					<input type="text" class="form-control" v-model="campos.descripcion" autocomplete="off" maxlength="250" placeholder="Referencia de la compra . . .">
				</div>
			</div>
			<div class="row form-group">
			    <div class="col-md-1 col-xs-12">
			    	<label>FLETE</label>
			    	<input type="number" step="0.01" class="form-control number" name="flete" v-model.number="totales.flete" autocomplete="off" min="0" required v-on:keyup="netix_totales()">
			    </div>
			    <div class="col-md-1 col-xs-12">
			    	<label>GASTOS</label>
			    	<input type="number" step="0.01" class="form-control number" name="gastos" v-model.number="totales.gastos" min="0" autocomplete="off" required v-on:keyup="netix_totales()">
			    </div>
			    <div class="col-md-2 col-xs-12">
					<label>CENTRO COSTO</label>
					<select class="form-control" v-model="campos.codcentrocosto">
						<option value="0">SIN CENTRO COSTO</option>
						<?php 
							foreach ($centrocostos as $key => $value) { ?>
								<option value="<?php echo $value["codcentrocosto"];?>"><?php echo $value["descripcion"];?></option>
							<?php }
						?>
					</select>
				</div>
			    <div class="col-md-2 col-xs-12">
			    	<label>CONDICION PAGO</label>
			    	<select class="form-control" name="condicionpago" v-model="campos.condicionpago" v-on:change="netix_condicionpago()">
			    		<option value="1">CONTADO</option>
			    		<option value="2">CREDITO</option>
			    	</select>
			    </div>

			    <div v-if="campos.condicionpago==1">
					<div class="col-md-2 col-xs-12">
				    	<label>TIPO DE PAGO</label>
			        	<select class="form-control" id="codtipopago" v-model="pagos.codtipopago" required>
				    		<?php 
				    			foreach ($tipopagos as $key => $value) { ?>
				    				<option value="<?php echo $value["codtipopago"];?>">
				    					<?php echo $value["descripcion"];?>
				    				</option>
				    			<?php }
				    		?>
				    	</select>
				    </div>
	    			<div v-show="pagos.codtipopago!=1">
				    	<div class="col-md-2 col-xs-12">
					    	<label>FECHA BANCO</label>
					    	<input type="text" class="form-control datepicker" id="fechadocbanco" autocomplete="off" required value="<?php echo date('Y-m-d');?>">
					    </div>
					    <div class="col-md-2 col-xs-12">
					    	<label>NRO VOUCHER</label>
				        	<input type="text" class="form-control" name="nrodocbanco" id="nrodocbanco" v-model="pagos.nrodocbanco" placeholder="Nro voucher" autocomplete="off">
					    </div>
				    </div>
		    	</div>
			    <div v-if="campos.condicionpago==2">
			    	<div class="col-md-1 col-xs-12">
				    	<label>DIAS</label>
			        	<input class="form-control" name="nrodias" v-model="campos.nrodias" v-on:keyup="netix_cuotas()" required autocomplete="off">
				    </div>
				    <div class="col-md-1 col-xs-12">
				    	<label>CUOTAS</label>
				    	<input class="form-control" name="nrocuotas" v-model="campos.nrocuotas" v-on:keyup="netix_cuotas()" required autocomplete="off">
				    </div>
				    <div class="col-md-2 col-xs-12">
				    	<label>INTERES (%)</label>
				    	<input class="form-control" name="tasainteres" v-model="campos.tasainteres" v-on:keyup="netix_cuotas()" required autocomplete="off">
				    </div>
				    <div class="col-md-2 col-xs-12">
				    	<div class="btn-group-vertical">
					    	<button type="button" class="btn btn-warning btn-sm">INTERES: {{campos.interes}}</button>
					    	<button type="button" class="btn btn-danger btn-sm">TOTAL CREDITO: {{campos.totalcredito}}</button>
					    </div>
				    </div>
				</div>
			</div>
		</div>

		<div class="netix_body_row table-responsive scroll-netix-view" style="height:calc(100vh - 465px);padding:0px; overflow:auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th width="7%" class="netix-item-mas" v-on:click="netix_item()"> <i class="fa fa-plus-square"></i> ITEM </th>
						<th width="30%">PRODUCTO</th>
						<th width="7%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">PRECIO</th>
						<th width="10%">SUBTOTAL</th>
						<th width="10%">I.G.V.</th>
						<th width="10%">ICBPER</th>
						<th width="10%">TOTAL</th>
						<th width="1%"> <i class="fa fa-trash-o"></i> </th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td class="netix-item-mas" v-on:click="netix_itemdetalle(index,dato)"> <i class="fa fa-plus-circle"></i> MAS </td>
						<td style="font-size:10px;">{{dato.producto}}</td>
						<td> <input type="hidden" v-model="dato.codunidad">{{dato.unidad}} </td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato)" min="0.001" required>
						</td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.codafectacionigv==21" v-model.number="dato.preciosinigv" min="0" readonly>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.codafectacionigv!=21" v-model.number="dato.preciosinigv" v-on:keyup="netix_calcular(dato)" min="0.001" required  v-bind:disabled="dato.porcdescuento==100">
						</td>
						<td> <input type="number" class="netix-input number" v-model.number="dato.valorventa" readonly> </td>
						<td> <input type="number" class="netix-input number" v-model.number="dato.igv" min="0" readonly> </td>
						<td> <input type="number" class="netix-input number" v-model.number="dato.icbper" min="0" readonly> </td>
						<td v-if="dato.codafectacionigv==21">
							<input type="number" step="0.01" class="netix-input number" v-model.number="dato.subtotal">
						</td>
						<td v-if="dato.codafectacionigv!=21">
							<input type="number" step="0.01" class="netix-input number" v-if="dato.calcular==0" v-model.number="dato.subtotal" readonly>
							<input type="number" step="0.01" class="netix-input number" v-if="dato.calcular!=0" v-model.number="dato.subtotal" v-on:keyup="netix_subtotal(dato)" required v-bind:disabled="dato.porcdescuento==100">
						</td>
						<td> 
							<button type="button" class="btn btn-danger btn-block btn-xs" style="margin-bottom:-1px;" v-on:click="netix_deleteitem(index,dato)">
								<i class="fa fa-trash-o"></i> 
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div> <br>

		<div class="netix_body_row" style="padding:2px;">
			<div class="text-center">
				<button type="button" class="btn btn-info btn-sm">IMP.BRUTO: S/. {{totales.bruto}}</button>
				<button type="button" class="btn btn-warning btn-sm">DESC: S/. {{totales.descuentos}}</button>

				<button type="button" class="btn btn-primary btn-sm">GRAV.: S/. {{operaciones.gravadas}}</button>
				<button type="button" class="btn btn-primary btn-sm">EXON.: S/. {{operaciones.exoneradas}}</button>
				<button type="button" class="btn btn-primary btn-sm">INAF.: S/. {{operaciones.inafectas}}</button>
				<button type="button" class="btn btn-primary btn-sm">GRAT.: S/. {{operaciones.gratuitas}}</button>

				<button type="button" class="btn btn-danger btn-sm"><b>IGV: S/. {{totales.igv}}</b></button>
				<button type="button" class="btn btn-danger btn-sm"><b>ISC: S/. {{totales.isc}}</b></button>
				<button type="button" class="btn btn-danger btn-sm"><b>ICBPER: S/. {{totales.icbper}}</b> </button>
				
				<button type="button" class="btn btn-success btn-sm"><b>TOTAL COMPRA S/. {{totales.importe}}</b></button>
			</div>
			<div class="row">
				<div class="col-md-4 col-xs-6">
					<button type="button" class="btn btn-warning btn-block" v-on:click="netix_compra()"> 
						<b> <i class="fa fa-plus-square"></i> NUEVA COMPRA</b> 
					</button>
				</div>
				<div class="col-md-4 col-xs-6">
					<button type="submit" class="btn btn-success btn-block" v-bind:disabled="estado==1"> 
						<b><i class="fa fa-save"></i> GUARDAR COMPRA</b> 
					</button>
				</div>
				<div class="col-md-4 col-xs-12">
					<button type="button" class="btn btn-danger btn-block" v-on:click="netix_atras()"> 
						<b> <i class="fa fa-arrow-left"></i> ATRAS</b> 
					</button>
				</div>
			</div>
		</div>
	</form>

	<div id="modal_itemdetalle" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-netix-titulo">
					<h4 class="modal-title"> <b style="letter-spacing:0.5px;">DETALLE DEL ITEM DE LA COMPRA</b> </h4> 
				</div>
				<div class="modal-body">
					<h5> <b>
						PRODUCTO: {{item.producto}} &nbsp; <span class="label label-warning">CANTIDAD: {{item.cantidad}} {{item.unidad}}</span>
					</b> </h5> <hr>

					<div class="row form-group">
				    	<div class="col-md-3 col-xs-12">
					    	<label>S/ BRUTO SIN IGV</label>
					    	<input type="number" class="netix-input number" v-model.number="item.preciobrutosinigv" v-on:keyup="netix_itemcalcular(item,0)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>S/ BRUTO CON IGV</label>
					    	<input type="number" class="netix-input number" v-model.number="item.preciobruto" v-on:keyup="netix_itemcalcular(item,1)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>DESCUENTO PRECIO (S/.)</label>
					    	<input type="number" class="netix-input number" v-model.number="item.descuento" v-on:keyup="netix_itemcalcular(item,-1)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>DESCUENTO PRECIO (%)</label>
					    	<input type="number" class="netix-input number" v-model.number="item.porcdescuento" v-on:keyup="netix_itemcalcular(item,-2)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					</div>
					<div class="row form-group">
				    	<div class="col-md-3 col-xs-12">
					    	<label>PRECIO SIN IGV</label>
					    	<input type="number" class="netix-input number" v-model.number="item.preciosinigv" v-on:keyup="netix_itemcalcular(item,2)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>PRECIO CON IGV</label>
					    	<input type="number" class="netix-input number" v-model.number="item.precio" v-on:keyup="netix_itemcalcular(item,3)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-2 col-xs-12">
					    	<label>AFECTACION</label>
					    	<select class="netix-input" v-model="item.codafectacionigv" v-on:change="netix_itemcalcular(item,2)">
					    		<option value="10">GRAVADO</option> 
					    		<option value="20">EXONERADO</option> 
					    		<option value="21">GRATUITO</option> 
					    		<option value="30">INAFECTO</option>
					    	</select>
					    </div>
					    <div class="col-md-1 col-xs-12">
					    	<label>ICBPER</label>
					    	<select class="netix-input" v-model="item.conicbper" v-on:change="netix_itemcalcular(item,3)">
					    		<option value="1">SI</option>
					    		<option value="0">NO</option>
					    	</select>
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>SUBTOTAL</label>
					    	<input type="number" class="netix-input number" v-model.number="item.valorventa" v-on:keyup="netix_itemcalcular(item,4)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					</div>
					<div class="form-group text-center">
						<button type="button" class="btn btn-info btn-sm"> <b>SUBTOTAL: S/. {{item.valorventa}}</b> </button>
						<button type="button" class="btn btn-danger btn-sm"> <b>IGV: S/. {{item.igv}}</b></button>
						<button type="button" class="btn btn-danger btn-sm"> <b>ICBPER: S/. {{item.icbper}}</b> </button>
						<button type="button" class="btn btn-success btn-sm"> <b>TOTAL: S/. {{item.subtotal}}</b> </button>
					</div>
					<div class="form-group">
						<label>DESCRIPCION DEL ITEM DE COMPRA</label>
						<textarea class="form-control" v-model="item.descripcion" rows="3" maxlength="250"></textarea>
					</div>

					<div class="text-center">
						<button type="button" class="btn btn-success" v-on:click="netix_itemcalcular_cerrar(item)">
							<i class="fa fa-save"></i> GUARDAR CAMBIOS DEL ITEM Y <i class="fa fa-times-circle"></i> CERRAR
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_compras/nuevo.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true"); </script>