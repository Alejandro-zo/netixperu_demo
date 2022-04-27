<div id="netix_notas">
	<div class="netix_header">
		<div class="row netix_header_title">
			<input type="hidden" id="formato" value="<?php echo $_SESSION['netix_formato'];?>">
			
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE NOTAS DE CREDITO REGISTRADAS</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVA NOTA</button>
			    <button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER</button>
			    <button type="button" class="btn btn-primary" v-on:click="netix_imprimir()"> <i class="fa fa-print"></i> IMPRIMIR </button>
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
							<th width="10px">DOCUMENTO</th>
							<th>RAZON SOCIAL</th>
							<th width="90px">FECHA</th>
							<th>TIPO</th>
							<th width="10px">COMPROBANTE</th>
							<th width="10px">C.REFERENCIA</th>
							<th width="10px">IMPORTE</th>
							<th>DESCRIPCION</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="dato in datos">
							<td v-if="dato.estado!=0"> 
								<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codkardex)"> 
							</td>
							<td v-if="dato.estado==0" style="padding:15px;"></td>
							<td>{{dato.documento}}</td>
							<td>{{dato.cliente}}</td>
							<td>{{dato.fechacomprobante}}</td>
							<td>{{dato.tipo}}</td>
							<td>{{dato.seriecomprobante}}-{{dato.nrocomprobante}}</td>
							<td>{{dato.seriecomprobante_ref}}-{{dato.nrocomprobante_ref}}</td>
							<td>S/&nbsp;{{dato.importe}}</td>
							<td>{{dato.descripcion}}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>

		<div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog" style="width:100%;margin:0px;">
				<div class="modal-content" align="center" style="border-radius:0px">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
							<i class="fa fa-times-circle"></i> 
						</button>
						<h4 class="modal-title">
							<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"];?> </b>
						</h4>
					</div>
					<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
						<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>
<script src="<?php echo base_url();?>netix/netix_notas/index.js"> </script>