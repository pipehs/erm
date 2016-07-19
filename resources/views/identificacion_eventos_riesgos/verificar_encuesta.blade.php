<!-- extends('master2') Se utilizará esta en el futuro para que no aparezca el menú de admin -->

@extends('master')

@section('title', 'Identificaci&oacute;n de eventos de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="identificacion.encuesta.{{ $encuesta['id'] }}">Responder Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta: {{ $encuesta['name'] }}</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif

			{!!Form::open(['route'=>'identificacion.resp_encuesta','method'=>'POST','class'=>'form-horizontal'])!!}

			<div class="form-group">
				<small>
			    {!!Form::label('Ingrese su Rut (sin dígito verificador)',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::text('id',null,
					['class'=>'form-control','required'=>'true','input maxlength'=>'8'])!!}
				</div>
				</small>
			</div>

					{!!Form::hidden('encuesta_id',$encuesta['id'])!!}
			<div class="row form-group">
				<center>
					{!!Form::submit('Ingresar a encuesta', ['class'=>'btn btn-primary','id'=>'responder'])!!}
				</center>
			</div>

			{!!Form::close()!!}

			
			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')
<script>
$(document).ready(function() {

	//función para validar checkboxes (no funciona bien aun)
	$('#responder').click(function() {
		if ($('#checkbox-inline :checkbox:checked').length > 0)
		{
			alert ("bien");
		}

		else
		{

			alert ("mal");
		}
	})

});
</script>

@stop
