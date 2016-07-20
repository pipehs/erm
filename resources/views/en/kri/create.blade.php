@extends('en.master')

@section('title', 'Create KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li>{!!Html::link('kri.create','Create KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Create KRI</span>
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

			Input the information for the new KRI.

			{!!Form::open(['route'=>'kri.store','method'=>'POST','class'=>'form-horizontal'])!!}
				@include('en.kri.form')

			{!!Form::close()!!}

			@if(isset($risk_id))
				<center>
					{!! link_to_route('riesgo_kri', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				</center>
			@else
				<center>
					{!! link_to_route('kri', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			@endif


			
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
{!!Html::script('assets/js/en/kri.js')!!}
@stop