@extends('master')

@section('title', 'Plan estratégico')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Gestión estratégica</a></li>
			<li><a href="objetivos">Plan estratégico</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-puzzle-piece"></i>
					<span>Plan estratégico</span>
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
	<hr>

	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			{!! link_to_route('plan_estrategico.create', $title = 'Generar Plan', $parameters = ['org_id' => $org_id], $attributes = ['class'=>'btn btn-primary']) !!}
		<?php break; ?>
		@endif
	@endforeach
	<hr>

	En esta secci&oacute;n podr&aacute; ver todos los planes estrat&eacute;gicos para la organizaci&oacute;n {{ $organization }}, adem&aacute;s de poder generar un nuevo plan estrat&eacute;gico.
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
				<thead>
					<th>Nombre<label><input type='text' placeholder='Filtrar' /></label></th>
					<th width="30%">Comentarios<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Estado<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Inicio de vigencia<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Fin de la vigencia<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Acci&oacute;n</th>
					<th>Acci&oacute;n</th>
				</thead>

				@foreach($planes as $plan)
					<tr>
						<td>{{ $plan['name'] }}</td>
						<td>@if ($plan['comments'] != NULL)
								{{ $plan['comments'] }}
							@else
								No se agregaron comentarios
							@endif
						</td>
						<td>@if ($plan['status'] == 0)
								No vigente
							@elseif ($plan['status'] == 1)
								Activo
							@else
								Estado no guardado correctamente
							@endif
						</td>
						<td>{{ $plan['initial_date'] }}</td>
						<td>{{ $plan['final_date'] }}</td>
						<td>
						@if($plan['status'] == 1 || $plan['status'] == 2)
							{!! link_to_route('plan_estrategico.edit', $title = 'Editar', $parameters = $plan['id'], $attributes = ['class'=>'btn btn-success']) !!}
						@elseif ($plan['status'] == 0)
							Plan no vigente
						@endif
						</td>
						<td>{!! link_to_route('objetivos_plan', $title = 'Ver objetivos', $parameters = $plan['id'], $attributes = ['class'=>'btn btn-primary']) !!}</td>

					</tr>
				@endforeach
			</table>

				<center>
					{!! link_to_route('objetivos.index', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
				</center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
		@if ($validador == 0)
			swal({   title: "Atención",
		   			   text: "No existe ningún plan estratégico vigente para la organización seleccionada. Por favor cree un plan para poder agregar los objetivos estratégicos",
		   			   type: "warning",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false });
		@endif

</script>
@stop