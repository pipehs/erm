@extends('en.master')

@section('title', 'Risk Assessments')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Risk Assessments</a></li>
			<li><a href="evaluacion_agregradas">Polls</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-6">
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
				<th width="50%">Description</th>
				<td>{{ $encuesta['description'] }}</td>
				</tr>
				<tr>
				<th>Creation date</th>
				<td>{{ $encuesta['created_at'] }}</td>
				</tr>
				<tr>
				<th>Expiration date</th>
				@if ($encuesta['expiration_date'] == "")
					<td>None</td>
				@else
					<td>{{ $encuesta['expiration_date'] }}</td>
				@endif
				</tr>
				<tr>
				<th>Related Risks</th>
				<td><ul>
						@foreach ($riesgos as $riesgo)
							<li>{{ $riesgo['risk_name'] }} - {{ $riesgo['subobj'] }}</li>
						@endforeach
						</ul>					
					</td>
				<tr>
				</table>

				<h5><b>Users who sent them this poll</b></h5>

				@if (!empty($stakeholders))
					<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
					<thead>
					<th>User</th><th>Answers</th>
					</thead>
					@foreach ($stakeholders as $user)
					<tr>
						<td>{{ $user['name'].' '.$user['surnames'] }}</td>
						@if ($user['answers'] == 0)
							<td>{!! link_to_route('ver_respuestas', $title = 'Ver', $parameters = ['eval_id'=>$encuesta['id'],'rut'=>$user['id']],
					 $attributes = ['class'=>'btn btn-success'])!!}</td>
						@else
							<td>This user doesn't send answers yet</td>
						@endif
					</tr>
					@endforeach
					</table>
				@else
					<b>This poll doesn't send to any user yet.</b><br><br>
				@endif
			@endif
			<center>
				{!! link_to_route('evaluacion_agregadas', $title = 'Return', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
