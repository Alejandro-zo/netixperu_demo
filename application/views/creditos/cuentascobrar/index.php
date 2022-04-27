<div id="netix_creditos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> 
				<input type="hidden" id="sessioncaja" value="<?php echo $_SESSION["netix_codcontroldiario"];?>">
				<h5>
					<span class="label label-danger" v-if="sessioncaja==0">LA CAJA NO ESTA APERTURADA</span> 
					<b>CUENTAS POR COBRAR EN EL SISTEMA</b> 
				</h5> 
			</div>
		</div>
	    <div class="row">
	    	<div class="col-md-8 netix_header_button">
		    	<button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO CREDITO</button>
		    	<button type="button" class="btn btn-info" v-on:click="netix_cobranza()"> <i class="fa fa-money"></i> COBRANZA </button>
			    <button type="button" class="btn btn-warning" v-on:click="netix_historial()"> <i class="fa fa-file-o"></i> HISTORIAL </button>
			    <button type="button" class="btn btn-success" v-on:click="netix_persona()"> <i class="fa fa-plus-circle"></i> NUEVO CLIENTE</button>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
		    </div>
	    </div>
	</div> <br>

	<div class="netix_body_card">
		<input type="hidden" id="netix_opcion" value="1">

		<div class="netix_cargando" v-if="cargando">
			<i class="fa fa-spinner fa-spin"></i> <h5>CARGANDO DATOS</h5>
		</div>

		<div v-if="!cargando">
			<div class="row">
				<div class="col-md-4 col-xs-12" v-for="dato in datos">
					<div class="profile_details">
						<div class="well profile_view">
							<div class="left col-xs-8" style="margin-top:0px; height:140px; overflow-y: auto;">
								<h5> <b>{{dato.razonsocial}}</b> </h5>
								<p> 
									<strong style="color:#13a89e">DOCUMENTO: {{dato.documento}}</strong> 
									{{dato.nombrecomercial}}
								</p>
								<ul class="list-unstyled">
									<li style="font-size:10px;"><i class="fa fa-building"></i> DIRECCION: {{dato.direccion}} </li>
									<li><i class="fa fa-phone"></i> TELF: {{dato.telefonos}} </li>
								</ul>
							</div>
							<div class="right col-xs-4 text-center">
								<img src="<?php echo base_url();?>public/img/personas/default.png" class="img-circle img-responsive">
							</div>

							<div class="col-xs-12 bottom text-center" style="padding:0px;">
		                        <div class="col-xs-12 col-md-2">
		                          	<input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codpersona)" v-if="sessioncaja==1"> 
		                        </div>
		                        <div class="col-xs-12 col-md-10">
		                          	<h6> <b>CREDITOS PENDIENTES: {{dato.creditos}}</b> </h6>
		                        </div>
		                    </div>
						</div>
					</div>
				</div>
			</div> 
			<?php include("application/views/netix/netix_paginacion.php");?>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_creditos/cuentascobrar.js"> </script>