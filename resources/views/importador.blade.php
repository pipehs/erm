@extends('master')

@section('title', 'Importador Excel')

@section('content')


<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('help','Importador Excel')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Importador Excel</span>
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

			En esta secci&oacute;n podr&aacute; importar documentos en Excel para la carga de datos maestros.

			{!!Form::open(['route'=>'importar_excel','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					
				<div class="form-group">
					<label for="kind" class="col-sm-4 control-label">Seleccione el dato maestro que desea cargar</label>
					<div class="col-sm-4">
						{!!Form::select('kind',['0'=>'Usuarios (PArauco)','1'=>'Organizaciones','2' => 'Categorías de Riesgos','3'=>'Procesos/Subprocesos(PArauco)','4'=>'Usuarios (KOAndina)','5'=>'Riesgos (KOAndina)','6'=>'Riesgos TI (KOAndina)','7'=>'Planes de Acción (PArauco)','8' => 'Riesgos (genérico)','9'=>'Controles (genérco)','10'=>'Hallazgos y Planes de acción (genérico)','11'=>'Riesgos (PArauco)','12'=>'Riesgos tipo','13'=>'Cuentas contables','14'=>'Actualizar comentarios controles','15'=>'Actulizar Categorías (PArauco)'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -'])!!}
					</div>
				</div>

				<div class="form-group">
					<label for="file" class="col-sm-4 control-label">Seleccione documento</label>
					<div class="col-sm-4">
						<input id="file-1" type="file" class="file" name="document" data-preview-file-type="any">
					</div>
				</div>

				<div class="form-group">
					<center>
						{!!Form::submit('Importar', ['class'=>'btn btn-success','id' => 'btnsubmit'])!!}
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


@stop