@extends('master')

@section('title', 'Monitor KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','Gestionar KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Monitor KRI</span>
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

			En esta secci&oacute;n podr&aacute; monitorear, crear y/o modificar los indicadores para los riesgos relevantes al negocio, adem&aacute;s de poder evaluar los mismos. <br><br>

			<div id="risks" style="float: center;">
					
				</div>

				<div id="info_kri" style="float: center;">
					@if ($kri == null)
						<center><b>Aun no se han creado ning&uacute;n KRI.</b></center><br><br>
					@else
						<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
						<thead>
						<th style="vertical-align:top;">KRI</th>
						<th style="vertical-align:top;">Descripci&oacute;n</th>
						<th style="vertical-align:top;">Periodicidad</th>
						<th style="vertical-align:top;">Unidad de medida de evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Resultado</th>
						<th style="vertical-align:top;">Descripci&oacute;n de la evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Riesgo</th>
						<th style="vertical-align:top;">Responsable</th>
						<th style="vertical-align:top;">Intervalo de evaluaci&oacute;n</th>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
						<th style="vertical-align:top;">Acci&oacute;n</th>
				<?php break; ?>
				@endif
			@endforeach
						</thead>

						@foreach ($kri as $k)

							<tr>
							<td>{{ $k['name'] }} </td>
							<td>{{ $k['description'] }}</td>
							<td>
							@if (is_null($k['periodicity']) || $k['periodicity'] === NULL)
								No definida
							@elseif ($k['periodicity'] == 0)
								Diario
							@elseif ($k['periodicity'] == 1)
								Semanal
							@elseif ($k['periodicity'] == 2)
								Mensual
							@elseif ($k['periodicity'] == 3)
								Trimestral
							@elseif ($k['periodicity'] == 4)
								Semestral
							@elseif ($k['periodicity'] == 5)
								Anual
							@elseif ($k['periodicity'] == 6)
								Cada vez que ocurra
							@else
								No definida
							@endif
							</td>
							<td>
							@if ($k['uni_med'] == 0)
								Porcentaje
							@elseif ($k['uni_med'] == 1)
								Monto
							@elseif ($k['uni_med'] == 2)
								Cantidad
							@endif
							</td>
							<td>{{ $k['last_eval'] }}</td>
							<td>
							@if ($k['eval'] == 0)
								<ul class="semaforo verde"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 1)
								<ul class="semaforo amarillo"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 2)
								<ul class="semaforo rojo"><li></li><li></li><li></li></ul>
							@elseif ($k['eval'] == 3)
								Ninguna
							@endif
							</td>
							<td>{{ $k['description_eval'] }}</td>
							<td>{{ $k['risk'] }}</td>
							<td>
							@if (empty($k['stakeholder']))
								No se han definido
							@else
								{{ $k['stakeholder']->name }} {{ $k['stakeholder']->surnames }}
							@endif
							</td>
							<td>
							@if ($k['date_min'] != null)
								{{ $k['date_min'] }} al {{ $k['date_max'] }}
							@else
								No definido
							@endif
							</td>
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
							<td>
							<a href="kri.edit.{{ $k['id'] }}" class="btn btn-primary">Editar</a>
							<a href="kri.evaluar.{{ $k['id'] }}" class="btn btn-success">Evaluar</a>
							<a href="kri.veranteriores.{{ $k['id'] }}" class="btn btn-info" id="ver_evaluaciones">Monitorear</a>
							<button class="btn btn-danger" onclick="eliminar2({{ $k['id'] }},'{{ $k['name'] }}','kri','El KRI')">Eliminar</button></td>
				<?php break; ?>
				@endif
			@endforeach
							</tr>
						@endforeach
						</table>
					@endif
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					<center><a href="kri.create" class="btn btn-success">Agregar nuevo KRI</a></center>
				<?php break; ?>
				@endif
			@endforeach
				</div>
				</br>

			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')

@stop