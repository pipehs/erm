@extends('master')

@section('title', 'Agregar Usuario')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="crear_usuario">Agregar Usuario</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Agregar Usuario</span>
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
			@if(Session::has('message'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			En esta secci&oacute;n podr&aacute; agregar usuarios con distintos privilegios al sistema.
			{!!Form::open(['route'=>'usuario.store','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}

				@include('usuarios.form')
					
				<div class="form-group">
					<center>
					{!!Form::submit('Agregar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
					</center>
				</div>
			{!!Form::close()!!}
				<center>
				{!! link_to_route('usuarios', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
function compararPass(pass,repass)
{
	if (pass != repass)
	{
		$("#divpass").attr('class','form-group has-error has-feedback');
		$("#divrepass").attr('class','form-group has-error has-feedback');
		$("#error_pass").html('<font color="red"><b>Ambas contraseñas deben ser iguales</b></font>');
		$("#guardar").attr('disabled','true');
	}
	else
	{
		$("#divpass").attr('class','form-group');
		$("#divrepass").attr('class','form-group');
		$("#error_pass").html('');
		$("#guardar").removeAttr('disabled');
	}
}
</script>
@stop