<div class="col-md-3 scroll-netix left_col" style="height:100vh;overflow:auto;overflow-x:hidden;">
	<div class="left_col scroll-view">

	    <div class="navbar nav_title text-center hidden-xs" style="background-image: url('<?php echo base_url();?>public/img/skins/skin-primary.png');">
	    	<a href="<?php echo base_url();?>netix/w/" style="font-size:20px;color:#fff;letter-spacing: 2px;padding-top:10px;font-weight:bold;">NETIX PERÚ</a>
	    </div>

	    <div class="hidden-lg hidden-md text-center vissible-xs" style="background-image: url('<?php echo base_url();?>public/img/skins/skin-primary.png'); width: 100%;margin-bottom:5px;">
	    	<img src="<?php echo base_url();?>public/img/netix_logo.png" style="height:50px;margin-bottom:8px;">
	    	<div class="row">
	    		<div class="col-xs-9"> <b style="color:#fff;font-size:16px"><?php echo $_SESSION["netix_empresa"];?> </b> </div>
		    	<div class="col-xs-3" style="margin-bottom: 8px;"> 
		    		<a class="menu_toggle" style="font-size:15px;color:#fff;background:#1ab394;padding: 4px 12px;border-radius: 3px;"><i class="fa fa-times"></i></a>
		    	</div>
	    	</div>
	    </div>

	    <div class="clearfix"></div>

	    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			<div class="menu_section">
				<ul class="nav side-menu">
					<?php 
	                    foreach ($netix_modulos as $key => $value) { 
	                        if(count($value["submodulos"])>0){ ?>
	                            <li>
	                            	<a>
										<i class="<?php echo $value["icono"];?>"></i> <?php echo $value["descripcion"];?> <span class="fa fa-chevron-down"></span>
									</a>
	                                <ul class="nav child_menu">
	                                    <?php 
	                                        foreach ($value["submodulos"] as $val) { ?>
	                                        	<li>
	                                        		<a href="<?php echo base_url().'netix/w/'.$val["url"];?>"><?php echo $val["descripcion"];?></a>
	                                        	</li>
	                                        <?php }
	                                    ?>
	                                </ul>
	                            </li>
	                        <?php } ?>
	                    <?php }
	                ?>
				</ul>
			</div>
	    </div>
	    <div class="sidebar-footer hidden-small hidden-xs">
			<a href="<?php echo base_url();?>netix/w/administracion/configuraciones">
				<span class="fa fa-cog" aria-hidden="true"></span> CONFIGURACIONES
			</a>
		</div>
	</div>
</div>

<div class="top_nav">
	<div class="nav_menu">
		<nav>
			<div class="nav toggle hidden-lg hidden-md vissible-xs">
				<a class="menu_toggle"><i class="fa fa-bars"></i></a>
			</div>

			<ul class="nav navbar-nav hidden-xs">
				<li class="netix_cabecera">
					<p style="color:#1ab394;padding-left: 10px;letter-spacing: 2px;"> <?php echo substr($_SESSION["netix_empresa"],0,50);?></p>
				</li>
			</ul>

		  	<ul class="nav navbar-nav navbar-right">
				<li>
					<a href="javascript:;" class="user-profile dropdown-toggle netix_profile" data-toggle="dropdown">
						<i class="fa fa-sign-out"></i> <?php echo strtoupper($_SESSION["netix_usuario"]);?></b> <span class=" fa fa-angle-down"></span>
					</a>
					<ul class="dropdown-menu dropdown-usermenu pull-right">
						<li> <a href="<?php echo base_url();?>"> <i class="fa fa-home pull-right"></i> SUCURSALES</a></li>
						<li> <a href="<?php echo base_url();?>netix/netix_perfil"> <i class="fa fa-user pull-right"></i> MI PERFIL</a></li>
						<li class="active"> <a href="<?php echo base_url();?>netix/netix_logout"> <i class="fa fa-sign-out pull-right"></i> CERRAR SESION</a></li>
					</ul>
				</li>
				<li role="presentation" class="dropdown">
					<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-envelope"></i> <span class="badge bg-white">0</span>
					</a>
					<ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
						<li>
							<a>
								<span>
									<span>FACTURACION ELECTRONICA</span> <span class="time">2019-08-31</span>
								</span>
								<span class="message">
									No tienes comprobantes pendientes
								</span>
							</a>
						</li>
						<!-- <li>
							<div class="text-center">
								<a> <strong>See All Alerts</strong> <i class="fa fa-angle-right"></i> </a>
							</div>
						</li> -->
					</ul>
				</li>
				<li class="hidden-xs">
					<p style="color:#999c9e;margin:0px;padding-top:4px;font-weight:600;padding-right: 15px"> SUCURSAL: <?php echo $_SESSION["netix_sucursal"];?> <br> CAJA: <?php echo $_SESSION["netix_caja"];?> / <?php echo $_SESSION["netix_almacen"];?> </p>
				</li>
				<li class="hidden-lg vissible-xs">
					<p style="color:#999c9e;font-weight:bold;font-size:16px;padding-top:12.5px;padding-right:5px;">NETIX SISTEMA</p> 
				</li>
		  	</ul>
		</nav>
	</div>
</div>