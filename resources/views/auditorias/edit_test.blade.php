@extends('master')

@section('title', 'Editar prueba auditor&iacute;a')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="programas_auditoria">Programas de auditor&iacute;as</a></li>
			<li><a href="programas_auditoria.edit.{{ $audit_test->id }}">Editar prueba</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Editar prueba de auditor&iacute;a</span>
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

			Modifique la informaci&oacute;n que desee de la prueba de auditor&iacute;a <b>{{ $audit_test['name'] }}</b>.
				<div id="cargando"><br></div>

				{!!Form::model($audit_test,['route'=>['programas_auditoria.update_test',$audit_test->id],'method'=>'PUT','class'=>'form-horizontal','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}	

					@include('auditorias.form_test')

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>
				{!!Form::close()!!}

				<center>
					{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
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

	type_id = {{ $type_id }}
	setTimeout(function() {
         getType(1);
    }, 500);
});
</script>
{!!Html::script('assets/js/type_audit_test.js')!!}
{!!Html::script('assets/js/descargar.js')!!}
@stop