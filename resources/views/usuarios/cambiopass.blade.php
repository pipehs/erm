@extends('master')

@section('title', 'Cambiar contraseña')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="usuarios.changepass">Cambiar contraseña</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Cambiar contraseña</span>
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

			En esta secci&oacute;n podr&aacute; cambiar su contraseña de ingreso al sistema.
			{!!Form::open(['route'=>'updatepass','method'=>'POST','class'=>'form-horizontal'])!!}

				<div class="form-group">
					<label for="pass_old" class='col-sm-4 control-label'>Ingrese contraseña actual</label>
					<div class="col-sm-3">
						<input type="password" class="form-control" name="pass_old" />
					</div>
				</div>

				<div class="form-group" id="divpass">
					<label for="password" class='col-sm-4 control-label'>Ingrese nueva contraseña</label>
					<div class="col-sm-3">
						<input type="password" class="form-control" name="password" id="pass" onchange="compararPass(this.value,form.repass.value)" />
					</div>
				</div>

				<div class="form-group" id="divrepass">
					<label for="repassword" class='col-sm-4 control-label'>Re-ingrese nueva contraseña</label>
					<div class="col-sm-3">
						<input type="password" class="form-control" name="repassword" id="repass"  onchange="compararPass(form.pass.value,this.value)"/>
						<div id="error_pass"></div>
					</div>
				</div>
					
				<div class="form-group">
					<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'guardar'])!!}
					</center>
				</div>
			{!!Form::close()!!}
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
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