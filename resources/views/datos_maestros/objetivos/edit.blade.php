@extends('master')

@section('title', 'Editar Objetivo corporativo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="objetivos">Objetivos</a></li>
			<li><a href="objetivos/{{ $objetivo['id'] }}/edit">Editar Objetivo</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Objetivos</span>
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
			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif
			Ingrese los nuevos datos para el objetivo seleccionado.
				{!!Form::model($objetivo,['route'=>['objetivos.update',$objetivo->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('datos_maestros.objetivos.form')
				{!!Form::close()!!}

				<center>
					<a href="objetivos_plan.{{$strategic_plan_id}}" class="btn btn-danger">Volver</a>	
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
$(document).ready(function () {
	$("#perspective").change();
});
</script>
{!!Html::script('assets/js/create_objectives.js')!!}
@stop