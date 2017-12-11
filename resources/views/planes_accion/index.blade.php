@extends('master')

@section('title', 'Mantenedor de Planes de Acci&oacute;n')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Planes de Acci&oacute;n')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Planes de acci&oacute;n</span>
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
@if (!isset($action_plans))
			En esta secci&oacute;n podr&aacute; ver y cerrar cualquier plan de acci&oacute;n ingresado en el sistema.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'action_plans2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<div id="tipo" style="display:none;">
			
			</div>

@elseif (isset($action_plans))

	<h4><b>Planes de acci&oacute;n creados para {{ $org }}</b></h4>


		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				{!! link_to_route('action_plan.create', $title = 'Agregar Nuevo Plan', $parameters = $org_id, $attributes = ['class'=>'btn btn-primary']) !!}
			<?php break; ?>
			@endif
		@endforeach
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					<th>Origen del hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Estado<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Porcentaje avance<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Comentarios avances<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Fecha avance<label><input type="text" placeholder="Filtrar" /></label></th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<th>Editar</th>
					<th>Cerrar</th>
					<th>Eliminar</th>
				<?php break; ?>
			@endif
		@endforeach
				</thead>

				@foreach ($action_plans as $action_plan)
					<tr>
						<td>{{ $action_plan['origin'] }}</td>
						<td>{{ $action_plan['issue'] }}</td>
						<td>
						@if ($action_plan['description'] == '')
							No se ha definido descripci&oacute;n
						@else
							@if (strlen($action_plan['description']) > 100)
								<div id="action_plan_{{$action_plan['id']}}" title="{{ $action_plan['description'] }}">{{ $action_plan['short_des'] }}...
								<div style="cursor:hand" onclick="expandir3({{ $action_plan['id'] }},'{{ $action_plan['description'] }}','{{ $action_plan['short_des'] }}')">
								<font color="CornflowerBlue">Ver completo</font>
								</div></div>
							@else
								{{ $action_plan['description'] }}
							@endif
						@endif
						</td>
						<td>{{ $action_plan['stakeholder'] }}.<br>{{ $action_plan['stakeholder_mail'] }}</td>
						<td>{{ $action_plan['status'] }}</td>
						<td>{{ $action_plan['final_date'] }}</td>
						<td>
						@if ($action_plan['percentage'] == NULL)
							No se ha agregado
						@else
							{{ $action_plan['percentage'] }}%
						@endif
						</td>
						<td>
						@if ($action_plan['percentage_comments'] == '' || $action_plan['percentage_comments'] == NULL)
							No se han agregado
						@else
							{{ $action_plan['percentage_comments'] }}
						@endif
						</td>
						<td>
						@if ($action_plan['percentage_date'] == NULL)
							No se ha agregado
						@else
							{{ date('d-m-Y',strtotime($action_plan['percentage_date'])) }}
						@endif
						</td>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<td>{!! link_to_route('action_plan.edit', $title = 'Editar', $parameters = ['org'=>$org_id,'id'=>$action_plan['id']],$attributes = ['class'=>'btn btn-success'])!!}</td>
						<td>
						@if ($action_plan['status_number'] == 1)
							Plan se encuentra cerrado.
						@else
							<button class="btn btn-info" onclick="closer({{ $action_plan['id'] }},'{{ $action_plan['description'] }}','action_plan','El plan de acción')">Cerrar</button></td>
						@endif
						<td><button class="btn btn-danger" onclick="eliminar2({{ $action_plan['id'] }},'{{ $action_plan['description'] }}','action_plan','El plan de acción')">Eliminar</button></td>
					<?php break; ?>
					@endif
				@endforeach
					</tr>
				@endforeach

				</table>
		<center>{!! link_to_route('action_plans', $title = 'Volver', $parameters = NULL,$attributes = ['class'=>'btn btn-danger'])!!}</center>

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