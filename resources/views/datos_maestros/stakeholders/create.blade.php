@extends('master')

@section('title', 'Agregar Usuario')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('stakeholders','Usuarios')!!}</li>
			<li>{!!Html::link('stakeholders.create','Agregar Usuario')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Usuario</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif
			
			Ingrese los datos del usuario.
				{!!Form::open(['route'=>'stakeholders.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
					@include('datos_maestros.stakeholders.form')
				{!!Form::close()!!}

			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
	$("#agregar_rol").click(function() {
		$("#rol").empty();
		$("#rol").append('<div class="form-group">{!!Form::label("Tipo",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::text("rol_nuevo",null,["class"=>"form-control","required"=>"true","placeholder"=>"Ingrese nuevo rol"])!!}</div></div>');

	});
</script>
@stop
