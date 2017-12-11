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

		<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Tipo Control<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Responsable del Control<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Evidencia<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Prop&oacute;sito<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Costo esperado<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>% de contribuci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
		<th>Acci&oacute;n</th>
		<th>Acci&oacute;n</th>
		<?php break; ?>
		@endif
	@endforeach
		</thead>

		@foreach($controls1 as $control)
			<tr>
				<td>{{ $control['name'] }}</td>
				<td>
				@if (strlen($control['description']) > 100)
					<div id="description_{{$control['id']}}" title="{{ $control['description'] }}">{{ $control['short_des'] }}...
					<div style="cursor:hand" onclick="expandir({{ $control['id'] }},'{{ $control['description'] }}','{{ $control['short_des'] }}');">
					<font color="CornflowerBlue">Ver completo</font>
					</div></div>
				@else
					{{ $control['description'] }}
				@endif
				</td>
				<td><ul>
				@foreach ($control['risks'] as $risk)
					@if (strlen($risk['description']) > 50)
						<li>{{ $risk['name'] }} - 
						<div id="descriptionrisk_{{$risk['id']}}" title="{{ $risk['description'] }}">{{ $risk['short_des'] }}...
						<div style="cursor:hand" onclick="expandirrisk({{ $risk['id'] }},'{{ $risk['description'] }}','{{ $risk['short_des'] }}');">
						<font color="CornflowerBlue">Ver completo</font>
						</div></div>
					@else	
						<li>{{ $risk['name'] }} - {{ $risk['description'] }}</li>
					@endif
				@endforeach
				</ul></td>
				<td>
				@if ($control['type2'] == 1)
					<ul>
					@foreach ($control['objectives'] as $obj)
							<li>{{ $obj['name'] }}</li>
					@endforeach
					</ul>
				@endif
				</td>
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
				<td>
				@if ($control['stakeholder'])
					{{ $control['stakeholder'] }}
				@else
					No se ha definido
				@endif
				</td>
				<td>
				@if ($control['evidence'] === NULL)
					No se ha definido
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

				@if (isset($cocacola))
					<td>
					@if ($control['porcentaje_cont'] === NULL)
						No se ha definido
					@else
						{{ $control['porcentaje_cont'] }} %
					@endif
					</td>
				@endif
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)	
				<td>{!! link_to_route('controles.edit', $title = 'Editar', $parameters = $control['id'].'.'.$org_id, $attributes = ['class'=>'btn btn-success']) !!}</td>
				<td><button class="btn btn-danger" onclick="eliminar2({{ $control['id'] }}.{{ $org_id }},'{{ $control['name'] }}','controles','El control')">Eliminar</button></td>
			<?php break; ?>
			@endif
		@endforeach
			</tr>
		@endforeach
		</table>

@endif

@if (isset($controls2))
	<h4><b>Controles a nivel de proceso</b></h4>
<!--	<div id="table-wrapper">
  	<div id="table-scroll">-->
		<table id="datatable-3" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Tipo Control<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Responsable del Control<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Evidencia<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Prop&oacute;sito<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Costo esperado<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>% de contribuci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
		<th>Acci&oacute;n</th>
		<th>Acci&oacute;n</th>
		<?php break; ?>
		@endif
	@endforeach
		</thead>

		@foreach($controls2 as $control)
			<tr>
				<td>{{ $control['name'] }}</td>
				<td>
				@if (strlen($control['description']) > 50)
					<div id="description_{{$control['id']}}" title="{{ $control['description'] }}">{{ $control['short_des'] }}...
					<div style="cursor:hand" onclick="expandir({{ $control['id'] }},'{{ $control['description'] }}','{{ $control['short_des'] }}');">
					<font color="CornflowerBlue">Ver completo</font>
					</div></div>
				@else
					{{ $control['description'] }}
				@endif
				</td>
				<td><ul>
				@foreach ($control['risks'] as $risk)
					@if (strlen($risk['description']) > 100)
						<li>{{ $risk['name'] }} - 
						<div id="descriptionrisk_{{$risk['id']}}" title="{{ $risk['description'] }}">{{ $risk['short_des'] }}...
						<div style="cursor:hand" onclick="expandirrisk({{ $risk['id'] }},'{{ $risk['description'] }}','{{ $risk['short_des'] }}');">
						<font color="CornflowerBlue">Ver completo</font>
						</div></div>
					@else	
						<li>{{ $risk['name'] }} - {{ $risk['description'] }}</li>
					@endif
				@endforeach
				</ul></td>
				<td>
				@if ($control['type2'] == 0)
					<ul>
					@foreach ($control['subprocesses'] as $sub)
						<li>{{ $sub['name'] }}</li>
					@endforeach
					</ul>
				@endif
				</td>
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
				
				<td>
				@if ($control['stakeholder'])
					{{ $control['stakeholder'] }}
				@else
					No se ha definido
				@endif
				</td>
				<td>
				@if ($control['evidence'] === NULL)
					No se ha definido
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

				@if (isset($cocacola))
					<td>
					@if ($control['porcentaje_cont'] === NULL)
						No se ha definido
					@else
						{{ $control['porcentaje_cont'] }} %
					@endif
					</td>
				@endif
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)	
				<td>{!! link_to_route('controles.edit', $title = 'Editar', $parameters = $control['id'].'.'.$org_id, $attributes = ['class'=>'btn btn-success']) !!}</td>
				<td><button class="btn btn-danger" onclick="eliminar2({{ $control['id'] }}.{{ $org_id }},'{{ $control['name'] }}','controles','El control')">Eliminar</button></td>
			<?php break; ?>
			@endif
		@endforeach
			</tr>
		@endforeach
		</table>
	<!--	</div>
		</div>-->
@endif
		<br/>
		<center>
			<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
		<center>
@else
	{!!Form::open(['url'=>'controles.index2','method'=>'GET','class'=>'form-horizontal'])!!}
	<div class="form-group">
		  <div class="row">
		    {!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
		    <div class="col-sm-4">
		      {!!Form::select('organization_id',$organizations, 
		           null, 
		          ['id' => 'organization','placeholder'=>'- Seleccione -','required'=>'true'])!!}
		    </div>
		 </div>
	</div>
<!-- 21-11-17: Quizás no sea necesario filtrar por categoría, además que el que aparezcan puede hacer que los usuarios quieran ver los controles según categoría de Riesgos (más complejo)
	
-->
	<div class="form-group" id="riesgos_objetivos" style="display: none;">
		{!!Form::label('Riesgo de negocio (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			<select name="objective_risk_id" id="objective_risk_id">
				<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
			</select>
		</div>
	</div>

	<div class="form-group" id="riesgos_procesos" style="display: none;">
		{!!Form::label('Riesgo de proceso (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			<select name="risk_subprocess_id" id="risk_subprocess_id">
				<!-- Aquí se agregarán los riesgos de proceso de la org seleccionada a través de Jquery -->
			</select>
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

@section('scripts2')
{!!Html::script('assets/js/get_risks.js')!!}
@stop

