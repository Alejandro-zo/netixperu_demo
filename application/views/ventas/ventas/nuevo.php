<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-4 col-xs-12"> <h5>{{titulo}}</h5> </div>
			<div class="col-md-4 col-xs-12" style="display:none">
				<h5 class="text-danger" align="right"><i class="fa fa-file-o"></i> VENTA SIN PEDIDO</h5>
			</div>
			<div class="col-md-2 col-xs-12" style="display:none">
				<button type="button" class="btn btn-success btn-block">BUSCAR PEDIDO</button>
			</div>

			<!-- <div class="col-md-5 col-xs-12" align="right">
				<h5>BUSCAR PRODUCTO CÓDIGO DE BARRA</h5>
			</div>
			<div class="col-md-3 col-xs-12">
				<input type="text" class="form-control" v-model="codigobarra" v-on:keyup.13="netix_codigobarra()" autofocus="true">
			</div> -->
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="comprobante" value="<?php echo $sucursal[0]['codcomprobantetipo'];?>">
		<input type="hidden" id="serie" value="<?php echo $sucursal[0]['seriecomprobante'];?>">
		<input type="hidden" id="stockalmacen" value="<?php echo $_SESSION["netix_stockalmacen"];?>">
		<input type="hidden" id="itemrepetir" value="<?php echo $_SESSION["netix_itemrepetir"];?>">
		<input type="hidden" id="igvsunat" value="<?php echo $_SESSION["netix_igv"];?>">
		<input type="hidden" id="icbpersunat" value="<?php echo $_SESSION["netix_icbper"];?>">
		<input type="hidden" id="formato" value="<?php echo $_SESSION['netix_formato'];?>">

		<div class="netix_body_row">
			<div class="row form-group">
				<div class="col-md-4 col-xs-10">
					<label>SELECCIONAR CLIENTE</label>
	    			<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true" v-on:change="netix_infocliente()">
	    				<option value="2">CLIENTES VARIOS</option>
	    			</select>
				</div>
				<div class="col-md-1 col-xs-2">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-success btn-block" v-on:click="netix_addcliente()" title="AGREGAR CLIENTE"> 
						<i class="fa fa-user-plus"></i>
					</button>
				</div>
				<?php 
					if ($_SESSION["netix_rubro"]==1) { ?>
						<div class="col-md-4 col-xs-10">
							<label>NRO DE PLACA</label>
							<input type="text" class="form-control" v-model="campos.nroplaca" autocomplete="off" maxlength="100" placeholder="Nro placa . . .">
						</div>
					<?php }else{ ?>
						<div class="col-md-4 col-xs-10">
							<label>GLOSA DE LA VENTA</label>
							<input type="text" class="form-control" v-model="campos.descripcion" autocomplete="off" maxlength="250" placeholder="Referencia de la venta . . .">
						</div>
					<?php }
				?>
				<div class="col-md-1 text-center">
					<label>DESPACHAR</label> <br>
					<input type="checkbox" style="height:20px;width:20px;" v-model="campos.retirar"> 
				</div>
				<!-- <div class="col-md-1 col-xs-2">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-warning btn-block" v-on:click="netix_addcliente()" title="MAS OPCIONES . . ." disabled="true"> 
						<i class="fa fa-plus"></i> <i class="fa fa-ellipsis-h"></i>
					</button>
				</div> -->
				<div class="col-md-2 col-xs-12">
					<label>FECHA VENTA</label>
	    			<input type="text" class="form-control datepicker" name="fechacomprobante" id="fechacomprobante" value="<?php echo $_SESSION["netix_fechaproceso"];?>" autocomplete="off" required>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-md-5 col-xs-12">
					<label>CLIENTE DE LA VENTA</label>
					<input type="text" class="form-control" id="cliente" v-model.trim="campos.cliente" autocomplete="off" maxlength="250" placeholder="Razon social del cliente . . ." required>
				</div>
				<div class="col-md-5 col-xs-12">
					<label>DIRECCION CLIENTE</label>
					<input type="text" class="form-control" id="direccion" v-model.trim="campos.direccion" autocomplete="off" maxlength="250" placeholder="Direccion del cliente . . ." required>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>FECHA KARDEX</label>
	    			<input type="text" class="form-control datepicker" name="fechakardex" id="fechakardex" value="<?php echo $_SESSION["netix_fechaproceso"];?>" autocomplete="off" required>
				</div>
			</div>
		</div>
		<div class="netix_body_row table-responsive scroll-netix-view" style="height:calc(100vh - 414px);padding:0px; overflow:auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th width="7%" class="netix-item-mas" v-on:click="netix_item()"> <i class="fa fa-plus-square"></i> ITEM </th>
						<th width="30%">PRODUCTO</th>
						<th width="7%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">PRECIO UNIT.</th>
						<th width="10%">I.G.V.</th>
						<th width="10%">ICBPER</th>
						<th width="10%">SUBTOTAL</th>
						<th width="1%"> <i class="fa fa-trash-o"></i> </th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td class="netix-item-mas" v-on:click="netix_itemdetalle(index,dato)"> <i class="fa fa-plus-circle"></i> MAS </td>
						<td style="font-size:10px;">{{dato.producto}}</td>
						<td> <input type="hidden" v-model="dato.codunidad">{{dato.unidad}} </td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.control==1" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato)" min="0.0001" v-bind:max="dato.stock" required>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.control==0" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato)" min="0.0001" required>
						</td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.codafectacionigv==21" v-model.number="dato.precio" min="0" readonly>
							<input type="number" step="0.0001" class="netix-input number" v-if="dato.codafectacionigv!=21" v-model.number="dato.precio" v-on:keyup="netix_calcular(dato)" min="0.001" required  v-bind:disabled="dato.porcdescuento==100">
						</td>
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

		<div class="netix_body_row">
			<div class="text-center">
				<button type="button" class="btn btn-info btn-sm">IMPORTE: S/. {{totales.bruto}}</button>
				<button type="button" class="btn btn-warning btn-sm">DESC: S/. {{totales.descuentos}}</button>

				<button type="button" class="btn btn-primary btn-sm">GRAV.: S/. {{operaciones.gravadas}}</button>
				<button type="button" class="btn btn-primary btn-sm">EXON.: S/. {{operaciones.exoneradas}}</button>
				<button type="button" class="btn btn-primary btn-sm">INAF.: S/. {{operaciones.inafectas}}</button>
				<button type="button" class="btn btn-primary btn-sm">GRAT.: S/. {{operaciones.gratuitas}}</button>

				<button type="button" class="btn btn-danger btn-sm"><b>IGV: S/. {{totales.igv}}</b></button>
				<button type="button" class="btn btn-danger btn-sm"><b>ISC: S/. {{totales.isc}}</b></button>
				<button type="button" class="btn btn-danger btn-sm"><b>ICBPER: S/. {{totales.icbper}}</b> </button>
				
				<button type="button" class="btn btn-success btn-sm"><b>TOTAL VENTA S/. {{totales.importe}}</b></button>
			</div>
			<div class="row">
				<div class="col-md-4 col-xs-6">
					<button type="button" class="btn btn-warning btn-block" v-on:click="netix_venta()"> 
						<b> <i class="fa fa-plus-square"></i> NUEVA VENTA</b> 
					</button>
				</div>
				<div class="col-md-4 col-xs-6">
					<button type="submit" class="btn btn-info btn-block" v-bind:disabled="estado==1"> 
						<b><i class="fa fa-arrow-right"></i> CONTINUAR VENTA</b> 
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
	
	<div id="modal_masconfiguraciones" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" align="center">
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;">DETALLE DEL ITEM DE LA VENTA</b> </h4> 
				</div>
				<div class="modal-body" style="height: 380px;">
					<div class="row form-group">
						<div class="col-md-4 col-xs-12" align="center">
							<label>RECOGIDO</label>
							<input type="checkbox" style="height:20px;width:20px;" v-model="campos.retirar" disabled="true"> 
						</div>
					</div>
					<div class="row form-group">
						<div class="col-md-4 col-xs-12">
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
						<div class="col-md-2 col-xs-12" v-if="rubro==1">
							<label>NRO PLACA</label>
							<input type="text" class="form-control" v-model="campos.nroplaca" autocomplete="off" maxlength="50" placeholder="Nro placa . . .">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_itemdetalle" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-netix-titulo">
					<h4 class="modal-title"> <b style="letter-spacing:0.5px;">DETALLE DEL ITEM DE LA VENTA</b> </h4> 
				</div>
				<div class="modal-body">
					<h5> <b>
						PRODUCTO: {{item.producto}} &nbsp; <span class="label label-warning">CANTIDAD: {{item.cantidad}} {{item.unidad}}</span>
					</b> </h5> <hr>

					<div class="row form-group">
				    	<div class="col-md-4 col-xs-12">
					    	<label>PRECIO BRUTO</label>
					    	<input type="number" class="netix-input number" v-model.number="item.preciobruto" v-on:keyup="netix_itemcalcular(item,0)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-4 col-xs-12">
					    	<label>DESCUENTO PRECIO (S/.)</label>
					    	<input type="number" class="netix-input number" v-model.number="item.descuento" v-on:keyup="netix_itemcalcular(item,-1)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-4 col-xs-12">
					    	<label>DESCUENTO PRECIO (%)</label>
					    	<input type="number" class="netix-input number" v-model.number="item.porcdescuento" v-on:keyup="netix_itemcalcular(item,-2)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					</div>
					<div class="row form-group">
				    	<div class="col-md-4 col-xs-12">
					    	<label>PRECIO SIN I.G.V.</label>
					    	<input type="number" class="netix-input number" v-model.number="item.preciosinigv" v-on:keyup="netix_itemcalcular(item,1)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-4 col-xs-12">
					    	<label>PRECIO UNITARIO</label>
					    	<input type="number" class="netix-input number" v-model.number="item.precio" v-on:keyup="netix_itemcalcular(item,2)" v-bind:disabled="item.codafectacionigv==21">
					    </div>
					    <div class="col-md-3 col-xs-12">
					    	<label>TIPO AFECTACION</label>
					    	<select class="netix-input" v-model="item.codafectacionigv" v-on:change="netix_itemcalcular(item,2)">
					    		<option value="10">GRAVADO</option> 
					    		<option value="20">EXONERADO</option> 
					    		<option value="21">GRATUITO</option> 
					    		<option value="30">INAFECTO</option>
					    	</select>
					    </div>
					    <div class="col-md-1 col-xs-12">
					    	<label>ICBPER</label>
					    	<select class="netix-input" v-model="item.conicbper" v-on:change="netix_itemcalcular(item,2)">
					    		<option value="1">SI</option>
					    		<option value="0">NO</option>
					    	</select>
					    </div>
					</div>
					<div class="form-group text-center">
						<button type="button" class="btn btn-info btn-sm"> <b>VALOR VENTA: S/. {{item.valorventa}}</b> </button>
						<button type="button" class="btn btn-danger btn-sm"> <b>IGV: S/. {{item.igv}}</b></button>
						<button type="button" class="btn btn-danger btn-sm"> <b>ICBPER: S/. {{item.icbper}}</b> </button>
						<button type="button" class="btn btn-success btn-sm"> <b>SUBTOTAL: S/. {{item.subtotal}}</b> </button>
					</div>
					<div class="form-group">
						<label>DESCRIPCION DEL ITEM DE VENTA</label>
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

	<div id="modal_pago" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header modal-netix-titulo text-center"> 
					<button type="button" class="close" data-dismiss="modal"> <i class="fa fa-times-circle"></i> </button>
					<h4 class="modal-title"> <b style="font-size:25px;">TOTAL VENTA S/. {{totales.importe}}</b>  </h4> 
				</div>
				<div class="modal-body">
					<form v-on:submit.prevent="netix_pagar()">
			        	<div class="row form-group">
					    	<div class="col-md-5 col-xs-12">
						    	<label>TIPO COMPROBANTE</label>
						    	<select class="form-control" name="codcomprobantetipo" id="codcomprobantetipo" v-model="campos.codcomprobantetipo" required v-on:change="netix_series()">
						    		<?php
						    			foreach ($comprobantes as $key => $value) { ?>
						    				<option value="<?php echo $value["codcomprobantetipo"];?>">
						    					<?php echo $value["descripcion"];?>
						    				</option>
						    			<?php }
						    		?>
						    	</select>
						    </div>
						    <div class="col-md-3 col-xs-12">
						    	<label>SERIE <b>(NRO: {{campos.nro}})</b></label>
					        	<select class="form-control" id="seriecomprobante" v-model="campos.seriecomprobante" v-on:change="netix_correlativo()" required>
						    		<option value="">SERIE</option>
						    		<option v-for="dato in series" v-bind:value="dato.seriecomprobante"> 
						    			{{dato.seriecomprobante}}
						    		</option>
						    	</select>
						    </div>
					    	<div class="col-md-4 col-xs-12">
						    	<label>CONDICION PAGO</label>
						    	<select class="form-control" name="condicionpago" v-model="campos.condicionpago" v-on:change="netix_condicionpago()">
						    		<option value="1">CONTADO</option>
						    		<option value="2">CREDITO</option>
						    	</select>
						    </div>
					    </div>
					    <div class="row form-group">
					    	<div class="col-md-12 col-xs-12">
						    	<label>SELECCIONAR VENDEDOR</label>
						    	<select class="form-control" name="codempleado" v-model="campos.codempleado" required>
						    		<option value="0">SIN VENDEDOR</option>
						    		<?php
						    			foreach ($vendedores as $key => $value) { ?>
						    				<option value="<?php echo $value["codpersona"];?>"> <?php echo $value["razonsocial"];?> </option>
						    			<?php }
						    		?>
						    	</select>
						    </div>
						</div>

					    <div class="row form-group" v-if="campos.condicionpago==2">
					    	<div class="col-md-5 col-xs-12">
						    	<label>NRO DIAS</label>
					        	<input class="form-control" name="nrodias" v-model="campos.nrodias" v-on:keyup="netix_cuotas()" required>
						    </div>
						    <div class="col-md-3 col-xs-12">
						    	<label>CUOTAS</label>
						    	<input class="form-control" name="nrocuotas" v-model="campos.nrocuotas" v-on:keyup="netix_cuotas()" required>
						    </div>
						    <div class="col-md-4 col-xs-12">
						    	<label>INTERES (%)</label>
						    	<input class="form-control" name="tasainteres" v-model="campos.tasainteres" v-on:keyup="netix_cuotas()" required>
						    </div>
					    </div>
					    <div class="row form-group" v-if="campos.condicionpago==2">
					    	<div class="col-md-12 col-xs-12">
						    	<label>RESPONSABLE DEL CREDITO</label>
					        	<select class="form-control" name="codpersona_convenio" v-model="campos.codpersona_convenio" required>
					        		<option value="1"><?php echo $_SESSION["netix_empresa"];?></option>
					        		<?php 
					        			foreach ($responsables as $key => $value) { ?>
					        				<option value="<?php echo $value["codpersona"];?>">
					        					<?php echo $value["razonsocial"]." ".$value["nombrecomercial"];?>
					        				</option>
					        			<?php }
					        		?>
					        	</select>
						    </div>
						</div>

					    <div v-if="campos.condicionpago==1">
					    	<h5 align="center"> <b> <i class="fa fa-money"></i> REGISTRAR PAGO DE LA VENTA</b> </h5> 
							<div class="netix-linea"></div>
					    	<div class="row form-group">
					    		<div class="col-md-4 col-xs-12" align="center">
				    				<label><i class="fa fa-money" style="font-size:35px;"></i> <br>PAGO CON EFECTIVO</label>
				    			</div>
							    <div class="col-md-4 col-xs-12">
				    				<label>S/. MONTO RECIBIDO</label>
				    				<input type="number" step="0.01" class="form-control number netix-money-success" min="0" required v-model="pagos.monto_efectivo" placeholder="S/. 0.00" v-on:keyup="netix_vuelto()">
				    			</div>
					    		<div class="col-md-4 col-xs-12">
				    				<label>VUELTO</label>
				    				<input type="number" step="0.01" class="form-control netix-money-error" readonly v-model="pagos.vuelto_efectivo">
				    			</div>
				    		</div>
				    		
							<div class="netix-linea"></div>
				    		<div class="row form-group">
				    			<div class="col-md-4 col-xs-12">
				    				<label> <i class="fa fa-money"></i> TARJETA O CHEQUE</label>
						        	<select class="form-control" v-model="pagos.codtipopago_tarjeta" v-on:change="netix_pagotarjeta()" required>
						        		<option value="0">SIN TARJETA</option>
							    		<?php 
							    			foreach ($tipopagos as $key => $value) { 
							    				if ($value["codtipopago"]!=1) { ?>
							    					<option value="<?php echo $value["codtipopago"];?>">
								    					<?php echo $value["descripcion"];?>
								    				</option>
							    				<?php } 
							    			}
							    		?>
							    	</select>
				    			</div>
				    			<div class="col-md-4 col-xs-12">
				    				<label>S/. MONTO</label>
				    				<input type="number" step="0.01" class="form-control number netix-money-success" min="0.01" id="monto_tarjeta" v-model="pagos.monto_tarjeta" placeholder="S/. 0.00" readonly>
				    			</div>
				    			<div class="col-md-4 col-xs-12">
							    	<label>NRO VOUCHER</label>
						        	<input type="text" class="form-control netix-money-default" id="nrovoucher" v-model.trim="pagos.nrovoucher" autocomplete="off" readonly>
							    </div>
				    		</div>
			    		</div>

					    <div v-if="campos.condicionpago==2">
					    	<div class="table-responsive" style="height:90px;">
					    		<table class="table table-bordered">
					    			<thead>
					    				<tr>
					    					<th>FECHA VENCE</th>
					    					<th>IMPORTE</th>
					    					<th>INTERES</th>
					    					<th>TOTAL</th>
					    				</tr>
					    			</thead>
					    			<tbody>
					    				<tr v-for="dato in cuotas">
					    					<td>{{dato.fechavence}}</td>
					    					<td>{{dato.importe}}</td>
					    					<td>{{dato.interes}}</td>
					    					<td>{{dato.total}}</td>
					    				</tr>
					    			</tbody>
					    		</table>
					    	</div>

					    	<div style="border-bottom:2px solid #13a89e;padding-bottom:10px;" align="center">
								<button type="button" class="btn btn-warning btn-sm"> <b>INTERES: S/. {{totales.interes}}</b></button>
								<button type="button" class="btn btn-danger btn-sm"> <b>TOTAL CREDITO: S/. {{campos.totalcredito}}</b> </button>
							</div>
				    	</div>
			            
					    <div class="row form-group" align="center"> <br>
							<div class="col-md-12">
								<button type="submit" class="btn btn-success btn-lg" v-bind:disabled="estado==1"> 
									<b>GUARDAR VENTA</b>
								</button>
								<button type="button" class="btn btn-danger btn-lg" data-dismiss="modal"> <b>CANCELAR</b> </button>
							</div>
						</div>
			        </form>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:100%;margin:0px;">
			<div class="modal-content" align="center" style="border-radius:0px">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title">
						<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"]." - ".$_SESSION["netix_sucursal"];?> </b>
					</h4>
				</div>
				<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
					<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_ventas/nuevo.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>