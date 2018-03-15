@extends('master')

@section('title', 'Auditor&iacute;a de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Auditor&iacute;a de Riesgos</a></li>
			<li><a href="ver_plan">Ver Plan</a></li>
		</ol>
	</div>
</div>
<center>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Plan de auditoría: {{ $plan_auditoria['name'] }}</span>
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
						{!! link_to_route('plan_auditoria.edit', $title = 'Editar', $parameters = $plan_auditoria['id'],
							 $attributes = ['class'=>'btn btn-success'])!!}
				<?php break; ?>
				@endif
			@endforeach

			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
			<th>Auditor&iacute;a(s)</th>
			<td>
				<ul>
				@foreach ($auditorias as $audit)
						<li>{{ $audit['name'] }} - {{ $audit['description'] }}   
				 		<button class="btn_ultra_small btn-danger" onclick="eliminar2({{ $audit['audit_audit_plan_id'] }},'{{ $audit['name'] }}','audit','La auditoría')">Eliminar</button></li>
				@endforeach
				</ul>					
			</td>
			</tr>
			<tr>
			<th width="30%">Descripci&oacute;n</th>
			<td>{{ $plan_auditoria['description'] }}</td>
			</tr>
			<tr>
			<th>Objetivos del plan</th>
			<td>
			@if ($plan_auditoria['objectives'] == NULL)
				No se han asignado
			@else
				{{ $plan_auditoria['objectives'] }}
			@endif
			</td>
			</tr>
			<tr>
			<th>Organizaci&oacute;n involucrada</th>
			<td>{{ $organizacion }}</td>
			</tr>
			<tr>
			</tr>
			<tr>
			<th>Alcances</th>
			<td>
			@if ($plan_auditoria['scopes'] == NULL)
				No se han asignado
			@else
				{{ $plan_auditoria['scopes'] }}
			@endif
			</td>
			</tr>
			<tr>
			<th>Estado</th>
			<td>
			@if ($plan_auditoria['status'] == 0)
				Abierto
			@else if ($plan_auditoria['status'] == 1)
				Cerrado
			@endif
			</td>
			</tr>
			<tr>
			<th>Recursos</th>
			<td>
			@if ($plan_auditoria['resources'] == NULL)
				No se han asignado
			@else
				{{ $plan_auditoria['resources'] }}
			@endif
			</td>
			</tr>
			<tr>
			<th>Metodolog&iacute;a</th>
			<td>
			@if ($plan_auditoria['methodology'] == NULL)
				No se ha asignado
			@else
				{{ $plan_auditoria['methodology'] }}
			@endif
			</td>
			</tr>
			<tr>
			<th>Fecha inicial</th>
			<td>{{ $plan_auditoria['initial_date'] }}</td>
			</tr>
			<tr>
			<th>Fecha final</th>
			<td>{{ $plan_auditoria['final_date'] }}</td>
			</tr>
			<tr>
			<th>Norma(s)</th>
			<td>
			@if ($plan_auditoria['rules'] == NULL)
				No se han asignado
			@else
				{{ $plan_auditoria['rules'] }}
			@endif
			</td>
			</tr>
			</table>

				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
			</div>
		</div>
	</div>
</div>
@stop
