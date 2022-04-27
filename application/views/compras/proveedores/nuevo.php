<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<br> <input type="hidden" name="codregistro" v-model="campos.codregistro">
		<input type="hidden" name="codsociotipo" v-model="campos.codsociotipo">

	    <div class="row form-group">
	    	<div class="col-md-6 col-xs-12">
		    	<label>TIPO DOCUMENTO</label>
		    	<select class="form-control" name="coddocumentotipo" v-model="campos.coddocumentotipo" required v-on:change="netix_tipodocumento()" ref="coddocumentotipo">
		    		<option value="">SELECCIONE</option>
		            <?php 
		                foreach ($tipodocumentos as $key => $value) { ?>
		                    <option value="<?php echo $value['coddocumentotipo'];?>"><?php echo $value["descripcion"];?></option>
		                <?php }
		            ?>
		    	</select>
		    </div>
		    <div class="col-md-4 col-xs-12">
		    	<label>DOCUMENTO</label>
	        	<input type="text" class="form-control" name="documento" v-model="campos.documento" id="documento" placeholder="Numero" required autocomplete="off" minlength="8" maxlength="15" ref="documento">
		    </div>
		    <div class="col-md-2 col-xs-12" style="padding-top:25px;">
		    	<button type="button" class="btn btn-success btn-block btn-consultar" v-on:click="netix_consultar();"> <i class="fa fa-search"></i> </button>
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>RAZON SOCIAL</label>
		        <input type="text" class="form-control" name="razonsocial" v-model="campos.razonsocial" placeholder="Razon social" required autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>NOMBRE COMERCIAL</label>
		        <input type="text" class="form-control" name="nombrecomercial" v-model="campos.nombrecomercial" placeholder="Nombre comercial" autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>DIRECCION</label>
		        <input type="text" class="form-control" name="direccion" v-model="campos.direccion" placeholder="Direccion" required autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>EMAIL</label>
		        <input type="text" class="form-control" name="email" v-model="campos.email" placeholder="Email" autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	        <div class="col-md-6">
	            <label>TELF./CEL.</label>
	            <input type="number" class="form-control" name="telefono" v-model="campos.telefono" placeholder="Telf./Cel." autocomplete="off">
	        </div>
	        <div class="col-md-6">
	            <label>DEPARTAMENTO</label>
	            <select class="form-control" name="departamento" v-model="campos.departamento" required v-on:change="netix_provincias()">
	                <option value="">SELECCIONE</option>
	                <?php 
	                    foreach ($departamentos as $key => $value) { ?>
	                        <option value="<?php echo $value['ubidepartamento'];?>"><?php echo $value["departamento"];?></option>
	                    <?php }
	                ?>
	            </select>
	        </div>
	    </div>
	    <div class="row form-group">
	        <div class="col-md-6">
	            <label>PROVINCIA</label>
	            <select class="form-control" name="provincia" v-model="campos.provincia" id="provincia" required v-on:change="netix_distritos()">
	                <option value="">SELECCIONE</option>
	            </select>
	        </div>
	        <div class="col-md-6">
	            <label>DISTRITO</label>
	            <select class="form-control" name="codubigeo" v-model="campos.codubigeo" id="codubigeo" required>
	                <option value="">SELECCIONE</option>
	            </select>
	        </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-6">
		    	<label>¿ TIENE CONVENIO ?</label>
		    	<select class="form-control" name="convenio" v-model="campos.convenio" required>
		    		<option value="0">NO TIENE CONVENIO</option>
		    		<option value="1">SI TIENE CONVENIO</option>
		    	</select>
		    </div>
		    <div class="col-xs-6">
		    	<label>TIPO SOCIO</label>
		    	<select class="form-control" name="codsociotipo" v-model="campos.codsociotipo" required>
		    		<option value="2">PROVEEDOR</option>
		    		<option value="3">CLIENTE / PROVEEDOR</option>
		    	</select>
		    </div>
		</div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>
</div>

<script> var campos = {codregistro:"",codsociotipo: 2,coddocumentotipo: "",documento: "",razonsocial: "",nombrecomercial:"",direccion: "",email: "",telefono: "",departamento: "",provincia: "",codubigeo: "", convenio:0}; </script>
<script src="<?php echo base_url();?>netix/netix_personas.js"></script>