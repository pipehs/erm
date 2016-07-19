@extends('en.master')

@section('title', 'Create Objective')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="objetivos">Objectives</a></li>
			<li><a href="objetivos/{{ $objetivo['id'] }}/edit">Edit Objective</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Edit Objective</span>
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
				{!!Form::model($objetivo,['route'=>['objetivos.update',$objetivo->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('en.datos_maestros.objetivos.form')
				{!!Form::close()!!}

				<center>
					<a href="objetivos?organizacion={{$org_id}}" class="btn btn-danger">Return</a>	
				<center>
			</div>
		</div>
	</div>
</div>
@stop
