@extends('master')

@section('title', 'Gestor de documentos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('documentos','Gestor de documentos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Gestor de Documentos</span>
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

			En esta secci&oacute;n podr&aacute; ver, agregar o eliminar los documentos asociados a los elementos del sistema.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'documentos.show','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group">
				{!!Form::label('Seleccione un tipo',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('kind',['1'=>'Controles','2'=>'Hallazgos','3'=>'Notas de auditoría','4'=>'Programas de auditoría','5'=>'Pruebas de auditoría','6'=>'Riesgos'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div id="seleccion" style="display:none;">
			</div>
			<div id="seleccion2" style="display:none;">
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>
			{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/type_documents.js')!!}
@stop