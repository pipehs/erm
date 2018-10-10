
@extends(Auth::user() ? 'master' : 'master2')


@section('title', 'Configuración de denuncia')

@section('content')
<style type="text/css">
	
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="denuncias">Sistema de denuncias</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Sistema de denuncias</span>
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
			<hr>
			<div class="row">
				<div class="col-sm-8">
				@if (isset($intro) && !empty($intro))
					@foreach ($intro as $i)
						<p style="text-align: justify;">{{ $i }}</p>
					@endforeach
					
				@else
					<h4 style="font-weight: bold;">Canal de denuncia</h4>
				@endif
				</div>
			</div>
			<div class="row">
				<hr>
			</div>
			<center>
			<p><button class="block" onclick="window.location.href='registro_denuncia'"><b>Registro de denuncia</button></p>
			<br>
			<p><button class="block" onclick="window.location.href='seguimiento_denuncia'">Seguimiento de denuncia</button></p>
			<br>
			@if (Auth::user() && Auth::user()->cc_user == 1)
				<p><button class="block"onclick="window.location.href='seguimiento_admin'">Seguimiento Administrador</button></p>
				<br>
				<p><button class="block" onclick="window.location.href='reportes_denuncias'">Reportes</button></p>
				<br>
				<p><button class="block" onclick="window.location.href='cc_config'">Configuración</button></p>
				<br>
			@endif

			</center>
			</div>
		</div>
	</div>
</div>

@stop

@section('scripts2')
<script>
</script>
@stop