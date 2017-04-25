@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
				<li><a href="evaluacion.respuestas.{{$eval_id}},{{$rut}}">Ver respuestas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta: {{ $encuesta }}</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('message') }}
				</div>
			@endif
			<h4><center><b>Respuestas enviadas por: {{ $user->name.' '.$user->surnames }}</b></center></h4>

			@foreach($riesgos as $riesgo)
				<b>{{ $riesgo['org'] }}- {{ $riesgo['risk_name'] }}:</b><br>
				<b><p style="color: blue;">Descripci&oacute;n Riesgo</b>: 
				{{ $riesgo['description'] }}</p><br>
				@if ($riesgo['type'] == 'subprocess')
					<b><p style="color: darkblue;">Subprocessos afectados</b>:</p>
				@else
					<b><p style="color: darkblue;">Objetivos afectados</b>:</p>
				@endif
					@foreach ($riesgo['subobj'] as $subobj)
						<li>{{ $subobj->name }} - {{ $subobj->description }}</li>
					@endforeach
					<br/>
				@foreach ($user_answers as $answer)
					@if ($answer['id'] == $riesgo['evaluation_risk_id'])
						Probabilidad: {{ $answer['probability']}}<br>
						Impacto: {{ $answer['impact'] }}
						<?php break; ?>
					@endif		
				@endforeach
				<hr>
			@endforeach

			<center>
				{!! link_to_route('evaluacion_agregadas', $title = 'Volver', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
			
			</div>
		</div>
	</div>

	<div class="col-xs-6 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-lightbulb-o"></i>
					<span>Recuerde que...</span>
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
				<table class="table" style="font-size:11px">
					<thead>
						<th>Valor</th><th>Descripci&oacute;n</th>
					</thead>
						@foreach ($tipos_impacto as $tipos)
							<tr>
							<td style="background-color: {{$tipos->color}}">{{ $tipos->value }} ({{ $tipos->name }})</td>
							<td style="background-color: {{$tipos->color}}">{{ $tipos->description }}</td>
							</tr>
						@endforeach
					</table>

					<h4><b>Descripci&oacute;n de valores de probabilidad</b></h4>
					<table class="table" style="font-size:11px">
					<thead>
						<th>Valor</th><th>Descripci&oacute;n</th>
					</thead>
						@foreach ($tipos_proba as $tipos)
							<tr>
							<td style="background-color: {{$tipos->color}}">{{ $tipos->value }} ({{ $tipos->name }})</td>
							<td style="background-color: {{$tipos->color}}">{{ $tipos->description }}</td>
							</tr>
						@endforeach
				</table>
			</div>
		</div>
	</div>
</div>
@stop
