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
						<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
						<thead>
						<th style="vertical-align:top;">KRI</th>
						<th style="vertical-align:top;">Descripci&oacute;n</th>
						<th style="vertical-align:top;">Periodicidad</th>
						<th style="vertical-align:top;">Unidad de medida de evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Resultado</th>
						<th style="vertical-align:top;">Descripci&oacute;n de la evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Riesgo</th>
						<th style="vertical-align:top;">Responsable del riesgo</th>
						<th style="vertical-align:top;">Fecha creaci&oacute;n</th>
						<th style="vertical-align:top;">Intervalo de evaluaci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						<th style="vertical-align:top;">Acci&oacute;n</th>
						</thead>

						@foreach ($kri as $k)

							<tr>
							<td>{{ $k['name'] }} </td>
							<td>{{ $k['description'] }}</td>
							<td>{{ $k['periodicity'] }}</td>
							<td>{{ $k['uni_med'] }}</td>
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
							<td>{{ $k['risk_stakeholder'] }}</td>
							<td>{{ $k['created_at'] }}</td>
							<td>
							@if ($k['date_min'] != null)
								{{ $k['date_min'] }} - {{ $k['date_max'] }}
							@else
								Ninguno
							@endif
							</td>
							<td><a href="kri.edit.{{ $k['id'] }}" class="btn btn-primary">Editar</a></td>
							<td><a href="kri.evaluar.{{ $k['id'] }}" class="btn btn-success">Evaluar</a></td>
							</tr>
						@endforeach
						</table>
					@endif

					<center><a href="kri.create" class="btn btn-success">Agregar nuevo KRI</a></center>
				</div>
				</br>

			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')

@stop