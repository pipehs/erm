@extends('en.master')

@section('title', 'Audit Plans')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="auditorias">Audits</a></li>
			<li><a href="plan_auditoria">Audit Plans</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Audit Plans</span>
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
	      <p>On this section you will be able to view and generate new audit plans.</p>

				@if(Session::has('message'))
					<div class="alert alert-success alert-dismissible" role="alert">
					{{ Session::get('message') }}
					</div>
				@endif
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
		{!! link_to_route('plan_auditoria.create', $title = 'Create new plan', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
		<th>Name</th>
		<th>Description</th>
		<th>Creation date</th>
		<th>Last update</th>
		<th>View</th>
	</thead>

	@foreach($planes as $plan)
		<tr>
			<td>{{ $plan['name'] }}</td>
			<td>{{ $plan['description'] }}</td>
			<td>{{ $plan['created_at'] }}</td>
			<td>{{ $plan['updated_at'] }}</td>
			<td>
				<div>
		            {!! link_to_route('plan_auditoria.show', $title = 'View', $parameters = $plan['id'], $attributes = ['class'=>'btn btn-warning']) !!}
		        </div><!-- /btn-group -->
			</td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop
