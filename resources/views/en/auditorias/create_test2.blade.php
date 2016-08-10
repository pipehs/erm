@extends('en.master')

@section('title', 'Create audit test')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="programas_auditoria">Audit programs</a></li>
			<li><a href="programas_auditoria.create_test">Create test</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Create audit test</span>
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

			Input data for the new audit test.
				<div id="cargando"><br></div>
				{!!Form::open(['route'=>'programas_auditoria.store_test','method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}

					<!-- Formulario compartido entre crear prueba (en programa), crear sin programa y editar prueba) -->
					@include('en.auditorias.form_test')

					{!!Form::hidden('audit_audit_plan_audit_program_id',$audit_program)!!}
					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary','id'=>'guardar'])!!}
						</center>
					</div>
				{!!Form::close()!!}

				<center>
					{!! link_to_route('programas_auditoria.show', $title = 'Return', $parameters = $audit_program, $attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
	</div>
</div>
@stop
@section('scripts2')
<script>
$(document).ready(function () {
	type_id = "NULL"
});
</script>
{!!Html::script('assets/js/en/type_audit_test.js')!!}
@stop