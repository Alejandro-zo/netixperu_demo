<!DOCTYPE html>
<html lang="en">
	<?php include("netix_css.php"); ?>

	<body class="nav-md">

		<div class="container body">
      		<div class="main_container">
      			<?php include("netix_menu.php"); ?>

		        <div class="right_col" id="netix_sistema"> </div>
		    </div>
      	</div>

      	<div class="compose col-md-4 col-xs-12" style="overflow-y: auto;">
			<div class="compose-header text-center">
				<h4>
					<b id="netix_tituloform"> FORMULARIO REGISTRO</b>
					<button type="button" class="close compose-close"> 
						<i class="fa fa-times" style="color:#fff;"></i> 
					</button>
				</h4>
			</div>
			<div class="compose-body" id="netix_formulario"> </div>
		</div>

	    <?php include("netix_js.php"); ?>
	    <script src="<?php echo base_url();?>netix/netix_base.js"></script>
	</body>
</html>