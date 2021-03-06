@extends('master')

@section('title', 'Agregar Categor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_risks">Categor&iacute;as de Riesgos</a></li>
			<li><a href="categorias_risks.edit.{{ $risk_category['id'] }}">Editar Categor&iacute;a</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Modificar Categor&iacute;a</span>
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
			Modifique los datos que desee cambiar de la categor&iacute;a.
			{!!Form::model($risk_category,['route'=>['categorias_risks.update',$risk_category->id],'method'=>'PUT','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
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

