@extends('master')

@section('title', 'Auditor&iacute;as - Ejecuci&oacute;n de auditor&iacute;as')

@section('content')

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

				{!!Form::open(['route'=>'agregar_ejecucion','method'=>'POST','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data'])!!}
	      			
	      			@include('auditorias.form_basico_audit')

					<div id="audit_programs"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'btn_guardar','disabled'=>'true'])!!}
						</center>
					</div>
					
				{!!Form::close()!!}

					<center>
						{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
						 $attributes = ['class'=>'btn btn-danger'])!!}
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
@stop
