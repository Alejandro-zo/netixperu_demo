<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE INVENTARIOS</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
	    		<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-database"></i> NUEVO INVENTARIO </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>

	<div class="netix_body_card">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="row">
				<div class="col-md-4 col-xs-12" v-for="dato in datos">
				    <div class="x_panel text-center">
				        <h5 v-if="dato.estado==1" class="name">{{dato.descripcion}} <br> <i class="fa fa-database" style="font-size:40px;"></i> </h5>
				        <h4 v-if="dato.estado==0" class="name">
				        	{{dato.descripcion}} <br> <br> <i class="fa fa-database" style="font-size:94px;color:#13a89e;"></i> 
				        </h4>
				        <p>
				        	<b>SUCURSAL</b> <br> {{dato.sucursal}} <br> <b>ALMACEN</b> <br> {{dato.almacen}} <br>
				        	<b>FECHA APERTURA</b> <br> {{dato.fechaapertura}} <br> <b>FECHA CIERRE</b> <br>
				        	<span v-if="dato.estado==1" class="label label-warning">ABIERTO</span> 
				        	<span  v-if="dato.estado==0">{{dato.fechaapertura}}</span>
				        </p>
				        <h3><b>IMPORTE S/. {{dato.importe_r}}</b></h3>

				        <button type="button" v-if="dato.estado==1" class="btn btn-success" v-on:click="netix_inventario(dato.codinventario)">INVENTARIO</button>
				    	<button type="button" v-if="dato.estado==1" class="btn btn-info" v-on:click="netix_verinventario(dato.codinventario)">VER</button>
				        <button type="button" v-if="dato.estado==1" class="btn btn-danger" v-on:click="netix_cerrarinventario(dato.codinventario)">
				        	CERRAR INVENTARIO
				        </button>

				        <button type="button" v-if="dato.estado!=1" class="btn btn-success" v-on:click="netix_verinventario(dato.codinventario)">VER INVENTARIO</button>
				        <button type="button" v-if="dato.estado!=1" class="btn btn-warning" v-on:click="netix_editarinventario(dato.codinventario)"><i class="fa fa-edit"></i> EDITAR</button>
				    </div>
				</div>
			</div> <br>

			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="editar_inventario" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content" align="center">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"><b>EDITAR INVENTARIO 000{{editar.codinventario}}</b></h4>
				</div>
				<div class="modal-body">
					<button type="button" class="btn btn-success" v-on:click="netix_masproductos()">ACTUALIZAR NUEVOS PRODUCTOS REGISTRADOS</button>
					<form class="form-horizontal" v-on:submit.prevent="netix_guardar_editar()">
						<div class="form-group">
							<h5><b>BUSCAR PRODUCTO</b></h5>
							<select class="form-control selectpicker ajax" name="codproducto" id="codproducto" required data-live-search="true" v-on:change="netix_unidades()">
			    				<option value="">SELECCIONE PRODUCTO</option>
			    			</select>
						</div>
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>PRODUCTO</th>
										<th>UNIDAD</th>
										<th>CANTIDAD</th>
										<th>S/. COSTO</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(dato, index) in editardetalle">
										<td>{{dato.descripcion}}</td>
										<td>{{dato.unidad}}</td>
										<td>
											<input type="number" class="form-control input-sm" step="0.01" v-model="dato.cantidad" required>
										</td>
										<td>{{dato.preciocosto}}</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="form-group text-center">
							<button type="submit" class="btn btn-success"><i class="fa fa-save"></i> GUARDAR EDITAR</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_inventarios/index.js"> </script>
<script src="<?php echo base_url();?>netix/netix_almacen/buscar.js"> </script>