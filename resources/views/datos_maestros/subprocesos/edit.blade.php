@extends('master')

@section('title', 'Modificar Subproceso')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="subprocesos">Subprocesos</a></li>
			<li><a href="subprocesos.edit.{{ $subproceso['id'] }}">Modificar Subprocesos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-file-o"></i>
					<span>Modificar Subproceso</span>
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
			Ingrese los nuevos datos del subproceso.
				{!!Form::model($subproceso,['route'=>['subprocesos.update',$subproceso->id],'method'=>'PUT','class'=>'form-horizontal','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					@include('datos_maestros.subprocesos.form')
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

