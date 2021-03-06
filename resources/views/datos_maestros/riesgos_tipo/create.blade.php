@extends('master')

@section('title', 'Agregar Riesgo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('riskstype','Riesgos Tipo')!!}</li>
			<li>{!!Html::link('riskstype.create','Agregar Riesgo')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Agregar Riesgo</span>
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
			Ingrese los datos del riesgo.
				{!!Form::open(['route'=>'riskstype.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
					@include('datos_maestros.riesgos_tipo.form')
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
{!!Html::script('assets/js/create_edit_risks.js')!!}
@stop

