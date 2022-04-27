<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12"> <h5> <b>REPORTE DE PEDIDOS</b> </h5> </div>
		</div>
		<div class="row">
			<div class="col-md-3">
				<h5> <b>REPORTE POR PRODUCTO</b> </h5> 
			</div>
			<div class="col-md-5">
				<input type="text" class="form-control" v-model="campos.buscar" placeholder="BUSCAR PRODUCTOS">
			</div>
			<div class="col-md-4">
				<button type="button" class="btn btn-success" v-on:click="buscar_producto_pedidos()">
					<i class="fa fa-search"></i> BUSCAR
				</button>
				<button type="button" class="btn btn-warning" v-on:click="pdf_producto_pedidos()">
					<i class="fa fa-print"></i> PDF
				</button>
				<button type="button" class="btn btn-info" v-on:click="excel_producto_pedidos()">
					<i class="fa fa-cloud"></i> EXCEL
				</button>
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<div class="table-responsive" style="height: 180px; overflow-y:auto;">
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>CODIGO</th>
						<th>PRODUCTO</th>
						<th>UNIDAD</th>
						<th>RAZON SOCIAL CLIENTE</th>
						<th>CANTIDAD</th>
						<th>PRECIO DESC.</th>
						<th>TOTAL DESC.</th>
						<th>PRECIO CATALOGO</th>
						<th>TOTAL CATALOGO</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="dato in productos">
						<td>{{dato.codigo}}</td>
						<td>{{dato.producto}}</td>
						<td>{{dato.unidad}}</td>
						<td>{{dato.cliente}}</td>
						<td>{{dato.cantidad}}</td>
						<td>{{dato.preciounitario}}</td>
						<td><b>{{dato.subtotal}}</b></td>
						<td>{{dato.preciorefunitario}}</td>
						<td><b>{{dato.subtotalref}}</b></td>
					</tr>
				</tbody>
				<tfoot>
					<tr v-for="dato in totales_productos">
						<th colspan="4">TOTAL</th>
						<th>{{dato.cantidad}}</th> <th></th>
						<th>{{dato.total}}</th> <th></th>
						<th>{{dato.totalref}}</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div class="netix_header" style="margin-top:5px;">
		<div class="row">
			<div class="col-md-3">
				<h5> <b>REPORTE POR CLIENTE</b> </h5> 
			</div>
			<div class="col-md-5">
				<select class="form-control selectpicker ajax" id="codpersona" required data-live-search="true">
					<option value="0">LISTA GENERAL - TODAS LAS PERSONAS</option>
				</select>
			</div>
			<div class="col-md-4">
				<button type="button" class="btn btn-success" v-on:click="buscar_cliente_pedidos()">
					<i class="fa fa-search"></i> BUSCAR
				</button>
				<button type="button" class="btn btn-warning" v-on:click="pdf_cliente_pedidos()">
					<i class="fa fa-print"></i> PDF
				</button>
				<button type="button" class="btn btn-info" v-on:click="excel_cliente_pedidos()">
					<i class="fa fa-cloud"></i> EXCEL
				</button>
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<div class="table-responsive" style="height: 180px; overflow-y:auto;">
			<div v-for="dato in datos">
				<h5><b>CLIENTE: {{dato.razonsocial}}</b></h5>
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>CODIGO</th>
							<th>PRODUCTO</th>
							<th>UNIDAD</th>
							<th>CANTIDAD</th>
							<th>PRECIO DESC.</th>
							<th>TOTAL DESC.</th>
							<th>PRECIO CATALOGO</th>
							<th>TOTAL CATALOGO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="d in dato.pedidos">
							<td>{{d.codigo}}</td>
							<td>{{d.producto}}</td>
							<td>{{d.unidad}}</td>
							<td>{{d.cantidad}}</td>
							<td>{{d.preciounitario}}</td>
							<td><b>{{d.subtotal}}</b></td>
							<td>{{d.preciorefunitario}}</td>
							<td><b>{{d.subtotalref}}</b></td>
						</tr>
					</tbody>
					<tfoot>
						<tr v-for="d in dato.totales">
							<th colspan="3">TOTAL</th>
							<th>{{d.cantidad}}</th> <th></th>
							<th>{{d.total}}</th> <th></th>
							<th>{{d.totalref}}</th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_reportes/pedidos.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>