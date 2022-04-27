<div id="netix_datos">
	<div class="netix_header" style="background: #1ab394;">
		<div class="row netix_header_title"> 
			<div class="col-md-12 col-xs-12 text-center">
				<h3 style="color:#fff;"> <b> <?php echo $info[0]["razonsocial"];?></b> </h3>
			</div> 
		</div>
	</div>

	<div class="row netix_row">
	    <div class="col-md-7 col-xs-12"> <br>
	    	<div class="netix_body_row" align="center">
				<img class="img-responsive" src="<?php echo base_url();?>public/img/empresa/<?php echo $info[0]['foto']?>" style="height:250px;"> <br> <br>

				<button type="button" class="btn btn-success" v-on:click="netix_editar()">
					<i class="fa fa-edit"></i> CONFIGURAR FACTURACIÓN <span class="hidden-xs">ELECTRONICA - EMPRESA</span>
				</button>
				<button type="button" class="btn btn-warning" v-on:click="netix_copia()">
					<i class="fa fa-database"></i> <span class="hidden-xs">GENERAR</span> COPIA SEGURIDAD
				</button>
			</div>
	    </div>

	    <div class="col-md-5 col-xs-12"> <br>
			<div class="x_panel" style="padding:10px 20px;">
				<input type="hidden" id="codempresa" value="<?php echo $_SESSION["netix_codempresa"];?>">
				<h5> <b><?php echo $info[0]["nombrecomercial"];?></b> </h5>
				<ul class="list-unstyled user_data">
					<li> <i class="fa fa-map-marker"></i> <?php echo $info[0]["direccion"];?> </li>
					<li> <i class="fa fa-briefcase"></i> NUMERO RUC: <?php echo $info[0]["documento"];?> </li>
					<li> <i class="fa fa-google-plus"></i> EMAIL: <?php echo $info[0]["email"];?> </li>
					<li> <i class="fa fa-phone"></i> TELF./CEL.: <?php echo $info[0]["telefono"];?> </li>
					<li class="m-top-xs">
						<i class="fa fa-external-link"></i> UBIGEO: 
						<?php echo $empresa[0]["departamento"]."-".$empresa[0]["provincia"]."-".$empresa[0]["distrito"]." (".$empresa[0]["ubigeo"].")";?>
					</li>
				</ul>

				<h5> <b>DATOS DE LA FACTURACION</b> </h5>
				<ul class="list-unstyled user_data">
					<li> <i class="fa fa-user-o"></i> USUARIO SOL: <?php echo $service[0]["usuariosol"];?> </li>
					<li> <i class="fa fa-user-o"></i> CLAVE SOL: <?php echo $service[0]["clavesol"];?> </li>
					<li> <i class="fa fa-google-plus"></i> EMAIL ENVIO: <?php echo $service[0]["envioemail"];?> </li>
					<li> <i class="fa fa-key"></i> EMAIL CLAVE: <?php echo $service[0]["claveemail"];?> </li>
					<li> <i class="fa fa-lock"></i> CLAVE CERTIFICADO: <?php echo $service[0]["certificado_clave"];?> </li>
					<li>
						<a download="<?php echo $service[0]['certificado_pfx'];?>" href="<?php echo base_url();?>sunat/certificado/<?php echo $service[0]['certificado_pfx'];?>" style="color:#337ab7"> 
							<b><i class="fa fa-cloud-download"></i> DESCARGAR CERTIFICADO</b>
						</a>
					</li>
				</ul>
				<h5 class="text-danger"> <b>ARCHIVOS PEM: <?php echo $pen;?></b> </h5>

				<?php 
					if ($service[0]["sunatose"]==0) { ?>
						<span class="label label-success">SERVICIO: SUNAT</span> 
					<?php }else{ ?>
						<span class="label label-success">SERVICIO: OSE</span> 
					<?php }
				?>

				<?php 
					if ($service[0]["serviceweb"]==0) { ?>
						<span class="label label-primary">ESTADO: PRODUCCION</span> 
					<?php }else{ ?>
						<span class="label label-primary">ESTADO: BETA HOMOLOGACION</span> 
					<?php }
				?> <br> <br>
			</div>
	    </div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_empresa/index.js"> </script>