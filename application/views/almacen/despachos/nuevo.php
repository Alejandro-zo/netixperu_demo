<div id="netix_despacho">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-12 col-xs-12">
				<?php 
					if ($info[0]["codmovimientotipo"]==2) {$tipo = 3; $operacion = "COMPRA"; $ope1 = "RECIBIDO"; $ope2 = "RECIBIR";  ?>
						<h5 style="letter-spacing:1px;"> <b>RECIBIR COMPRA *** COMPROBANTE: <?php echo $info[0]["seriecomprobante"]." - ".$info[0]["nrocomprobante"]?></b> </h5>
					<?php }else{ $tipo = 4; $operacion = "VENTA"; $ope1 = "DESPACHADO"; $ope2 = "DESPACHAR"; ?>
						<h5 style="letter-spacing:1px;"> <b>DESPACHAR VENTA *** COMPROBANTE: <?php echo $info[0]["seriecomprobante"]." - ".$info[0]["nrocomprobante"]?></b> </h5>
					<?php }
				?>
			</div>
		</div>
	</div> <br>
	
	<div class="netix_body row">
		<div class="col-md-7 col-xs-12"> <br>
			<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
				<div class="x_panel">
					<h5 align="center" style="border-bottom:2px solid #f3f3f3;padding-bottom:10px;">
		        		<b>DETALLE DE LA <?php echo $operacion;?></b>
		        	</h5>
		        	
					<div class="detalle" style="height:250px;">
						<table class="table table-bordered">
							<thead>
								<tr align="center" >
									<th width="25%">PRODUCTO</th>
									<th width="18%">UNIDAD</th>
									<th width="12%">CANTIDAD</th>
									<th width="15%"><?php echo $ope1;?></th>
									<th width="15%">PENDIENTE</th>
									<th width="15%"><?php echo $ope2;?></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="(dato,index) in detalle">
									<td>{{dato.producto}}</td>
									<td>{{dato.unidad}} </td>
									<td>{{dato.cantidad}} </td>
									<td>{{dato.recogido}} </td>
									<td style="color:#d43f3a"> <b>{{dato.pendiente}}</b> </td>
									<td> 
										<input type="number" step="0.01" class="netix-input number" v-model.number="dato.recoger" min="0" v-bind:max="dato.pendiente" required style="border:2px solid #13a89e;width:100%;">
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div class="col-md-12" align="center"> <br>
					<button type="submit" class="btn btn-success btn-lg" v-bind:disabled="estado==1"> <b>GUARDAR <?php echo $ope1; ?></b> </button>
					<button type="button" class="btn btn-danger btn-lg" v-on:click="netix_cerrar()"> <b>CANCELAR</b> </button>
				</div>
			</form>
		</div>

		<div class="col-md-5 col-xs-12"> <br>
	        <div class="x_panel">
	        	<h5 align="center" style="border-bottom:2px solid #f3f3f3;padding-bottom:10px;">
	        		<b>ENTREGAS O DESPACHOS REALIZADOS</b>
	        	</h5>

	        	<div class="entregas" style="height:200px;">
					<table class="table table-bordered">
						<thead>
							<tr align="center" >
								<th width="10%">KARDEX</th>
								<th width="20%">FECHA</th>
								<th width="20%">PRODUCTO</th>
								<th width="20%">UNIDAD</th>
								<th width="20%">CANTIDAD</th>
								<th width="10%"> </th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in entregados">
								<td>00{{dato.codkardexalmacen}}</td>
								<td>{{dato.fechakardex}}</td>
								<td>{{dato.producto}}</td>
								<td>{{dato.unidad}} </td>
								<td>{{dato.cantidad}} </td>
								<td> 
									<button type="button" class="btn btn-danger" v-on:click="netix_eliminar(dato)"><i class="fa fa-trash-o"></i></button>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
	        </div>
	    </div>
	</div>
</div>

<script> var campos = {"codkardex":"<?php echo $info[0]['codkardex'];?>","codmovimientotipo":"<?php echo $info[0]['codmovimientotipo'];?>","codcomprobantetipo":"<?php echo $tipo;?>"};</script>
<script src="<?php echo base_url();?>netix/netix_despachos/nuevo.js"> </script>
<script>
	var div_altura = jQuery(document).height(); var detalle = div_altura - 320; var entregas = div_altura - 250;
	$(".detalle").slimScroll({position:'right',size:"5px", color:'#98a6ad',wheelStep:10,height:detalle+"px"});
	$(".entregas").slimScroll({position:'right',size:"5px", color:'#98a6ad',wheelStep:10,height:entregas+"px"});
</script>