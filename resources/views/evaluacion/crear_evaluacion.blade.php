@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion">Crear Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Crear Encuesta de Evaluaci&oacute;n</span>
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

		@if(Session::has('error'))
			<div class="alert alert-danger alert-dismissible" role="alert">
			{{ Session::get('error') }}
			</div>
		@endif

En esta secci&oacute;n podr&aacute; crear encuestas para la evaluación de los riesgos identificados.

{!!Form::open(['url'=>'evaluacion.store','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
	                 	<div class="row">
	                  		{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
	                  		<div class="col-sm-4">
	                    		{!!Form::select('organization_id',$organizations, 
	                         		null, 
	                         	['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
	                  		</div>
	                	</div>
	                </div>

	                <div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('risk_category_id',$categories,
							 	   null, 
							 	   ['id'=>'risk_category_id','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
		               <div class="row">
		                 <label for="risk_subcategory_id" class='col-sm-4 control-label'>Sub-categor&iacute;a 1</label>
		                 <div class="col-sm-4">
		                    <select id="risk_subcategory_id" name="risk_subcategory_id"></select>
		                 </div>
		              </div>
		            </div>

		            <div class="form-group">
		               <div class="row">
		                 <label for="risk_subcategory_id2" class='col-sm-4 control-label'>Sub-categor&iacute;a 2</label>
		                 <div class="col-sm-4">
		                    <select id="risk_subcategory_id2" name="risk_subcategory_id2"></select>
		                 </div>
		              </div>
		            </div>

					<div class="form-group" id="riesgos_objetivos" style="display: none;">
						{!!Form::label('Riesgos de negocio',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="objective_risk_id[]" id="objective_risk_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_procesos" style="display: none;">
						{!!Form::label('Riesgos de proceso',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="risk_subprocess_id[]" id="risk_subprocess_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de proceso de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::submit('Generar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
$(document).ready(function () {
	$('#org').change();
});
</script>
{!!Html::script('assets/js/get_risks.js')!!}
@stop