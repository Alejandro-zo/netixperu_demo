<div id="netix_datos"> 
	
	<div class="col-md-12"> <br>
		<div class="row" align="center">
            <div class="animated flipInY col-md-3 col-xs-12">
                <div class="alert alert-danger">
                	<h2 style="color:#fff; font-size:25px;"> 
                		<b> SU CAJA ESTÁ <br> <i class="fa fa-credit-card" style="font-size:60px;"></i> <br> {{totales.estado}} </b>
                	</h2>
                </div>
            </div>

            <div class="animated flipInY col-md-3 col-xs-12">
                <div class="alert alert-warning">
                  	<h2 style="color:#fff; font-size:25px;"> 
                		<b> TOTAL EN CAJA <br> <i class="fa fa-dollar" style="font-size:60px;"></i> <br> S/. {{totales.caja}} </b>
                	</h2>
                </div>
            </div>

            <div class="animated flipInY col-md-3 col-xs-12">
                <div class="alert alert-success">
                  	<h2 style="color:#fff; font-size:25px;"> 
                		<b> TOTAL BANCO <br> <i class="fa fa-dollar" style="font-size:60px;"></i> <br> S/. {{totales.banco}} </b>
                	</h2>
                </div>
            </div>

            <div class="animated flipInY col-md-3 col-xs-12">
                <div class="alert alert-info">
                  	<h2 style="color:#fff; font-size:25px;"> 
                		<b> TOTAL GENERAL <br> <i class="fa fa-credit-card" style="font-size:60px;"></i> <br> S/. {{totales.general}} </b>
                	</h2>
                </div>
            </div>
    	</div>
	</div>

	<!-- <div class="col-md-6 col-xs-12">
        <div class="x_panel">
          	<div class="x_title">
            	<h5 style="font-size:15px;"><b>MONTO DE INGRESOS</b></h5> <div class="clearfix"></div>
          	</div>
          	<div class="x_content" id="netix_ingresos" style="height:250px;"> 
          		<h4 align="center"> <br> <i class="fa fa-spinner fa-spin" style="font-size:50px;"></i> <br> <br> CARGANDO TIPO PAGOS</h4>
          	</div>
        </div>
    </div>

    <div class="col-md-6 col-xs-12">
        <div class="x_panel">
          	<div class="x_title">
            	<h5 style="font-size:15px;"><b>MONTO DE EGRESOS</b></h5> <div class="clearfix"></div>
          	</div>
          	<div class="x_content" id="netix_egresos" style="height:250px;">
          		<h4 align="center"> <br> <i class="fa fa-spinner fa-spin" style="font-size:50px;"></i> <br> <br> CARGANDO TIPO PAGOS</h4>
          	</div>
        </div>
    </div> -->

    <div class="col-md-3 col-xs-12">
        <div class="x_panel">
          	<div class="x_title">
            	<h5 style="font-size:15px;"><b>STOCK MINIMO PRODUCTOS</b></h5> <div class="clearfix"></div>
          	</div>
          	<div class="x_content">
          		<?php 
          			foreach ($stockminimos as $key => $value) { ?>
          				<article class="media event">
          					<a class="pull-left date"> <p class="month">Stock</p> <p class="day">S</p> </a>
			              	<div class="media-body">
				                <a class="title"><?php echo $value["descripcion"];?></a> 
				                <p> <b>MARCA:</b> <?php echo $value["marca"];?></p>
                        <?php 
                          if ($value["stock"]<=0) { ?>
                            <p class="label label-danger">STOCK <?php echo $value["stock"]." ".$value["unidad"];?></p>
                          <?php }else{ ?>
                            <p class="label label-success">STOCK <?php echo $value["stock"]." ".$value["unidad"];?></p>
                          <?php }
                        ?>
			              	</div>
			            </article>
          			<?php }
          		?>
          	</div>
        </div>
    </div>

    <div class="col-md-3 col-xs-12">
        <div class="x_panel">
          	<div class="x_title">
            	<h5 style="font-size:15px;"><b>STOCK MAXIMO PRODUCTOS</b></h5> <div class="clearfix"></div>
          	</div>
          	<div class="x_content">
	            <?php 
          			foreach ($stockmaximos as $key => $value) { ?>
          				<article class="media event">
          					<a class="pull-left date"> <p class="month">Stock</p> <p class="day">S</p> </a>
			              	<div class="media-body">
				                <a class="title"><?php echo $value["descripcion"];?></a> 
				                <p> <b>MARCA:</b> <?php echo $value["marca"];?></p>
                        <?php 
                          if ($value["stock"]<=0) { ?>
                            <p class="label label-danger">STOCK <?php echo $value["stock"]." ".$value["unidad"];?></p>
                          <?php }else{ ?>
                            <p class="label label-success">STOCK <?php echo $value["stock"]." ".$value["unidad"];?></p>
                          <?php }
                        ?>
			              	</div>
			            </article>
          			<?php }
          		?>
          	</div>
        </div>
    </div>

    <div class="col-md-3 col-xs-12">
    	<div class="x_panel">
	        <div class="x_title">
	          	<h5 style="font-size:15px;"><b>MEJORES CLIENTES</b></h5> <div class="clearfix"></div>
	        </div>
	        <ul class="list-unstyled scroll-view">
	        	<?php 
          			foreach ($clientes as $key => $value) { ?>
          				<li class="media event">
				            <a class="pull-left border-green profile_thumb"> <i class="fa fa-user green"></i> </a>
				            <div class="media-body">
				              	<a class="title"> <?php echo $value["razonsocial"];?> </a>
				              	<p><strong>VENTAS S/. <?php echo round($value["importe"],2);?> </strong></p> 
				              	<p> <small>TOTAL VENTAS <?php echo $value["cantidad"];?></small> </p>
				            </div>
			          	</li>
          			<?php }
          		?>
	        </ul>
        </div>
    </div>

    <div class="col-md-3 col-xs-12">
    	<div class="x_panel">
	        <div class="x_title">
	          	<h5 style="font-size:15px;"><b>MEJORES PROVEEDORES</b></h5> <div class="clearfix"></div>
	        </div>
	        <ul class="list-unstyled scroll-view">
	          	<?php 
          			foreach ($proveedores as $key => $value) { ?>
          				<li class="media event">
				            <a class="pull-left border-green profile_thumb"> <i class="fa fa-user green"></i> </a>
				            <div class="media-body">
				              	<a class="title"> <?php echo $value["razonsocial"];?> </a>
				              	<p><strong>COMPRAS S/. <?php echo round($value["importe"],2);?> </strong></p> 
				              	<p> <small>TOTAL COMPRAS <?php echo $value["cantidad"];?></small> </p>
				            </div>
			          	</li>
          			<?php }
          		?>
	        </ul>
        </div>
    </div>
</div>

<!-- <script src="<?php echo base_url();?>public/js/highcharts.js"> </script> -->
<script src="<?php echo base_url();?>netix/netix_empresa/dashboard.js"> </script>