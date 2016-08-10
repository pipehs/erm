@extends('en.master')

@section('title', 'Risk Assessments')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Risk Assessments</a></li>
			<li><a href="evaluacion_manual">Manual evaluation</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Risk Assessments</span>
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

		<p>Select the risk that you want to evaluate adding impact and probability for it.</p>
			
			{!!Form::open(['url'=>'evaluacion.store','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
	                 	<div class="row">
	                  		{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
	                  		<div class="col-sm-6">
	                    		{!!Form::select('organization_id',$organizations, 
	                         		null, 
	                         	['id' => 'org','placeholder'=>'- Select -','required'=>'true'])!!}
	                  		</div>
	                	</div>
	                </div>

					<div class="form-group" id="riesgos_objetivos" style="display: none;">
						{!!Form::label('Bussiness risks',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							<select name="objective_risk_id[]" id="objective_risk_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_procesos" style="display: none;">
						{!!Form::label('Process risks',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							<select name="risk_subprocess_id[]" id="risk_subprocess_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de proceso de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<br>

					<div class="form-group">
						<center>
						{!!Form::hidden('manual','manual')!!}
						{!!Form::submit('Evaluate', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

			{!!Form::close()!!}
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/get_risks.js')!!}
@stop