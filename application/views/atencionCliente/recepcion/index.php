<div id="netix_datos">
    <div class="netix_header">
        <div class="row netix_header_title">
            <div class="col-md-8 col-xs-12"> <h5>ATENCIÓN AL CLIENTE</h5> </div>
        </div>
        <div class="row">
            <div class="col-md-8 netix_header_button">
                <button type="button" class="btn btn-success" v-on:click="netix_nuevo()"> <i class="fa fa-plus-square"></i> NUEVO </button>
                <button type="button" class="btn btn-info" v-on:click="netix_editar()"> <i class="fa fa-edit"></i> EDITAR </button>
                <button type="button" class="btn btn-danger" v-on:click="netix_eliminar()"> <i class="fa fa-trash-o"></i> ELIMINAR </button>
                <button type="button" class="btn btn-warning" v-on:click="netix_prueba()"> <i class="fa fa-arrow"></i> PRUEBA </button>
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
    <div class="netix_body">
        <div class="netix_cargando" v-if="cargando">
            <img src="<?php echo base_url();?>public/img/netix_loading.gif"> <h5>CARGANDO DATOS</h5>
        </div>

        <div v-if="!cargando">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th width="5px;"> <center> <i class="fa fa-circle-o"></i> </center> </th>
                            <th width="5px"><i class="fa fa-file-o"></i></th>
                            <th>CLIENTE</th>
                            <th>EMPLEADO</th>
                            <th>PRODUCTO</th>
                            <th>MARCA</th>
                            <th>MODELO</th>
                            <th width="80px">FECHA</th>
                            <th>DESCRIPCIÓN</th>
                            <th>TIPO</th>
                            <!--<th>COMPROBANTE</th>-->
                            <th width="100px">IMPORTE</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="dato in datos" v-bind:class="[dato.estado==0 ? 'netix_anulado':'']">
                            <td v-if="dato.estado!=0"> 
                                <input type="radio" class="netix_radio" name="netix_seleccionar" v-on:click="netix_seleccionar(dato.codrecepcion)"> 
                            </td>
                            <td v-if="dato.estado==0" style="padding:15px;"></td> 
                            <td>{{dato.codrecepcion}}</td>
                            <td>{{dato.nombrepersona}}</td>
                            <td>{{dato.nombreempleado}}</td>
                            <td>{{dato.producto}}</td>
                            <td>{{dato.marca}}</td>
                            <td>{{dato.modelo}}</td>
                            <td>{{dato.fecharecepcion}}</td>
                            <td>{{dato.descripcion}}</td>
                            <td>{{dato.tipopago}}</td>
                        <!--    <td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>-->
                            <td> <b style="font-size:17px;">S/. {{dato.importe}}</b> </td>
                            
                        </tr>
                    </tbody>
                </table>
            </div>
            <?php include("application/views/netix/netix_paginacion.php");?>

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
    </div>

    </div>
</div>

<script src="<?php echo base_url();?>netix/netix_recepcion/index.js"> </script>