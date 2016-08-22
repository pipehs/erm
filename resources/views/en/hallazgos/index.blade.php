@extends('en.master')

@section('title', 'Issues Management')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Issues')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Issues</span>
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

			On this section you will be able to creat, edit and close issues for the master data relevant to an organization. This issues could be related to a process, subprocess, or the organization itself.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'hallazgos_lista','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>

			<div class="form-group">
				{!!Form::label('Select kind',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('kind',['0'=>'Process','1'=>'Subprocess','2'=>'Organization','3'=>'Process Control','4'=>'Bussiness Control','5'=>'Audit Program','6'=>'Audit'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Select -'])!!}
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

@if (isset($issues))

				@if ($kind == 0)
					<h4><b>{{ $org }}: Process Issues</b></h4>
				@elseif($kind == 1)
					<h4><b>{{ $org }}: Subprocess Issues</b></h4>
				@elseif ($kind == 2)
					<h4><b>{{ $org }}: Organization Issues</b></h4>
				@elseif ($kind == 3)
					<h4><b>{{ $org }}: Process Control Issues</b></h4>
				@elseif ($kind == 4)
					<h4><b>{{ $org }}: Bussiness Control Issues</b></h4>
				@elseif ($kind == 5)
					<h4><b>{{ $org }}: Audit Program Issues</b></h4>
				@elseif ($kind == 6)
					<h4><b>{{ $org }}: Audit Issues</b></h4>
				@endif

		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				{!! link_to_route('create_hallazgo', $title = 'Create Issue', $parameters = ['org'=>$org_id,'kind'=>$kind],$attributes = ['class'=>'btn btn-success'])!!}
			<?php break; ?>
			@endif
		@endforeach
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					@if ($kind == 0)
						<th>Process<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif($kind == 1)
						<th>Subprocess<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 2)
						<th>Organization<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 3 || $kind == 4)
						<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 5)
						<th>Audit Program<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 6)
						<th>Plan de auditor&iacute;a - Auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@endif
					<th>Issue<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Classification<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Recommendations<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Action Plan<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Status<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan Deadline<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Evidence</th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<th>Edit</th>
					<th>Delete</th>
				<?php break; ?>
			@endif
		@endforeach
				</thead>

				@foreach ($issues as $issue)
					<tr>
						<td>{{ $issue['origin'] }}</td>
						<td>{{ $issue['name'] }}</td>
						<td>{{ $issue['classification'] }}</td>
						<td>{{ $issue['recommendations'] }}</td>
						<td>{{ $issue['plan'] }}</td>
						<td>{{ $issue['status'] }}</td>
						<td>{{ $issue['final_date'] }}</td>
						<td>
						@if ($issue['evidence'] == NULL)
							Doesn't have documents
						@else
							<div style="cursor:hand" id="descargar_{{ $issue['id'] }}" onclick="descargar(2,'{{$issue['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Download</u></font></div>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
							<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $issue['id'] }},2)">
							</br>
					<?php break; ?>
					@endif
				@endforeach
						@endif
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<td>{!! link_to_route('edit_hallazgo', $title = 'Editar', $parameters = ['org'=>$org_id,'id'=>$issue['id']],$attributes = ['class'=>'btn btn-success'])!!}</td>
						<td>
						<button class="btn btn-danger" onclick="eliminar2({{ $issue['id'] }},'{{ $issue['name'] }}','hallazgo','El hallazgo')">Delete</button></td>
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