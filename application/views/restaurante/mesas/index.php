<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5><b>LISTA DE MESAS - <?php echo $_SESSION["netix_sucursal"];?></b></h5> </div>
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

	<div class="netix_body">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="row">
				<div class="col-md-2 col-xs-6" v-for="dato in datos">
				    <div class="x_panel netix-libre" style="border:2px solid #ccc;">
				    	<h4><b>MESA</b></h4>
				    	<h3>{{dato.nromesa}}</h3>
				    	<div class="netix_radio_1"> 
				        	<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codmesa)"> 
				        </div>
				    	<p style="margin:0px;">{{dato.ambiente}}</p>
				    </div>
				</div>
			</div>
		</div> <hr>

		<?php include("application/views/netix/netix_paginacion.php");?>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_datos.js"> </script>