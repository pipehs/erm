@extends('en.master')

@section('title', 'Action Plans')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Action Plans')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Action Plans</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif
			On this section you will be able to manage any action plan entered on the system. <br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'action_plans2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Select Organization',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Select', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<div id="tipo" style="display:none;">
			
			</div>

@if (isset($action_plans))

	<h4><b>Action plans created for {{ $org }}</b></h4>


		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				
			<?php break; ?>
			@endif
		@endforeach
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					<th>Issue origin<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Issue<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Action plan<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Status<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan final date<label><input type="text" placeholder="Filtrar" /></label></th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<th>Edit</th>
					<th>Close</th>
					<th>Delete</th>
				<?php break; ?>
			@endif
		@endforeach
				</thead>

				@foreach ($action_plans as $action_plan)
					<tr>
						<td>{{ $action_plan['origin'] }}</td>
						<td>{{ $action_plan['issue'] }}</td>
						<td>{{ $action_plan['description'] }}</td>
						<td>{{ $action_plan['stakeholder'] }}.<br>{{ $action_plan['stakeholder_mail'] }}</td>
						<td>{{ $action_plan['status'] }}</td>
						<td>{{ $action_plan['final_date'] }}</td>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<td>{!! link_to_route('edit_hallazgo', $title = 'Edit', $parameters = ['org'=>$org_id,'id'=>$action_plan['id']],$attributes = ['class'=>'btn btn-success'])!!}</td>
						<td>
						@if ($action_plan['status_number'] == 1)
							The plan is closed.
						@else
							<button class="btn btn-info" onclick="closer({{ $action_plan['id'] }},'{{ $action_plan['description'] }}','action_plan','El plan de acción')">Close</button></td>
						@endif
						<td><button class="btn btn-danger" onclick="eliminar2({{ $action_plan['id'] }},'{{ $action_plan['description'] }}','action_plan','El plan de acción')">Delete</button></td>
					<?php break; ?>
					@endif
				@endforeach
					</tr>
				@endforeach

@endif


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>

</script>
@stop