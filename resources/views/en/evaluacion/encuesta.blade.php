@extends('en.master')

@section('title', 'Risk Assessments')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			@if ($tipo == 1)
				<li><a href="evaluacion.encuesta.{{ $id }}">Answer poll</a></li>
			@else
				<li><a href="evaluacion.encuesta.0">Evaluate Risks</a></li>
			@endif
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Poll: {{ $encuesta }}</span>
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
			@if ($tipo == 1)
				<h4><center>Evaluation Poll</center></h4>
			@else
				<h4><center>Manual Evaluation</center></h4>
			@endif

			@if (empty($user_answers)) <!-- Si es que no hay respuestas se guardará nueva eval, de lo contrario se editará -->
				{!!Form::open(['route'=>'evaluacion.guardarEvaluacion','method'=>'POST','class'=>'form-horizontal'])!!}
			@else
				{!!Form::open(['route'=>'evaluacion.updateEvaluacion','method'=>'POST','class'=>'form-horizontal'])!!}
			@endif

			@if ($tipo == 0)
				<div class="form-group">
					<small>
				    {!!Form::label('Input your Id (without check digit)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('rut',null,
						['class'=>'form-control','required'=>'true','input maxlength'=>'8'])!!}
					</div>
					</small>
				</div>
			@else
				{!!Form::hidden('rut',$stakeholder)!!}
			@endif

			<p>For each one of the risks, enter a level on probability and impact for it.</p>

			@foreach($riesgos as $riesgo)
				@if ($tipo == 1)
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['evaluation_risk_id'])!!}
				@else
					{!!Form::hidden('evaluation_risk_id[]',$riesgo['risk_id'])!!}
				@endif
				<b>- {{ $riesgo['risk_name'] }} - {{ $riesgo['subobj'] }} - {{ $riesgo['orgproc'] }}:</b><br>
				<b><p style="color: blue;">Descripci&oacute;n Riesgo</b>: 
				{{ $riesgo['description'] }}</p><br>
				Probability:<br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<?php $cont = 0; //verificador para ver si hay respuesta ?>
							@foreach ($user_answers as $answer)
								@if ($answer['id'] == $riesgo['evaluation_risk_id'] && $answer['probability'] == $i)
									<input type="radio" name="proba_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}" checked> {{ $i }} ({{ $tipos_proba[$i-1] }})
								<?php $cont += 1; ?>
								@endif		
							@endforeach

							@if ($cont == 0)
								<input type="radio" name="proba_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[$i-1] }})
							@endif
						@else
							<input type="radio" name="proba_{{$riesgo['risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[$i-1] }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
				Impact:<br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						@if ($tipo == 1)
							<?php $cont = 0; //lo mismo para impacto ?>
							@foreach ($user_answers as $answer)
								@if ($answer['id'] == $riesgo['evaluation_risk_id'] && $answer['impact'] == $i)
									<input type="radio" name="criticidad_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}" checked> {{ $i }} ({{ $tipos_impacto[$i-1] }})
									<?php $cont += 1; ?>
								@endif
							@endforeach

							@if ($cont == 0)
								<input type="radio" name="criticidad_{{$riesgo['evaluation_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[$i-1] }})
							@endif
						@else
							<input type="radio" name="criticidad_{{$riesgo['risk_id']}}_{{$riesgo['type']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[$i-1] }})
						@endif
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<hr>
			@endforeach
			@if ($tipo == 1)
				{!!Form::hidden('evaluation_id',$id)!!}
			@endif
				{!!Form::hidden('tipo',$tipo)!!}
			<div class="row form-group">
				<center>
					@if ($tipo == 1)
						{!!Form::submit('Send Answers', ['class'=>'btn btn-primary'])!!}
					@else
						{!!Form::submit('Send Poll', ['class'=>'btn btn-primary'])!!}
					@endif
				</center>
			</div>

			{!!Form::close()!!}

			
			</div>
		</div>
	</div>
</div>
@stop
