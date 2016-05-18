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
	<div class="col-xs-12 col-sm-8">
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
				<b>- {{ $riesgo['risk_name'] }} - {{ $riesgo['subobj'] }} - {{ $riesgo['orgproc'] }}:</b><br>
				<b><p style="color: blue;">Descripci&oacute;n Riesgo</b>: 
				{{ $riesgo['description'] }}</p><br>
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

	<div class="col-xs-6 col-sm-4">
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
				<table class="table table-bordered table-striped table-hover table-heading table-datatable">
				<thead>
				<th>Valor</th><th>Probabilidad</th><th>Impacto</th>
				</thead>
				@for ($i=0;$i<5;$i++)
					<tr>
						<td>({{$i+1}})</td><td>{{ $tipos_proba[$i] }}</td><td>{{ $tipos_impacto[$i] }}</td>
					</tr>
				@endfor
				</table>
			</div>
		</div>
	</div>
</div>
@stop
