@extends('en.master')

@section('title', 'Audit Plan')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Audit Plan</a></li>
			<li><a href="ver_plan">Plan</a></li>
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
					<span>Audit Plan: {{ $plan_auditoria['name'] }}</span>
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
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
			{!! link_to_route('plan_auditoria.edit', $title = 'Edit', $parameters = $plan_auditoria['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}
	<?php break; ?>
	@endif
@endforeach

			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
			<th>Audit(s)</th>
			<td><ul>
					@foreach ($auditorias as $audit)
						<li>{{ $audit['name'] }} - {{ $audit['description'] }}</li>
					@endforeach
					</ul>					
				</td>
			</tr>
			<tr>
			<th width="30%">Description</th>
			<td>{{ $plan_auditoria['description'] }}</td>
			</tr>
			<tr>
			<th>Plan Objectives</th>
			<td>{{ $plan_auditoria['objectives'] }}</td>
			</tr>
			<tr>
			<th>Organization involved</th>
			<td>{{ $organizacion }}</td>
			</tr>
			<tr>
			<th>Related Objectives</th>
			<td>
			@if($objetivos != NULL)
					<ul>
					@foreach ($objetivos as $obj)
						<li>{{ $obj }}</li>
					@endforeach
					</ul>
			@else
				Doesn't have
			@endif					
			</td>
			</tr>
			<tr>
			<th>Riesgos de negocio</th>
			<td>
			@if($riesgos_neg != NULL)	
					<ul>
					@foreach ($riesgos_neg as $risk)
						<li>{{ $risk }}</li>
					@endforeach
					</ul>
			@else
				Doesn't have
			@endif					
			</td>
			</tr>
			<tr>
			<th>Process risk</th>
			<td>
			@if($riesgos_proc != NULL)
					<ul>
					@foreach ($riesgos_proc as $risk)
						<li>{{ $risk }}</li>
					@endforeach
					</ul>
			@else
				Doesn't have
			@endif					
			</td>
			</tr>
			<tr>
			<th>Scopes</th>
			<td>{{ $plan_auditoria['scopes'] }}</td>
			</tr>
			<tr>
			<th>Status</th>
			<td>
			@if ($plan_auditoria['status'] == 0)
				Open
			@else if ($plan_auditoria['status'] == 1)
				Closed
			@endif
			</td>
			</tr>
			<tr>
			<th>Resources</th>
			<td>{{ $plan_auditoria['resources'] }}</td>
			</tr>
			<tr>
			<th>Methodology</th>
			<td>{{ $plan_auditoria['methodology'] }}</td>
			</tr>
			<tr>
			<th>Initial date</th>
			<td>{{ $plan_auditoria['initial_date'] }}</td>
			</tr>
			<tr>
			<th>Final date</th>
			<td>{{ $plan_auditoria['final_date'] }}</td>
			</tr>
			<tr>
			<th>Rule(s)</th>
			<td>{{ $plan_auditoria['rules'] }}</td>
			</tr>
			</table>

			<center>
				{!! link_to_route('plan_auditoria', $title = 'Return', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
