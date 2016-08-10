@extends('en.master')

@section('title', 'Edit audit program')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_auditoria">Audits</a></li>
			<li><a href="programas_auditoria.edit.{{ $audit_audit_plan_audit_program->id }}">Audit Program</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Edit Audit Program</span>
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

			Change the data that you want to update from the audit program <b>{{ $program['name'] }}</b>.
				<div id="cargando"><br></div>

				{!!Form::model($program,['route'=>['programas_auditoria.update_program',
				$audit_audit_plan_audit_program->id],'method'=>'PUT','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}
					@include('en.auditorias.form_program')

					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary','id'=>'guardar'])!!}
						</center>
					</div>
				{!!Form::close()!!}

				<center>
					{!! link_to_route('programas_auditoria.show', $title = 'Return', $parameters = $audit_audit_plan_audit_program->id,
                 		$attributes = ['class'=>'btn btn-success'])!!}
				<center>
			</div>
		</div>
	</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/descargar.js')!!}
<script>
</script>
@stop