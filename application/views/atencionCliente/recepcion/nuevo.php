<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
		<!-- ES PARA HACER EL REGISTRO DEL CODIGO
		<input type="hidden" name="codregistro" v-model="campos.codregistro">
		<input type="hidden" name="codsociotipo" v-model="campos.codsociotipo">-->

		<!--DATOS DEL EMPLEADO QUE ATIENDE-->
	    <div class="row form-group">
	    	<div class="col-xs-12" style="display: flex; align-items: center; justify-content: center; ">
	    		<label style="text-decoration: underline; font-size: 15px;">DATOS DEL EMPLEADO</label>
	    	</div><br><br>	

            <div class="col-xs-12">
                <label>SELECCIONAR EMPLEADO</label>
                <select class="form-control" name="codempleado" v-model="campos.codempleado" required>
                    <option value="">SELECCIONE</option>
                    <?php
                    foreach ($empleados as $key => $value) { ?>
                        <option value="<?php echo $value['codpersona'];?>"><?php echo $value["razonsocial"];?></option>
                    <?php }
                    ?>
                </select>
            </div>
        </div>	    
        <!--DATOS DEL CLIENTE-->
	    <div class="row form-group">
	    	<div class="col-xs-12" style="display: flex; align-items: center; justify-content: center; ">
	    		<label style="text-decoration: underline; font-size: 15px;">DATOS DEL CLIENTE</label>
	    	</div><br><br>

	    	<div class="col-md-5 col-xs-12">
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
		    <div class="col-md-5 col-xs-12">
		    	<label>NRO. DOCUMENTO</label>
	        	<input type="text" class="form-control line-danger" name="documento" v-model="campos.documento" id="documento" placeholder="N° DOCUMENTO" required autocomplete="off" minlength="8" maxlength="15" ref="documento">
		    </div>
		    <div class="col-md-2 col-xs-12" style="padding-top:25px;">
		    	<button type="button" class="btn btn-success btn-block btn-consultar" v-on:click="netix_consultar();"> <i class="fa fa-search"></i> </button>
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
                <label>NOMBRES COMPLETOS</label>
                <input type="text" name="razonsocial" v-model.trim="campos.razonsocial" class="form-control" required autocomplete="off" placeholder="Nombres completos . . ." />
            </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-12">
		        <label>DIRECCIÓN</label>
		        <input type="text" class="form-control" name="direccion" v-model="campos.direccion" placeholder="Direccion" required autocomplete="off">
		    </div>
	    </div>
	    <div class="row form-group">
	    	<div class="col-xs-6">
		        <label>EMAIL</label>
		        <input type="text" class="form-control" name="email" v-model="campos.email" placeholder="Email" autocomplete="off">
		    </div>

		    <div class="col-md-6">
	            <label>TELF./CEL.</label>
	            <input type="number" class="form-control" name="telefono" v-model="campos.telefono" placeholder="Telf./Cel." autocomplete="off">
	        </div>
	    </div>
	    <div class="row form-group">
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

	        <div class="col-md-6">
	            <label>PROVINCIA</label>
	            <select class="form-control" name="provincia" v-model="campos.provincia" id="provincia" required v-on:change="netix_distritos()">
	                <option value="">SELECCIONE</option>
	            </select>
	        </div>
	    </div>	    	    	    
	    <div class="row form-group"> 
	    		<div class="col-md-6">
	            <label>DISTRITO</label>
	            <select class="form-control" name="codubigeo" v-model="campos.codubigeo" id="codubigeo" required>
	                <option value="">SELECCIONE</option>
	            </select>
	        </div>


	        <div class="col-md-6">
	        	<label >FECHA DE RECEPCIÓN</label>
	            <input type="date" class="form-control input-sm datepicker" id="fechadesde" value="<?php echo date('Y-m-d');?>" autocomplete="off">

	        </div>
	    </div>


        <div class="row form-group">
        	<div class="col-xs-12" style="display: flex; align-items: center; justify-content: center; ">
        		<label style="text-decoration: underline; font-size: 15px;">DATOS DEL PRODUCTO</label>
        	</div><br><br>

            <div class="col-md-4">
	            <label>PRODUCTO</label>
	            <input type="text" class="form-control" name="descripcion" v-model="campos.telefono" placeholder="Producto..." autocomplete="off">
	        </div>

	        <div class="col-md-4">
	            <label>MARCA</label>
	            <input type="text" class="form-control" name="marca" v-model="campos.telefono" placeholder="Marca..." autocomplete="off">
	        </div>

	        <div class="col-md-4">
	            <label>MODELO</label>
	            <input type="text" class="form-control" name="modelo" v-model="campos.telefono" placeholder="Modelo..." autocomplete="off">
	        </div>
        </div>

		<div class="row form-group">
            <div class="col-xs-12">
                <label>DESCRIPCIÓN DEL PROBLEMA</label>
                <input type="text" style="height:50px;" class="form-control" name="descripcion" v-model="campos.telefono" placeholder="Problema..." autocomplete="off">
            </div>
        </div>

        <div class="row form-group">
            <div class="col-xs-12">
                <label>TIPO PAGO</label>
                <select class="form-control" name="codtipopago" v-model="campos.codtipopago" required>
                    <option value="">SELECCIONE</option>
                    <?php
                    foreach ($pago as $key => $value) { ?>
                        <option value="<?php echo $value['codtipopago'];?>"><?php echo $value["descripcion"];?></option>
                    <?php }
                    ?>
                </select>
            </div>
        </div>

		<div class="ln_solid"></div>
		<div class="form-group" align="center">
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"><i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()"><i class="fa fa-circle-o"></i> CERRAR</button>
		</div>
	</form>
</div>



<script> var campos = {codempleado:"", coddocumentotipo:"", documento:"", nombrepersona:"", direccion: "",email: "",telefono: "",departamento: "",provincia: "",codubigeo: "",nombreempleado:"", producto:"",marca: "",modelo: "",fecharecepcion: "",descripcion:"",tipopago: ""}; </script>
<script src="<?php echo base_url();?>netix/netix_personas.js"></script>

<script> $(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true"); </script>