<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12">
				<h5>LISTA DE CREDITOS POR COBRAR</h5> 
			</div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
	    		<button type="button" class="btn btn-warning" v-on:click="netix_editar('CLIENTE')">
			        <i class="fa fa-edit"></i> CAMBIAR DE CLIENTE
			    </button>
			    <button type="button" class="btn btn-info" v-on:click="netix_imprimir()">
			        <i class="fa fa-print"></i> IMPRIMIR CREDITO
			    </button>
			    <button type="button" class="btn btn-success" v-on:click="pdf_creditos()">
			        <i class="fa fa-print"></i> CREDITOS PENDIENTES
			    </button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>

	<div class="netix_body">
		<div class="netix_cargando" v-if="cargando">
			<i class="fa fa-spinner fa-spin"></i> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="table-responsive">
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="5px"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th>DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th>F.&nbsp;CREDITO</th>
							<th>F.&nbsp;&nbsp;&nbsp;VENCE</th>
							<th>COMPROBANTE</th>
							<th>IMPORTE</th>
							<th>INTERES</th>
							<th>SALDO</th>
							<th width="5px">ESTADO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td> <input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codcredito)"> </td>
							<td>{{dato.documento}}</td>
							<td>{{dato.razonsocial}}</td>
							<td>{{dato.fechacredito}}</td>
							<td>{{dato.fechavencimiento}}</td>
							<td>{{dato.comprobante}}</td>
							<td> <b style="font-size:17px;">{{dato.importe}}</b> </td>
							<td>{{dato.interes}}</td>
							<td> <b style="font-size:17px;color:#a94442">{{dato.saldo}}</b> </td>
							<td>
								<span class="label label-danger" v-if="dato.estado==1">PENDIENTE</span>
								<span class="label label-success" v-if="dato.estado==2">CANCELADO</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/reportes.js"> </script>