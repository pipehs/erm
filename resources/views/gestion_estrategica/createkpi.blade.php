@extends('master')

@section('title', 'Crear KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI')!!}</li>
			<li>{!!Html::link('kpi.create.$org_id','Crear KPI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Crear KPI</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			Ingrese la informaci&oacute;n para el nuevo indicador.

			{!!Form::open(['route'=>'kpi.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
				@include('gestion_estrategica.kpiform')

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
@stop