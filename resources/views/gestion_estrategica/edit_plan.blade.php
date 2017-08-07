@extends('master')

@section('title', 'Editar plan estrat&eacute;gico')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_estrategico.edit.{{ $strategic_plan['id'] }}">Editar Plan Estrat&eacute;gico</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Editar Plan Estrat&eacute;gico</span>
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
			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
				</div>
			@endif
			Ingrese los nuevos datos para el plan estrat&eacute;gico.
				{!!Form::model($strategic_plan,['route'=>['plan_estrategico.update',$strategic_plan->id],'method'=>'PUT','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}
					<div class="form-group">	
					<label class="col-sm-4 control-label">Nombre plan</label>
						<div class="col-sm-3">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

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
						<label class="col-sm-4 control-label">Duraci&oacute;n del plan (ingrese cantidad de años)</label>
						<div class="col-sm-1">
							{!!Form::number('duration',$duration,['class'=>'form-control','min'=>'1','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>

					{!!Form::hidden('org_id',$strategic_plan->organization_id)!!}
				{!!Form::close()!!}

				<center>
					<a href="plan_estrategico?organizacion={{$strategic_plan->organization_id}}" class="btn btn-danger">Volver</a>	
				<center>
			</div>
		</div>
	</div>
</div>
@stop
