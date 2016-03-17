@extends('master')

@section('title', 'Auditor&iacute;a de Riesgos')

@stop

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

			{!! link_to_route('plan_auditoria.edit', $title = 'Editar', $parameters = $plan_auditoria['id'],
				 $attributes = ['class'=>'btn btn-success'])!!}

			<table class="table table-bordered table-striped table-hover table-heading table-datatable" width="50%">
			<tr>
			<th>Auditor&iacute;a(s)</th>
			<td><ul>
					@foreach ($auditorias as $audit)
						<li>{{ $audit['name'] }} - {{ $audit['description'] }}</li>
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
			<td>{{ $plan_auditoria['objectives'] }}</td>
			</tr>
			<tr>
			<th>Organizaci&oacute;n involucrada</th>
			<td>{{ $organizacion }}</td>
			</tr>
			<tr>
			<th>Objetivos Relacionados</th>
			<td><ul>
					@foreach ($objetivos as $obj)
						<li>{{ $obj }}</li>
					@endforeach
					</ul>					
				</td>
			</tr>
			<tr>
			<th>Riesgos de negocio</th>
			<td><ul>
					@foreach ($riesgos_neg as $risk)
						<li>{{ $risk }}</li>
					@endforeach
					</ul>					
				</td>
			</tr>
			<tr>
			<th>Riesgos de proceso</th>
			<td><ul>
					@foreach ($riesgos_proc as $risk)
						<li>{{ $risk }}</li>
					@endforeach
					</ul>					
				</td>
			</tr>
			<tr>
			<th>Alcances</th>
			<td>{{ $plan_auditoria['scopes'] }}</td>
			</tr>
			<tr>
			<th>Estado</th>
			<td>{{ $plan_auditoria['status'] }}</td>
			</tr>
			<tr>
			<th>Recursos</th>
			<td>{{ $plan_auditoria['resources'] }}</td>
			</tr>
			<tr>
			<th>Metodolog&iacute;a</th>
			<td>{{ $plan_auditoria['methodology'] }}</td>
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
			<td>{{ $plan_auditoria['rules'] }}</td>
			</tr>
			</table>

			<center>
				{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			</div>
		</div>
	</div>
</div>
@stop
