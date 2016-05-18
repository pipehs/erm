@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="evaluacion_manual">Evaluacion Manual</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Evaluaci&oacute;n de riesgos</span>
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

		<p>Seleccione los riesgos que desee evaluar agregando impacto y probabilidad del mismo manualmente.</p>
			
			{!!Form::open(['url'=>'evaluacion.store','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
						{!!Form::label('Seleccione riesgos de subprocesos',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							{!!Form::select('risk_subprocess_id[]',$riesgos_sub,null, 
							 	   ['id' => 'el2','multiple'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione riesgos de negocio',
						null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							{!!Form::select('objective_risk_id[]',$riesgos_obj,null, 
							 	   ['id' => 'el2','multiple'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::hidden('manual','manual')!!}
						{!!Form::submit('Evaluar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

			{!!Form::close()!!}
			</div>
		</div>
	</div>
</div>
@stop