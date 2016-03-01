@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion_encuestas">Encuestas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuestas de Evaluaci&oacute;n</span>
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

		<p>Seleccione la encuesta que desea ver o enviar.</p>
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="margin: 0 auto;">
			<thead>
			<th>Nombre</th>
			<th>Ver</th>
			<th>Enviar</th>
			<th>Consolidar</th>
			</thead>

				@foreach ($encuestas as $encuesta)
					<tr>
					<td>{{ $encuesta['name'] }}</td>
					<td>
					 {!! link_to_route('evaluacion_encuestas.show', $title = 'Ver', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-success']) !!}
					 </td>
					<td>
					{!! link_to_route('evaluacion_encuestas.enviar', $title = 'Enviar', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-primary']) !!}
					</td>
					<td>
					{!! link_to_route('evaluacion_encuestas.consolidar', $title = 'Consolidar', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-danger']) !!}
					</td>
					 </tr>
				@endforeach
			</div>
		</div>
	</div>
</div>
@stop