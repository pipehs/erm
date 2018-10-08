@extends(Auth::user() ? 'master' : 'master2')

@section('title', 'Seguimiento de denuncias')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="seguimiento_admin">Seguimiento de denuncia</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Seguimiento de denuncia</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif

		@if (!isset($case))
			<p>Ingrese el ID y contraseña del caso que desea revisar</p>

			{!!Form::open(['route'=>'ver_denuncia','method'=>'GET','class'=>'form-horizontal'])!!}	

			<div class="form-group">
				{!!Form::label('ID *',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-4">
					{!!Form::number('id',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
				</div>
			</div>

			<div class="form-group">
				{!!Form::label('Contraseña',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-4">
					<input type="password" class="form-control" name="password" required />
				</div>
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Enviar', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
				</center>
			</div>

			{!!Form::close()!!}

		@else

		@endif
			
		
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>

			</div>
		</div>
	</div>
</div>

@stop

