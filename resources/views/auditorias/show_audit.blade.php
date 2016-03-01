@extends('master')

@section('title', 'Auditor&iacute;a de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Auditor&iacute;a de Riesgos</a></li>
			<li><a href="ver_plan">Ver Plan</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Auditoría: {{ $auditoria['name'] }}</span>
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
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
			<th>Planes en los que se aplica</th>
			<td><ul>
					@foreach ($planes as $plan)
						<li><a href="plan_auditoria.show.{{ $plan['id'] }}">{{ $plan['name'] }}</a></li>
					@endforeach
					</ul>					
				</td>
			</tr>
			<tr>
			<th width="30%">Descripci&oacute;n</th>
			<td>{{ $auditoria['description'] }}</td>
			</tr>

			</table>

			<center>
				{!! link_to_route('auditorias', $title = 'Volver', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-success'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
