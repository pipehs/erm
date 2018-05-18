@extends('master')

@section('title', 'Configuración')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('configuration','Configuración')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Configuración</span>
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
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
			
			Antes de continuar, debe ingresar correctamente los datos de configuración.<br/>

			{!!Form::open(['route'=>'configuration.store','method'=>'POST','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}

			<div class="form-group">
				<label for="system_url" class="col-sm-4 control-label">URL del sistema<a href="#" class="popper" data-popbox="pop1">?</a></label>
				<div class="col-sm-4">
					{!!Form::text('system_url',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
				</div>
			</div>

			@if (isset($all)) {{-- Seteamos todos los atributos de configuración --}}
			

			@endif		
			<div class="form-group">
				<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			<center>
   				{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
   			<center>
			</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<p>Esta es la URL que identifica la base del sistema, y el que será tomado para el envío de las encuestas. Por ejemplo: www.b-grc.com</p>
</div>
@stop

@section('scripts2')

<script>

</script>
@stop