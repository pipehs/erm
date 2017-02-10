@extends('master')

@section('title', 'Controles')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="controles">Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Controles</span>
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

@if (isset($controls1) || isset($controls2))

	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			{!! link_to_route('controles.create', $title = 'Agregar Nuevo Control', $parameters = $org_id, $attributes = ['class'=>'btn btn-primary']) !!}
		<?php break; ?>
		@endif
	@endforeach
@if (isset($controls1))
	<h4><b>Controles a nivel de entidad</b></h4>
	<div id="table-wrapper">
  	<div id="table-scroll">
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Origen del Control</th>
		<th>Riesgo(s)/Objetivo(s)</th>
		<th>Tipo Control</th>
		<th>Fecha Agregado</th>
		<th>Responsable del Control</th>
		<th>Evidencia</th>
		<th>Prop&oacute;sito</th>
		<th>Costo esperado</th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
		<th>Acci&oacute;n</th>
		<th>Acci&oacute;n</th>
		<?php break; ?>
		@endif
	@endforeach
		<th>Acci&oacute;n</th>
		</thead>

		@foreach($controls1 as $control)
			<tr>
				<td>{{ $control['name'] }}</td>
				<td>{{ $control['description'] }}</td>
				<td>
				@if ($control['type2'] == 0)
					De Proceso
				@elseif ($control['type2'] == 1)
					De Negocio
				@endif
				</td>
				<td><ul>
				@foreach ($objective_risks as $subneg)
					@if ($subneg['control_id'] == $control['id'])
						<li>* {{ $subneg['risk'] }} - {{ $subneg['subneg'] }}</li>
					@endif
				@endforeach
				</ul></td>
				<td>
				@if ($control['type'] === 0)
					Manual
				@elseif ($control['type'] == 1)
					Semi-autom&aacute;tico
				@elseif ($control['type'] == 2)
					Autom&aacute;tico
				@else
					No se ha definido
				@endif	
				</td>
				<td>{{ $control['created_at'] }}</td>			
				<td>
				@if ($control['stakeholder'])
					{{ $control['stakeholder'] }}
				@else
					No se ha definido
				@endif
				</td>
				<td>
				@if ($control['evidence'] === NULL)
					No se ha agregado
				@else
					{{ $control['evidence'] }}
				@endif
				</td>
				<td>
				@if ($control['purpose'] == 0)
					Preventivo
				@elseif ($control['purpose'] == 1)
					Detectivo
				@elseif ($control['purpose'] == 2)
					Correctivo
				@else
					Error al ingresar prop&oacute;sito
				@endif
				</td>
				<td>
				@if ($control['expected_cost'] === NULL)
					No se ha definido
				@else
					{{ $control['expected_cost'] }}
				@endif
				</td>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)	
				<td>{!! link_to_route('controles.edit', $title = 'Editar', $parameters = $control['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
				<td><button class="btn btn-danger" onclick="eliminar2({{ $control['id'] }},'{{ $control['name'] }}','controles','El control')">Eliminar</button></td>
			<?php break; ?>
			@endif
		@endforeach
			<td>{!! link_to_route('controles.docs', $title = 'Ver', $parameters = $control['id'], $attributes = ['class'=>'btn btn-warning']) !!}</td>
			</tr>
		@endforeach
		</table>
	</div>
	</div>
@endif

@if (isset($controls2))
	<h4><b>Controles a nivel de proceso</b></h4>
	<div id="table-wrapper">
  	<div id="table-scroll">
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Nombre</th>
		<th>Descripci&oacute;n</th>
		<th>Origen del Control</th>
		<th>Riesgo(s)/Subproceso(s)</th>
		<th>Tipo Control</th>
		<th>Fecha Agregado</th>
		<th>Responsable del Control</th>
		<th>Evidencia</th>
		<th>Prop&oacute;sito</th>
		<th>Costo esperado</th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
		<th>Acci&oacute;n</th>
		<th>Acci&oacute;n</th>
		<?php break; ?>
		@endif
	@endforeach
		<th>Acci&oacute;n</th>
		</thead>

		@foreach($controls2 as $control)
			<tr>
				<td>{{ $control['name'] }}</td>
				<td>{{ $control['description'] }}</td>
				<td>
				@if ($control['type2'] == 0)
					De Proceso
				@elseif ($control['type2'] == 1)
					De Negocio
				@endif
				</td>
				<td><ul>
				@foreach ($risks_subprocess as $subneg)
					@if ($subneg['control_id'] == $control['id'])
						<li>* {{ $subneg['risk'] }} - {{ $subneg['subneg'] }}</li>
					@endif
				@endforeach
				</ul></td>
				<td>
				@if ($control['type'] === 0)
					Manual
				@elseif ($control['type'] == 1)
					Semi-autom&aacute;tico
				@elseif ($control['type'] == 2)
					Autom&aacute;tico
				@else
					No se ha definido
				@endif
				</td>
				<td>{{ $control['created_at'] }}</td>
				
				<td>
				@if ($control['stakeholder'])
					{{ $control['stakeholder'] }}
				@else
					No se ha definido
				@endif
				</td>
				<td>
				@if ($control['evidence'] === NULL)
					No se ha agregado
				@else
					{{ $control['evidence'] }}
				@endif
				</td>
				<td>
				@if ($control['purpose'] == 0)
					Preventivo
				@elseif ($control['purpose'] == 1)
					Detectivo
				@elseif ($control['purpose'] == 2)
					Correctivo
				@else
					Error al ingresar prop&oacute;sito
				@endif
				</td>
				<td>
				@if ($control['expected_cost'] === NULL)
					No se ha definido
				@else
					{{ $control['expected_cost'] }}
				@endif
				</td>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)	
				<td>{!! link_to_route('controles.edit', $title = 'Editar', $parameters = $control['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
				<td><button class="btn btn-danger" onclick="eliminar2({{ $control['id'] }},'{{ $control['name'] }}','controles','El control')">Eliminar</button></td>
			<?php break; ?>
			@endif
		@endforeach
			<td>{!! link_to_route('controles.docs', $title = 'Ver', $parameters = $control['id'], $attributes = ['class'=>'btn btn-warning']) !!}</td>
			</tr>
		@endforeach
		</table>
		</div>
		</div>
@endif
		<br/>
		<center>
			{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
		<center>
@else
	{!!Form::open(['url'=>'controles.index2','method'=>'GET','class'=>'form-horizontal'])!!}
	<div class="form-group">
		  <div class="row">
		    {!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
		    <div class="col-sm-4">
		      {!!Form::select('organization_id',$organizations, 
		           null, 
		          ['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
		    </div>
		 </div>
	</div>
	<br>
	<div class="form-group">
		<center>
			{!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
		</center>
	</div>
	{!!Form::close()!!}
@endif

			</div>
		</div>
	</div>
</div>
@stop

