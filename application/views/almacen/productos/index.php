<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>LISTA DE PRODUCTOS</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO </button>
		    	<button type="button" class="btn btn-info" v-on:click="netix_ver()"> <i class="fa fa-file"></i> VER </button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
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
			<div class="row" style="margin-bottom: 5px;">
				<div class="col-md-2 col-xs-6" v-for="dato in datos">
					<div class="netix_card netix_card_view text-center">
						<div class="card_title">
							<button type="button" class="btn btn-success btn-xs btn-block" style="margin:0px;">
								PRECIO S/. <b>{{dato.precio}}</b>
							</button>
							<img v-bind:src="`<?php echo base_url();?>public/img/productos/${dato.foto}`" style="width:100%;height:70px;">
						</div>
						<div class="scroll-netix-view" style="height:95px;overflow:auto;overflow-x:hidden;">
							<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codproducto)"><br>
							<span class="label label-danger" v-if="dato.stock<=0">STOCK: {{dato.stock}} {{dato.unidad}} </span>
							<span class="label label-info" v-if="dato.stock>0">STOCK: {{dato.stock}} {{dato.unidad}} </span>
							<h6> <b>{{dato.codigo}}-{{dato.descripcion}}</b> </h6>
						</div>
					</div>
			    </div>
			</div>
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_almacen/productos_index.js"> </script>
	              