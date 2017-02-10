@extends('master2')

@section('title', 'Encuesta respondida')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluacion</a></li>
			<li><a href="#">Encuesta</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Encuesta</span>
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

		@if (Auth::guest())

		@else
			<center>
				{!! link_to_route('home', $title = 'Ir al inicio', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-success'])!!}
			<center>
        @endif

			</div>
		</div>
	</div>
</div>
@stop
@section('scripts')

@stop

