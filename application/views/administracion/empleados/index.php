<div id="netix_datos">
    <div class="netix_header">
        <div class="row netix_header_title">
            <div class="col-md-8 col-xs-12"> <h5>LISTA DE EMPLEADOS</h5> </div>
        </div>
        <div class="row">
            <div class="col-md-8 netix_header_button">
                <button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO </button>
                <button type="button" class="btn btn-info" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
                <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
            </div>
            <div class="col-md-4 col-xs-12">
                <input type="text" class="form-control" v-model="buscar" v-on:keyup="netix_buscar()" placeholder="BUSCAR REGISTRO . . .">
            </div>
        </div>
    </div> <br>
    
    <div class="netix_body_card">
        <input type="hidden" id="netix_opcion" value="1">

        <div class="netix_cargando" v-if="cargando">
            <img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
        </div>

        <div v-if="!cargando">
            <div class="row">
                <div class="col-md-3 col-xs-12" v-for="dato in datos">
                    <div class="x_panel" align="center"> <br>
                        <img v-bind:src="`<?php echo base_url();?>public/img/personas/${dato.foto}`" class="img-circle img-thumbnail" style="height:80px;">
                        <div class="x_content">
                            <h4 class="name">{{dato.razonsocial}}</h4>
                            <div>
                                <h6> <span class="label label-danger">{{dato.documento}}</span> </h6>
                                <h6> <i class="fa fa-home"></i> {{dato.direccion}} </h6>
                                <h6> <i class="fa fa-phone"></i> {{dato.telefono}}</h6>
                                <h6> <i class="fa fa-google-plus"></i> {{dato.email}} </h6>
                                <h6> <b>CARGO</b> {{dato.cargo}} </h6>
                                <h6> <b>AREA</b> {{dato.area}} </h6>
                            </div>
                            <div class="netix_radio_1"> 
                                <input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codpersona)"> 
                            </div>
                            <span> <a class="text-danger"> <b>S/. {{dato.sueldo}}</b> </a> </span> <br>
                        </div>
                    </div>
                </div>
            </div> <hr>

            <?php include("application/views/netix/netix_paginacion.php");?>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>netix/netix_datos.js"> </script>