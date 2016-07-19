@extends('en.master')

@section('title', 'Risks Assessments')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Risk Assessments</a></li>
			<li><a href="consolidar">Consolidate poll</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Consolidate poll</span>
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
		<p>On this section you will be able to consolidate the values of the risk assessments for each one of the created polls. Remember that consolidating this values (or others entered for you), these can not been modified again.</p> 

		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="margin: 0 auto;">
			<thead>
			<th>Associated risks</th>
			<th>Probability</th>
			<th>Impact</th>
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
			{!!Form::submit('Consolidate', ['class'=>'btn btn-success'])!!}
			</center>
		</div>

		{!!Form::close()!!}
			<center>
					{!! link_to_route('evaluacion_agregadas', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
			<center>

			</div>
		</div>
	</div>
</div>
@stop