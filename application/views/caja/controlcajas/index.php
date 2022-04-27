<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12">
				<input type="hidden" id="estadocaja" value="<?php echo $_SESSION['netix_codcontroldiario'];?>">
				<?php 
					$fecha = explode("-", $_SESSION["netix_fechaproceso"]);
				?>
				<h5> <b><?php echo $_SESSION["netix_caja"];?> AL DIA <?php echo $fecha[2]." / ".$fecha[1]." / ".$fecha[0];?></b> </h5> 
			</div>
		</div>
	</div> <br>
	
	<div class="netix_body">
		<div class="row">
			<div class="col-md-4 col-xs-12">
				<div class="animated flipInY col-xs-12">
					<div class="alert alert-success netix_caja_alert" role="alert">
						<strong>SALDO INICIAL <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h4> <b>EN CAJA:</b> S/. <?php echo round($caja[0]["saldoinicialcaja"],2);?> </h4>
						<h4> <b>EN BANCO:</b> S/. <?php echo round($caja[0]["saldoinicialbanco"],2);?> </h4>
					</div>

	                <div class="x_panel netix_caja_alert" style="padding:25px 10px 10px 10px !important">
	                	<input type="hidden" id="f_arqueo" value="<?php echo date('Y-m-d');?>">

						<button type="button" class="btn btn-success btn-block btn-lg" v-on:click="pdf_arqueo_caja()">
							<b><i class="fa fa-print"></i> ARQUEO ACTUAL</b>
						</button>
						<button type="button" class="btn btn-warning btn-block btn-lg" v-on:click="pdf_arqueo_excel()">
							<b><i class="fa fa-download"></i> EXCEL ARQUEO ACTUAL</b>
						</button>

						<a href="<?php echo base_url();?>netix/w/caja/arqueos" class="btn btn-primary btn-block btn-lg">
							<b> CIERRES DE CAJA ANTERIORES</b>
						</a>
						<button type="button" class="btn btn-danger btn-block btn-lg" v-on:click="netix_cerrarcaja()">
							<b> CERRAR CAJA</b>
						</button>
	                </div>
				</div>

				<!-- <div class="col-xs-12">
		            <div class="x_panel">
		            	<div class="table-responsive">
			                <table class="table table-striped">
								<thead>
									<tr>
										<th>COMPROBANTE</th>
										<th>INGRESOS</th>
										<th>EGRESOS</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										foreach ($comprobantes as $key => $value) { ?>
											<tr>
												<td><?php echo $value["descripcion"];?></td>
												<td><?php echo $value["ingresos"];?></td>
												<td><?php echo $value["egresos"];?></td>
											</tr>
										<?php }
									?>
								</tbody>
			                </table>
		                </div>
		            </div>
		        </div> -->
			</div>

			<div class="col-md-8 col-xs-12">
				<?php
					$sc_ingresos = 0; $sc_egresos = 0; $sc_actual = 0; $sb_ingresos = 0; $sb_egresos = 0; $sb_actual = 0;
					$sc_ingresos = $saldocaja["ingresos"]; $sc_egresos = $saldocaja["egresos"]; $sc_actual = $saldocaja["total"];
					$sb_ingresos = $saldobanco["ingresos"]; $sb_egresos = $saldobanco["egresos"]; $sb_actual = $saldobanco["total"];

					$sc_actual = $sc_actual + $caja[0]["saldoinicialcaja"];
					$sb_actual = $sb_actual + $caja[0]["saldoinicialbanco"];
				?>

				<div class="animated flipInY col-md-4 col-xs-12">
					<div class="alert alert-warning netix_caja_alert" role="alert">
						<strong>INGRESOS <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h4> <b>CAJA:</b>  S/. <?php echo round($sc_ingresos,2);?> </h4>
						<h4> <b>BANCO:</b>  S/. <?php echo round($sb_ingresos,2);?> </h4>
					</div>
				</div>
				<div class="animated flipInY col-md-4 col-xs-12">
					<div class="alert alert-danger netix_caja_alert" role="alert">
						<strong>EGRESOS <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h4> <b>CAJA:</b>  S/. <?php echo round($sc_egresos,2);?> </h4>
						<h4> <b>BANCO:</b>  S/. <?php echo round($sb_egresos,2);?> </h4>
					</div>
				</div>
				<div class="animated flipInY col-md-4 col-xs-12">
					<div class="alert alert-success netix_caja_alert" role="alert">
						<strong>SALDO&nbsp;ACTUAL <br> <i class="fa fa-dollar" style="font-size:40px;"></i> </strong>
						<h4> <b>CAJA:</b>  <b id="saldo_actual">S/. <?php echo round($sc_actual,2);?></b> </h4>
						<h4> <b>BANCO:</b>  S/. <?php echo round($sb_actual,2);?> </h4>
					</div>
				</div>

				<div class="col-xs-12">
		        	<div class="x_panel">
		              	<div class="table-responsive">
			                <table class="table table-striped" style="margin-bottom:0px !important;">
								<thead>
									<tr>
										<th>FORMA DE PAGO</th>
										<th>TRANSACCIONES</th>
										<th>INGRESOS</th>
										<th>EGRESOS</th>
										<th>TOTAL</th>
									</tr>
								</thead>
								<tbody>
									<?php $item = 0; $total = 0; $neto = 0;
										foreach ($tipopagos as $key => $value) { 
											$item = $item + 1;
											$total = $total + round(($value["ingresos"] - $value["egresos"]),2); 
											if ($item == 1) {
												$neto = round(($value["ingresos"] - $value["egresos"]),2);
											} ?>
											<tr>
												<td> <b><?php echo $value["descripcion"];?></b> </td>
												<td><?php echo $value["transacciones"];?></td>
												<td><?php echo $value["ingresos"];?></td>
												<td><?php echo $value["egresos"];?></td>
												<td>S/. <?php echo round(($value["ingresos"] - $value["egresos"]),2);?></td>
											</tr>
										<?php }
									?>
								</tbody>
								<tbody>
									<tr>
										<th colspan="4">TOTAL NETO</th>
										<th>S/. <?php echo number_format($total,2);?></th>
									</tr>
									<tr>
										<th colspan="4">TOTAL CAJA (SOLO TRANSACCIONES EN EFECTIVO)</th>
										<?php
											$total = $neto + $caja[0]["saldoinicialcaja"];
											if ($total <= 0) {
												$color = "color:#d43f3a;font-size:20px;";
											}else{
												$color = "color:#06B8AC;font-size:20px;";
											}
										?>
										<th> <b style="<?php echo $color;?>">S/. <?php echo number_format($total,2);?></b> </th>
									</tr>
								</tbody>
			                </table>
			            </div>
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

<script src="<?php echo base_url();?>netix/netix_caja/controlcaja.js"> </script>
<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
</script>