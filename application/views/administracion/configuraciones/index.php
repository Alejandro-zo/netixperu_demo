<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title"> <div class="col-md-12 col-xs-12"> <h5>CONFIGURACIONES DE TU EMPRESA</h5> </div> </div>
	</div> <br>

	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<input type="hidden" name="codpersona" v-model="campos.codpersona">
		<input type="hidden" name="codempresa" v-model="campos.codempresa">
		<input type="hidden" name="itemrepetircomprobante" v-model="campos.itemrepetircomprobante">

		<div class="row netix_row">
			<div class="col-md-6 col-xs-12">
				<div class="netix_body_row">
					<h5 class="text-center"><b>DATOS DE LA EMPRESA</b></h5> <hr>

					<div class="row form-group">
					    <div class="col-md-7 col-xs-7">
					    	<label>RUC EMPRESA</label>
				        	<input type="text" class="form-control" name="documento" v-model="campos.documento" id="documento" placeholder="Numero" required autocomplete="off" minlength="11" maxlength="11">
					    </div>
					    <div class="col-md-5 col-xs-5">
					    	<label>&nbsp;</label>
			                <button type="button" class="btn btn-primary btn-block btn-consultar" v-on:click="netix_consultar()"> 
			                	<i class="fa fa-undo"></i> CONSULTAR SUNAT
			                </button>
			            </div>
				    </div>
					<div class="row form-group">
				    	<div class="col-xs-12">
					        <label>RAZON SOCIAL</label>
					        <input type="text" class="form-control" name="razonsocial" v-model="campos.razonsocial" placeholder="Razon social" required autocomplete="off">
					    </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-xs-8">
					        <label>NOMBRE COMERCIAL</label>
					        <input type="text" class="form-control" name="nombrecomercial" v-model="campos.nombrecomercial" placeholder="Nombre comercial" autocomplete="off">
					    </div>
					    <div class="col-xs-4">
					        <label>FECHA OPERA.</label>
					        <select class="form-control" name="fechaoperaciones" v-model="campos.fechaoperaciones" required>
					        	<option value="0">FECHA ACTUAL</option>
					        	<option value="1">FECHA CAJA</option>
					        </select>
					    </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-md-8 col-xs-12">
					        <label>DIRECCION</label>
					        <input type="text" class="form-control" name="direccion" v-model="campos.direccion" placeholder="Direccion" required autocomplete="off">
					    </div>
					    <div class="col-md-4 col-xs-12">
					    	<label>CLAVE SEGURIDAD</label>
				        	<input type="password" class="form-control" name="claveseguridad" v-model="campos.claveseguridad" placeholder="Clave" autocomplete="off" maxlength="50">
					    </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-md-6 col-xs-12">
					        <label>EMAIL EMPRESA</label>
					        <input type="text" class="form-control" name="email" v-model="campos.email" placeholder="Email" autocomplete="off">
					    </div>
				        <div class="col-md-6">
				            <label>TELF./CEL.</label>
				            <input type="text" class="form-control" name="telefono" v-model="campos.telefono" placeholder="Telf./Cel." autocomplete="off" maxlength="100">
				        </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-xs-12">
					        <label>SLOGAN EMPRESA</label>
					        <textarea class="form-control" name="slogan" v-model="campos.slogan" placeholder="Slogan . . ." autocomplete="off" rows="3"></textarea>
					    </div>
				    </div>
				</div>
			</div>

			<div class="col-md-6 col-xs-12">
				<div class="netix_body_row">
					<h5 class="text-center"><b>CONFIGURAR PARAMETROS SUNAT</b></h5> <hr>

					<div class="row form-group">
				    	<div class="col-md-4 col-xs-6">
				    		<label>IGV SUNAT (%)</label>
					        <input type="number" step="0.01" class="form-control" name="igvsunat" v-model.number="campos.igvsunat" autocomplete="off" required>
					    </div>
					    <div class="col-md-4 col-xs-6">
				    		<label>ICBPER SUNAT (%)</label>
					        <input type="number" step="0.01" class="form-control" name="icbpersunat" v-model.number="campos.icbpersunat" autocomplete="off" required>
					    </div>
					    <div class="col-md-4 col-xs-6">
				    		<label>ISC SUNAT (%)</label>
					        <input type="number" step="0.01" class="form-control" name="iscsunat" v-model.number="campos.iscsunat" autocomplete="off" required>
					    </div>
					</div>
					<div class="row form-group">
					    <div class="col-md-12 col-xs-12">
							<div class="">
								<label v-if="campos.itemrepetircomprobante==1" >
								  	REPETIR ITEM (BIEN/SERVICIO) EN EL COMPROBANTE <input type="checkbox" class="js-switch" v-on:click="netix_itemrepetir()" checked/>
								</label>
								<label v-else="campos.itemrepetircomprobante!=1" >
								  	REPETIR ITEM (BIEN/SERVICIO) EN EL COMPROBANTE <input type="checkbox" class="js-switch" v-on:click="netix_itemrepetir()"/>
								</label>
							</div>
					    </div>
				    </div>
				</div> <br>

				<div class="netix_body_row">
					<h5 class="text-center"><b>LOGOS DE LA EMPRESA</b></h5>
					<div class="row form-group">
				    	<div class="col-md-6">
					        <label>LOGO EMPRESA</label>
					        <input type="file" class="form-control" name="logo" accept="image/*">
					    </div>
					    <div class="col-md-6">
					        <label>LOGO AUSPICIADOR</label>
					        <input type="file" class="form-control" name="auspiciador" accept="image/*">
					    </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-xs-12">
					        <label>PUBLICIDAD</label>
					        <textarea class="form-control" name="publicidad" v-model="campos.publicidad" placeholder="Publicidad . . ." autocomplete="off" rows="1"></textarea>
					    </div>
				    </div>
				    <div class="row form-group">
				    	<div class="col-xs-12">
					        <label>AGRADECIMIENTO</label>
					        <textarea class="form-control" name="agradecimiento" v-model="campos.agradecimiento" placeholder="Agradecimiento . . ." autocomplete="off" rows="1"></textarea>
					    </div>
				    </div>

				    <div class="form-group text-center"> <br>
				    	<button type="submit" class="btn btn-success"> 
				    		<i class="fa fa-bookmark-o"></i> GUARDAR CONFIGURACION
		                </button>
				    </div>
				</div>
			</div>

		</div>

		<input type="hidden" id="publicidad_texto" value="<?php echo $empresa[0]["publicidad"];?>">
		<input type="hidden" id="agradecimiento_texto" value="<?php echo $empresa[0]["agradecimiento"];?>">
	</form>
</div>

<script>
	if ($(".js-switch")[0]) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) { var switchery = new Switchery(html, { color: '#26B99A' }); });
    }

	var campos = {
		codpersona:"<?php echo $info[0]["codpersona"];?>",
		codempresa:"<?php echo $empresa[0]["codempresa"];?>",
		documento:"<?php echo $info[0]["documento"];?>",
		razonsocial:"<?php echo $info[0]["razonsocial"];?>",
		nombrecomercial:"<?php echo $info[0]["nombrecomercial"];?>",
		direccion:"<?php echo $info[0]["direccion"];?>",
		claveseguridad:"<?php echo $empresa[0]["claveseguridad"];?>",
		email:"<?php echo $info[0]["email"];?>",
		telefono:"<?php echo $info[0]["telefono"];?>",
		slogan:"<?php echo $empresa[0]["slogan"];?>",
		igvsunat:"<?php echo $empresa[0]["igvsunat"];?>",
		icbpersunat:"<?php echo $empresa[0]["icbpersunat"];?>",
		iscsunat:"<?php echo $empresa[0]["iscsunat"];?>",
		itemrepetircomprobante:"<?php echo $empresa[0]["itemrepetircomprobante"];?>",
		fechaoperaciones:"<?php echo $empresa[0]["fechaoperaciones"];?>",
		publicidad:'', agradecimiento:''
	};
</script>
<script src="<?php echo base_url();?>netix/netix_empresa/configuraciones.js"> </script>