@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			@if ($tipo == 1)
				<li><a href="evaluacion.encuesta.{{ $id }}">Responder Encuesta</a></li>
			@else
				<li><a href="evaluacion.encuesta.0">Evaluar riesgos</a></li>
			@endif
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-7">
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
			<span><b>Organización asociada: {{ $org_name }}</b></span>
			@if(Session::has('message'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('message') }}
				</div>
			@endif
			@if ($tipo == 1)
				<h4><center>Encuesta de evaluaci&oacute;n</center></h4>
			@else
				<h4><center>Evaluaci&oacute;n manual</center></h4>
			@endif

			@if (empty($user_answers)) <!-- Si es que no hay respuestas se guardará nueva eval, de lo contrario se editará -->
				{!!Form::open(['route'=>'evaluacion.guardarEvaluacion','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
			@else
				{!!Form::open(['route'=>'evaluacion.updateEvaluacion','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
			@endif

			@if ($tipo == 0)
				<div class="form-group">
					<small>
				    {!!Form::label('Ingrese su Rut o DNI (sin dígito verificador en caso de Chile)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('rut',null,
					['class'=>'form-control','required'=>'true'])!!}
					</div>
					</small>
				</div>
			@else
				{!!Form::hidden('rut',$stakeholder)!!}
			@endif

			<p>Por cada riesgo identificado, señale un nivel de probabilidad e impacto del mismo.</p>

			@foreach($riesgos as $riesgo)
				@if ($tipo == 1)
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['evaluation_risk_id'])!!}
				@else
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['org_risk_id'])!!}
				@endif
				<b>- {{ $riesgo['risk_name'] }}:</b><br>
				@if ($riesgo['type'] == 'subprocess')
					<b>Subprocesos afectados</b><br>
				@else
					<b>Objetivos afectados</b><br>
				@endif

				@foreach ($riesgo['subobj'] as $subobj)
					<li>{{ $subobj->name }} - {{ $subobj->description }}</li>
				@endforeach
				<b><p style="color: blue;">Descripci&oacute;n Riesgo</b>: 
				{{ $riesgo['description'] }}</p><br>
				<b>Probabilidad:</b><br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<?php $cont = 0; //verificador para ver si hay respuesta ?>
							@foreach ($user_answers as $answer)
								@if ($answer['id'] == $riesgo['evaluation_risk_id'] && $answer['probability'] == $i)
									<input type="radio" name="proba_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}" checked> {{ $i }} ({{ $tipos_proba[5-$i]->name }})
								<?php $cont += 1; ?>
								@endif		
							@endforeach

							@if ($cont == 0)
								<input type="radio" name="proba_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[5-$i]->name }})
							@endif
						@else
							<input type="radio" name="proba_{{$riesgo['org_risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[5-$i]->name }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
				<b>Impacto:</b><br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<?php $cont = 0; //lo mismo para impacto ?>
							@foreach ($user_answers as $answer)
								@if ($answer['id'] == $riesgo['evaluation_risk_id'] && $answer['impact'] == $i)
									<input type="radio" name="criticidad_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}" checked> {{ $i }} ({{ $tipos_impacto[$i-1]->name }})
									<?php $cont += 1; ?>
								@endif
							@endforeach

							@if ($cont == 0)
								<input type="radio" name="criticidad_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[5-$i]->name }})
							@endif
						@else
							<input type="radio" name="criticidad_{{$riesgo['org_risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[5-$i]->name }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
				<b>Comentarios (opcional):</b>
				@if ($tipo == 1)
					<?php $cont = 0; //lo mismo para comentarios ?>
					@foreach ($user_answers as $answer)
						@if ($answer['id'] == $riesgo['evaluation_risk_id'] && $answer['comments'] != NULL)
							<textarea name="comments_{{$riesgo['evaluation_risk_id']}}" class="form-control" rows="3" cols="3">{{ $answer['comments'] }}</textarea>
							<?php $cont += 1; ?>
						@endif
					@endforeach

					@if ($cont == 0)
						<textarea name="comments_{{$riesgo['evaluation_risk_id']}}" class="form-control" rows="3" cols="3"></textarea>
					@endif
				@else
					<textarea name="comments_{{$riesgo['org_risk_id']}}" class="form-control" rows="3" cols="3"></textarea>
				@endif
				<hr>
			@endforeach
			@if ($tipo == 1)
				{!!Form::hidden('evaluation_id',$id)!!}
			@endif
				{!!Form::hidden('tipo',$tipo)!!}
			<div class="row form-group">
				<center>
					@if ($tipo == 1)
						{!!Form::submit('Enviar Respuestas', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
					@else
						{!!Form::submit('Enviar Evaluación', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
					@endif
				</center>
			</div>

			{!!Form::close()!!}
			</div>
		</div>
	</div>

	<div class="col-xs-12 col-sm-5">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-info-circle"></i>
					<span>Descripci&oacute;n de valores de evaluaci&oacute;n</span>
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

			<h4><b>Descripci&oacute;n de valores de impacto</b></h4>
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
