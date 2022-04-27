<div id="netix_unidades">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-8 col-xs-12"> <h5>PRODUCTOS X UNIDADES</h5> </div>
		</div>
	    <div class="row">
	    	<div class="col-md-2"> 
				<button type="button" class="btn btn-info btn-block" v-on:click="cambiar_unidad()">CAMBIAR UNIDAD</button>
			</div>
			<div class="col-md-2"> 
				<button type="button" class="btn btn-warning btn-block" v-on:click="productos_almacen()">ASIGNAR ALMACENES</button>
			</div>
			<div class="col-md-2"> 
				<button type="button" class="btn btn-success btn-block" v-on:click="actualizar_stock()">ACTUALIZAR STOCK</button>
			</div>
		    <div class="col-md-6 col-xs-12">
		    	<input type="text" class="form-control" v-model="buscar" placeholder="BUSCAR PRODUCTO . . .">
		    </div>
	    </div>
	</div> <br>
	
	<div class="netix_body lista scroll-netix-view" style="height:300px;overflow:auto;overflow-x:hidden;">
		<div class="table-responsive">
			<table class="table table-condensed table-bordered">
				<thead>
					<tr>
						<th width="3%"> # </th>
						<th width="3%"> <i class="fa fa-circle-o-notch"></i> </th>
						<th width="33%">PRODUCTO</th>
						<th width="7%">UNIDAD</th>
						<th width="3%">F</th>
						<th width="7%">CANTIDAD</th>
						<th width="7%">P.COSTO</th>
						<th width="8%" style="background:#23c6c8;color:#fff;">S/.&nbsp;COSTO</th>
						<th width="7%">P.VENTA</th>
						<th width="8%" style="background:#1ab394;color:#fff;">S/.&nbsp;VENTA</th>
						<th width="8%">P.MIN</th>
						<th width="10%" style="background:#f8ac59;color:#fff;">S/.&nbsp;MINIMO</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="(dato, index) in buscar_productos">
						<td>{{dato.nro}}</td>
						<td> <input type="radio" name="netix_seleccionar" v-on:click="netix_marcar(dato)" style="height:15px;width:15px;"> </td>
						<td style="font-size:10px;">{{dato.descripcion}}</td>
						<td>{{dato.unidad}}</td>
						<td>{{dato.factor}}</td>
						<td>{{dato.stock}}</td>
						<td>{{dato.preciocosto}}</td>
						<td style="background:#23c6c8;color:#fff;">{{dato.costo}}</td>
						<td>{{dato.precioventa}}</td>
						<td style="background:#1ab394;color:#fff;">{{dato.venta}}</td>
						<td>{{dato.preciomin}}</td>
						<td style="background:#f8ac59;color:#fff;">{{dato.minimo}}</td>
					</tr>
				</tbody>
				<tfoot>
					<tr v-for="dato in totales">
						<td colspan="7" style="text-align:right;"> <b>TOTALES GENERALES</b> </td>
						<td><b>{{dato.costo}}</b></td> <td></td>
						<td><b>{{dato.venta}}</b></td> <td></td>
						<td><b>{{dato.minimo}}</b></td>
					</tr>
					<tr v-for="dato in totales">
						<td colspan="7" style="text-align:right;"> <b>RESUMEN TOTAL (S/. VENTA - S/. COSTO)</b> </td>
						<td colspan="5"><b>{{dato.venta - dato.costo}}</b></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>

	<div id="modal_cambiar_unidad" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" style="font-size:30px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title"> <b>{{campos.descripcion}} | UNIDAD MEDIDA: {{campos.unidad}}</b> </h4>
				</div>
				<div class="modal-body" style="height:330px">
					<div class="row">
						<div class="col-md-6 col-xs-12 text-center">
							<h5><b>UNIDAD ACTUAL</b></h5> <hr>
							<h4><span class="label label-danger">UNIDAD MEDIDA: {{campos.unidad}}</span></h4> <br>

							<ul class="list-inline widget_tally">
								<li>
									<p> <span class="month"><b>FACTOR MEDIDA</b></span> <span class="count">{{campos.factor}}</span> </p>
								</li>
                                <li>
                                  	<p> <span class="month"><b>STOCK ACTUAL</b></span> <span class="count">{{campos.stock}}</span> </p>
                                </li>
                                <li>
                                  	<p> <span class="month"><b>PRECIO COSTO</b></span> <span class="count">{{campos.preciocosto}}</span> </p>
                                </li>
                                <li>
                                  	<p> <span class="month"><b>PRECIO VENTA</b></span> <span class="count">{{campos.precioventa}}</span> </p>
                                </li>
                                <li>
                                  	<p> <span class="month"><b>PRECIO MINIMO</b></span> <span class="count">{{campos.preciomin}}</span> </p>
                                </li>
                            </ul>
						</div>
						<div class="col-md-6 col-xs-12">
							<h5 class="text-center"><b>UNIDAD NUEVA</b></h5> <hr>
							<h5>SELECCIONE NUEVA UNIDAD</h5>
							<select class="form-control" id="codunidad">
								<option value="">SELECCIONE UNIDAD</option>
								<?php 
									foreach ($unidades as $key => $value) { ?>
										<option value="<?php echo $value['codunidad'];?>"><?php echo $value["descripcion"]."(OFICIAL: ".$value["oficial"].")";?></option>
									<?php }
								?>
							</select> <br>
							<div class="alert alert-danger text-center">
								ATENCION USUARIO: AL CAMBIAR LA UNIDAD DE MEDIDA, SE REEMPLAZARA EN LAS VENTAS, COMPRAS, KARDEX E INVENTARIO
							</div>
							<button type="button" class="btn btn-success btn-block" v-on:click="guardar_cambiar_unidad" v-bind:disabled="estado==1">CAMBIAR UNIDAD DE MEDIDA</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?php echo base_url();?>netix/netix_almacen/unidades.js"></script>

<script> 
	var div_altura = jQuery(document).height(); var productos = div_altura - 200; $(".lista").css("height",productos+"px");
</script>