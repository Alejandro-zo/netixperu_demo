<div id="netix_datos">
	<div class="netix_header">
		<div class="row netix_header_title">
			<div class="col-md-5 col-xs-12"> <h5>FACTURACION ELECTRONICA</h5> </div>
			<div class="col-md-2 col-xs-12">
				<a href="https://e-menu.sunat.gob.pe/cl-ti-itmenu/MenuInternet.htm" class="btn btn-warning btn-block" target="_blank">
					<i class="fa fa-flag-o"></i> PORTAL DE SUNAT
				</a>
			</div>
			<div class="col-md-3 col-xs-12">
				<button type="button" class="btn btn-success btn-block" v-on:click="netix_consultas()">
					<i class="fa fa-print"></i> CONSULTA COMPROBANTES
				</button>
			</div>
			<div class="col-md-2 col-xs-12">
				<div class="input-group">
					<input type="text" class="form-control datepicker" readonly id="fecha" value="<?php echo date('Y-m-d');?>">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button> 
					</span>
				</div>
			</div>
		</div>

		<div class="" role="tabpanel" data-example-id="togglable-tabs">
			<ul class="nav nav-tabs bar_tabs" role="tablist">
				<li role="presentation" class="active">
					<a href="#facturas" role="tab" data-toggle="tab" aria-expanded="true">
						<span class="hidden-xs">FACTURAS ELECTRÓNICAS</span> <b>F.E.</b>
					</a>
				</li>
				<li role="presentation" class="">
					<a href="#boletas" role="tab" data-toggle="tab" aria-expanded="false">
						<span class="hidden-xs">BOLETAS ELECTRÓNICAS</span> <b>B.E.</b>
					</a>
				</li>
				<li role="presentation" class="">
					<a href="#notas" role="tab" data-toggle="tab" aria-expanded="false">
						<span class="hidden-xs">NOTAS DE CREDITO Y DEBITO</span> <b>N.C.D</b>
					</a>
				</li>
				<li role="presentation" class="">
					<a href="#sunat" role="tab" data-toggle="tab" aria-expanded="false">
						<span class="hidden-xs">CONSULTAS SUNAT</span> <b>S</b>
					</a>
				</li>
			</ul>
			<div id="tab_facturacion" class="tab-content">
				<div role="tabpanel" class="tab-pane fade active in" id="facturas">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="100px"><i class="fa fa-send"></i> ENVIAR</th>
									<th>DOCUMENTO</th>
									<th>RAZON SOCIAL</th>
									<th>COMPROBANTE</th>
									<th>FECHA</th>
									<th>IMPORTE</th>
									<th width="10px">ESTADO</th>
									<th width="50px">XML</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="dato in facturas">
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-bind:id="dato.codkardex" v-on:click="comprobantes_enviar(dato.codkardex,'01')">
											<i class="fa fa-send"></i> ENVIAR
										</button> 
									</td>
									<td> {{dato.documento}} </td>
									<td> {{dato.cliente}} </td>
									<td> {{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
									<td> {{dato.fechacomprobante}} </td>
									<td> S/. {{dato.importe}} </td>
									<td>
										<b v-if="dato.estado==0" style="color:#d43f3a">PENDIENTE</b>
										<b v-if="dato.estado==3" style="color:#eea236">CON EXCEPCIONES</b> 
										<b v-if="dato.estado==4" style="color:#eea236">RECHAZADO</b> 
									</td>
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-on:click="comprobantes_xml(dato.codkardex,'01')">
											<i class="fa fa-cloud-download"></i> XML
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="row">
						<div class="col-md-8 col-xs-12">
							<h5 class="text-danger"><b>RESUMEN DE FACTURAS ANULADAS</b></h5>
						</div>
						<div class="col-md-4 col-xs-12">
							<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="resumenes_generar(1)">
								<i class="fa fa-file-o"></i> GENERAR RESUMEN FACTURAS ANULADAS
							</button>
						</div>
					</div>
					
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="100px"><i class="fa fa-send"></i> ENVIAR</th>
									<th>FECHA RESUMEN</th>
									<th>PERIODO</th>
									<th>NOMBRE XML</th>
									<th>ESTADO</th>
									<th width="50px">VER</th>
									<th width="10px"><i class="fa fa-trash-o"></i></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="dato in facturas_anuladas">
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-bind:id="dato.periodo" v-on:click="resumenes_enviar(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)">
											<i class="fa fa-send"></i> ENVIAR
										</button>
									</td>
									<td> {{dato.fecharesumen}} </td>
									<td> {{dato.periodo}} </td>
									<td> {{dato.nombre_xml}} </td>
									<td>
										<b v-if="dato.estado==0" style="color:#d43f3a">PENDIENTE</b>
										<b v-if="dato.estado==3" style="color:#eea236">CON EXCEPCIONES</b> 
										<b v-if="dato.estado==4" style="color:#eea236">RECHAZADO</b> 
									</td>
									<td> 
										<button type="button" class="btn btn-primary btn-sm" v-on:click="resumenes_ver(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)"><i class="fa fa-file"></i> VER</button>
									</td>
									<td>
										<button type="button" class="btn btn-danger btn-sm" v-on:click="resumenes_anular(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="boletas">
					
					<div class="row">
						<div class="col-md-4 col-xs-12">
							<h5 class="text-success"><b>RESUMEN DE BOLETAS</b></h5>
						</div>
						<div class="col-md-4 col-xs-12">
							<button type="button" class="btn btn-info btn-sm btn-block" v-on:click="resumenes_generar(3)">
								<i class="fa fa-file-o"></i> GENERAR RESUMEN DE BOLETAS
							</button>
						</div>
						<div class="col-md-4 col-xs-12">
						    <button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="resumenes_generar(4)">
						       	<i class="fa fa-file-o"></i> GENERAR RESUMEN BOLETAS ANULADAS
						    </button>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="100px"><i class="fa fa-send"></i> ENVIAR</th>
									<th width="50px">TIPO</th>
									<th>FECHA RESUMEN</th>
									<th>PERIODO</th>
									<th>NOMBRE XML</th>
									<th>ESTADO</th>
									<th width="50px">XML</th>
									<th width="50px">VER</th>
									<th width="10px"><i class="fa fa-trash-o"></i></th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="dato in resumenes_boletas">
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-bind:id="dato.periodo" v-on:click="resumenes_enviar(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)">
											<i class="fa fa-send"></i> ENVIAR
										</button>
									</td>
									<td>
										<span class="label label-danger" v-if="dato.codresumentipo==3">RES. BOLETAS</span>
										<span class="label label-danger" v-if="dato.codresumentipo==4">RES. BOLETAS ANULADAS</span>
									</td>
									<td> {{dato.fecharesumen}} </td>
									<td> {{dato.periodo}} </td>
									<td> {{dato.nombre_xml}} </td>
									<td>
										<b v-if="dato.estado==0" style="color:#d43f3a">PENDIENTE</b>
										<b v-if="dato.estado==3" style="color:#eea236">CON EXCEPCIONES</b> 
										<b v-if="dato.estado==4" style="color:#eea236">RECHAZADO</b> 
									</td>
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-on:click="resumenes_xml(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)"> <i class="fa fa-cloud-download"></i> XML</button>
									</td>
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-primary btn-sm" v-on:click="resumenes_ver(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)"><i class="fa fa-file"></i> VER</button>
									</td>
									<td style="padding-top:5px;">
										<button type="button" class="btn btn-danger btn-sm" v-on:click="resumenes_anular(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)">
											<i class="fa fa-trash-o"></i>
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="notas">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead>
								<tr>
									<th width="100px"><i class="fa fa-send"></i> ENVIAR</th>
									<th>DOCUMENTO</th>
									<th>RAZON SOCIAL</th>
									<th>COMPROBANTE</th>
									<th>FECHA</th>
									<th>IMPORTE</th>
									<th width="10px">ESTADO</th>
									<th width="50px">XML</th>
								</tr>
							</thead>
							<tbody>
								<tr v-for="dato in notas">
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-bind:id="dato.codkardex" v-on:click="comprobantes_enviar(dato.codkardex,'07')">
											<i class="fa fa-send"></i> ENVIAR
										</button> 
									</td>
									<td> {{dato.documento}} </td>
									<td> {{dato.cliente}} </td>
									<td> {{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
									<td> {{dato.fechacomprobante}} </td>
									<td> S/. {{dato.importe}} </td>
									<td>
										<b v-if="dato.estado==0" style="color:#d43f3a">PENDIENTE</b>
										<b v-if="dato.estado==3" style="color:#eea236">CON EXCEPCIONES</b> 
										<b v-if="dato.estado==4" style="color:#eea236">RECHAZADO</b> 
									</td>
									<td style="padding-top:5px;"> 
										<button type="button" class="btn btn-success btn-sm" v-on:click="comprobantes_xml(dato.codkardex,'07')">
											<i class="fa fa-cloud-download"></i> XML
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane fade" id="sunat">

					<form class="form-horizontal" v-on:submit.prevent="netix_consultasunat()" style="padding:10px 30px; border:2px solid #e7eaec;background:#f3f3f4">
						<div class="row">
							<div class="col-md-4 col-xs-12">
								<div class="form-group">
									<label>TIPO COMPROBANTE</label>
									<select v-model="sunat.tipo" class="form-control" required>
										<option value="01">FACTURA ELECTRONICA</option>
									</select>
								</div>
							</div>
							<div class="col-md-2 col-xs-12">
								<div class="form-group">
									<label>SERIE</label>
									<input type="text" v-model.trim="sunat.serie" class="form-control" required autocomplete="off" minlength="4" maxlength="4" style="text-transform: uppercase;" />
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
									<label>NRO COMPROBANTE</label>
									<input type="text" v-model.trim="sunat.nrocomprobante" class="form-control" required autocomplete="off" maxlength="8" />
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="form-group">
									<label>CONSULTAR CPE</label>
									<button type="submit" class="btn btn-success btn-block"><i class="fa fa-filter"></i> EN SUNAT</button>
								</div>
							</div>
						</div>
						<span><b style="color:#1c84c6;font-weight:bold">RESPUESTA SUNAT:</b> <span id="sunat_respuesta">SIN RESPUESTA</span></span>
					</form> <br>

					<div class="row">
						<div class="col-md-3 col-xs-12">
							<div class="form-group">
								<label><i class="fa fa-calendar"></i> DESDE</label>
								<input type="text" class="form-control input-sm datepicker" id="fecha_desde" value="<?php echo date('Y-m-d');?>">
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<div class="form-group">
								<label><i class="fa fa-calendar"></i> HASTA</label>
								<input type="text" class="form-control input-sm datepicker" id="fecha_hasta" value="<?php echo date('Y-m-d');?>">
							</div>
						</div>
						<div class="col-md-3 col-xs-12">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-warning btn-sm btn-block" v-on:click="sunat_recepcion()">
								<i class="fa fa-filter"></i> RECEPCION SUNAT
							</button>
						</div>
						<div class="col-md-3 col-xs-12">
							<label>&nbsp;</label>
							<button type="button" class="btn btn-primary btn-sm btn-block" v-on:click="sunat_quitar_icbper()">
								<i class="fa fa-cog"></i> QUITAR ICBPER DE LOS PENDIENTES
							</button>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered">
							<thead >
			                    <tr>
			                        <th width="10px"> <i class="fa fa-align-center"></i> </th>
			                        <th width="230px"> <i class="fa fa-code"></i> TIPO COMPROBANTE</th>
			                        <th width="100px"> <i class="fa fa-calendar-o"></i> FECHA</th>
			                        <th> <i class="fa fa-file-o"></i> ARCHIVO COMPROBANTE XML</th>
			                        <th width="10px"> <i class="fa fa-undo"></i></th>
			                    </tr>
			                </thead>
			                <tbody>
			                	<!-- <tr v-for="dato in datos">
			                		<td>{{dato.codigo}}</td>
			                		<td v-if="dato.tipo==0">
			                			<label class="badge badge-teal" v-if="dato.tipocom=='01'">FACTURA ELECTRONICA</label>
			                            <label class="badge badge-warning" v-if="dato.tipocom=='09'">GUIA DE REMISION</label>
			                            <label class="badge badge-info" v-if="dato.tipocom=='20'">RETENCION ELECTRONICA</label>
			                		</td>
			                		<td v-else="dato.tipo!=0">
			                			<label class="badge badge-teal">{{dato.tipocom}}</label>
			                		</td>
			                		<td>{{dato.fecha}}</td>
			                		<td>ARCHIVO {{dato.archivo}}.xml</td>
			                        <td>
			                        	<button type="button" class="btn btn-warning btn-sm btn-table" v-on:click="cpe_actualizar(dato)">
			                                <i class="fa fa-undo"></i>
			                            </button>
			                        </td>
			                	</tr> -->
			                </tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_resumenes" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"> <i class="fa fa-times-circle"></i> </button>
					<h4 class="modal-title" align="center"> <b style="letter-spacing:1px;">INFORMACION DEL RESUMEN</b> </h4>
				</div>

				<div class="modal-body" style="height:350px;overflow-y:auto;">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>RAZON SOCIAL</th>
								<th>COMPROBANTE</th>
								<th>F.COMPROBANTE</th>
								<th>F.ANULADO</th>
								<th width="100px">MOTIVO</th>
								<th>TOTAL</th>
								<th width="5px"><i class="fa fa-trash-o"></i></th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="dato in resumenes_info">
								<td>{{dato.cliente}}</td>
								<td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
								<td>{{dato.fechacomprobante}}</td>
								<td>{{dato.fechaanulacion}}</td>
								<td>{{dato.motivobaja}}</td>
								<td>{{dato.importe}}</td>
								<td>
									<button type="button" class="btn btn-danger btn-xs" v-on:click="resumenes_eliminar_kardex(dato)">
										<i class="fa fa-trash-o"></i>
									</button>
								</td>
							</tr>
						</tbody>
					</table>
					<div class="text-center">
						<button type="button" class="btn btn-info" v-on:click="resumenes_siguiente_correlativo()">SIGUIENTE CORRELATIVO</button>
						<button type="button" class="btn btn-danger" v-on:click="resumenes_actualizar()">ACTUALIZAR RESUMEN</button>
						<button type="button" class="btn btn-warning" v-on:click="resumenes_quitar_ticket()">QUITAR TICKET</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="modal_consultas" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" style="width:100%;margin:0px;">
			<div class="modal-content">
				<div class="modal-header" style="background:#13a89e;color:#fff;">
					<button type="button" class="close" data-dismiss="modal" style="font-size:27px;margin-bottom:0px;">
						<i class="fa fa-times-circle"></i> 
					</button>
					<h4 class="modal-title" align="center"> 
						<b style="letter-spacing:1px;"><?php echo $_SESSION["netix_empresa"];?> - CONSULTA COMPROBANTES</b> 
					</h4>
				</div>

				<div class="modal-body" id="consultas_modal">
					<div class="row">
						<div class="col-md-3">
							<div class="x_panel">
								<h5 class="text-center"> <b>CONSULTA COMPROBANTES ELECTRONICOS</b> </h5> <hr>
								<div class="row">
									<div class="col-md-6 col-xs-12">
										<label>FECHA DESDE</label>
										<input type="text" class="form-control datepicker" id="fdesde" value="<?php echo date('Y-m-d');?>">
									</div>
									<div class="col-md-6 col-xs-12">
										<label>FECHA HASTA</label>
										<input type="text" class="form-control datepicker" id="fhasta" value="<?php echo date('Y-m-d');?>">
									</div>
								</div> <br>
								<div class="row">
									<div class="col-md-6 col-xs-12">
										<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_reportes_cpe('reporte_facturas_enviados','comprobantes')"><i class="fa fa-send"></i> F.E ENVIADAS</button>
									</div>
									<div class="col-md-6 col-xs-12">
										<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="netix_reportes_cpe('reporte_facturas_anulados','resumenes')"><i class="fa fa-trash-o"></i> F.E ANULADAS</button>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 col-xs-12">
										<button type="button" class="btn btn-success btn-sm btn-block" v-on:click="netix_reportes_cpe('reporte_boletas_enviados','resumenes')"><i class="fa fa-send"></i> B.E ENVIADAS</button> 
									</div>
									<div class="col-md-6 col-xs-12">
										<button type="button" class="btn btn-danger btn-sm btn-block" v-on:click="netix_reportes_cpe('reporte_boletas_anulados','resumenes')"><i class="fa fa-trash-o"></i> B.E ANULADAS</button>
									</div>
								</div>
							</div>

							<div class="x_panel">
								<h5 class="text-center"> <b>FACTURAS ELECTRONICAS</b> </h5> <hr>

								<ul class="list-inline">
									<li>
										<p>
											<span class="icon"><i class="fa fa-square green"></i></span> 
											<b class="name">ACTIVAS: {{facturas_datos.enviados}}</b> 
										</p>
									</li>
									<li>
										<p>
											<span class="icon"><i class="fa fa-square red"></i></span> 
											<b class="name">ANULADAS: {{facturas_datos.anulados}}</b>
										</p>
									</li>
									<li>
										<p>
											<span class="icon"><i class="fa fa-square blue"></i></span> 
											<b class="name">TOTAL FACTURAS PENDIENTES: {{facturas_datos.pendientes}}</b>
										</p>
									</li>
								</ul>
							</div>
							<div class="x_panel">
								<h5 class="text-center"> <b>BOLETAS ELECTRONICAS</b> </h5> <hr>

								<ul class="list-inline">
									<li>
										<p>
											<span class="icon"><i class="fa fa-square green"></i></span> 
											<b class="name">ACTIVAS: {{boletas_datos.enviados}}</b>
										</p>
									</li>
									<li>
										<p>
											<span class="icon"><i class="fa fa-square red"></i></span>
											<b class="name">ANULADAS: {{boletas_datos.anulados}}</b>
										</p>
									</li>
									<li>
										<p>
											<span class="icon"><i class="fa fa-square blue"></i></span> 
											<b class="name">TOTAL BOLETAS PENDIENTES: {{boletas_datos.pendientes}}</b>
										</p>
									</li>
								</ul>
							</div>
						</div>

						<div class="col-md-9 table-responsive panel_comprobantes" style="height:350px; overflow-y: auto;">
							<table class="table table-bordered table-condensed" v-if="tipo_reporte=='comprobantes'">
								<thead>
									<tr>
										<th colspan="9" class="text-center">LISTA DE FACTURAS ELECTRONICAS</th>
									</tr>
									<tr>
										<th width="5px;">XML</b></th>
										<th width="5px;">CDR</b></th>
										<th width="5px;">CORREO</b></th>
										<th width="10px;">DOCUMENTO</th>
										<th>RAZON SOCIAL</th>
										<th>COMPROBANTE</th>
										<th width="80px;">FECHA</th>
										<th width="10px;">IMPORTE</th>
										<th>SUNAT CDR</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="dato in comprobantes_lista">
										<td> 
											<button type="button" class="btn btn-success btn-xs" v-on:click="comprobantes_xml(dato.codkardex,'01')" style="margin:0px;"><i class="fa fa-cloud-download"></i> XML</button>
										</td>
										<td>
											<button type="button" class="btn btn-danger btn-xs" v-on:click="comprobantes_cdr(dato.codkardex)" style="margin:0px;"><i class="fa fa-cloud"></i> CDR</button>
										</td>
										<td>
											<button type="button" class="btn btn-warning btn-xs" v-on:click="comprobantes_correo(dato)" style="margin:0px;"><i class="fa fa-send"></i> CORREO</button>
										</td>
										<td>{{dato.documento}}</td>
										<td>{{dato.razonsocial}}</td>
										<td>{{dato.seriecomprobante}}-{{dato.nrocomprobante}}</td>
										<td>{{dato.fechacomprobante}}</td>
										<td>{{dato.importe}}</td>
										<td style="font-size:9px;">{{dato.sunat}}</td>
									</tr>
								</tbody>
							</table>

							<div v-if="tipo_reporte=='resumenes'">
								<table class="table table-bordered table-condensed">
									<tr>
										<th colspan="9" class="text-center">LISTA DE RESUMENES ELECTRONICOS</th>
									</tr>
								</table>
								<div v-for="dato in resumenes_lista">
									<table class="table table-bordered table-condensed">
										<thead>
											<tr>
												<th width="5px;">XML</b></th>
												<th width="5px;">CDR</b></th>
												<th>NOMBRE XML</th>
												<th width="10px;">PERIODO</th>
												<th width="10px;">F.&nbsp;RESUMEN</th>
												<th colspan="2">SUNAT CDR</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td> 
													<button type="button" class="btn btn-success btn-xs" v-on:click="resumenes_xml(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)" style="margin:0px;"><i class="fa fa-cloud-download"></i> XML</button>
												</td>
												<td>
													<button type="button" class="btn btn-danger btn-xs" v-on:click="resumenes_cdr(dato.codresumentipo,dato.periodo,dato.nrocorrelativo)" style="margin:0px;"><i class="fa fa-cloud"></i> CDR</button>
												</td>
												<td><b>{{dato.nombre_xml}}</b></td>
												<td><b>{{dato.periodo}}</b></td>
												<td><b>{{dato.fecharesumen}}</b></td>
												<td colspan="2"><b>{{dato.descripcion_cdr}}</b></td>
											</tr>

											<tr>
												<td colspan="2"><b>COMPROBANTE</b></td>
												<td><b>MOTIVO BAJA</b></td>
												<td><b>IMPORTE</b></td>
												<td><b>DOCUMENTO</b></td>
												<td><b>RAZON SOCIAL</b></td>
												<th width="5px;">CDR</b></th>
											</tr>
											<tr v-for="d in dato.lista">
												<td colspan="2">{{d.seriecomprobante}}-{{d.nrocomprobante}}</td>
												<td>{{d.motivo}}</td>
												<td>{{d.importe}}</td>
												<td>{{d.documento}}</td>
												<td>{{d.razonsocial}}</td>
												<td>
													<button type="button" class="btn btn-warning btn-xs" v-on:click="consulta_cdr(dato.ticket)" style="margin:0px;"><i class="fa fa-cloud"></i> CDR</button>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div id="netix_infosunat" class="modal fade">
        <div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"> <i class="fa fa-times-circle"></i> </button>
					<h4 class="modal-title" align="center"> <b style="letter-spacing:1px;">INFORMACION COMPROBANTES DE SUNAT</b> </h4>
				</div>

				<div class="modal-body" style="height:350px;overflow-y:auto;">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="font-size:12px;">
                            <thead>
                                <tr>
                                    <th class="font-11" width="10px"> <i class="fa fa-align-center"></i></th>
                                    <th class="font-11"> <i class="fa fa-code"></i> DNI/RUC</th>
                                    <th class="font-11"> <i class="fa fa-user"></i> RAZON SOCIAL</th>
                                    <th class="font-11"> <i class="fa fa-calendar-o"></i> FECHA</th>
                                    <th class="font-11"> <i class="fa fa-dropbox"></i> COMPROBANTE</th>
                                    <th class="font-11"> <i class="fa fa-dollar"></i> TOTAL</th>
                                    <th class="font-11"> <i class="fa fa-flag"></i> DESCRIPCION DESDE SUNAT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="dato in sunatrecepcion">
                                    <td>{{dato.codkardex}}</td>
                                    <td>{{dato.documento}}</td>
                                    <td>{{dato.cliente}}</td>
                                    <td>{{dato.fechacomprobante}}</td>
                                    <td>{{dato.seriecomprobante}} - {{dato.nrocomprobante}}</td>
                                    <td>{{dato.importe}}</td>
                                    <td>
                                        <span class="badge badge-secondary" v-if="dato.descripcion=='>El comprobante existe y está aceptado.</'">{{dato.descripcion}}</span>
                                        <span class="badge badge-warning" v-else="dato.descripcion=='>El comprobante existe pero está de baja.</'">{{dato.descripcion}}</span>
                                        <span class="badge badge-danger" v-else="dato.descripcion!='>El comprobante existe y está aceptado.</'">{{dato.descripcion}}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url();?>netix/netix_facturacion/index.js"> </script>
<script>
	var pantalla = jQuery(document).height(); $("#consultas_modal").css({height: pantalla - 65}); 
	$(".panel_boletas").css({height: pantalla - 505}); $(".panel_comprobantes").css({height: pantalla - 75});

	$(".datepicker").datetimepicker({format: 'YYYY-MM-DD',ignoreReadonly: true}).attr("readonly","true");
</script>