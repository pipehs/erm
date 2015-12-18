@extends('master')

@section('title', 'Agregar Organizaci&oacute;n')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="organization">Organizaciones</a></li>
			<li>{!! link_to('organization.create', $title = 'Agregar Organizaci&oacute;n') !!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Organizaciones</span>
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
			Ingrese los datos de la organizaci&oacute;n.
				{!!Form::open(['route'=>'organization.store','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('datos_maestros.organization.form')
				{!!Form::close()!!}

				<center>
				{!! link_to_route('organization.index', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop
