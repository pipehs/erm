@extends('en.master')

@section('title', 'Polls of risk events identification')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identification of risk events</a></li>
			<li><a href="encuestas">Polls</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Select poll</span>
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

		@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
		@endif

		@if(Session::has('error'))
			<div class="alert alert-danger alert-dismissible" role="alert">
			{{ Session::get('error') }}
			</div>
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

<p>On this section you will be able to view the answers for each poll of identification of risk events. </p> 

@if (isset($polls))

	{!!Form::open(['url'=>'encuestas','method'=>'GET','class'=>'form-horizontal'])!!}
	<div class="row form-group">
		{!!Form::label('Select poll',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			{!!Form::select('encuesta',$polls,
								 	   null,
								 	   ['required' => 'true',
								 	   	'placeholder' => '- Select -',
								 	   	'id' => 'el2'])
							!!}
		</div>
	</div>	

	<center>
		<div class="row form-group">
		  {!!Form::submit('Select', ['class'=>'btn btn-success','name'=>'aplicar'])!!}
		</div>
	</center>
@elseif (isset($stakeholders))
	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Id</th><th>Name</th><th>View Answers</th>
	</thead>
	@if ($answers == "[]")
		@foreach ($stakeholders as $stakeholder)
			<tr>
				<td>{{ $stakeholder['id'] }}-{{ $stakeholder['dv'] }}</td>
				<td>{{ $stakeholder['name'] }} {{ $stakeholder['surnames'] }}</td>
				<td>This user has not yet answered</td>
			</tr>
		@endforeach
	@else
		@foreach ($stakeholders as $stakeholder)
			<?php $cont = 0; ?>
			@foreach ($answers as $answer)
				@if ($answer['stakeholder_id'] == $stakeholder['id'])
					<?php $cont += 1; ?>
				@endif
			@endforeach
			<tr>
				<td>{{ $stakeholder['id'] }}-{{ $stakeholder['dv'] }}</td>
				<td>{{ $stakeholder['name'] }} {{ $stakeholder['surnames'] }}</td>

				@if ($cont == 0)
					<td>This user has not yet answered</td>
				@else
					<td>
						{!! link_to_route('encuestas.show', $title = 'Show',
					 $parameters = ['poll_id'=>$poll_id,'stakeholder_id'=>$stakeholder['id']],
					  $attributes = ['class'=>'btn btn-success']) !!}
					</td>
				@endif
			</tr>
		@endforeach
	@endif
	</table>

	<center>
			{!! link_to_route('encuestas', $title = 'Return', 
				$parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
	</center>
@endif

		

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')

@stop