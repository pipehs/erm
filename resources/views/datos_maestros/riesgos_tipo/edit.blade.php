@extends('master')

@section('title', 'Modificar Riesgo')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('riesgostipo','Riesgos Tipo')!!}</li>
			<li>{!!Html::link('riesgostipo.edit','Modificar Riesgo')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Modificar Riesgo</span>
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
				{!!Form::model($riesgo,['route'=>['riesgostipo.update',$riesgo->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('datos_maestros.riesgos_tipo.form')
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'riesgostipo','method'=>'GET'])!!}
					{!!Form::submit('Volver', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script>
$(document).ready(function() {

	$("#agregar_causa").click(function() {
		$("#causa").empty();
		$("#causa").append('<div class="form-group">{!!Form::label("Causa",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("causa_nueva",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

	$("#agregar_efecto").click(function() {
		$("#efecto").empty();
		$("#efecto").append('<div class="form-group">{!!Form::label("Efecto",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("efecto_nuevo",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});
});
</script>
@stop

