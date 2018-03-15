@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion_agregradas">Ver Encuestas</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-10">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					@if (isset($encuesta))
						<span>Encuesta: {{ $encuesta['name'] }}</span>
					@else
						<span>Evaluaci&oacute;n Manual</span>
					@endif
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

			@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
			</div>
			@endif

			@if (!isset($tipo) && isset($encuesta))
				<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
				<tr>
				<th width="25%">Descripci&oacute;n</th>
				<td>{{ $encuesta['description'] }}</td>
				</tr>
				<tr>
				<th>Fecha Creaci&oacute;n</th>
				<td>{{ $encuesta['created_at'] }}</td>
				</tr>
				<tr>
				<th>Fecha Expiraci&oacute;n</th>
				@if ($encuesta['expiration_date'] == "")
					<td>Ninguna</td>
				@else
					<td>{{ $encuesta['expiration_date'] }}</td>
				@endif
				</tr>
				<tr>
				<th>Riesgos Relacionados <br/>
				(Organizaci&oacute;n - Riesgo - Descripci&oacute;n)
				</th>
				<td>
						@foreach ($riesgos as $riesgo)
							<li>{{ $riesgo['org'] }} - {{ $riesgo['risk_name'] }} - {{ $riesgo['description'] }}</li>
						@endforeach							
				</td>
				<tr>
				</table>

				<h5><b>Usuarios a los que se les ha enviado esta encuesta</b></h5>

				@if (!empty($stakeholders))
					<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
					<thead>
					<th>Usuario</th><th>Respuestas</th>
					</thead>
					@foreach ($stakeholders as $user)
					<tr>
						<td>{{ $user['name'].' '.$user['surnames'] }}</td>
						@if ($user['answers'] == 0)
							<td>{!! link_to_route('ver_respuestas', $title = 'Ver', $parameters = ['eval_id'=>$encuesta['id'],'rut'=>$user['id']],
					 $attributes = ['class'=>'btn btn-success'])!!}</td>
						@else
							<td>Este usuario aun no envía respuestas</td>
						@endif
					</tr>
					@endforeach
					</table>
				@else
					<b>Esta encuesta aun no ha sido enviada a ning&uacute;n usuario.</b><br><br>
				@endif
			@endif
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
			</div>
		</div>
	</div>
</div>
@stop
