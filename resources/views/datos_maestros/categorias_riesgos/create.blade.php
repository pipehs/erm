@extends('master')

@section('title', 'Agregar Categor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_risks">Categor&iacute;as de Riesgos</a></li>
			<li><a href="categorias_risks.create">Agregar Categor&iacute;a</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Agregar Categor&iacute;a</span>
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
			Ingrese los datos de la nueva categor&iacute;a.
			{!!Form::open(['route'=>'categorias_risks.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
				@include('datos_maestros.categorias_riesgos.form')
			{!!Form::close()!!}
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
			</div>
		</div>
	</div>
</div>
@stop

