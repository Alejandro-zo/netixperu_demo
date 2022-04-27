<!DOCTYPE html>
<html lang="en">
	<?php include("netix_css.php"); ?>

	<body class="nav-md">
		<div class="container body">
      		<div class="main_container">
      			<div class="top_nav" style="margin-left: 0px;">
					<div class="nav_menu" style="padding:0px;background: #2f4050;">
						<div class="navbar nav_title text-center hidden-xs" style="background-image: url('<?php echo base_url();?>public/img/skins/skin-primary.png');">
					    	<a href="<?php echo base_url();?>" style="font-size:20px;color:#fff;letter-spacing: 2px;padding-top:10px;font-weight:bold;">NETIX PERÚ</a>
					    </div>
						<nav>
							<div class="nav toggle hidden-xs hidden-lg">
								<a id="menu_toggle"><i class="fa fa-bars"></i></a>
							</div>

							<ul class="nav navbar-nav hidden-xs">
								<li style="margin:20px 16px 10px 16px;font-size: 20px;font-weight: bold;color:#1ab394;letter-spacing: 2px;">
									<p>EMPRESA: <?php echo $_SESSION["netix_empresa"];?></p>
								</li>
							</ul>

						  	<ul class="nav navbar-nav navbar-right">
								<li style="margin: 10px;">
									<a href="javascript:;" class="user-profile dropdown-toggle netix_profile" data-toggle="dropdown" style="color: #fff !important;">
										<i class="fa fa-sign-out"></i> USUARIO: <?php echo strtoupper($_SESSION["netix_usuario"]);?></b> <span class=" fa fa-angle-down"></span>
									</a>
									<ul class="dropdown-menu dropdown-usermenu pull-right">
										<li class="active"> <a href="<?php echo base_url();?>netix/netix_logout"> <i class="fa fa-sign-out pull-right"></i> CERRAR SESION</a></li>
									</ul>
								</li>
								<li class="hidden-lg vissible-xs" style="margin: 10px;">
									<p style="color:#fff;font-weight:bold;font-size:16px;padding-top:12.5px;padding-right:5px;">NETIX SISTEMA</p> 
								</li>
						  	</ul>
						</nav>
					</div>
				</div>

		        <div class="right_col" id="netix_administrar" style="margin-left: 0px;">
                  	<div class="netix_header text-center">
						<h3 class="netix_administrar">
							<span>N</span>ETIX UN <span>S</span>ISTEMA <span>C</span>OMERCIAL PARA <span>P</span>YMES EN EL <span>P</span>ERÚ
						</h3> 
					</div> <br>
              		<?php 
              			foreach ($info as $value) { ?>
              				<div class="col-md-3 col-xs-12">
		                        <div class="netix_card netix_card_view text-center">
									<div class="card_title">
										<h4> <b><?php echo $value["descripcion"];?></b> </h4>
										<p> <?php echo $value["direccion"];?> </p>
										<a href="#"> <img src="<?php echo base_url();?>public/img/sucursal.png"> </a>
									</div>
									<div class="card_content">
										<h5 class="text-center">ADMINISTRAR SUCURSAL</h5>
										<div class="form-group">
											<label>SELECCIONAR ALMACEN</label>
											<select class="form-control" id="netix_almacen_<?php echo $value['codsucursal'];?>">
												<?php 
													foreach ($value["almacenes"] as $val) { ?>
														<option value="<?php echo $val["codalmacen"];?>"><?php echo $val["descripcion"];?></option>
													<?php }
												?>
											</select>
										</div>

										<div class="form-group">
											<label>SELECCIONAR CAJA</label>
											<select class="form-control" id="netix_caja_<?php echo $value['codsucursal'];?>">
												<?php 
													foreach ($value["cajas"] as $val) { ?>
														<option value="<?php echo $val["codcaja"];?>"><?php echo $val["descripcion"];?></option>
													<?php }
												?>
											</select>
										</div>

										<div class="form-group"> <br>
											<button type="button" class="btn btn-success btn-block" v-on:click="administrar(<?php echo $value["codsucursal"];?>)">ADMINISTRAR SUCURSAL</button>
										</div>
									</div>
		                        </div>
		                    </div>
              			<?php }
              		?>
		      	</div>
		    </div>
      	</div>

	    <?php include("netix_js.php"); ?>
	    <script src="<?php echo base_url();?>netix/netix_ministrar.js"> </script>
	</body>
</html>