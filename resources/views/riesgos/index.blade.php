@extends('master')

@section('title', 'Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="riesgos">Identificación de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos</span>
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
				<p>En esta secci&oacute;n podr&aacute; identificar un riesgo formal en base al an&aacute;lisis realizado sobre los eventos de riesgo. Tambi&eacute;n podr&aacute; ver los riesgos previamente identificados.</p>
			<?php break; ?>

			@else
				<p>On this section you will be able to view all identified risks on the system.</p>
			@endif
		@endforeach

		{!!Form::open(['url'=>'riesgos.index2','method'=>'GET','class'=>'form-horizontal'])!!}

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

@if (isset($riesgos)) {{-- AGREGADO 26-07 obliga a seleccionar primero organización --}}

<h4><b>Riesgos de: {{ $org_selected }} </b></h4>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				
				<center>
				{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Proceso', $parameters = ['P' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-warning']) !!}
				&nbsp;&nbsp;
				{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Negocio', $parameters = ['N' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-primary']) !!}
				</center>
			<?php break; ?>

			@else
				
			@endif
		@endforeach
			
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
			<thead>
			<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Tipo<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Categor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha Creaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha Expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Subprocesos u Objetivos Relacionados<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Causa(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Efecto(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<th>Editar</th>
			<?php break; ?>
			@endif
		@endforeach
			</thead>
			@foreach ($riesgos as $riesgo)
				<tr>
				<td>{{ $riesgo['nombre'] }}</td>
				<td>{{ $riesgo['descripcion'] }}</td>
				@if ($riesgo['tipo'] == 0)
					<td>Riesgo de Proceso</td>
				@else
					<td>Riesgo de Negocio</td>
				@endif
				<td>{{ $riesgo['categoria'] }}</td>
				@if ($riesgo['fecha_creacion'] == NULL)
					<td>Error al grabar fecha de creaci&oacute;n</td>
				@else
					<td>{{$riesgo['fecha_creacion']}}</td>
				@endif

				@if ($riesgo['fecha_exp'] == NULL)
					<td>Ninguna</td>
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
					No se han especificado causas
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
					No se han especificado efectos
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
				<td>{!! link_to_route('riesgos.edit', $title = 'Editar', $parameters = $riesgo['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
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


