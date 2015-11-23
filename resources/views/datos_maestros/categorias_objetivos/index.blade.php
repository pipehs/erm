@extends('master')

@section('title', 'Categor&iacute;as de Objetivos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_objetivos">Categor&iacute;as de Objetivos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Categor&iacute;s de Objetivos</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">

		{!! link_to_route('categorias_objetivos.create', $title = 'Agregar Categor&iacute;a', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('categorias_objetivos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('categorias_objetivos.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

	<table class="table table-bordered table-striped table-hover table-heading table-datatable">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Expiraci&oacute;n</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>
	@foreach ($objective_categories as $objective_category)
		<tr>
		<td>{{$objective_category['nombre']}}</td>
		<td>{{$objective_category['descripcion']}}</td>
		<td>{{$objective_category['fecha_creacion']}}</td>
		<td>{{$objective_category['fecha_exp']}}</td>
		<td>
			<div>
			@if ($objective_category['estado'] == 0)
	            {!! link_to_route('categorias_objetivos.edit', $title = 'Editar', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('categorias_objetivos.desbloquear', $title = 'Desbloquear', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($objective_category['estado'] == 0)
	            {!! link_to_route('categorias_objetivos.bloquear', $title = 'Bloquear', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-danger']) !!}
	        @else
	        	{!! link_to_route('categorias_objetivos.bloquear', $title = 'Eliminar', $parameters = $objective_category['id'], $attributes = ['class'=>'btn btn-danger']) !!}
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

