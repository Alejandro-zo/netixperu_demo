<div id="netix_form">
	<form id="formulario" class="form-horizontal" v-on:submit.prevent="netix_guardar()"> <br>
		<input type="hidden" name="codregistro" v-model="campos.codregistro">

		<div class="row form-group">
			<div class="col-md-12 col-xs-12">
				<label>DESCRIPCION PRODUCTO (*)</label>
	        	<input type="text" id="descripcion" v-model="campos.descripcion" class="form-control" autocomplete="off" placeholder="Descripcion . . ." maxlength="100" required />
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-12">
				<label>FAMILIA PRODUCTO (*)</label>
				<div class="input-group">
					<select class="form-control" id="codfamilia" v-model="campos.codfamilia" required>
			        	<option value="">SELECCIONE ...</option>
			        	<option v-for="dato in familias" v-bind:value="dato.codfamilia"> {{dato.descripcion}} </option>
			        </select>
					<span class="input-group-btn">
						<button type="button" class="btn btn-success" v-on:click="netix_nuevo_extencion('almacen/familias')"><i class="fa fa-plus-square"></i></button>
					</span>
				</div>
			</div>
			<div class="col-md-6 col-xs-12">
				<label>LINEA PRODUCTO (*)</label>
				<div class="input-group">
					<select class="form-control" id="codlinea" v-model="campos.codlinea" required>
			        	<option value="">SELECCIONE ...</option>
			        	<option v-for="dato in lineas" v-bind:value="dato.codlinea"> {{dato.descripcion}} </option>
			        </select>
					<span class="input-group-btn">
						<button type="button" class="btn btn-success" v-on:click="netix_nuevo_extencion('almacen/lineas')"><i class="fa fa-plus-square"></i></button>
					</span>
				</div>
			</div>
        </div>
        <div class="row form-group">
			<div class="col-md-12 col-xs-12">
				<label>MARCA PRODUCTO (*)</label>
				<div class="input-group">
					<select class="form-control" id="codmarca" v-model="campos.codmarca" required>
			        	<option value="">SELECCIONE ...</option>
			        	<option v-for="dato in marcas" v-bind:value="dato.codmarca"> {{dato.descripcion}} </option>
			        </select>
					<span class="input-group-btn">
						<button type="button" class="btn btn-success" v-on:click="netix_nuevo_extencion('almacen/marcas')"><i class="fa fa-plus-square"></i></button>
					</span>
				</div>
			</div>
        </div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-6">
				<label>CODIGO MANUAL</label>
	        	<input type="text" id="codigo" v-model.trim="campos.codigo" class="form-control" autocomplete="off" placeholder="Codigo . . ." />
			</div>
			<div class="col-md-6 col-xs-6"> 
	    		<label>TIPO PRODUCTO</label>
	    		<select id="controlstock" v-model="campos.controlstock" class="form-control">
	        		<option value="1">PRODUCTO / BIEN</option>
	        		<option value="0">SERVICIO</option>
	        	</select>
	    	</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-6">
				<label>IMAGEN PRODUCTO</label>
				<input type="hidden" name="codproducto" id="codproducto">
				<span class="foto">
					<input type="file" name="foto" id="foto" accept="image/*" class="upload" />
				</span>
				<label for="foto"> <span><i class="fa fa-upload"></i> CARGAR IMAGEN</span> </label>
			</div>
			<div class="col-md-6 col-xs-6">
				<label>PARA VENDER</label>
				<select id="paraventa" v-model="campos.paraventa" class="form-control">
	        		<option value="1">SI</option>
	        		<option value="0">NO</option>
	        	</select>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-6 col-xs-6">
				<label>CALCULAR CON EL TOTAL</label>
	        	<select id="calcular" v-model="campos.calcular" class="form-control">
	        		<option value="0">SIN CALCULAR</option>
	        		<option value="1">CANTIDAD</option>
	        		<option value="2">PRECIO</option>
	        	</select>
			</div>
	    	<div class="col-md-6 col-xs-6">
				<label>DESTINO ATENCION</label>
	        	<select v-model.trim="campos.codatencion" class="form-control">
	        		<?php 
	        			foreach ($atenciones as $key => $value) { ?>
	        				<option value="<?php echo $value["codatencion"];?>"><?php echo $value["descripcion"];?></option>
	        			<?php }
	        		?>
	        	</select>
			</div>
	    </div>
	    <div class="row form-group">
			<div class="col-md-12 col-xs-12">
				<label>CARACTERISTICAS PRODUCTO</label>
	        	<input type="text" v-model="campos.caracteristicas" class="form-control" autocomplete="off" placeholder="Caracteristicas . . ." maxlength="255" />
			</div>
		</div>
	    <div class="row form-group">
	    	<div class="col-md-1">
	    		<input type="hidden" id="afectoigvcompra" v-model="campos.afectoigvcompra">
	    		<input type="checkbox" id="afectoigvcompra_check" style="height:18px;width:18px;"> 
	    	</div>
	    	<div class="col-md-3"> <label style="padding-top:3px;">IGV COMPRA</label> </div>
	    	<div class="col-md-1"> 
	    		<input type="hidden" id="afectoigvventa" v-model="campos.afectoigvventa">
	    		<input type="checkbox" id="afectoigvventa_check" style="height:18px;width:18px;"> 
	    	</div>
	    	<div class="col-md-3"> <label style="padding-top:3px;">IGV VENTA</label> </div>

	    	<div class="col-md-1"> 
	    		<input type="hidden" id="afectoicbper" v-model="campos.afectoicbper">
	    		<input type="checkbox" id="afectoicbper_check" style="height:18px;width:18px;"> 
	    	</div>
	    	<div class="col-md-3"> <label style="padding-top:3px;">ICBPER</label> </div>
	    </div> 

	    <h5 class="text-center" style="background:#1ab394;color:#fff;padding:15px 0px;"> <b>UNIDADES DE MEDIDA Y PRECIOS</b> </h5>
		<div class="row form-group">
        	<div class="col-md-8 col-xs-6">
	    		<label>UNIDAD PRODUCTO</label>
                <select class="form-control" id="codunidad" v-model="campos_1.codunidad">
		    		<option value="">SELECCIONE</option>
		            <?php 
		                foreach ($unidades as $key => $value) { ?>
		                    <option value="<?php echo $value['codunidad'];?>">
		                    	<?php echo $value["descripcion"]." (".$value["oficial"].")";?> 
		                    </option>
		                <?php }
		            ?>
		    	</select>
		    </div>
		    <div class="col-md-4 col-xs-6">
	    		<label>FACTOR&nbsp;UNIDAD</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.factor" placeholder="Factor" autocomplete="off">
		    </div>
        </div>
        <div class="row form-group">
        	<div class="col-md-4 col-xs-6">
	    		<label>PRECIO COMPRA</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.preciocompra" placeholder="S/." autocomplete="off">
		    </div>
		    <div class="col-md-4 col-xs-6">
	    		<label>PRECIO V.PUBLICO</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.pventapublico" placeholder="S/." autocomplete="off">
		    </div>
        	<div class="col-md-4 col-xs-6">
	    		<label>PRECIO V. MINIMO</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.pventamin" placeholder="S/." autocomplete="off">
		    </div>
		</div>
		<div class="row form-group">
			<div class="col-md-4 col-xs-6">
	    		<label>PRECIO V. CREDITO</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.pventacredito" placeholder="S/." autocomplete="off">
		    </div>
        	<div class="col-md-4 col-xs-6">
	    		<label>PRECIO V. XMAYOR</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.pventaxmayor" placeholder="S/." autocomplete="off">
		    </div>
		    <div class="col-md-4 col-xs-6">
	    		<label>PRECIO V. ADICI.</label>
                <input type="number" step="0.001" class="form-control" v-model.number="campos_1.pventaadicional" placeholder="S/." autocomplete="off">
		    </div>
		</div>
		<div class="row form-group">
			<div class="col-md-8 col-xs-12">
	    		<label>CODIGO BARRA</label>
                <input type="text" class="form-control" v-model="campos_1.codigobarra" placeholder="Codigo barra" autocomplete="off" maxlength="30" v-on:keypress="netix_codigobarra($event)">
		    </div>
			<div class="col-md-4 col-xs-12">
		    	<label><br></label>
                <button type="button" class="btn btn-success btn-block" v-on:click="netix_addunidad()"><i class="fa fa-plus-circle"></i> AGREGAR</button>
	    	</div>
		</div>
		<div class="table-responsive">
			<table class="table table-bordered table-condensed">
				<thead style="background:#2f4050;color: #fff;">
					<tr>
						<th>EDITAR</th>
						<th>ELIMINAR</th>
						<th>UNIDAD</th>
						<th>FACTOR</th>
						<th>P.COMPRA</th>
						<th>P.VENTA</th>
						<th>P.MINIMO</th>
						<th>P.CREDITO</th>
						<th>P.MAYOR</th>
						<th>P.OTROS</th>
						<th>C.BARRA</th>
					</tr>
				</thead>
				<tbody style="font-size:13px;">
					<tr v-for="(uni,index) in unidades" :key="uni.factor">
						<td>
							<button type="button" class="btn btn-warning btn-xs btn-table" v-on:click="netix_ediunidad(index,uni)">EDITAR</button>
						</td>
						<td>
							<button type="button" class="btn btn-danger btn-xs btn-table" v-on:click="netix_deleteunidad(index,uni)">ELIMINAR</button>
						</td>
						<td><b>{{uni.unidad}}</b></td>
						<td>{{uni.factor}}</td>
						<td>{{uni.preciocompra}}</td>
						<td>{{uni.pventapublico}}</td>
						<td>{{uni.pventamin}}</td>
						<td>{{uni.pventacredito}}</td>
						<td>{{uni.pventaxmayor}}</td>
						<td>{{uni.pventaadicional}}</td>
						<td>{{uni.codigobarra}}</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="form-group text-center"> <br>
			<button type="submit" class="btn btn-success" v-bind:disabled="estado==1"> <i class="fa fa-save"></i> GUARDAR </button>
			<button type="button" class="btn btn-danger" v-on:click="netix_cerrar()">CERRAR</button>
		</div>
	</form>

	<div id="modal_extencion" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body" id="extencion_modal"> </div>
			</div>
		</div>
	</div>
</div>

<script>
	var campos = {codregistro:"",descripcion:"",codfamilia:"",codlinea:"",codmarca:"",codigo:"",codigobarra:"",calcular:"0",controlstock:"1",afectoigvcompra:"0",afectoigvventa:"0",afectoicbper:"0",codatencion:"0",paraventa:"0", caracteristicas:""};
	var campos_1 = {codunidad:"",unidad:"",factor:"1",preciocompra:"0.00",pventapublico:"0.00",pventamin:"0.00",pventacredito:"0.00",pventaxmayor:"0.00",pventaadicional:"0.00",codigobarra:""};
</script>
<script src="<?php echo base_url();?>netix/netix_almacen/productos.js"></script>