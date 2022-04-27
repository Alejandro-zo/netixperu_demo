<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>VENTAS POR DESPACHAR / COMPRAS POR RECIBIR</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
	    		<button type="button" class="btn btn-info" v-on:click="netix_operacion(20)"> <i class="fa fa-exchange"></i> DESPACHAR VENTA </button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_operacion(2)"> <i class="fa fa-shopping-cart"></i> RECIBIR COMPRA </button>
			    <button type="button" class="btn btn-success" v-on:click="netix_buscarkardex()"> <i class="fa fa-search"></i> BUSCAR KARDEX </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>
	
	<div class="netix_body">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th>OPERACION</th>
							<th>DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th>FECHA</th>
							<th>TIPO</th>
							<th>COMPROBANTE</th>
							<th>IMPORTE</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td> <input type="radio" v-if="dato.estado!=0" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex,dato.codmovimientotipo)"> </td>
							<td>
								<span class="label label-danger" v-if="dato.codmovimientotipo==2">COMPRA</span>
								<span class="label label-warning" v-else="dato.codmovimientotipo==20">VENTA</span>
							</td>
							<td>{{dato.documento}}</td>
							<td>{{dato.razonsocial}}</td>
							<td>{{dato.fechakardex}}</td>
							<td>{{dato.tipo}}</td>
							<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
							<td>S/. {{dato.importe}}</td>
						</tr>
					</tbody>
				</table>
			</div> <hr>

			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>

	<div id="modal_buscarkardex" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:2px;">BUSCAR EL KARDEX DE COMPRA O VENTA</b> </h4>
				</div>
				<div class="modal-body">
					<form id="formulario_filtro" v-on:submit.prevent="netix_filtrar()">
						<div class="row form-group">
							<div class="col-xs-12">
								<label>CLIENTE DE LA VENTA O PROVEEDOR DE LA COMPRA</label>
				    			<select class="form-control selectpicker ajax" name="codpersona" v-model="filtro.codpersona" id="codpersona" required data-live-search="true"> </select>
							</div>
						</div>
						<div class="row form-group">
							<div class="col-md-6 col-xs-12">
								<label>SERIE COMPROBANTE</label>
				    			<input type="text" class="form-control" name="seriecomprobante" v-model.trim="filtro.seriecomprobante" required maxlength="4" autocomplete="off">
							</div>
							<div class="col-md-6 col-xs-12">
								<label>NRO COMPROBANTE</label>
				    			<input type="text" class="form-control" name="seriecomprobante" v-model.trim="filtro.nrocomprobante" required maxlength="10" autocomplete="off">
							</div>
						</div>

						<div class="form-group" align="center">
							<button type="submit" class="btn btn-success">BUSCAR COMPROBANTE</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
						</div>

						<div class="row form-group">
							<div class="col-xs-12">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th> </th>
											<th>OPER</th>
											<th>FECHA</th>
											<th>TIPO</th>
											<th>COMPROBANTE</th>
											<th>IMPORTE</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="dato in filtros">
											<td style="padding-bottom:9px !important;">
												<button type="button" class="btn btn-success btn-xs" v-on:click="netix_seleccionar_1(dato.codkardex)"> <i class="fa fa-check"></i> </button>
											</td>
											<td>
												<span class="label label-danger" v-if="dato.codmovimientotipo==2">COMPRA</span>
												<span class="label label-warning" v-else="dato.codmovimientotipo==20">VENTA</span>
											</td>
											<td>{{dato.fechakardex}}</td>
											<td>{{dato.tipo}}</td>
											<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
											<td>S/. {{dato.importe}}</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_despachos/index.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>