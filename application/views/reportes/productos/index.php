<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-5">
				<h5> <b>REPORTE GENERAL DE PRODUCTOS</b> </h5> 
			</div>
			<div class="col-md-7" style="margin-top:5px;text-align:right;">
				<button type="button" class="btn btn-warning btn-sm" v-on:click="stock_general()">
					<i class="fa fa-print"></i> PDF STOCK PRODUCTOS
				</button>
				<button type="button" class="btn btn-danger btn-sm" v-on:click="stock_valorizado()">
					<i class="fa fa-print"></i> PDF KARDEX VALORIZADO
				</button>
			</div>
		</div>

		<div class="row netix_header" style="padding:5px 0px;">
			<div class="col-md-3">
				<label>ALMACEN</label>
				<select class="form-control" v-model="campos.codalmacen">
					<?php 
						foreach ($almacenes as $key => $value) { ?>
							<option value="<?php echo $value['codalmacen'];?>"><?php echo $value["descripcion"];?></option>
						<?php } ?>
					?>
				</select>
			</div>
			<div class="col-md-3">
				<label>LINEA PRODUCTO</label>
				<select class="form-control" v-model="campos.codlinea">
					<option value="0">TODAS LAS LINEAS DE PRODUCTOS</option>
					<?php 
						foreach ($lineas as $key => $value) { ?>
							<option value="<?php echo $value['codlinea'];?>"><?php echo $value["descripcion"];?></option>
						<?php } ?>
					?>
				</select>
			</div>
			<div class="col-md-2">
				<label>STOCK PRODUCTO</label>
				<select class="form-control" v-model="campos.stock">
					<option value="0">TODOS</option>
					<option value="1">CON STOCK</option>
					<option value="2">SIN STOCK</option>
				</select>
			</div>
			<div class="col-md-2">
				<label>A LA FECHA (KARDEX)</label>
				<input type="text" class="form-control datepicker" id="fecha" v-model="campos.fecha" v-on:blur="netix_fecha()">
			</div>
			<div class="col-md-1">
				<label style="margin-top:5px;">CTRL&nbsp;STOCK</label> <br>
				<label style="margin-top:5px;">ACTIVOS</label>
			</div>

			<div class="col-md-1">
				<input type="checkbox" style="height:20px;width:20px;" v-model="campos.controlstock"> <br>
				<input type="checkbox" style="height:20px;width:20px;" v-model="campos.estado">
			</div>
		</div>

		<div class="row netix_header" style="padding:5px 0px;">
			<div class="col-md-5">
				<input type="text" class="form-control input-sm" v-model="campos.buscar" placeholder="BUSCAR PRODUCTO . . ." v-on:keyup.13="buscar_productos()">
			</div>
			<div class="col-md-1">
				<button type="button" class="btn btn-success btn-block btn-sm" v-on:click="buscar_productos()">
					<i class="fa fa-search"></i>
				</button>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-warning dropdown-toggle btn-block btn-sm" type="button" aria-expanded="false">COMPRAS Y VENTAS <span class="caret"></span>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li> <br> </li>
						<li> <a v-on:click="compras_producto()"><i class="fa fa-search"></i> LISTA DE COMPRAS</a> </li>
						<li class="divider"></li>
						<li> <a v-on:click="ventas_producto()"><i class="fa fa-search"></i> LISTA DE VENTAS</a> <br> </li>
					</ul>
	            </div>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-info dropdown-toggle btn-block btn-sm" type="button" aria-expanded="false">PRECIOS PRODUCTOS <span class="caret"></span>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li> <br> </li>
						<li> <a v-on:click="pdf_precios()"><i class="fa fa-print"></i> PRECIOS PRODUCTOS PDF</a> </li>
						<li class="divider"></li>
						<li> <a v-on:click="pdf_precios_stock()"><i class="fa fa-file"></i> PRECIOS + STOCK PRODUCTOS PDF</a> <br> </li>
						<li class="divider"></li>
						<li> <a v-on:click="pdf_precios_stock_costo()"><i class="fa fa-file"></i> PRECIOS + STOCK COSTOS PDF</a> <br> </li>
						<li class="divider"></li>
						<li> <a v-on:click="excel_precios()"><i class="fa fa-file"></i> EXCEL PRECIOS</a> <br> </li>
					</ul>
	            </div>
			</div>
			<div class="col-md-2">
				<div class="btn-group">
					<button data-toggle="dropdown" class="btn btn-danger dropdown-toggle btn-block btn-sm" type="button" aria-expanded="false">KARDEX PRODUCTOS <span class="caret"></span>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li> <br> </li>
						<li> <a v-on:click="pdf_kardexproductos()"><i class="fa fa-print"></i> KARDEX PRODUCTOS PDF</a> </li>
						<li class="divider"></li>
						<li> <a v-on:click="excel_kardexproductos()"><i class="fa fa-file"></i> KARDEX PRODUCTOS EXCEL</a> <br> </li>
					</ul>
	            </div>
			</div>
		</div>
	</div> <br>

	<div class="netix_body">
		<div class="detalle" v-if="consultar.precios==1" style="height:150px;overflow-y:auto;">
			<div v-for="dato in datos" v-if="dato.tiene!=0">
				<h6 align="center"> <b>LINEA: {{dato.descripcion}}</b> </h6>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th style="width:5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
							<th style="width:5px;">ID</th>
							<th style="width:10px;">CODIGO</th>
							<th style="width:35%;">DESCRIPCION</th>
							<th style="width:15%;">UNIDAD</th>
							<th style="width:10%;">STOCK</th>
							<th style="width:10%;">V.X.RECOGER</th>
							<th style="width:10%;">C.X.RECOGER</th>
							<th style="width:10%;">FISICO</th>
							<th style="width:10%;">P.COSTO</th>
							<th style="width:10%;">P.MINIMO</th>
							<th style="width:10%;">P.VENTA</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="d in dato.productos">
							<td> <input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(d)"> </td>
							<td>{{d.codproducto}}</td>
							<td>{{d.codigo}}</td>
							<td>{{d.descripcion}}</td>
							<td>{{d.unidad}}</td>
							<td> 
								<button type="button" class="btn btn-success btn-xs btn-block" v-on:click="netix_kardex(d)">
									<i class="fa fa-arrow-right"></i> {{d.stock}}
								</button>
							</td>
							<td> 
								<button type="button" class="btn btn-primary btn-xs btn-block" v-on:click="netix_recoger(d,20)">
									<i class="fa fa-arrow-right"></i> {{d.ventarecogo}}
								</button>
							</td>
							<td> 
								<button type="button" class="btn btn-danger btn-xs btn-block" v-on:click="netix_recoger(d,2)">
									<i class="fa fa-arrow-right"></i> {{d.comprarecogo}}
								</button>
							</td>
							<td>{{d.fisico}}</td>
							<td>{{d.preciocosto}}</td>
							<td>{{d.preciominimo}}</td>
							<td>{{d.precioventa}}</td>
						</tr>
					</tbody>
				</table>
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

	<div id="modal_kardex" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;" id="producto_kardex"></b> </h4>
				</div>
				<div class="modal-body" style="height:450px;overflow-y:auto;padding:5px 15px;">
					<div class="row">
						<div class="col-md-1" style="padding-top:7px;"> <label>F.&nbsp;DESDE</label> </div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm datepicker" id="fechadesde_k" value="<?php echo date('Y-m-01');?>">
						</div>
						<div class="col-md-1" style="padding-top:7px;"> <label>F.&nbsp;HASTA</label> </div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm datepicker" id="fechahasta_k" value="<?php echo date('Y-m-d');?>">
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-success btn-block btn-sm" v-on:click="netix_kardex_1()">VER KARDEX</button>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-info btn-block btn-sm" v-on:click="netix_kardex_pdf()"><i class="fa fa-print"></i> PDF</button>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-warning btn-block btn-sm" v-on:click="netix_kardex_excel()"><i class="fa fa-file"></i> EXCEL</button>
						</div>
					</div>

					<table class="table table-bordered table-condensed" style="font-size:10px;">
						<thead>
							<tr>
								<th rowspan="2" width="3px"><i class="fa fa-calendar"></i></th>
								<th rowspan="2" width="70px">FECHA</th>
								<th rowspan="2">COMPROBANTE</th>
								<th rowspan="2">RAZON SOCIAL</th>
								<th colspan="3">ENTRADAS</th>
								<th colspan="3">SALIDAS</th>
								<th colspan="3">EXISTENCIAS</th>
							</tr>
							<tr>
								<th>CANTIDAD</th>
								<th>P.U.</th>
								<th>TOTAL</th>
								<th>CANTIDAD</th>
								<th>P.U.</th>
								<th>TOTAL</th>

								<th>CANTIDAD</th>
								<th>PRECIO</th>
								<th>TOTAL</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in existencias_a">
								<td colspan="10" align="center"><b>SALDO ANTERIOR</b></td>
								<td><b>{{dato.existencia_cantidad}}</b></td>
								<td><b>{{dato.existencia_precio}}</b></td>
								<td><b>{{dato.existencia_total}}</b></td>
							</tr>
							<tr v-for="dato in existencias">
								<td>
									<button type="button" class="btn btn-success btn-xs" style="margin:0px !important" v-on:click="netix_cambiar_fecha(dato)"><i class="fa fa-calendar"></i></button>
								</td>
								<td>{{dato.fechacomprobante}}</td>
								<td>{{dato.seriecomprobante}}-{{dato.nrocomprobante}}</td>
								<td style="font-size:8px;">{{dato.razonsocial}}</td>

								<td> <b v-if="dato.tipo==1">{{dato.cantidad}}</b> </td>
								<td> <b v-if="dato.tipo==1">{{dato.preciounitario}}</b> </td>
								<td> <b v-if="dato.tipo==1">{{dato.total}}</b> </td>
								<td> <b v-if="dato.tipo!=1">{{dato.cantidad}}</b> </td>
								<td> <b v-if="dato.tipo!=1">{{dato.preciounitario}}</b> </td>
								<td> <b v-if="dato.tipo!=1">{{dato.total}}</b> </td>

								<td> <b>{{dato.existencia_cantidad}}</b> </td>
								<td> <b>{{dato.existencia_precio}}</b> </td>
								<td> <b>{{dato.existencia_total}}</b> </td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_comprasventas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b style="letter-spacing:1px;" id="producto_compraventa"></b> </h4>
				</div>
				<div class="modal-body" style="height:450px;overflow-y:auto;padding:5px 15px;">
					<div class="row">
						<div class="col-md-1" style="padding-top:7px;"> <label>DESDE</label> </div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm datepicker" id="fechadesde_cv" value="<?php echo date('Y-m-01');?>">
						</div>
						<div class="col-md-1" style="padding-top:7px;"> <label>HASTA</label> </div>
						<div class="col-md-2">
							<input type="text" class="form-control input-sm datepicker" id="fechahasta_cv" value="<?php echo date('Y-m-d');?>">
						</div>
						<div class="col-md-2">
							<select class="form-control input-sm" id="codmoneda">
								<option value="0">TODOS</option>
								<?php
									foreach ($monedas as $key => $value) { ?>
										<option value="<?php echo $value["codmoneda"];?>"><?php echo $value["descripcion"];?></option>
									<?php }
								?>
							</select>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-success btn-block btn-sm" v-on:click="netix_compraventas()"><i class="fa fa-search"></i> BUSCAR</button>
						</div>
						<div class="col-md-2">
							<button type="button" class="btn btn-info btn-sm" v-on:click="netix_compraventas_pdf()"><i class="fa fa-print"></i></button>
							<button type="button" class="btn btn-warning btn-sm" v-on:click="netix_compraventas_excel()"><i class="fa fa-download"></i> EXCEL</button>
						</div>
					</div>

					<table class="table table-bordered table-condensed" style="font-size:11px;">
						<thead>
							<tr>
								<th>FECHA</th>
								<th>RAZON SOCIAL</th>
								<th>COMPROBANTE</th>
								<th>CANTIDAD</th>
								<th>P.U.</th>
								<th>TOTAL</th>
								<th>MONEDA</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in compraventas">
								<td>{{dato.fechacomprobante}}</td>
								<td>{{dato.razonsocial}}</td>
								<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
								<td>{{dato.cantidad}}</td>
								<td>{{dato.preciounitario}}</td>
								<td>{{dato.subtotal}}</td>
								<td>{{dato.moneda}}</td>
							</tr>
						</tbody>
						<tbody>
							<tr>
								<th colspan="5" align="right">TOTAL S/</th>
								<th colspan="2" id="compraventas_total">0.00</th>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_recoger" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"><b id="producto_recoger"></b></h4>
				</div>
				<div class="modal-body" style="height:450px;overflow-y:auto;padding:5px 15px;">
					<table class="table table-bordered table-condensed" style="font-size:12px;">
						<thead>
							<tr>
								<th>OPERACION</th>
								<th>DOCUMENTO</th>
								<th>RAZON SOCIAL</th>
								<th>FECHA</th>
								<th>TIPO</th>
								<th>COMPROBANTE</th>
								<th>IMPORTE</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in recoger">
								<td>
									<span class="label label-danger" v-if="dato.codmovimientotipo==2">COMPRA</span>
									<span class="label label-warning" v-else="dato.codmovimientotipo==20">VENTA</span>
								</td>
								<td>{{dato.documento}}</td>
								<td>{{dato.razonsocial}}</td>
								<td>{{dato.fechakardex}}</td>
								<td>{{dato.tipo}}</td>
								<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
								<td>S/. {{dato.importe}}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_kardex_fecha" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h5 class="modal-title"> <b style="letter-spacing:1px;" id="producto_kardex_fecha"></b> </h5>
				</div>
				<div class="modal-body" style="height:300px;overflow-y:auto;padding:5px 15px;">
					<input type="hidden" id="c_codkardex">
					<div class="form-group">
						<label>FECHA KARDEX</label>
						<input type="text" class="form-control input-sm datepicker" id="c_fechakardex" value="<?php echo date('Y-m-01');?>">
					</div>
					<div class="form-group">
						<label>FECHA COMPROBANTE</label>
						<input type="text" class="form-control input-sm datepicker" id="c_fechacomprobante" value="<?php echo date('Y-m-01');?>">
					</div> <br>
					<div class="text-center">
						<button type="button" class="btn btn-success" v-on:click="netix_cambiar_fecha_1()">GUARDAR CAMBIO DE FECHAS</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script> 
	var campos = {"codalmacen":<?php echo $_SESSION["netix_codalmacen"];?>,"codlinea":0,"stock":0,"fecha":"<?php echo date("Y-m-d");?>","controlstock":1,"estado":1,"buscar":""};

	var pantalla = jQuery(document).height(); $("#reportes_modal").css({height: pantalla - 65});
	$(".detalle").css({height: pantalla - 280});
</script>
<script src="<?php echo base_url();?>netix/netix_reportes/productos.js"> </script>
<script>
	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD'});
</script>