@extends('master')

@section('title', 'Usuarios del sistema')

@section('content')
<style>
input[type="number"] {
   width:30px;
   text-align: center;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="crear_usuario">Riesgo controlado</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Configuraci&oacute;n de criterio para riesgo controlado</span>
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

			En esta secci&oacute;n podr&aacute; configurar los valores que autom&acute;ticamente se le asignar&aacute; a un riesgo una vez que su control sea evaluado (efectiva o inefectivamente). Los parametros son los siguientes:<br>
			<ul>
				<li><b>Dimensi&oacute;n de la evaluaci&oacute;n:</b> Identifica si se configura el valor del riesgo en probabilidad o impacto del mismo.</li>
				<li><b>Evaluaci&oacute;n Inherente:</b> Identifica el valor inherente que se le asigna a un control.</li>
				<li><b>Resultado del control: </b> Los par&aacute;metros del resultado del control pueden ser efectivo o inefectivo.</li>
				<li><b>Evaluaci&oacute;n Controlado: </b> Identifica el valor del riesgo luego de que este es controlado, en probabilidad e impacto como resultado de una evaluaci&oacute;n del control asociado al mismo.</li>
			</ul>
			<hr>
			{!!Form::open(['route'=>'criteria.update','method'=>'POST','class'=>'form-horizontal'])!!}

				<table class="table table-bordered table-striped table-hover table-heading" style="font-size:11px; width:60%" align="center">
				<thead>
				<th>Dimensi&oacute;n de la evaluaci&oacute;n</th>
				<th style="width:20%">Evaluaci&oacute;n inherente<label></label></th>
				<th>Resultado del control</th>
				<th style="width:20%">Evaluaci&oacute;n controlado</th>	
				</thead>
				@foreach ($tabla as $dato)
					<tr>
					<td>
					@if ($dato->dim_eval == 1)
						Probabilidad de ocurrencia
					@elseif ($dato->dim_eval == 2)
						Impacto
					@endif
					</td>
					<td>
					 	<input type="number" name="eval_in_risk_{{ $dato->id }}" value="{{ $dato->eval_in_risk }}" min="1" max="5" disabled>
					</td>
					<td>
					@if ($dato->control_evaluation == 2)
						Inefectivo
					@elseif ($dato->control_evaluation == 1)
						Efectivo
					@endif
					</td>
					<td>
						{!!Form::number('eval_ctrl_risk_'.$dato->id,$dato->eval_ctrl_risk, 
					 	   null,['id' => 'el2','min' => '1','max' => '5'])!!}
					</td>
					</tr>
				@endforeach
				</table>
				<div class="form-group">
					<center>
					{!!Form::submit('Guardar cambios', ['class'=>'btn btn-success','id'=>'guardar'])!!}
					</center>
				</div>
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