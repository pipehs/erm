@extends('master')

@section('title', 'Modificar Riesgo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('riskstype','Riesgos Tipo')!!}</li>
			<li>{!!Html::link('riskstype.edit','Modificar Riesgo')!!}</li>
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
				{!!Form::model($riesgo,['route'=>['riskstype.update',$riesgo->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('datos_maestros.riesgos_tipo.form')
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'riskstype','method'=>'GET'])!!}
					{!!Form::submit('Volver', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
{!!Html::script('assets/js/create_edit_risks.js')!!}
@stop

