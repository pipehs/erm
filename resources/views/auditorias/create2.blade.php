@extends('master')

@section('title', 'Agregar Plan de auditor&iacute;a')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Auditor&iacute;as')!!}</li>
			<li>{!!Html::link('nuevo_plan','Agregar Auditor&iacute;as')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Plan de Auditor&iacute;as</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			A continuaci&oacute;n ingrese la información asociada a cada auditor&iacute;a que se realizar&aacute;
			dentro del plan
				{!!Form::open(['route'=>'agregar_plan','method'=>'POST','class'=>'form-horizontal'])!!}
				<?php $i = 1; //contador primero para distintas auditorías ?>
				@foreach ($audits as $audit)
					<b>{{ $audit['name'] }}</b>

					<div class="form-group">
						{!!Form::label('Seleccione riesgos de proceso',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="risk_subprocess_audit_<?php echo $audit['id']; ?>[]" multiple="multiple">
								@foreach ($risk_subprocess_id as $risk_subprocess)
									<option value="{{ $risk_subprocess['id'] }}">{{ $risk_subprocess['name'] }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione riesgos de negocio',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="objective_risk_audit_<?php echo $audit['id']; ?>[]" multiple="multiple">
								@foreach ($objective_risk_id as $objective_risk)
									<option value="{{ $objective_risk['id'] }}">{{ $objective_risk['name'] }}</option>
								@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recursos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('resources_'.$audit['id'],null,['class'=>'form-control','required'=>'true','max'=>$plan['resources']])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha Inicio',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::date('initial_date_'.$audit['id'],null,['class'=>'form-control'
																,'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha t&eacute;rmino',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::date('final_date_'.$audit['id'],null,['class'=>'form-control'
															,'required'=>'true'])!!}
						</div>
					</div>

					<!-- enviamos id de auditoría -->
					{!!Form::hidden('audit_'.$i,$audit['id'])!!}
					<?php $i += 1; ?>
				@endforeach

				<!--volvemos a enviar como hidden todos los datos del plan de auditoría -->
				{!!Form::hidden('organization_id',$plan['organization_id'])!!}
				{!!Form::hidden('plan_name',$plan['name'])!!}
				{!!Form::hidden('plan_description',$plan['description'])!!}
				{!!Form::hidden('objectives',$plan['objectives'])!!}
				{!!Form::hidden('scopes',$plan['scopes'])!!}
				{!!Form::hidden('plan_resources',$plan['resources'])!!}
				{!!Form::hidden('stakeholder_id',$plan['stakeholder_id'])!!}
				{!!Form::hidden('methodology',$plan['methodology'])!!}
				{!!Form::hidden('plan_initial_date',$plan['initial_date'])!!}
				{!!Form::hidden('plan_final_date',$plan['final_date'])!!}
				{!!Form::hidden('rules',$plan['rules'])!!}

				<!--enviamos riesgos de negocio del plan -->
				<?php $i = 1; ?>
				@foreach ($objective_risk_id as $objective_risk)
					{!!Form::hidden('objective_risk_'.$i,$objective_risk['id'])!!}
					<?php $i += 1; ?>
				@endforeach

				<!--enviamos riesgos de proceso del plan -->
				<?php $i = 1; ?>
				@foreach ($risk_subprocess_id as $risk_subprocess)
					{!!Form::hidden('risk_subprocess_'.$i,$risk_subprocess['id'])!!}
					<?php $i += 1; ?>
				@endforeach

				<!--enviamos stakeholder team del plan si es que existe-->
				<?php $i = 1; ?>
				@foreach ($stakeholder_team as $stakeholder)
					{!!Form::hidden('stakeholder_team_'.$i,$stakeholder)!!}
					<?php $i += 1; ?>
				@endforeach

				<!--enviamos objetivos del negocio-->
				<?php $i = 1; ?>
				@foreach ($objective_id as $objective)
					{!!Form::hidden('objective_id_'.$i,$objective)!!}
					<?php $i += 1; ?>
				@endforeach

				<div class="form-group">
					<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
					</center>
				</div>
				{!!Form::close()!!}

				<center>
					{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

</script>
@stop
