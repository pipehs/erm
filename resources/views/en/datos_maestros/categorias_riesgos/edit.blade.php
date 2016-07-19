@extends('en.master')

@section('title', 'Edit category')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Master data</a></li>
			<li><a href="categorias_risks">Risk Categories</a></li>
			<li><a href="categorias_risks.edit.{{ $risk_category['id'] }}">Edit category</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Edit category</span>
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
			Change the values that you want to update.
			{!!Form::model($risk_category,['route'=>['categorias_risks.update',$risk_category->id],'method'=>'PUT','class'=>'form-horizontal'])!!}
					@include('en.datos_maestros.categorias_riesgos.form')
			{!!Form::close()!!}
				<center>
				{!!Form::open(['url'=>'categorias_risks','method'=>'GET'])!!}
					{!!Form::submit('Return', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

