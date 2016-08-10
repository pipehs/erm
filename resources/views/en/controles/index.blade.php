@extends('en.master')

@section('title', 'Controls')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="controles">Controls</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Controls</span>
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
		{!! link_to_route('controles.create', $title = 'Create new control', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}
	<?php break; ?>
	@endif
@endforeach

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Name</th>
	<th>Description</th>
	<th>Origin of the control</th>
	<th>Risk(s)/Subprocess(es) - Risk(s)/Objective(s) </th>
	<th>Control kind</th>
	<th>Creation date</th>
	<th>Update date</th>
	<th>Control owner</th>
	<th>Evidence</th>
	<th>Purpose</th>
@foreach (Session::get('roles') as $role)
	@if ($role != 6)
	<th>Edit</th>
	<?php break; ?>
	@endif
@endforeach
	<th>Documents</th>
	</thead>

	@foreach($controls as $control)
		<tr>
			<td>{{ $control['name'] }}</td>
			<td>{{ $control['description'] }}</td>
			<td>
			@if ($control['type2'] == 0)
				Process
			@elseif ($control['type2'] == 1)
				Bussiness
			@endif
			</td>
			<td><ul>
			@foreach ($risk_subneg as $subneg)
				@if ($subneg['control_id'] == $control['id'])
					<li>* {{ $subneg['risk'] }} - {{ $subneg['subneg'] }} - {{ $subneg['organization'] }}</li>
				@endif
			@endforeach
			</ul></td>
			<td>
			@if ($control['type'] == 0)
				Manual
			@elseif ($control['type'] == 1)
				Semi-automatic
			@elseif ($control['type'] == 2)
				Automatic
			@endif	
			</td>
			<td>{{ $control['created_at'] }}</td>
			<td>{{ $control['updated_at'] }}</td>
			
			<td>{{ $control['stakeholder'] }}</td>
			@if ($control['evidence'] == NULL)
				<td>No specified</td>
			@else
				<td>{{ $control['evidence'] }}</td>
			@endif
			<td>
			@if ($control['purpose'] == 0)
				Preventive
			@elseif ($control['purpose'] == 1)
				Detective
			@elseif ($control['purpose'] == 2)
				Corrective
			@else
				Error storing purpose
			@endif
			</td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)	
			<td> 
			<div>
	            {!! link_to_route('controles.edit', $title = 'Edit', $parameters = $control['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        </div><!-- /btn-group -->
			</td>
		<?php break; ?>
		@endif
	@endforeach
		<td>
			<div>
	            {!! link_to_route('controles.docs', $title = 'View', $parameters = $control['id'], $attributes = ['class'=>'btn btn-warning']) !!}
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

