<div id="netix_operacion">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-7 col-xs-12"> <h5>PRODUCTOS PARA LA VENTA - RECETAS</h5> </div>
			<div class="col-md-4 col-xs-12">
				<input type="text" class="form-control" v-model="buscar" placeholder="BUSCAR PRODUCTO . . .">
			</div>
		</div>

		<div class="row">
			<div class="col-md-1"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> DESDE</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechadesde" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-1"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> HASTA</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechahasta" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="consumo_total()"> 
					<i class="fa fa-print"></i> CONSUMO INGREDIENTES TOTALIZADO
				</button>
			</div>
			<div class="col-md-3">
				<button type="button" class="btn btn-warning btn-sm btn-block" v-on:click="consumo_fechas()"> 
					<i class="fa fa-print"></i> CONSUMO INGREDIENTES FECHAS
				</button>
			</div>
		</div> <br>
	</div> <br>

	<div class="netix_body">
		<div class="table-responsive lista" style="overflow-y:auto;height:200px;">
			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th width="3%"> # </th>
						<th width="40%">PRODUCTO</th>
						<th width="5%">S/.&nbsp;COSTO</th>
						<th width="5%">S/.&nbsp;VENTA</th>
						<th width="40%">VER RECETA</th>
						<th width="7%">RECETA</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato, index) in buscar_productos">
						<td>{{dato.nro}}</td>
						<td>{{dato.descripcion}} <b style="color:#06B8AC">{{dato.unidad}}</b></td>
						<td><b>{{dato.preciocosto}}</b></td>
						<td><b>{{dato.precioventa}}</b></td>
						<td style="padding:0px;">
							<div style="background:#FAFAFA; font-size:13px;padding:5px 10px;height:50px;overflow-y:auto;">
								<ul class="list-unstyled text-left">
									<li v-for="d in dato.receta"><i class="fa fa-check text-success"></i> CANT. {{d.cantidad}} <strong>{{d.producto}} - {{d.unidad}}</strong></li>
								</ul>
							</div>
						</td>
						<td>
							<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_receta(dato)">RECETA</button>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<div id="modal_receta" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:100%;margin:0px;">
			<div class="modal-content" align="center" style="border-radius:0px">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:3px;" id="titulo_receta"></b> </h4>
				</div>
				<div class="modal-body" id="receta_modal" style="height:400px;padding:10px;">
					<div class="row">
						<div class="col-md-6 col-xs-12">
							<h4><b>LISTA DE PRODUCTOS EN LA RECETA</b></h4> <hr>
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>PRODUCTO</th>
										<th>UNIDAD</th>
										<th width="10px">CANTIDAD</th>
										<th><i class="fa fa-trash-o"></i></th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(dato,index) in detalle">
										<td>{{dato.producto}}</td>
										<td>{{dato.unidad}}</td>
										<td>
											<input type="number" step="0.001" class="netix-input number" v-model.number="dato.cantidad" min="0.001" required>
										</td>
										<td> 
											<button type="button" class="btn btn-danger btn-xs" style="margin-bottom:-1px;" v-on:click="netix_deleteitem(index,dato)">
												<i class="fa fa-trash-o"></i> 
											</button>
										</td>
									</tr>
								</tbody>
							</table>
							<div class="text-center">
								<button type="button" class="btn btn-success" v-on:click="netix_guardar()" v-bind:disabled="estado==1">GUARDAR RECETA</button>
								<button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
							</div>
						</div>
						<div class="col-md-6 col-xs-12">
							<div class="x_panel" style="height:450px;overflow-y:auto;">
								<div id="lista_productos"></div>
							</div>
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
						<b style="letter-spacing:4px;"><?php echo $_SESSION["netix_empresa"];?> </b>
					</h4>
				</div>
				<div class="modal-body" id="reportes_modal" style="height:450px;padding:0px;">
					<iframe id="netix_pdf" src="" style="width:100%; height:100%; border:none;"> </iframe>
				</div>
			</div>
		</div>
	</div>

</div>

<script>
	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65}); 
	var productos = pantalla - 220; $(".lista").css("height",productos+"px");
	$("#receta_modal").css({height: pantalla - 70});
</script>

<script src="<?php echo base_url();?>netix/netix_restaurante/recetas.js"></script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>