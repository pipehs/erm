@extends('en.master')

@section('title', 'Risks')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="riesgos">Risks Identification</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Risks</span>
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
			<p>On this section you will be able to identify a formal risk based on the analysis of risks events. Also you can view the risks previously identified</p>
		<?php break; ?>

		@else
			<p>On this section you will be able to view all identified risks on the system.</p>
		@endif
	@endforeach

	{!!Form::open(['url'=>'riesgos.index2','method'=>'GET','class'=>'form-horizontal'])!!}

	<div class="form-group">
	   <div class="row">
	     {!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
	     <div class="col-sm-4">
	       {!!Form::select('organization_id',$organizations, 
	            null, 
	           ['id' => 'org','placeholder'=>'- Select -','required'=>'true'])!!}
	     </div>
	  </div>
	</div>
	<br>
	<div class="form-group">
		<center>
			{!!Form::submit('Select', ['class'=>'btn btn-success'])!!}
		</center>
	</div>

@if (isset($riesgos)) {{-- AGREGADO 26-07 obliga a seleccionar primero organización --}}

<h4><b>Risks of: {{ $org_selected }} </b></h4>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<center>
			{!! link_to_route('riesgos.create', $title = 'Create Process Risk', $parameters = ['P' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-warning']) !!}
			&nbsp;&nbsp;
			{!! link_to_route('riesgos.create', $title = 'Create Bussiness Risk', $parameters = ['N' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-primary']) !!}
			</center>
		<?php break; ?>
		@endif
	@endforeach
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
		<thead>
		<th>Name<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Description<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Kind<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Category<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Creation date<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Expiration date<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Related Subprocess or Objective<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Cause(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Effect(s)<label><input type="text" placeholder="Filtrar" /></label></th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<th>Edit</th>
		<?php break; ?>
		@endif
	@endforeach
		</thead>
		@foreach ($riesgos as $riesgo)
			<tr>
			<td>{{ $riesgo['nombre'] }}</td>
			<td>{{ $riesgo['descripcion'] }}</td>
			@if ($riesgo['tipo'] == 0)
				<td>Process Risk</td>
			@else
				<td>Bussiness Risk</td>
			@endif
			<td>{{ $riesgo['categoria'] }}</td>
			@if ($riesgo['fecha_creacion'] == NULL)
				<td>Error storing created date</td>
			@else
				<td>{{$riesgo['fecha_creacion']}}</td>
			@endif

			@if ($riesgo['fecha_exp'] == NULL)
				<td>None</td>
			@else
				<td>{{$riesgo['fecha_exp']}}</td>
			@endif
			<td>{{ $riesgo['stakeholder'] }}</td>
			<td>
			<ul>
			@foreach($relacionados as $subonegocio)
				@if ($subonegocio['risk_id'] == $riesgo['id'])
							<li>{{ $subonegocio['nombre'] }}</li>
				@endif
			@endforeach
			</ul>	
			</td>
			<td>
			@if ($riesgo['causas'] == NULL)
				No causes specified
			@else
				@if (gettype($riesgo['causas']) == "array") 
					@foreach ($riesgo['causas'] as $causa)
						<li>{{ $causa }}</li>
					@endforeach
				@else
					{{ $riesgo['causas'] }}
				@endif
			@endif
			</td>
			<td>
			@if ($riesgo['efectos'] == NULL)
				No effects specified
			@else
				@if (gettype($riesgo['efectos']) == "array") 
					@foreach ($riesgo['efectos'] as $efecto)
						<li>{{ $efecto }}</li>
					@endforeach
				@else
					{{ $riesgo['efectos'] }}
				@endif
			@endif
			</td>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					<td>{!! link_to_route('riesgos.edit', $title = 'Edit', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
				<?php break; ?>
				@endif
			@endforeach
			</tr>
		@endforeach
		</table>
@endif
			</div>
		</div>
	</div>
</div>
@stop


