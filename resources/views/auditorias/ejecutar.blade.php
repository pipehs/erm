@extends('master')

@section('title', 'Auditor&iacute;as - Ejecuci&oacute;n de auditor&iacute;as')

@section('content')

<style>
td {
	vertical-align:top;
}
</style>

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Auditor&iacute;as</a></li>
			<li><a href="ejecutar_pruebas">Ejecuci&oacute;n de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Ejecuci&oacute;n de auditor&iacute;as</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
	      	<p>En esta secci&oacute;n podr&aacute; ejecutar los planes de auditor&iacute;a con sus respectivos programas.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif

				@if(Session::has('error'))
					<div class="alert alert-success alert-danger" role="alert">
					{{ Session::get('error') }}
					</div>
				@endif

				<div id="cargando"><br></div>

				{!!Form::open(['route'=>'agregar_ejecucion','method'=>'POST','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
	      			
	      			@include('auditorias.form_basico_audit')

					<div id="audit_programs"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'btnsubmit','disabled'=>'true'])!!}
						</center>
					</div>
					
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
{!!Html::script('assets/js/audits.js')!!}
{!!Html::script('assets/js/ejecutar_audit.js')!!}
{!!Html::script('assets/js/descargar.js')!!}

<script>

$(document).ready(function () {
	rol = 0; //para ver si puede eliminar archivos o no
	@foreach (Session::get('roles') as $role)
		@if ($role == 1)
			rol = 1
			<?php break; //si es admin terminamos ciclo para no repetir menú ?>
		@endif
	@endforeach

	//seteamos datos si es que se está editando
	@if (Session::has('org_id'))
		setTimeout(organizations, 0)
		setTimeout(audit_plans,1000)
		setTimeout(audits,2000)	
	@endif
});

function organizations()
{
	$("#organizations").val({{Session::get('org_id')}});
	$("#organizations").change();
}
function audit_plans()
{
	$("#audit_plans").val({{Session::get('audit_plan_id')}});
	$("#audit_plans").change();
}
function audits()
{
	$("#audit").val({{Session::get('audit_id')}});
	$("#audit").change();
}

function gestionarHallazgos(id)
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
	else
	{
		window.location.href = 'hallazgos_test.'+id
	}
}
</script>
@stop
