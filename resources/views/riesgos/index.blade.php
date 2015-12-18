@extends('master')

@section('title', 'Riesgos Tipo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="riesgos">Identificación de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos</span>
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

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

		<p>En esta secci&oacute;n podr&aacute; identificar un riesgo formal en base al an&aacute;lisis realizado sobre los eventos de riesgo. 
		Tambi&eacute;n podr&aacute; ver los riesgos previamente identificados.</p>
		<center>
		{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Proceso', $parameters = 'P', $attributes = ['class'=>'btn btn-primary']) !!}
		&nbsp;&nbsp;
		{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Negocio', $parameters = 'N', $attributes = ['class'=>'btn btn-success']) !!}
		</center>
	
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Tipo</th>
	<th>Categor&iacute;a</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Expiraci&oacute;n</th>
	<th>Subprocesos u Objetivos Relacionados</th>
	<th>Causa</th>
	<th>Efecto</th>
	</thead>
	@foreach ($riesgos as $riesgo)
		<tr>
		<td>{{ $riesgo['nombre'] }}</td>
		<td>{{ $riesgo['descripcion'] }}</td>
		<td>{{ $riesgo['tipo'] }}</td>
		<td>{{ $riesgo['categoria'] }}</td>
		<td>{{ $riesgo['fecha_creacion'] }}</td>
		<td>{{ $riesgo['fecha_exp'] }}</td>
		<td>
		<ul>
		@foreach($relacionados as $subonegocio)
			@if ($subonegocio['risk_id'] == $riesgo['id'])
				<li>{{ $subonegocio['nombre'] }}</li>
			@endif
		@endforeach
		</ul>	
		</td>
		<td>{{ $riesgo['causa'] }}</td>
		<td>{{ $riesgo['efecto'] }}</td>
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

