@extends('master')

@section('title', 'Editar Control')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('controles','Controles')!!}</li>
			<li>{!!Html::link('controles.edit','Editar Control')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Editar Control</span>
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
				<div class="alert alert-success alert-dismissible" role="alert">
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

			<h4>{{ $kind }} para Control {{ $control }}.</h4>
				{!!Form::model($eval,['route'=>['control_evaluation.update',$eval->id],'method'=>'PUT','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data'])!!}
					@include('controles.evaluation_form')
				{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop
<script>
function gestionarHallazgos()
{
	@if(Session::has('message'))
		guardado = true
	@else
		guardado = false
	@endif
	
	if (guardado == false)
	{
		swal('Cuidado','Antes de poder gestionar hallazgos debe guardar los resultados de la prueba','warning');
	}
}
</script>
@section('scripts2')
{!!Html::script('assets/js/evaluar.js')!!}
@stop

