<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE PROVEEDORES</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO </button>
			    <button type="button" class="btn btn-info" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
			    <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
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
				<div class="col-md-3 col-xs-12" v-for="dato in datos">
					<div class="netix_card netix_card_view text-center">
						<div class="card_title">
							<h4 style="height:70px;font-size:15px;"> <b>{{dato.razonsocial}}</b> </h4>
							<a href="#"> <img src="<?php echo base_url();?>public/img/personas/default.png"> </a>
						</div>
						<div class="card_content scroll-netix-view" style="height:120px;overflow:auto;overflow-x:hidden;">
							<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codpersona)" v-if="dato.codpersona>2">
							<h6 style="color:#1ab394"> <b>DOCUMENTO: {{dato.documento}}</b> </h6>
							<p> <i class="fa fa-building"></i> {{dato.direccion}} </p>
							<p> <i class="fa fa-phone"></i> TELF: {{dato.telefonos}} | CORREO: {{dato.email}} </p>
						</div>
					</div>
				</div>
			</div> <hr>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_datos.js"> </script>