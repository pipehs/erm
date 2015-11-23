@extends('master')

@section('title', 'Procesos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="procesos">Procesos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<span>Procesos</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="no-move"></div>
			</div>
			<div class="box-content">

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

		{!! link_to_route('procesos.create', $title = 'Agregar Proceso', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
		{!! link_to_route('procesos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
	@else
		{!! link_to_route('procesos.verbloqueados', $title = 'Ver Bloqueados', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
	@endif
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2">
					<thead>
						<tr>
							<th><small>Nombre</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>Descripci&oacute;n</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>Fecha Creaci&oacute;n</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>Fecha Expiraci&oacute;n</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>¿Depende de otro proceso?</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>Subprocesos</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th><small>Organizaciones</small><label><input type="text" placeholder="Filtrar" /></label></th>
							<th>Acci&oacute;n</th>
							<th>Acci&oacute;n</th>
						</tr>
					</thead>
	<tr style="display:none;">
	<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
	@foreach ($procesos as $proceso)
		<tr>
		<td>{{ $proceso['nombre'] }}</td>
		<td>{{ $proceso['descripcion'] }}</td>
		<td>{{ $proceso['fecha_creacion'] }}</td>
		<td>{{ $proceso['fecha_exp'] }}</td>
		<td>{{ $proceso['proceso_dependiente'] }}</td>
		<td><ul style="none">
		@foreach ($subprocesos as $subproceso)
			@if ($subproceso['proceso_id'] == $proceso['id'])
				<li>{{ $subproceso['nombre'] }}</li>
			@endif
		@endforeach
		</ul></td>
		<td><ul>
		@foreach ($organizaciones as $organizacion)
			@if ($organizacion['proceso_id'] == $proceso['id'])
				<li>{{ $organizacion['nombre'] }}</li>
			@endif
		@endforeach
		</ul></td>
		<td><div>
			@if ($proceso['estado'] == 0)
	            {!! link_to_route('procesos.edit', $title = 'Editar', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('procesos.desbloquear', $title = 'Desbloquear', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group --></td>
		<td><div>
			@if ($proceso['estado'] == 0)
	            {!! link_to_route('procesos.bloquear', $title = 'Bloquear', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-danger']) !!}
	        @else
	        	{!! link_to_route('procesos.bloquear', $title = 'Eliminar', $parameters = $proceso['id'], $attributes = ['class'=>'btn btn-danger']) !!}
	        @endif
	        </div><!-- /btn-group -->
	    </td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
<script>
// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect2);
}
function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Add Drag-n-Drop feature
	WinMove();
});
</script>
@stop
