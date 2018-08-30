@extends('master')

@section('title', 'Agregar Proceso')
@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('procesos','Procesos')!!}</li>
			<li>{!!Html::link('procesos.create','Agregar Procesos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Agregar Proceso</span>
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
			Ingrese los datos del proceso.
				{!!Form::open(['route'=>'procesos.store','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					@include('datos_maestros.procesos.form')
				{!!Form::close()!!}

			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<p>Este documento asociado a procesos, será relacionado a todas las organizaciones que se encuentren asociadas al mismo. Posteriormente en la opción de asignar atributos específicos, se podrá agregar atributos específicos para cada organización independiente</p>
</div>
@stop

