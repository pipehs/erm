@extends('master')

@section('title', 'Agregar Organizaci&oacute;n')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="objetivos">Objetivos</a></li>
			<li><a href="objetivos/create">Agregar Objetivo</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-puzzle-piece"></i>
					<span>Agregar Objetivo</span>
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
			Ingrese los datos del nuevo objetivo corporativo para la organizaci&oacute;n {{ $_GET['nombre_organizacion'] }}
				{!!Form::open(['route'=>'objetivos.store','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('datos_maestros.objetivos.form')
				{!!Form::close()!!}
				<center>
				{!! link_to_route('objetivos.index', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script>
$(document).ready(function() {
	// Initialize datepicker
	$('#input_date').datepicker({setDate: new Date()});
	// Initialize datepicker
	$('#input_date2').datepicker({setDate: new Date()});
	// Add Drag-n-Drop feature
	WinMove();
});
</script>
@stop

