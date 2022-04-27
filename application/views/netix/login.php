<!DOCTYPE html>
<html lang="en">
    <?php include("netix_css.php"); ?>

    <body style="background: url('<?php echo base_url();?>public/img/netix_login.jpg') no-repeat fixed; background-size: cover;">
		<div class="col-md-8 hidden-xs netix_portada">
			<div>
				<img src="<?php echo base_url();?>public/img/netix_logo.png" style="height:100px;">
				<h2 class="netix_title"><span>S</span>ISTEMA <span>C</span>OMERCIAL <span>N</span>ETIX <span>P</span>ERÚ</h2>
				<h3 class="netix_subtitle">UN CONTROL DE TU FACTURACIÓN ELECTRÓNICA SUNAT</h3>
			</div>
		</div>

		<div class="col-md-4 netix_login">
			<img src="<?php echo base_url()?>public/img/netix_control.png" style="width:100%;"> <br>
			<h2 class="netix_titulologin"><span>N</span>ETIX <span>L</span>OGIN </h2>

			<section class="login_content" id="netix_login">
	        	<form id="form_login" onsubmit="return netix_login()">
		            <div class="netix_formlogin">
		            	<label>NOMBRE DE USUARIO</label>
		            	<input type="text" class="form-control" id="netix_usuario" placeholder="" autocomplete="off" required autofocus="true">
		            	<label>CLAVE DE USUARIO</label>
			            <input type="password" class="form-control" id="netix_clave" placeholder="" required>

			            <div id="netix_cargando" style="display:none;">
			            	<img src="<?php echo base_url()?>public/img/netix_loading.gif" style="width:70px;"> <br>
			            </div>
			            <div id="netix_mensaje" style="display:none;">
			            	<h5 align="center"> <b>USUARIO O CLAVE NO EXISTE</b> </h5>
			            </div>
			            <button type="submit" class="btn btn-success btn-block"> INICIAR SESION </button>
		            </div>
	        	</form>
	        </section>

	        <img src="<?php echo base_url()?>public/img/netix_logo.png" style="width:50px;"> <br>
	        <p style="font-size:12px;color:#888888;padding: 10px 0px;">TODOS LOS DERECHOS RESERVADOS © GRUPO NETIX </p>
		</div>

		<script src="<?php echo base_url();?>public/js/jquery.min.js"></script>
		<script src="<?php echo base_url();?>public/js/bootstrap.js"></script>
		<script> var url = "<?php echo base_url();?>";</script>
		<script src="<?php echo base_url();?>netix/netix_login.js"> </script>
    </body>
</html>