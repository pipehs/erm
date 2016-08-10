@extends('en.master')

@section('title', 'Risks Audit')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Risks Audit</a></li>
			<li><a href="show_program.{{$program['id']}}">Show Program</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Program: {{ $program['name'] }}</span>
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

			<ul style="text-align: left;">
			<li><b>Description: {{ $program['description'] }}</b></li>
			<li><b>Creation date: {{ $program['created_at'] }}</b></li>
			@if ($program['expiration_date'] == NULL)
				<li><b>Final date: Not specified</b></li>
			@else
				<li><b>Final date: {{ $program['expiration_date'] }}</b></li>
			@endif
			<li><b>Associated document: 

			@if ($program['evidence'] == NULL)
				The program doesn't have any associated document
			@else
			<table>
			<tr>
			<td>
				<div style="cursor:hand" id="descargar_{{ $program['id'] }}" onclick="descargar(4,'{{$program['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Download</u></font></div>
			</td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<td>&nbsp;&nbsp;
				<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $program['id'] }},1)">
			</td>
		<?php break; ?>
		@endif
	@endforeach
			</tr>
			</table>
			</br>
			@endif
			</b></li>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)		
			<li>{!! link_to_route('programas_auditoria.edit', $title = 'Edit program', $parameters = $program['id'],
				 $attributes = ['class'=>'btn btn-info'])!!}</li>
		<?php break; ?>
		@endif
	@endforeach
			<hr>
			<li><b><u>Audit tests on the program</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
			{!! link_to_route('programas_auditoria.create_test', $title = 'Create test', $parameters = $program['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}
			<?php break; ?>
			@endif
		@endforeach
			</li>
			</ul>
			<hr>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Creation date</th>
				<th>Update date</th>
				<th>Kind</th>
				<th>Status</th>
				<th>Result</th>
				<th>Responsable</th>
				<th>Hours / Man</th>
				<th>Document</th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<th>Action</th>
			<?php break; ?>
			@endif
		@endforeach
			</tr>

			@foreach ($program['tests'] as $test)
				<tr>

					<td>{{ $test['name'] }}</td>

					<td>
					@if ($test['description'] == NULL)
						Without description
					@else
						{{ $test['description'] }}
					@endif
					</td>

					<td>{{ $test['created_at'] }}</td>

					<td>{{ $test['updated_at'] }}</td>

					<td>
					@if ($test['type'] == 0)
						Design test
					@elseif ($test['type'] == 1)
						Operational effectiveness test
					@elseif ($test['type'] == 2)
						Compliance test
					@elseif ($test['type'] == 1)
						Substantive test
					@else
						Not specified
					@endif
					</td>

					<td>
					@if ($test['status'] == 0)
						Open
					@elseif($test['status'] == 1)
						In execution
					@elseif($test['status'] == 2)
						Closed
					@else
						Not specified
					@endif
					</td>				
					
					<td>
					@if ($test['results'] == 0)
						Ineffective
					@elseif($test['results'] == 1)
						Effective
					@elseif($test['results'] == 2)
						In process
					@else
						Not specified
					@endif
					</td>

					<td>
					@if ($test['stakeholder'] == NULL)
						Not assigned
					@else
						{{ $test['stakeholder'] }}
					@endif
					</td>

					<td>
					@if ($test['hh'] == NULL)
						Without Hours / Man assigned
					@else
						{{ $test['hh'] }}
					@endif
					</td>

					<td>
					@if ($test['evidence'] == NULL)
						No documents
					@else
						<div style="cursor:hand" id="descargar_{{ $test['id'] }}" onclick="descargar(5,'{{$test['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Download</u></font></div>
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
						<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $test['id'] }},0)">
						<?php break; ?>
						@endif
					@endforeach

						</br>
					@endif
					</td>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<td>{!! link_to_route('programas_auditoria.edit_test', $title = 'Edit', $parameters = $test['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}</td>
			<?php break; ?>
			@endif
		@endforeach

				</tr>
			@endforeach
			</table>

			<center>
				{!! link_to_route('programas_auditoria', $title = 'Return', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
