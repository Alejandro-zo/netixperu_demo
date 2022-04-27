<div id="netix_datos"> 
	<div class="row netix_header">
		<div class="col-md-8"> <h5 style="letter-spacing:1px;"> <b>REPORTE GENERAL DE COMPRAS</b> </h5> </div>
	</div>

	<div class="row" style="padding:10px;">
		<div class="col-md-2">
			<label>SUCURSAL</label>
			<input type="hidden" id="fecharef" value="<?php echo date("Y-m-d");?>">
			<select class="form-control" v-model="campos.codsucursal" v-on:change="netix_cajas()">
				<option value="0">TODAS SUCURSALES</option>
				<?php 
					foreach ($sucursales as $key => $value) { ?>
						<option value="<?php echo $value["codsucursal"];?>"><?php echo $value["descripcion"];?></option>	
					<?php }
				?>
			</select>
		</div>
		<div class="col-md-2">
			<label>CAJA</label>
			<select class="form-control" v-model="campos.codcaja">
				<option value="0">TODAS CAJAS</option>
				<option v-for="dato in cajas" v-bind:value="dato.codcaja"> {{dato.descripcion}} </option>
			</select>
		</div>
		<div class="col-md-2">
			<label>COMPRAS DESDE</label>
			<input type="hidden" id="fechad" value="<?php echo date("Y-m-01");?>">
			<input type="text" class="form-control datepicker" id="fechadesde" v-model="campos.fechadesde" v-on:blur="netix_fecha()">
		</div>
		<div class="col-md-2">
			<label>COMPRAS HASTA</label>
			<input type="hidden" id="fechah" value="<?php echo date("Y-m-d");?>">
			<input type="text" class="form-control datepicker" id="fechahasta" v-model="campos.fechahasta" v-on:blur="netix_fecha()">
		</div>
		<div class="col-md-1">
			<label style="margin-top:5px;">ACTIVAS</label> <br>
			<input type="checkbox" style="height:20px;width:20px;" v-model="campos.estado">
		</div>
		<div class="col-md-3">
			<button type="button" class="btn btn-success" v-on:click="ver_grafico()">VER <br>GRAFICO </button>
			<button type="button" class="btn btn-warning" v-on:click="pdf_compras()">PDF <br>COMPRS </button>
			<button type="button" class="btn btn-danger" v-on:click="mas_reportes()"> <i class="fa fa-print"></i> <br> MAS</button>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12" align="center">
			<div class="x_panel" style="height:100%;" id="reporte_compras">
				<h4 style="padding-top:150px;"> 
					<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i> <br> <br> REPORTE DE COMPRAS
				</h4>
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

<script src="<?php echo base_url();?>public/js/highcharts.js"> </script>

<script> 
	var campos = {"codsucursal":<?php echo $_SESSION["netix_codsucursal"];?>,"codcaja":<?php echo $_SESSION["netix_codcaja"];?>,"fechadesde":$("#fechad").val(),"fechahasta":$("#fechah").val(),"estado":1};
	var pantalla = jQuery(document).height(); $("#reporte_compras").css({height: pantalla - 200}); $("#reportes_modal").css({height: pantalla - 65});
</script>
<script src="<?php echo base_url();?>netix/netix_reportes/compras.js"> </script>
<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD'}); </script>