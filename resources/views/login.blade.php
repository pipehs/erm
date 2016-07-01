@extends('master2')

@section('title', 'Home')

@section('content')
<div class="container-fluid">
	<div id="page-login" class="row">
		<div class="col-xs-12 col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3">
			<div class="box">
				<div class="box-content">
					<div class="text-center">
						<h3 class="page-header">Ingreso Sistema - B-GRC</h3>
					</div>
					
				    {!! Form::open(['route' => 'log.store', 'class' => 'form', 'method' => 'POST']) !!}
					<div class="form-group">
						<label class="control-label">E-mail</label>
						<input type="email" class="form-control" name="email" />
					</div>
					<div class="form-group">
						<label class="control-label">Contraseña</label>
						<input type="password" class="form-control" name="password" />
					</div>
					<div class="text-center">
						{!!Form::submit('Ingresar', ['class'=>'btn btn-primary'])!!}
					</div>
					<div class="text-center">
						<a href="crear_usuario">Crear Usuario</a></p>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</div>

@stop