<div class="row">
	<div class="col-md-6 hidden-xs">
		<p>TOTAL REGISTROS <b>{{paginacion.total}}</b> ENCONTRADOS</p>
	</div>
	<div class="col-md-6">
		<ul class="pagination pull-right">
			<li class="page-item disabled" v-if="paginacion.actual <= 1">
		    	<a class="page-link"> <i class="fa fa-angle-left"></i> ATRAS </a> 
		    </li>
		    <li class="page-item" v-if="paginacion.actual > 1">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(paginacion.actual - 1)"> 
		    		<i class="fa fa-angle-left"></i> ATRAS 
		    	</a> 
		    </li>

		    <li class="page-item" v-for="pag in netix_paginas" v-bind:class="[pag==netix_actual ? 'active':'']">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(pag)">{{pag}}</a> 
		    </li>

		    <li class="page-item" v-if="paginacion.actual < paginacion.ultima">
		    	<a class="page-link" href="#" v-on:click.prevent="netix_paginacion(paginacion.actual + 1)"> 
		    		SIGUE <i class="fa fa-angle-right"></i> 
		    	</a> 
		    </li>
		    <li class="page-item disabled" v-if="paginacion.actual >= paginacion.ultima">
		    	<a class="page-link"> SIGUE <i class="fa fa-angle-right"></i> </a> 
		    </li>
		</ul>
	</div>
</div>