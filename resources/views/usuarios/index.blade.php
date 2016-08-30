@extends('master')

@section('title', 'Usuarios del sistema')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="crear_usuario">Usuarios del sistema</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Usuarios del sistema</span>
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
				<div class="alert alert-success alert-dismissible" role="alert">
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

			En esta secci&oacute;n podr&aacute; gestionar todos los usuarios que pueden tener acceso al sistema.<br>
			
			{!! link_to_route('usuario.create', $title = 'Crear Usuario', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
			<thead>
			<th>Rut<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Nombre completo<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>E-mail<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Fecha agregado<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Rol(es)<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Acci&oacute;n</th>
			<th>Acci&oacute;n</th>		
			</thead>
			@foreach ($users as $user)
				<tr>
				<td>{{ $user['id'] }}-{{ $user['dv'] }}
				<td>{{ $user['name'] }} {{ $user['surnames']}}</td>
				<td>{{ $user['email'] }}</td>
				<td>{{ $user['created_at'] }}</td>
				<td>
				<ul>
				@foreach($user['roles'] as $rol)
					<li>{{ $rol }}</li>
				@endforeach
				</ul>	
				</td>
				<td>{!! link_to_route('usuarios.edit', $title = 'Editar', $parameters = $user['id'], $attributes = ['class'=>'btn btn-success']) !!}</td>
				<td><button class="btn btn-danger" onclick="eliminar2({{ $user['id'] }},'{{ $user['name'] }} {{ $user['surnames'] }}','usuarios','El usuario de sistema')">Eliminar</button></td>
				</tr>
			@endforeach
			</table>
				<center>
					{!! link_to_route('/', $title = 'Volver', $parameters = NULL,
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