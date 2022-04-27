<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()"> <br>
		<input type="hidden" name="codempresa" v-model="campos.codempresa">

	    <div class="row form-group">
		    <div class="col-md-12 col-xs-12">
	    		<label>RUBRO DE EMPRESA NETIX PERÚ</label>
		        <select class="form-control" name="rubro" v-model="campos.rubro">
		        	<option value="0">NEGOCIO GENERAL</option>
		        	<option value="1">GRIFOS - ESTACION DE SERVICIOS</option>
		        	<option value="2">COMERCIAL - TIENDAS COMERCIALES</option>
		        	<option value="3">BAR - RESTAURANTE</option>
		        	<option value="4">TIENDA DE PERFUMERIA</option>
		        </select>
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>DEPARTAMENTO</label>
		        <input type="text" class="form-control" name="departamento" v-model="campos.departamento" placeholder="Departamento" autocomplete="off">
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>PROVINCIA</label>
	        	<input type="text" class="form-control" name="provincia" v-model="campos.provincia" placeholder="Provincia"  autocomplete="off">
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>PROVINCIA</label>
		        <input type="text" class="form-control" name="distrito" v-model="campos.distrito" placeholder="Distrito" autocomplete="off">
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>UBIGEO</label>
	        	<input type="text" class="form-control" name="ubigeo" v-model="campos.ubigeo" placeholder="Ubigeo" maxlength="6" autocomplete="off">
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>EMAIL ENVIO</label>
		        <input type="email" class="form-control" name="envioemail" v-model="campos.envioemail" placeholder="Email" autocomplete="off">
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>CLAVE EMAIL</label>
	        	<input type="text" class="form-control" name="claveemail" v-model="campos.claveemail" placeholder="Clave email"  autocomplete="off">
		    </div>
	    </div>
		
		<div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>USUARIO SOL</label>
		        <input type="text" class="form-control" name="usuariosol" v-model.trim="campos.usuariosol" placeholder="Usuario sol" autocomplete="off">
		    </div>
		    <div class="col-md-6 col-xs-12">
		    	<label>CLAVE SOL</label>
	        	<input type="text" class="form-control" name="clavesol" v-model="campos.clavesol" placeholder="Clave sol" autocomplete="off">
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>CERTIFICADO PFX</label>
		        <input type="file" class="form-control" name="certificado_pfx" accept=".pfx">
		    </div>
	    	<div class="col-md-6 col-xs-12">
		        <label>CLAVE CERTIFICADO</label>
		        <input type="text" class="form-control" name="certificado_clave" v-model="campos.certificado_clave" placeholder="Clave certificado" autocomplete="off">
		    </div>
	    </div>

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		        <label>SERVICIO SUNAT/OSE</label>
		        <select class="form-control" name="sunatose" v-model="campos.sunatose">
		        	<option value="0">SERVICIO SUNAT</option>
		        	<option value="1">SERVICIO OSE</option>
		        </select>
		    </div>
		    <div class="col-md-6 col-xs-12">
		        <label>ESTADO WEB SERVICE</label>
		        <select class="form-control" name="serviceweb" v-model="campos.serviceweb">
		        	<option value="0">PRODUCCION SUNAT/OSE</option>
		        	<option value="1">BETA HOMOLAGACION</option>
		        </select>
		    </div>
		</div>

		<div class="row form-group">
			<div class="col-md-12 col-xs-12" v-if="campos.sunatose==0">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE SUNAT</label>
					<input type="text" name="servicesunat" v-model.trim="campos.servicesunat" class="form-control" autocomplete="off" placeholder="Web Service SUNAT . . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE SUNAT DEMO</label>
					<input type="text" name="servicesunat_demo" v-model.trim="campos.servicesunat_demo" class="form-control" autocomplete="off" placeholder="Web Service SUNAT Demo . . ." maxlength="100" />
				</div>
			</div>
			<div class="col-md-12 col-xs-12" v-else="campos.sunatose!=1">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE OSE</label>
					<input type="text" name="serviceose" v-model.trim="campos.serviceose" class="form-control" autocomplete="off" placeholder="Web Service OSE . . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE OSE DEMO</label>
					<input type="text" name="serviceose_demo" v-model.trim="campos.serviceose_demo" class="form-control" autocomplete="off" placeholder="Web Service OSE Demo . . ." maxlength="100" />
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12 col-xs-12" v-if="campos.sunatose==0">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE SUNAT GUIA</label>
					<input type="text" name="servicesunatguia" v-model.trim="campos.servicesunatguia" class="form-control" autocomplete="off" placeholder="Web Service SUNAT Guia . . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE SUNAT GUIA DEMO</label>
					<input type="text" name="servicesunatguia_demo" v-model.trim="campos.servicesunatguia_demo" class="form-control" autocomplete="off" placeholder="Web Service SUNAT Guia Demo . . ." maxlength="100" />
				</div>
			</div>
			<div class="col-md-12 col-xs-12" v-else="campos.sunatose!=1">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE OSE GUIA</label>
					<input type="text" name="serviceoseguia" v-model.trim="campos.serviceoseguia" class="form-control" autocomplete="off" placeholder="Web Service OSE Guia. . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE OSE GUIA DEMO</label>
					<input type="text" name="serviceoseguia_demo" v-model.trim="campos.serviceoseguia_demo" class="form-control" autocomplete="off" placeholder="Web Service OSE Guia Demo . . ." maxlength="100" />
				</div>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12 col-xs-12" v-if="campos.sunatose==0">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE SUNAT RETENCION</label>
					<input type="text" name="servicesunatretencion" v-model.trim="campos.servicesunatretencion" class="form-control" autocomplete="off" placeholder="Web Service SUNAT Guia . . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE SUNAT RETENCION DEMO</label>
					<input type="text" name="servicesunatretencion_demo" v-model.trim="campos.servicesunatretencion_demo" class="form-control" autocomplete="off" placeholder="Web Service SUNAT Guia Demo . . ." maxlength="100" />
				</div>
			</div>
			<div class="col-md-12 col-xs-12" v-else="campos.sunatose!=1">
				<div class="form-group" v-if="campos.serviceweb==0">
					<label>URL WEB SERVICE OSE RETENCION</label>
					<input type="text" name="serviceoseretencion" v-model.trim="campos.serviceoseretencion" class="form-control" autocomplete="off" placeholder="Web Service OSE Guia. . ." maxlength="100" />
				</div>
				<div class="form-group" v-if="campos.serviceweb!=0">
					<label>URL WEB SERVICE OSE RETENCION DEMO</label>
					<input type="text" name="serviceoseretencion_demo" v-model.trim="campos.serviceoseretencion_demo" class="form-control" autocomplete="off" placeholder="Web Service OSE Guia Demo . . ." maxlength="100" />
				</div>
			</div>
		</div>

		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> 
	var campos = {
		codempresa:"<?php echo $empresa[0]["codempresa"];?>",
		rubro:"<?php echo $empresa[0]["rubro"];?>",
		departamento:"<?php echo $empresa[0]["departamento"];?>",
		provincia:"<?php echo $empresa[0]["provincia"];?>",
		distrito:"<?php echo $empresa[0]["distrito"];?>",
		ubigeo:"<?php echo $empresa[0]["ubigeo"];?>",

		usuariosol:"<?php echo $service[0]["usuariosol"];?>",
		clavesol:"<?php echo $service[0]["clavesol"];?>",
		envioemail:"<?php echo $service[0]["envioemail"];?>",
		claveemail:"<?php echo $service[0]["claveemail"];?>",
		certificado_clave:"<?php echo $service[0]["certificado_clave"];?>",
		sunatose:"<?php echo $service[0]["sunatose"];?>",
		serviceweb:"<?php echo $service[0]["serviceweb"];?>",

		servicesunat:"<?php echo $service[0]['servicesunat'];?>",
		servicesunat_demo:"<?php echo $service[0]['servicesunat_demo'];?>",
		serviceose:"<?php echo $service[0]['serviceose'];?>",
		serviceose_demo:"<?php echo $service[0]['serviceose_demo'];?>",
		servicesunatguia:"<?php echo $service[0]['servicesunatguia'];?>",
		servicesunatguia_demo:"<?php echo $service[0]['servicesunatguia_demo'];?>",
		serviceoseguia:"<?php echo $service[0]['serviceoseguia'];?>",
		serviceoseguia_demo:"<?php echo $service[0]['serviceoseguia_demo'];?>",
		servicesunatretencion:"<?php echo $service[0]['servicesunatretencion'];?>",
		servicesunatretencion_demo:"<?php echo $service[0]['servicesunatretencion_demo'];?>",
		serviceoseretencion:"<?php echo $service[0]['serviceoseretencion'];?>",
		serviceoseretencion_demo:"<?php echo $service[0]['serviceoseretencion_demo'];?>"
	}; 
</script>
<script src="<?php echo base_url();?>netix/netix_empresa/editar.js"></script>