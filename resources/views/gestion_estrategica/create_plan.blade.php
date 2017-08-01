@extends('master')

@section('title', 'Plan estratégico')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Gestión estratégica</a></li>
			<li><a href="objetivos">Crear plan estratégico</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-puzzle-piece"></i>
					<span>Crear plan estratégico {{ $org_name }}</span>
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
	<hr>

		{!!Form::open(['route'=>'plan_estrategico.store','method'=>'POST','class'=>'form-horizontal'])!!}

				{!!Form::hidden('organization_id',$org_id)!!}
				<div class="form-group">	
				<label class="col-sm-4 control-label">Nombre plan</label>
					<div class="col-sm-3">
						{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
					</div>
				</div>

				@if (isset($father_org_name) && $father_org_name != NULL && $father_objectives != NULL && isset($father_objectives))
					<div class="form-group">
					<label class="col-sm-4 control-label">Seleccione los objetivos que desea heredar de {{ $father_org_name }}</label>
						<div class="col-sm-3">
						<select name="objectives_id[]" multiple id="el2">
							@foreach ($father_objectives as $obj)
								<option value="{{ $obj->id }}" selected>{{ $obj->code }} - {{ $obj->name }}</option>
							@endforeach
						</select>
						</div>
					</div>
				@endif
				<div class="form-group">
					{!!Form::label('Comentarios',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('comments',null,['class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
					</div>
				</div>
				<div id="exp_date" class="form-group">
					<label class="col-sm-4 control-label">Fecha inicio de vigencia</label>
					<div class="col-sm-3">
						{!!Form::date('initial_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)','required'=>'true'])!!}
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Horizonte del plan (duraci&oacute;n en años)</label>
					<div class="col-sm-1">
						{!!Form::number('duration',null,['class'=>'form-control','min'=>'1','required'=>'true'])!!}
					</div>
				</div>
								
				<div class="form-group">
					<center>
					{!!Form::submit('Agregar', ['class'=>'btn btn-primary'])!!}
					</center>
				</div>

				<center>
					{!! link_to_route('objetivos.index', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger']) !!}
				</center>

		{!!Form::close()!!}
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')

@stop