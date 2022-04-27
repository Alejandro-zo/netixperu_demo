<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12"> <h5>{{titulo}}</h5> </div>
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="stockalmacen" value="<?php echo $_SESSION["netix_stockalmacen"];?>">
		<input type="hidden" id="itemrepetir" value="<?php echo $_SESSION["netix_itemrepetir"];?>">
		<input type="hidden" id="igvsunat" value="<?php echo $_SESSION["netix_igv"];?>">
		<input type="hidden" id="icbpersunat" value="<?php echo $_SESSION["netix_icbper"];?>">

		<div class="netix_body_row">
			<div class="row">
				<div class="col-md-5 col-xs-10">
					<label>SELECCIONAR DESTINATARIO (CLIENTE)</label>
	    			<select class="form-control selectpicker ajax" id="coddestinatario" v-model="campos.coddestinatario" required data-live-search="true" required v-on:change="netix_comprobantes()">
	    				<option value="">SELECCIONE . . .</option>
	    			</select>
				</div>
				<div class="col-md-1 col-xs-2">
					<label>&nbsp;</label>
					<button type="button" class="btn btn-success btn-block" v-on:click="netix_addcliente()" title="AGREGAR CLIENTE"> 
						<i class="fa fa-user-plus"></i>
					</button>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>FECHA GUIA</label>
	    			<input type="text" class="form-control datepicker" id="fechaemision" value="<?php echo date('Y-m-d');?>" autocomplete="off" required>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>SERIE GUIA</label>
	    			<select class="form-control" id="seriecomprobante" v-model="campos.seriecomprobante" required>
	    				<option value="">SELECCIONE . . .</option>
	    				<?php 
	    					foreach ($comprobantes as $key => $value) { ?>
	    						<option value="<?php echo $value["seriecomprobante"];?>">
	    							<?php echo $value["seriecomprobante"];?> (NRO: <?php echo $value["nrocorrelativo"] + 1;?>)
	    						</option>
	    					<?php }
	    				?>
	    			</select>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>CONCEPTO</label>
	    			<select class="form-control" id="codmodalidadtraslado" v-model="campos.codmodalidadtraslado" required>
	    				<option value="">SELECCIONE . . .</option>
	    				<option value="01">VENTA</option>
	    				<option value="02">COMPRA</option>
	    			</select>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-xs-12"> <br>
					<div class="form-group">
						<label class="text-success">PUNTO PARTIDA</label>
						<input type="text" class="form-control" v-model.trim="campos.punto_partida" autocomplete="off" maxlength="150" required>
					</div>
					<div class="form-group">
						<label class="text-success">UBIGEO PARTIDA</label>
						<select class="form-control ubigeos ajax" name="ubigeo_partida" v-model="campos.ubigeo_partida" id="ubigeo_partida" required data-live-search="true">
							<option>SELECCIONE . . .</option>
						</select>
					</div>
				</div>
				<div class="col-md-6 col-xs-12"> <br>
					<div class="form-group">
						<label class="text-danger">PUNTO LLEGADA</label>
						<input type="text" class="form-control" v-model.trim="campos.punto_llegada" autocomplete="off" maxlength="150"  required>
					</div>
					<div class="form-group">
						<label class="text-danger">UBIGEO LLEGADA</label>
						<select class="form-control ubigeos ajax" name="ubigeo_llegada" v-model="campos.ubigeo_llegada" id="ubigeo_llegada" required data-live-search="true">
							<option>SELECCIONE . . .</option>
						</select>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 col-xs-12">
					<div class="row">
						<div class="col-md-4 form-group">
							<label>MOD. TRASPORTE</label>
							<select class="form-control" name="tipo_trasporte" id="tipo_trasporte" required>
								<option value="">SELECCIONE . . .</option>
			    				<option value="01">TRANSPORTE PUBLICO</option>
			    				<option value="02">TRANSPORTE PRIVADO</option>
			    			</select>
						</div>
						<div class="col-md-4 form-group">
							<label>N° PLACA</label>
							<input type="text" class="form-control" id="nroplaca" v-model.trim="campos.nroplaca" autocomplete="off" maxlength="50">
						</div>
						<div class="col-md-4 form-group">
							<label>FECHA TRASLADO</label>
	    					<input type="text" class="form-control datepicker" name="fechatraslado" id="fechatraslado" value="<?php echo date("Y-m-d"); ?>" autocomplete="off" required>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-xs-10">
							<label>SELECCIONAR EMPRESA DE TRASPORTE</label>
			    			<select class="form-control selectpicker ajax" name="codempresa_traslado" v-model="campos.codempresa_traslado" id="codempresa_traslado" required data-live-search="true">
			    				<option value="">SELECCIONE . . .</option>
			    			</select>
						</div>
						<div class="col-md-2 col-xs-2">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_addcliente()" title="AGREGAR EMPRESA">
								<i class="fa fa-user-plus"></i>
							</button>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 form-group">
							<label>DNI</label>
							<input type="text" class="form-control" id="dniconductor" v-model.trim="campos.dniconductor" minlength="8" maxlength="8">
						</div>
						<div class="col-md-8 form-group">
							<label>CONDUCTOR</label>
							<input type="text" class="form-control" id="conductor" v-model.trim="campos.conductor" autocomplete="off" maxlength="100">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4 form-group">
							<label>LICENCIA</label>
							<input type="text" class="form-control" id="licencia" v-model.trim="campos.licencia" autocomplete="off" maxlength="50">
						</div>
						<div class="col-md-8 form-group">
							<label>GLOSA GUIA</label>
							<input type="text" class="form-control" id="descripcion" v-model.trim="campos.descripcion" autocomplete="off" maxlength="250" required>
						</div>
					</div>
				</div>
				<div class="col-md-6 col-xs-12"> <br>
					<div class="row">
						<div class="col-md-4 form-group">
							<label>FECHA DESDE</label>
							<input type="text" class="form-control datepicker" id="fecha_desde" value="<?php echo date("Y-m-d"); ?>" autocomplete="off">
						</div>
						<div class="col-md-4 form-group">
							<label>FECHA HASTA</label>
							<input type="text" class="form-control datepicker" id="fecha_hasta" value="<?php echo date("Y-m-d"); ?>" autocomplete="off">
						</div>
						<div class="col-md-4 form-group">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-success btn-block" v-on:click="netix_comprobantes()">
								<i class="fa fa-search"></i> BUSCAR
							</button>
						</div>
					</div>

					<div class="table-responsive" style="height: 165px; overflow-y: auto;">
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
							<tbody>
								<tr v-for="dato in comprobantes" style="cursor:pointer;" v-bind:id="dato.codkardex">
									<td>{{dato.cliente}}</td>
									<td>{{dato.fechacomprobante}}</td>
									<td>{{dato.seriecomprobante}}-{{dato.nrocomprobante}}</td>
									<td>{{dato.importe}}</td>
									<td>
										<button type="button" class="btn btn-success btn-block btn-xs" style="margin-bottom:-1px;" v-on:click="netix_detalle(dato)">
											<i class="fa fa-check"></i> SELECCIONAR
										</button> 
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="netix_body_row table-responsive scroll-netix-view" style="height:150px;padding:0px; overflow:auto;">
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th width="7%" class="netix-item-mas" v-on:click="netix_item()"> <i class="fa fa-plus-square"></i> ITEM </th>
						<th width="30%">PRODUCTO</th>
						<th width="7%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">PRECIO</th>
						<th width="10%">I.G.V.</th>
						<th width="10%">SUBTOTAL</th>
						<th width="10%">PESO KG</th>
						<th width="10%">T.PESO</th>
						<th width="1%"> <i class="fa fa-trash-o"></i> </th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td class="netix-item-mas" v-on:click="netix_itemdetalle(index,dato)"> <i class="fa fa-plus-circle"></i> MAS </td>
						<td style="font-size:10px;">{{dato.producto}}</td>
						<td> <input type="hidden" v-model="dato.codunidad">{{dato.unidad}}</td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato)" min="0.1" required>
						</td>
						<td>
							<input type="number" step="0.0001" class="netix-input number" v-model.number="dato.precio" v-on:keyup="netix_calcular(dato)" required>
						</td>
						<td> <input type="number" class="netix-input number" v-model.number="dato.igv" min="0" readonly> </td>
						<td>{{dato.subtotal}}</td>
						<td> <input type="number" class="netix-input number" v-model.number="dato.pesokg" min="0" v-on:keyup="netix_peso(dato)"> </td>
						<td>{{dato.pesototal}}</td>
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
			<div class="row">
				<div class="col-md-7 col-xs-6 text-center">
					<button type="button" class="btn btn-info btn-sm">PESO KG: S/. {{campos.pesototal}}</button>
					<button type="button" class="btn btn-info btn-sm">VALOR GUIA: S/. {{campos.valorguia}}</button>
					<button type="button" class="btn btn-danger btn-sm">IGV: S/. {{campos.igv}}</button>
					<button type="button" class="btn btn-success btn-sm">TOTAL S/. {{campos.importe}}</button>
				</div>
				<div class="col-md-3 col-xs-6">
					<button type="submit" class="btn btn-info btn-block" v-bind:disabled="estado==1"> 
						<b><i class="fa fa-save"></i> GUARDAR GUIA</b> 
					</button>
				</div>
				<div class="col-md-2 col-xs-12">
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
					<h4 class="modal-title"> <b style="letter-spacing:0.5px;">DETALLE DEL ITEM DE LA GUIA</b> </h4> 
				</div>
				<div class="modal-body">
					<h5> <b>
						PRODUCTO: {{item.producto}} &nbsp; <span class="label label-warning">CANTIDAD: {{item.cantidad}} {{item.unidad}}</span>
					</b> </h5> <hr>

					<div class="row form-group">
				    	<div class="col-md-4 col-xs-12">
					    	<label>PRECIO BASE</label>
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
					</div>
					<div class="form-group text-center">
						<button type="button" class="btn btn-info btn-sm"> <b>VALOR GUIA: S/. {{item.valorguia}}</b> </button>
						<button type="button" class="btn btn-danger btn-sm"> <b>IGV: S/. {{item.igv}}</b></button>
						<button type="button" class="btn btn-success btn-sm"> <b>SUBTOTAL: S/. {{item.subtotal}}</b> </button>
					</div>
					<div class="form-group">
						<label>DESCRIPCION DEL ITEM DE GUIA</label>
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

<script src="<?php echo base_url();?>netix/netix_guias/nuevo.js"> </script>
<script src="<?php echo base_url();?>netix/netix_guias/ubigeos.js"> </script>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>