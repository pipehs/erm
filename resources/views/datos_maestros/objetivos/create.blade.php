@extends('master')

@section('title', 'Agregar Organizaci&oacute;n')

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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif
			Ingrese los datos del nuevo objetivo corporativo para la organizaci&oacute;n {{ $_GET['nombre_organizacion'] }}
				{!!Form::open(['route'=>'objetivos.store','method'=>'POST','class'=>'form-horizontal'])!!}
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
{!!Html::script('assets/js/create_objectives.js')!!}
@stop