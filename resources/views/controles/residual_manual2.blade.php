@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos Residual')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluar riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Evaluación de Riesgos Residual</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif

			<h4><center>Evaluaci&oacute;n manual de Riesgo residual</center></h4>

			{!!Form::open(['route'=>'residual_manual.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}

			<p>Por cada riesgo identificado, señale un nivel de probabilidad e impacto del mismo, tanto de forma cuantitativa (impacto y probabilidad neta) como cualitativa (Riesgo residual basado en las descripciones descritas).</p>

			@foreach($riesgos as $riesgo)
				{!!Form::hidden('evaluation_risk_id[]',$riesgo['org_risk_id'])!!}

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
				{{ $riesgo['description'] }}</p>

				<b>Control(es) asociado(s)</b><br>
				@if (!empty($riesgo['controls']))
					@foreach ($riesgo['controls'] as $control)
						<li>{{ $control->name }} - {{ $control->description }}</li>
					@endforeach
				@else
					<div class="alert alert-danger alert-dismissible" role="alert">
						<small>No existen Controles asociados al Riesgo</small>
					</div>
				@endif
				<br>

				<table class="table table-bordered table-striped table-heading" style="font-size:11px">
					<thead>
						<th>Evaluación Cuantitativa</th>
						<th>Evaluación Cualitativa</th>
					</thead>
					<tr>
					<td>
					@if (isset($riesgo['last_m']) && !empty($riesgo['last_m']) && $riesgo['last_m'] != NULL)
						@if ($riesgo['last_m']->kind == 1)
							<b>Impacto Bruto: {{$riesgo['last_m']->impact}} Pesos<br>
						@elseif ($riesgo['last_m']->kind == 2)
							<b>Impacto Bruto: {{$riesgo['last_m']->impact}} Dólares<br>
						@elseif ($riesgo['last_m']->kind == 3)
							<b>Impacto Bruto: {{$riesgo['last_m']->impact}} Euros<br>
						@elseif ($riesgo['last_m']->kind == 4)
							<b>Impacto Bruto: {{$riesgo['last_m']->impact}} UF<br>
						@endif

						<b>Probabilidad Bruta: {{$riesgo['last_m']->probability}}%<br>
						<b>Exposición Bruta: {{(($riesgo['last_m']->impact*$riesgo['last_m']->probability)/100)}}<br>

						@if ($riesgo['last_m']->calification == 1)
							<b>Calificación: H<br>
						@elseif ($riesgo['last_m']->calification == 2)
							<b>Calificación: M<br>
						@elseif ($riesgo['last_m']->calification == 3)
							<b>Calificación: L<br>
						@endif
					@else
						<div class="alert alert-danger alert-dismissible" role="alert">
							No existe evaluación cuantitativa bruta del Riesgo
						</div>
					@endif
					</td>
					<td>
						@if (!empty($riesgo['evaluation_risk']))
							<b>Probabilidad: {{ $riesgo['evaluation_risk']->avg_probability }}<br>
							<b>Impacto: {{ $riesgo['evaluation_risk']->avg_impact }}<br>
						@else
							<div class="alert alert-danger alert-dismissible" role="alert">
								No existe evaluación inherente del Riesgo
							</div>
						@endif
					</td>
					</tr>
				</table>
				<h5><b>Evaluación Cuantitativa</b></h5>
				@if (isset($ebt) && !empty($ebt) && $ebt != NULL && isset($riesgo['last_m']) && !empty($riesgo['last_m']) && $riesgo['last_m'] != NULL)
					<div class="form-group">
						{!!Form::label('Impacto neto',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-3">
							<input type="number" name="impact_{{$riesgo['org_risk_id']}}" id="impact_{{$riesgo['org_risk_id']}}" class="form-control" min="0" onchange="generate_exposition2({{$ebt->ebt}},{{$riesgo['org_risk_id']}})">
						</div>
						<div class="col-sm-2">
							{!!Form::select('kind',$kinds,$ebt->kind_ebt,['id'=>'kind','placeholder'=>'- Seleccione -','disabled' => 'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Probabilidad neta',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-5">
							<select name="probability_{{$riesgo['org_risk_id']}}" id="probability_{{$riesgo['org_risk_id']}}" onchange="generate_exposition2({{$ebt->ebt}},{{$riesgo['org_risk_id']}})">
								<option value="" selected>- Seleccione -</option>
								<option value="0">0 %</option>
								<option value="10">10 %</option>
								<option value="20">20 %</option>
								<option value="30">30 %</option>
								<option value="40">40 %</option>
								<option value="50">50 %</option>
								<option value="60">60 %</option>
								<option value="70">70 %</option>
								<option value="80">80 %</option>
								<option value="90">90 %</option>
								<option value="100">100 %</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Exposición bruta',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-3">
							<input type="number" name="exposition_{{$riesgo['org_risk_id']}}" id="exposition_{{$riesgo['org_risk_id']}}" min="0" disabled="true">
						</div>
						<div class="col-sm-2">
							<select name="calification_{{$riesgo['org_risk_id']}}" id="calification_{{$riesgo['org_risk_id']}}">
								<option value="1">H</option>
								<option value="2">M</option>
								<option value="3">L</option>
							</select>

							<input type="hidden" name="calification2_{{$riesgo['org_risk_id']}}" id="calification2_{{$riesgo['org_risk_id']}}">
						</div>
					</div>
				@else
					<div class="alert alert-danger alert-dismissible" role="alert">
						Antes de ingresar una evaluación cuantitativa del Riesgo, debe primero que todo definir un EBT (Earns Before Tax) para la Organización, y luego ingresar la materialidad bruta del Riesgo en la sección de Identificación de Riesgos 
					</div>
				@endif
				<h5><b>Evaluación Cualitativa</b></h5>
				<b>Probabilidad:</b><br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						<input type="radio" name="proba_{{$riesgo['org_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_proba[5-$i]->name }})

						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
				<b>Impacto:</b><br>
				@for($i=1; $i<=5; $i++)
				<div class="radio-inline">
					<label>
						<input type="radio" name="criticidad_{{$riesgo['org_risk_id']}}" required="true" value="{{ $i }}"> {{ $i }} ({{ $tipos_impacto[5-$i]->name }})

						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				@endfor
				<br><br>
			@endforeach
				
			<div class="row form-group">
				<center>
					{!!Form::submit('Enviar Evaluación', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
				</center>
			</div>

			{!!Form::close()!!}
			</div>
		</div>
	</div>

	<div class="col-xs-12 col-sm-4">
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
@section('scripts2')
	{!!Html::script('assets/js/create_edit_risks.js')!!}
@stop