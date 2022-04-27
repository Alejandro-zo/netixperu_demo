<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12">
				<h5>LISTA DE SOCIOS - CON CREDITOS CONVENIO</h5> 
			</div>
		</div>
	    <div class="row">
	    	<div class="col-md-2 col-xs-12">
	    		<label style="padding-top: 5px"><i class="fa fa-users"></i> SELECCIONE EMPRESA</label>
	    	</div>
	    	<div class="col-md-4 col-xs-12">
		    	<select class="form-control" v-model="codpersona" v-on:change="netix_buscar()">
		    		<option value="0">TODOS</option>
		    		<?php 
		    			foreach ($empresas_convenio as $key => $value) { ?>
		    				<option value="<?php echo $value["codpersona"];?>"><?php echo $value["razonsocial"];?></option>
		    			<?php }
		    		?>
		    	</select>
		    </div>
		    <div class="col-md-3 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
		    <div class="col-md-3 netix_header_button">
			    <button type="button" class="btn btn-success" v-on:click="netix_buscar()"><i class="fa fa-search"></i></button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_resumen()"><i class="fa fa-print"></i> RESUMEN</button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_detallado()"><i class="fa fa-print"></i> DETALLADO</button>
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
							<th width="5px">SUC</th>
							<th>CONVENIO CON</th>
							<th>DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th>F.&nbsp;CREDITO</th>
							<th>F.&nbsp;&nbsp;&nbsp;VENCE</th>
							<th>COMPROBANTE</th>
							<th>IMPORTE</th>
							<th>INTERES</th>
							<th>SALDO</th>
							<th width="5px">TIPO</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td>00{{dato.codsucursal}}</td>
							<td>{{dato.convenio}}</td>
							<td>{{dato.documento}}</td>
							<td>{{dato.razonsocial}}</td>
							<td>{{dato.fechacredito}}</td>
							<td>{{dato.fechavencimiento}}</td>
							<td>{{dato.comprobante}}</td>
							<td> <b style="font-size:17px;">{{dato.importe}}</b> </td>
							<td>{{dato.interes}}</td>
							<td> <b style="font-size:17px;color:#a94442">{{dato.saldo}}</b> </td>
							<td>
								<span class="label label-success" v-if="dato.tipo==1">POR COBRAR</span>
								<span class="label label-danger" v-if="dato.tipo==2">POR PAGAR</span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/convenios.js"> </script>