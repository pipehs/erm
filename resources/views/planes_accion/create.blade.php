@extends('master')

@section('title', 'Agregar plan de acci&oacute;n')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('action_plans','Planes de acci&oacute;n')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Plan de acci&oacute;n</span>
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

			Ingrese los del nuevo plan de acci&oacute;n para la organizaci&oacute;n <b>{{ $org }}</b> <br><br>

			{!!Form::open(['route'=>'action_plan.store','method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					@include('planes_accion.form')
			{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

$('#kind').change(function() {

	if ($('#kind').val() != '')
	{
		$("#issues").empty();
		//obtenemos todas las causas
		$.get('get_issues.'+$('#kind').val()+'.{{ $org_id }}', function (result) {
			//parseamos datos obtenidos
			var datos = JSON.parse(result);
			var issues = '<option value="" disabled selected>- Seleccione </option>';
			$(datos).each( function() {
				issues += '<option value='+this.id+'>'+this.name+'</option>';
			});

			$("#issues").append(issues);
			$("#issues").change();
		});
	}
		
});
</script>
@stop