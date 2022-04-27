<div id="netix_ingresos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE INGRESOS ALMACEN</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO INGRESO</button>
		    	<button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER INGRESO </button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_trasferencias()">
			        <span class="badge bg-green"> <b style="color:#fff;">VER</b> </span> 
			        <i class="fa fa-exchange"></i> TRASFERENCIAS
			    </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>
	
	<div class="netix_body">
		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th>TIPO MOVIMIENTO</th>
							<th>FECHA</th>
							<th>COMPROBANTE</th>
							<th>COMPROBANTE REF.</th>
							<th>IMPORTE</th>
							<th>ESTADO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
							<td> <input type="radio" v-if="dato.estado!=0" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex)"> </td>
							<td>{{dato.tipomovimiento}}</td>
							<td>{{dato.fechakardex}}</td>
							<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
							<td>{{dato.tipo}} ({{dato.seriecomprobante_ref}} - {{dato.nrocomprobante_ref}})</td>
							<td>S/. {{dato.importe}}</td>
							<td>
								<span class="label label-danger" v-if="dato.estado==0">ANULADO</span>
								<span class="label label-warning" v-if="dato.estado==1">ACTIVO</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="modal_transferencias" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content" v-show="transferencias==1" align="center">
				<div class="modal-header">
					<h4 class="modal-title" style="letter-spacing:1px;">
						<i class="fa fa-exchange" style="font-size:80px"></i> <br> <b>LISTA DE TRANFERENCIAS A ESTE ALMACEN</b> 
					</h4>
				</div>
				<div class="modal-body" style="height:270px;">
					<div class="col-md-6 col-xs-12" v-for="dato in listatransferencias">
						<div class="x_panel">
							<h4>
								<span class="label label-danger" style="color:#fff;">FECHA TRANSFERIDO: {{dato.fechakardex}}</span>
							</h4> <hr>
							<div style="text-align:left;">
								<h5>ALMACEN ORIGEN: {{dato.almacen}}</h5>
								<h5>DOCUMENTO: {{dato.seriecomprobante}} - {{dato.nrocomprobante}} | <i>ID: {{dato.codkardex}}</i></h5>
								<h5>DOCUMENTO REFERENCIA: {{dato.seriecomprobante_ref}} - {{dato.nrocomprobante_ref}}</h5>
								<h5 align="center"> 
									<span class="label label-warning">TOTAL IMPORTE TRANSFERIDO: S/. {{dato.importe}}</span>
								</h5>
								<h5 align="center"> 
									<button type="button" class="btn btn-success" v-on:click="netix_detalle(dato)">VER DETALLE</button> 
								</h5>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
				</div>
			</div>

			<div class="modal-content" v-show="transferencias==0" align="center">
				<div class="modal-header"> <h4 class="modal-title"> 
					<b>LISTA DE PRODUCTOS <span class="label label-warning">{{texto_transferencia}}</span></b> </h4> 
				</div>
				<div class="modal-body" style="height:450px;">
					<form id="formulario_trans" class="form-horizontal" v-on:submit.prevent="netix_guardartransferencia()">
						<table class="table table-bordered">
							<thead>
								<tr align="center" >
									<th width="40%">PRODUCTO</th>
									<th width="15%">UNIDAD</th>
									<th width="15%">CANTIDAD</th>
									<th width="15%">PRECIO</th>
									<th width="15%">SUBTOTAL</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(dato, index) in detalletransferencia">
									<td>
										<input type="hidden" class="netix-input-inv" v-model="dato.codproducto" readonly>
										<input type="text" class="netix-input-inv" v-model="dato.producto" readonly> 
									</td>
									<td>
										<input type="hidden" class="netix-input-inv" v-model="dato.codunidad" readonly>
										<input type="text" class="netix-input-inv" v-model="dato.unidad" readonly>
									</td>
									<td> 
										<input type="number" step="0.001" class="netix-input-inv" v-model="dato.cantidad" v-on:keyup="netix_calcular(dato)"  min="0.001" required> 
									</td>
									<td> 
										<input type="number" step="0.01" class="netix-input-inv" v-model="dato.preciounitario" v-on:keyup="netix_calcular(dato)" min="0.01" required> 
									</td>
									<td> 
										<input type="number" step="0.01" class="netix-input-inv" v-model="dato.subtotal" readonly> 
									</td>
								</tr>
							</tbody>
						</table>
						<div class="modal-footer">
							<button type="submit" class="btn btn-success" v-bind:disabled="estado_envio==1">ACEPTAR TRANSFERENCIA</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_almacen/ingresos.js"> </script>