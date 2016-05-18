@extends('master')

@section('title', 'Evaluaci&oacute;n de riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluaci&oacute;n de Riesgos</a></li>
			<li><a href="consolidar">Consolidar encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Consolidar encuesta</span>
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

		<p>Aquí podrá consolidar los valores de evaluaci&oacute;n de riesgos para una encuesta en particular. 
		Recuerde que al consolidar estos valores (u otros nuevos ingresados por ud), estos no podr&aacute;n 
		ser modificados.</p>

		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="margin: 0 auto;">
			<thead>
			<th>Riesgos asociados</th>
			<th>Probabilidad</th>
			<th>Impacto</th>
			</thead>
			{!!Form::open(['route'=>'evaluacion.consolidar2','method'=>'POST','class'=>'form-horizontal'])!!}
				@foreach ($evaluations_risks as $evaluation)
					<tr>
					<td>{{ $evaluation['risk_name'] }} - {{ $evaluation['subobj_name']}} - {{ $evaluation['orgproc_name'] }}</td>
					<td>
					<center>
					{!!Form::number('probability_'.$evaluation['id'],$evaluation['probability'],
					['class'=>'form-control','style'=>'width: 50px;',
					'max'=>'5','min'=>'1','step'=>'0.1'])!!}
					</center>
					</td>
					<td>
					<center>
					{!!Form::number('impact_'.$evaluation['id'],$evaluation['impact'],
					['class'=>'form-control','style'=>'width: 50px;',
					'max'=>'5','min'=>'1','step'=>'0.1'])!!}
					</center>
					</td>
					 </tr>
				@endforeach

		</table>
		<div class="form-group">
			<center>
			{!!Form::hidden('evaluation_id',$evaluation_id)!!}
			{!!Form::submit('Consolidar', ['class'=>'btn btn-success'])!!}
			</center>
		</div>

		{!!Form::close()!!}
			<center>
					{!! link_to_route('evaluacion_agregadas', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
			<center>

			</div>
		</div>
	</div>
</div>
@stop