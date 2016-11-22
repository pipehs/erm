@extends('master')

@section('title', 'Controles')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="evaluar_controles">Evaluar Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluar Controles</span>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif


		{!!Form::open(['route'=>'evaluar_controles2','method'=>'GET','class'=>'form-horizontal',
						'enctype'=>'multipart/form-data'])!!}
			<div id="cargando"><br></div>
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'organization_id','required'=>'true','placeholder'=>'- Seleccione -','onchange'=>'selectControls()'])!!}
				</div>
			</div>

			<div class="form-group">
				<label for="control_kind" class="col-sm-4 control-label">Seleccione tipo de control</label>
				<div class="col-sm-3">
					{!!Form::select('control_kind',['1'=>'Controles de entidad','2' => 'Controles de proceso'],null, 
							 	   ['id' => 'control_kind','required'=>'true','placeholder'=>'- Seleccione -','onchange'=>'selectControls()'])!!}
				</div>
			</div>

			<div id="procesos" style="display: none;"></div>
			<div id="subprocesos" style="display: none;"></div>
			<div id="control" style="display: none;"></div>
			<div id="create_edit" style="display: none;"></div>
			<div id="button" style="display: none;">
				<div class="form-group">
						<center>
						{!!Form::submit('Selecionar', ['class'=>'btn btn-success'])!!}
						</center>
				</div>
			</div>

		{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/controls_by_org.js')!!}

{!!Html::script('assets/js/descargar.js')!!}
@stop

