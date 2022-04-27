<div id="netix_inventario">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-3 col-xs-12" style="padding-top:5px;"> <h5>PRODUCTOS DEL INVENTARIO</h5> </div>

			<div class="col-md-4 col-xs-12" style="padding-top:5px;">
				<input type="text" class="form-control" v-model="buscar" placeholder="BUSCAR PRODUCTO . . .">
			</div>
			<div class="col-md-3" style="padding-top:5px;"> 
				<select class="form-control" v-model="tiporeporte">
					<option value="0">LISTA GENERAL</option>
					<option value="1">PRODUCTOS CON STOCK</option>
					<option value="2">PRODUCTOS SIN STOCK</option>
				</select>
			</div>
			<div class="col-md-2 col-xs-12" style="padding-top:5px;text-align:right;">
				<button type="button" class="btn btn-success" v-on:click="netix_pdf()"><i class="fa fa-print"></i> PDF</button>
				<button type="button" class="btn btn-warning" v-on:click="netix_excel()">EXCEL</button>
			</div>
		</div>
	</div> <br>

	<div class="netix_body_row">
		<input type="hidden" name="codregistro" v-model="campos.codregistro">
		<div class="table-responsive scroll-netix-view" style="height:calc(100vh - 220px);padding:0px; overflow:auto;">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th width="5px">#</th>
						<th width="10px">CODIGO</th>
						<th>PRODUCTO</th>
						<th width="10px">UNIDAD</th>
						<th>MARCA</th>
						<th width="10%">CANTIDAD</th>
						<th width="10%">P. COSTO</th>
						<th width="10%">P. VENTA</th>
						<th width="10%">IMPORTE</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato, index) in buscar_productos">
						<td>{{index + 1}}</td>
						<td> <input type="text" class="netix-input-inv" v-model="dato.codigo" readonly> </td>
						<td> <input type="text" class="netix-input-inv" v-model="dato.descripcion" readonly> </td>
						<td>
							<input type="hidden" class="netix-input-inv" v-model="dato.codunidad" readonly>
							<input type="text" class="netix-input-inv" v-model="dato.unidad" readonly>
						</td>
						<td width="10px">{{dato.marca}}</td>
						<td> 
							<input type="number" class="netix-input-inv" v-model="dato.cantidad" readonly> 
						</td>
						<td> 
							<input type="number" class="netix-input-inv" v-model="dato.preciocosto" readonly>
						</td>
						<td> <input type="number" class="netix-input-inv" v-model="dato.precioventa" readonly> </td>
						<td> <input type="number" class="netix-input-inv" v-model="dato.importe" readonly> </td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="8"> <center> <b>TOTAL COSTO (S/. IMPORTE VALORIZADO)</b> </center> </td>
						<td> <input type="number" class="netix-input-inv" v-model="campos.importe" readonly> </td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
	
	<div class="text-center"> <br>
		<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR VISTA</button>
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

<script src="<?php echo base_url();?>netix/netix_inventarios/inventario.js"></script>
<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>