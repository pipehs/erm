@extends('en.master')

@section('title', 'Create Role')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Master Data')!!}</li>
			<li>{!!Html::link('roles','Roles')!!}</li>
			<li>{!!Html::link('roles.create','Create Role')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Create Role</span>
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
			Input data for the new role.
				{!!Form::open(['route'=>'roles.store','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('en.datos_maestros.roles.form')
				{!!Form::close()!!}

				<center>
				{!!Form::open(['url'=>'roles','method'=>'GET'])!!}
					{!!Form::submit('Return', ['class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop