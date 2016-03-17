@extends('master')

@section('title', 'Agregar Plan de auditor&iacute;a')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_auditoria">Auditor&iacute;as</a></li>
			<li><a href="plan_auditoria.create">Agregar plan de auditor&iacute;a</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Plan de Auditor&iacute;as</span>
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

			Ingrese la informaci&oacute;n asociada al nuevo plan de auditor&iacute;a.
				<div id="cargando"><br></div>
				{!!Form::open(['route'=>'agregar_plan','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('auditorias.form')
				{!!Form::close()!!}

				<center>
					{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-success'])!!}
				<center>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Informaci&oacute;n de riesgos asociados a una organizaci&oacute;n</span>
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
				<div id="cargando2"></div>
				<table id="riesgos" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Informaci&oacute;n hist&oacute;rica de &uacute;ltimo plan de auditor&iacute;a</span>
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

				<div id="informacion">Sin informaci&oacute;n</div>
			</div>
		</div>
	</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/create_edit_audit_plan.js')!!}
@stop
