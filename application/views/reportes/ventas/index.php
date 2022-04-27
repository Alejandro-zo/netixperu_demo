<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-2 col-xs-12"> <h5>REPORTE VENTAS</h5> </div>
			<div class="col-md-1" style="text-align:right;padding-top:5px;"> <label>SUCURSAL</label> </div>
			<div class="col-md-2">
				<select class="form-control input-sm" v-model="campos.codsucursal" v-on:change="netix_cajas()">
					<option value="0">TODAS SUCURSALES</option>
					<?php 
						foreach ($sucursales as $key => $value) { ?>
							<option value="<?php echo $value["codsucursal"];?>"><?php echo $value["descripcion"];?></option>	
						<?php }
					?>
				</select>
			</div>
			<div class="col-md-2">
				<select class="form-control input-sm" v-model="campos.codcaja" disabled>
					<option value="0">TODAS CAJAS</option>
					<option v-for="dato in cajas" v-bind:value="dato.codcaja"> {{dato.descripcion}} </option>
				</select>
			</div>
			<div class="col-md-1" style="text-align:right;padding-top:5px;"> <label> ALMACEN</label> </div>
			<div class="col-md-2">
				<select class="form-control input-sm" v-model="campos.codalmacen">
					<option value="0">TODOS ALMACENES</option>
					<option v-for="dato in almacenes" v-bind:value="dato.codalmacen"> {{dato.descripcion}} </option>
				</select>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-success btn-block btn-sm" v-on:click="ver_grafico()">GRAFICO</button>
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-danger btn-block btn-sm" v-on:click="mas_reportes()"><i class="fa fa-print"></i> MAS</button>
			</div>
		</div>
		<div class="row" style="padding:5px 0px;">
			<div class="col-md-1"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> DESDE</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechadesde" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-1"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> HASTA</label></div>
			<div class="col-md-2">
				<input type="text" class="form-control input-sm datepicker" id="fechahasta" value="<?php echo date('Y-m-d');?>" autocomplete="off">
			</div>
			<div class="col-md-1"> <label style="padding-top:6px;">VENDEDOR</label> </div>
			<div class="col-md-3">
				<select class="form-control input-sm" v-model="campos.codvendedor">
					<option value="0">TODOS LOS VENDEDORES</option>
					<?php 
						foreach ($vendedores as $key => $value) { ?>
							<option value="<?php echo $value["codpersona"];?>"><?php echo $value["razonsocial"];?></option>
						<?php }
					?>
				</select>
			</div>
			<div class="col-md-2">
				<select class="form-control input-sm" v-model="campos.codusuario">
					<option value="0">TODOS LOS USUARIOS</option>
					<?php 
						foreach ($usuarios as $key => $value) { ?>
							<option value="<?php echo $value["codusuario"];?>"><?php echo $value["usuario"];?></option>
						<?php }
					?>
				</select>
			</div>
		</div>
		<div class="row" style="padding:5px 0px;">
			<div class="col-md-1"> <label style="padding-top:6px;"><i class="fa fa-user"></i> CLIENTE</label> </div>
			<div class="col-md-5">
				<select class="form-control selectpicker ajax" name="codpersona" v-model="campos.codpersona" id="codpersona" required data-live-search="true">
					<option value="0">SELECCIONAR CLIENTE</option>
				</select>
			</div>
			<div class="col-md-2">
				<button type="button" class="btn btn-info btn-block btn-sm" v-on:click="pdf_productos_vendidos()">
					PRODUCTOS VENDIDOS
				</button>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-success dropdown-toggle btn-sm" type="button" aria-expanded="false">VENTAS VENDEDOR/USUARIO <span class="caret"></span>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li> <br> </li>
						<li> <a v-on:click="pdf_ventas_vendedor_resumen()"><i class="fa fa-print"></i> VENTAS RESUMEN</a> </li>
						<li class="divider"></li>
						<li> <a v-on:click="pdf_ventas_vendedor()"><i class="fa fa-print"></i> VENTAS DETALLADO</a> <br> </li>
					</ul>
	            </div>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle btn-sm" type="button" aria-expanded="false">VENTAS CLIENTE <span class="caret"></span>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li> <br> </li>
						<li> <a v-on:click="pdf_ventas_cliente()"><i class="fa fa-print"></i> VENTAS RESUMEN</a> </li>
						<li class="divider"></li>
						<li> <a v-on:click="pdf_ventas_cliente_detallado()"><i class="fa fa-print"></i> VENTAS DETALLADO</a> <br> </li>
					</ul>
	            </div>
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<div class="x_panel" style="height:100%; text-align:center;" id="reporte_ventas">
			<h4 style="padding-top:150px;"> 
				<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i> <br> <br> REPORTE DE VENTAS
			</h4>
		</div>
	</div>

	<div id="modal_reportes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b>GENERAR REPORTES DE VENTAS POR COMPROBANTE</b> </h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-2"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> DESDE</label></div>
						<div class="col-md-4">
							<input type="text" class="form-control input-sm datepicker" id="fechadesde_mas" value="<?php echo date('Y-m-d');?>" autocomplete="off">
						</div>
						<div class="col-md-2"> <label style="padding-top:6px;"><i class="fa fa-calendar"></i> HASTA</label></div>
						<div class="col-md-4">
							<input type="text" class="form-control input-sm datepicker" id="fechahasta_mas" value="<?php echo date('Y-m-d');?>" autocomplete="off">
						</div>
					</div> <br>

					<div class="row">
						<div class="col-md-8" style="height:260px;overflow-y:scroll;">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>MARCAR</th>
										<th>TIPO COMPROBANTE</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										foreach ($comprobantes as $key => $value) { ?>
											<tr>
												<td align="center">
													<input type="checkbox" name="comprobantes" value="<?php echo $value['codcomprobantetipo'];?>" style="height:20px;width:20px;" checked>
												</td>
												<td><?php echo $value["descripcion"];?></td>
											</tr>
										<?php }
									?>
								</tbody>
							</table>
						</div>
						<div class="col-md-4">
							<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="pdf_reporte_ventas(1)">
								REPORTE DE VENTAS
							</button>
							<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="pdf_reporte_ventas_det(1)">
								VENTAS DETALLADO
							</button>
							<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="pdf_reporte_ventas(0)">
								VENTAS ANULADAS
							</button>
							<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="pdf_reporte_ventas_det(0)">
								ANULADAS DETALLADO
							</button>

							<h5 class="text-center"><b>FORMATOS SUNAT</b></h5>
							<button type="button" class="btn btn-warning btn-sm btn-block" v-on:click="pdf_contable_ventas()">FORMATO CONTABLE <br> VENTAS PDF</button>
							<button type="button" class="btn btn-warning btn-sm btn-block" v-on:click="excel_contable_ventas()">FORMATO CONTABLE <br> VENTAS EXCEL</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>public/js/highcharts.js"> </script>

<script> 
	var campos = {"codsucursal":'<?php echo $_SESSION['netix_codsucursal'];?>',"codcaja":0,"codalmacen":0,"codpersona":0,"codvendedor":0,"codusuario":0,"fechadesde":"","fechahasta":"","estado":1};

	var pantalla = jQuery(document).height(); $("#reporte_ventas").css({height: pantalla - 250});
</script>
<script src="<?php echo base_url();?>netix/netix_reportes/ventas.js"> </script>
<script src="<?php echo base_url();?>netix/netix_personas_2.js"> </script>

<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD'}); </script>