@extends('master')

@section('title', 'Gesti&oacute;n Estrat&eacute;gica - KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>KPI</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
				</div>
			@endif

			KPI generados para el objetivo: <b>{{ $obj_selected }}</b><br><br>

@if (isset($kpi))
	<div style="float: center;">
		@if (empty($kpi))
			<center><b>Aun no se ha creado ning&uacute;n KPI para {{$obj_selected}}.</b></center><br><br>

			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
		@else
			<h4><b>{{ $obj_selected }}</b></h4>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
				<th style="vertical-align:top;">Objetivo</th>
				<th style="vertical-align:top;">Indicador</th>
				<th style="vertical-align:top;">Descripci&oacute;n</th>
				<th style="vertical-align:top;">Periodicidad</th>
				<th style="vertical-align:top;">&Uacute;ltima medici&oacute;n</th>
				<th style="vertical-align:top;">Fecha &uacute;ltima medici&oacute;n</th>
				<th style="vertical-align:top;">Unidad de medida</th>
				<th style="vertical-align:top;">Meta</th>
				<th style="vertical-align:top;">Responsable</th>			
				

				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<?php break; ?>
					@endif
				@endforeach
				</thead>
				@foreach ($kpi as $k)
					<tr>

						<td>{{ $k['objective'] }}</td>
						<td>{{ $k['name'] }}</td>
						<td>{{ $k['description'] }}</td>
						<td>
						@if ($k['periodicity'] == 1)
							Mensual
						@elseif ($k['periodicity'] == 2)
							Semestral
						@elseif ($k['periodicity'] == 3)
							Trimestral
						@elseif ($k['periodicity'] == 4)
							Anual
						@endif
						</td>						
						<td>{{ $k['last_eval'] }}</td>
						<td>{{ $k['date_last_eval'] }}</td>
						<td>{{ $k['measurement_unit'] }}</td>
						<td>{{ $k['goal'] }}</td>
						<td>{{ $k['stakeholder'] }}</td>
						<td>{!!link_to_route('kpi.edit2', $title = 'Editar', $parameters=['id'=>$k['id'],'obj_id'=>$obj_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
						<td>{!! link_to_route('kpi.evaluate', $title = 'Mediciones', $parameters = ['id'=>$k['id'],'obj_id'=>$obj_id], $attributes = ['class'=>'btn btn-primary']) !!}</td>
						@if ($k['status'] == 1 && !$k['status_validate'])
							<td>KPI validado</td>
						@elseif ($k['status_validate'])
							<td>
								<button class="btn btn-danger" onclick="validatekpi({{ $k['id'] }},
												'{{ $k['name'] }}')">Validar</button>
							</td>
						@else	
							<td>No hay medici&oacute;n para validar</td>
						@endif
					</tr>
				@endforeach
				</table>

					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
							<center><a href="kpi.create2.{{$obj_id}}" class="btn btn-success">Agregar nuevo KPI</a></center>
						<?php break; ?>
						@endif
					@endforeach
		@endif
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