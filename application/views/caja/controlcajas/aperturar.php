<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12">
				<input type="hidden" id="estadocaja" value="<?php echo $_SESSION['netix_codcontroldiario'];?>">
				<?php 
					$fecha = explode("-", $_SESSION["netix_fechaproceso"]);
				?>
				<h5> 
					<span class="label label-danger">CAJA CERRADA</span>
					<b><?php echo $_SESSION["netix_caja"];?> AL DIA <?php echo $fecha[2]." / ".$fecha[1]." / ".$fecha[0];?></b>
				</h5> 
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<div class="row">
			<div class="col-md-7 col-xs-12">
				<div class="col-xs-1" style="padding-top: 7px;">
					<label>FECHA</label>
	        	</div>
	        	<div class="col-xs-4">
	        		<input type="text" id="fecha" class="form-control datepicker" autocomplete="off" value="<?php echo date('Y-m-d');?>" />
	        	</div>
	        	<div class="col-xs-4">
	        		<button type="button" class="btn btn-success btn-block" v-on:click="netix_aperturar()" v-bind:disabled="estado==1">
	        			<b>APERTURAR CAJA</b>
	        		</button>
	        	</div>
	        	<div class="col-xs-3">
					<a href="<?php echo base_url();?>netix/w/caja/arqueos" class="btn btn-primary btn-block"><i class="fa fa-undo"></i> CIERRES</a>
	        	</div>
				<div class="x_panel col-xs-12" id="netix_graficocaja" style="height:300px;">
					<h4 align="center" style="padding-top:130px;">CARGANDO GRAFICO</h4>
				</div>
				<div class="animated flipInY col-md-6 col-xs-12">
					<div class="alert alert-warning netix_caja_alert" role="alert">
						<strong>SALDO CAJA <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h1> <b>S/. <?php echo number_format(round($saldocaja["total"],2) ,2);?> </b> </h1>
					</div>
				</div>
				<div class="animated flipInY col-md-6 col-xs-12">
					<div class="alert alert-success netix_caja_alert" role="alert">
						<strong>SALDO BANCO <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h1> <b>S/. <?php echo number_format(round($saldobanco["total"],2) ,2);?> </b> </h1>
					</div>
				</div>
			</div>

			<div class="col-md-5 col-xs-12">
				<div class="x_panel">
	              	<h2 align="center" style="font-size:25px;"> <b>GENERAR REPORTES DE CAJA</b> </h2> <hr>
	              	<div class="x_content">
	              		<div class="row form-group">
	              			<div class="col-md-2"></div>
							<div class="col-md-4 col-xs-12">
								<label>FECHA DESDE</label>
					        	<input type="text" id="f_desde" class="form-control datepicker" autocomplete="off" value="<?php echo date('Y-m-d');?>" />
							</div>
							<div class="col-md-4 col-xs-12">
								<label>FECHA HASTA</label>
					        	<input type="text" id="f_hasta" class="form-control datepicker" autocomplete="off" value="<?php echo date('Y-m-d');?>" />
							</div>
						</div>

						<div class="row" align="center"> <br>
	              			<button type="button" class="btn btn-warning btn-lg" v-on:click="pdf_movimientos()">IMPRIMIR MOVIMIENTOS</button>
	              		</div> <hr>

	              		<h2 align="center" style="font-size:25px;"> <b>ARQUEO DE CAJA</b> </h2> <hr>
	              		<div class="row form-group">
	              			<div class="col-md-4"></div>
							<div class="col-md-4 col-xs-12">
								<label>FECHA APERTURA</label>
					        	<input type="text" id="f_arqueo" class="form-control datepicker" autocomplete="off" value="<?php echo date('Y-m-d');?>" />
							</div>
						</div>

						<div class="row" align="center"> <br>
	              			<button type="button" class="btn btn-success btn-lg" v-on:click="pdf_arqueo()">IMPRIMIR ARQUEO</button>
	              		</div> <hr>
	              	</div>
	            </div>
			</div>
		</div>
	</div>

	<div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:100%;margin:0px;">
			<div class="modal-content" align="center" style="border-radius:0px">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title">
						<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"]." - ".$_SESSION["netix_sucursal"];?> </b>
					</h4>
				</div>
				<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
					<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>public/js/highcharts.js"> </script>
<script src="<?php echo base_url();?>netix/netix_caja/controlcaja.js"> </script>
<script> 
	$(".datepicker").datetimepicker({format:'YYYY-MM-DD'});
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>