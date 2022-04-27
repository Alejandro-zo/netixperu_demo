<div id="netix_form"> <br>
	<div class="row netix_row">
		<div class="col-md-7 col-xs-12">
			<h5 class="text-center"> <b><?php echo $info[0]["descripcion"];?></b> </h5>

			<h6><b>FAMILIA:</b> <?php echo $info[0]["familia"];?></h6>
			<h6><b>LINEA: </b>  <?php echo $info[0]["linea"];?></h6>
			<h6><b>MARCA: </b>  <?php echo $info[0]["marca"];?></h6>
			<h6><span class="label label-primary">CODIGO DEL PRODUCTO: <?php echo $info[0]["codigo"];?></span></h6>
			<h6><b>CONTROL STOCK:</b> <?php if ($info[0]["controlstock"]==1) {echo "SI - BIEN";}else{echo "NO - SERVICIO";} ?> </h6>
		</div>
		<div class="col-md-5 col-xs-12">
			<div class="netix_card_image">
				<img src="<?php echo base_url();?>public/img/productos/<?php echo $info[0]['foto']?>" style="width: 100%">
			</div>
		</div>
	</div>

	<div class="netix_row">
		<p class="text-center"> <br>
			<span class="label label-info">ICBPER: <?php if ($info[0]["afectoicbper"]==1) {echo "SI";}else{echo "NO";} ?> </span> &nbsp;
			<span class="label label-success">IGV COMPRA: <?php if ($info[0]["afectoigvcompra"]==1) {echo "SI";}else{echo "NO";} ?> </span> &nbsp;
			<span class="label label-warning">IGV VENTA: <?php if ($info[0]["afectoigvventa"]==1) {echo "SI";}else{echo "NO";} ?> </span>
		</p> <hr>
		<h5 class="text-center"> <b>UNIDADES DE MEDIDA / PRECIOS</b> </h5>

		<div class="table-responsive">
			<table class="table table-bordered table-condensed">
				<thead style="background:#2f4050;color: #fff;">
					<tr>
						<th>UNIDAD</th>
						<th>FACTOR</th>
						<th>STOCK</th>
						<th>P.COMPRA</th>
						<th>P.VENTA</th>
						<th>P.MINIMO</th>
						<th>P.CREDITO</th>
						<th>P.MAYOR</th>
						<th>P.OTROS</th>
						<th>C.BARRA</th>
						<th>ESTADO</th>
					</tr>
				</thead>
				<tbody style="font-size:13px;">
					<?php 
						foreach ($unidades as $key => $value) {
							$anulado = ""; if ($value["estado"]!=1) {$anulado = "netix_anulado";} ?>

							<tr class="<?php echo $anulado;?>">
								<td><b><?php echo $value["unidad"];?></b></td>
								<td><?php echo $value["factor"];?></td>
								<td><span class="label label-warning"><?php echo $value["stock"];?></span></td>
								<td><?php echo number_format($value["preciocompra"],3);?></td>
								<td><?php echo number_format($value["pventapublico"],3);?></td>
								<td><?php echo number_format($value["pventamin"],3);?></td>
								<td><?php echo number_format($value["pventacredito"],3);?></td>
								<td><?php echo number_format($value["pventaxmayor"],3);?></td>
								<td><?php echo number_format($value["pventaadicional"],3);?></td>
								<td><?php echo $value["codigobarra"]?></td>
								<td style="padding: 5px;">
									<?php 
										if ($value["estado"]==1) { ?>
											<span class="label label-success">ACTIVO</span>
										<?php }else{ ?>
											<span class="label label-danger">ANULADO</span>
										<?php }
									?>
								</td>
							</tr>
						<?php }
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>