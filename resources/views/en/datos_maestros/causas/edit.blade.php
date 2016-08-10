@extends('en.master')

@section('title', 'Edit cause')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master data</a></li>
			<li><a href="causas">Causes</a></li>
			<li><a href="causas.edit.{{ $causa['id'] }}">Edit cause</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Edit Cause</span>
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
			Change the data that you want to update.
			{!!Form::model($causa,['route'=>['causas.update',$causa->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
				@include('en.datos_maestros.causas.form')
			{!!Form::close()!!}
				<center>
				{!!Form::open(['url'=>'causas','method'=>'GET'])!!}
					{!!Form::submit('Return', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop