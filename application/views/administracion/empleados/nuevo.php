<div id="netix_form">
    <form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()">
        <br> <input type="hidden" name="codregistro" v-model="campos.codregistro">
        <input type="hidden" name="coddocumentotipo" v-model="campos.coddocumentotipo">

        <div class="row form-group">
            <div class="col-md-6 col-xs-12">
                <label>SELECCIONAR SUCURSAL</label>
                <select class="form-control" name="codsucursal" v-model="campos.codsucursal" required>
                    <option value="">SELECCIONE</option>
                    <?php 
                        foreach ($sucursales as $key => $value) { ?>
                            <option value="<?php echo $value['codsucursal'];?>"><?php echo $value["descripcion"];?></option>
                        <?php }
                    ?>
                </select>
            </div>
            <div class="col-md-4 col-xs-12">
                <label>NRO DE DNI</label>
                <input type="number" name="documento" v-model.trim="campos.documento" class="form-control" required autocomplete="off" placeholder="N° DNI . . ." />
            </div>
            <div class="col-md-2 col-xs-12" style="padding-top:23px;">
                <button type="button" class="btn btn-success btn-block btn-consultar" v-on:click="netix_consultar()"> <i class="fa fa-search"></i> </button>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6 col-xs-12">
                <label>SELECCIONAR AREA</label>
                <select class="form-control" name="codarea" v-model="campos.codarea" required>
                    <option value="">SELECCIONE</option>
                    <?php 
                        foreach ($areas as $key => $value) { ?>
                            <option value="<?php echo $value['codarea'];?>"><?php echo $value["descripcion"];?></option>
                        <?php }
                    ?>
                </select>
            </div>
            <div class="col-md-6 col-xs-12">
                <label>SELECCIONAR CARGO</label>
                <select class="form-control" name="codcargo" v-model="campos.codcargo" required>
                    <option value="">SELECCIONE</option>
                    <?php 
                        foreach ($cargos as $key => $value) { ?>
                            <option value="<?php echo $value['codcargo'];?>"><?php echo $value["descripcion"];?></option>
                        <?php }
                    ?>
                </select>
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
                <label>DIRECCION</label>
                <input type="text" name="direccion" v-model.trim="campos.direccion" class="form-control" required autocomplete="off" placeholder="Direccion . . ." />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6 col-xs-12">
                <label>EMAIL</label>
                <input type="email" name="email" v-model.trim="campos.email" class="form-control" autocomplete="off" placeholder="Email . . ." />
            </div>
            <div class="col-md-6 col-xs-12">
                <label>SUELDO</label>
                <input type="number" name="sueldo" v-model="campos.sueldo" class="form-control" required autocomplete="off" placeholder="Sueldo . . ." />
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-6 col-xs-12">
                <label>TELF./CEL.</label>
                <input type="number" name="telefono" v-model="campos.telefono" class="form-control" placeholder="Telf./Cel." autocomplete="off" onkeypress="return store_numeros(event)">
            </div>
            <div class="col-md-6 col-xs-12">
                <label>SEXO</label>
                <select class="form-control" name="sexo" v-model="campos.sexo" required >
                    <option value="">SELECCIONE</option>
                    <option value="M">MASCULINO</option>
                    <option value="F">FEMENINO</option>
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

<script> var campos = {codregistro:"",coddocumentotipo:"2",codsucursal: "",documento: "",codarea: "",codcargo:"",razonsocial:"",direccion:"",email:"",sueldo:"0.00",telefono:"",sexo:""};</script>
<script src="<?php echo base_url();?>netix/netix_personas.js"></script>