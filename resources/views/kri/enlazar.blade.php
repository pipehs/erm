@extends('master')

@section('title', 'Enlazar Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li>{!!Html::link('kri.enlazar','Enlazar Riesgos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Enlazar Riesgos</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if(Session::has('warning'))
				<div class="alert alert-warning alert-dismissible" role="alert">
				{{ Session::get('warning') }}
				</div>
			@endif

			Seleccione el riesgo de proceso y el riesgo de negocio que desea enlazar para la posterior medición de KRI
				{!!Form::open(['route'=>'kri.guardar_enlace','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}

				<div id="risk_subprocess" style="float: left; width: 50%">
					<div class="form-group">
						{!!Form::label('Riesgos (Riesgo-Subproceso-Proceso)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							<select name="risk_subprocess_id" required="true">
								<option value="" selected disabled>-Seleccione-</option>
								@foreach ($risk_subprocess as $risk)
									<option value="{{ $risk['id'] }}">
										{{ $risk['name'] }} - {{ $risk['description']}}
									</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<div id="objective_risk" style="float: left; width: 50%">
					<div class="form-group">
						{!!Form::label('Riesgos (Riesgo-Objetivo)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
							<select name="objective_risk_id" required="true">
								<option value="" selected disabled>-Seleccione-</option>
								@foreach ($objective_risk as $risk)
									<option value="{{ $risk['id'] }}">
										{{ $risk['name'] }} - {{ $risk['description']}}
									</option>
								@endforeach
							</select>
						</div>
					</div>
				</div>

				<div class="form-group">
						<center>
						{!!Form::submit('Enlazar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>

				{!!Form::close()!!}
				<center><h4><b>Riesgos enlazados</b></h4></center>
				<div width="50%">
					<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px; width:50%; text-align:center; margin:auto;">
					<thead>
					<th>Riesgo de subproceso<label><input type='text' placeholder='Filtrar' /></label></th>
					<th>Riesgo de negocio<label><input type='text' placeholder='Filtrar' /></label></th>
					</thead>

					@foreach($enlaces as $enlace)
						<tr>
							<td>{{ $enlace['sub_name'] }}</td>
							<td>{{ $enlace['obj_name'] }}</td>
						</tr>
					@endforeach
					</table>
				</div>
				<center>
					{!! link_to_route('kri', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop