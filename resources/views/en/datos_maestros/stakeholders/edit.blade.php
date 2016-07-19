@extends('en.master')

@section('title', 'Edit Stakeholder')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master Data</a></li>
			<li><a href="stakeholders">Stakeholders</a></li>
			<li><a href="stakeholders.edit.{{ $stakeholder['id'] }}">Edit Stakeholder</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Edit Stakeholder</span>
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
			Change the data that you want to update for the stakeholder.
				{!!Form::model($stakeholder,['route'=>['stakeholders.update',$stakeholder->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('en.datos_maestros.stakeholders.form')
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'stakeholders','method'=>'GET'])!!}
					{!!Form::submit('Return', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
	$("#agregar_rol").click(function() {
		$("#rol").empty();
		$("#rol").append('<div class="form-group">{!!Form::label("Tipo",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::text("rol_nuevo",null,["class"=>"form-control","required"=>"true"])!!}</div></div>');

	});
</script>
@stop

