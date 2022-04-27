<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12"> <h5>REGISTRO NUEVO PEDIDO AL CLIENTE: <?php echo $persona[0]["razonsocial"];?></h5> </div>
		</div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" id="codpersona" value="<?php echo $persona[0]["codpersona"];?>">
		<input type="hidden" id="persona" value="<?php echo $persona[0]["razonsocial"];?>">
		<input type="hidden" id="stockalmacen" value="<?php echo $_SESSION["netix_stockalmacen"];?>">

		<div class="netix_body_row">
			<div class="row form-group">
				<div class="col-md-5 col-xs-12">
					<label>CLIENTE DE REFERENCIA</label>
					<input type="text" class="form-control" id="cliente" v-model.trim="campos.cliente" autocomplete="off" maxlength="250" placeholder="Razon social del cliente . . ." required>
				</div>
				<div class="col-md-5 col-xs-12">
					<label>DIRECCION CLIENTE REFERENCIA</label>
					<input type="text" class="form-control" id="direccion" v-model.trim="campos.direccion" autocomplete="off" maxlength="250" placeholder="Direccion del cliente . . ." required>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>FECHA PEDIDO</label>
	    			<input type="text" class="form-control datepicker" name="fechapedido" id="fechapedido" value="<?php echo date('Y-m-d');?>" autocomplete="off" required>
				</div>
			</div>
			<div class="row form-group">
				<div class="col-md-3 col-xs-12">
					<label>DESCUENTA STOCK</label>
					<select class="form-control" name="afectastock" v-model="campos.afectastock" disabled>
						<option value="1">SI DESCUENTA</option>
						<option value="0">NO DESCUENTA</option>
					</select>
				</div>
				<div class="col-md-2 col-xs-12">
					<label>AFECTA STOCK</label>
					<select class="form-control" name="afectacaja" v-model="campos.afectacaja">
						<option value="1">SI AFECTA</option>
						<option value="0">NO AFECTA</option>
					</select>
				</div>
				<div class="col-md-7 col-xs-12">
					<label>DESCRIPCION DEL PEDIDO</label>
					<input type="text" class="form-control" v-model.trim="campos.descripcion" autocomplete="off" maxlength="200" placeholder="Descripcion del pedido . . .">
				</div>
			</div>
		</div> <br>

		<div class="netix_body_row table-responsive scroll-netix-view" style="height:calc(100vh - 370px);padding:0px; overflow:auto;">
			<table class="table table-striped">
				<thead>
					<tr align="center" >
						<th width="7%" class="netix-item-mas" v-on:click="netix_item()"> <i class="fa fa-plus-square"></i> ITEM </th>
						<th width="50%">PRODUCTO</th>
						<th width="10%">UNIDAD</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">PRECIO</th>
						<th width="10%">SUBTOTAL</th>
						<th width="5%"> <i class="fa fa-trash-o"></i> </th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato,index) in detalle">
						<td> 
							<button type="button" class="btn btn-warning btn-block btn-xs" style="margin-bottom:-1px;" v-on:click="netix_itemdetalle(index,dato)">
								<i class="fa fa-file-o"></i> 
							</button> 
						</td>
						<td style="font-size:10px;">{{dato.producto}}</td>
						<td> <input type="hidden" v-model="dato.codunidad">{{dato.unidad}} </td>
						<td>
							<input type="number" step="1" class="netix-input number" v-model.number="dato.cantidad" v-on:keyup="netix_calcular(dato)" min="1" required>
						</td>
						<td> 
							<input type="number" step="0.01" class="netix-input number" v-model.number="dato.precio" v-on:keyup="netix_calcular(dato,3)" min="0.01" required>
						</td>
						<td>
							<input type="number" step="0.01" class="netix-input number" v-if="dato.calcular==0" v-model.number="dato.subtotal" readonly>
							<input type="number" step="0.01" class="netix-input number" v-if="dato.calcular!=0" v-model.number="dato.subtotal" v-on:keyup="netix_subtotal(dato)" required>
						</td>
						<td> 
							<button type="button" class="btn btn-danger btn-xs" style="margin-bottom:-1px;" v-on:click="netix_deleteitem(index,dato)">
								<i class="fa fa-trash-o"></i> 
							</button> 
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div class="netix_body_row">
			<div class="col-md-3">
				<button type="button" class="btn btn-info btn-block"> <b>SUBTOTAL: S/. {{totales.subtotal}}</b> </button>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-warning btn-block"> <b>IGV: S/. {{totales.igv}}</b> </button>
			</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-info btn-block"> <b>TOTAL PEDIDO S/. {{totales.importe}}</b> </button>
			</div>
			<div class="col-md-2">
				<button type="submit" class="btn btn-success btn-block" v-bind:disabled="estado==1"> <b>GUARDAR PEDIDO</b> </button>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-danger btn-block" v-on:click="netix_atras()"> <b>ATRAS</b> </button>
			</div>
		</div>
	</form>
	
	<div id="modal_itemdetalle" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" align="center">
				<div class="modal-header"> 
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;">DETALLE DEL ITEM DEL PEDIDO</b> </h4> 
				</div>
				<div class="modal-body" style="height: 380px;">
					<h4 align="center">
						{{itemdetalle.producto}} <br> <br> <span class="label label-warning">UNIDAD: {{itemdetalle.unidad}}</span> 
					</h4> <hr>

					<h6>DESCRIPCION DEL ITEM DEL PEDIDO</h6>
					<textarea class="form-control" v-model="itemdetalle.descripcion" rows="3" maxlength="250"></textarea>
					<div align="center"> <br>
						<button type="button" class="btn btn-success" v-on:click="netix_cerrar_itemdetalle()">
							GUARDAR Y CERRAR
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

<script src="<?php echo base_url();?>netix/netix_pedidos/nuevo.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script>
	var pantalla = jQuery(document).height(); var detalle = pantalla - 310;
	$(".detalle").slimScroll(
		{position:'right',size:"15px", color:'#98a6ad',wheelStep:10,height:detalle+"px"}
	);
	$("#reportes_modal").css({height: pantalla - 65});

	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>