@extends('master')

@section('title', 'Hallazgos de evaluación de control')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Hallazgos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Hallazgos</span>
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

			<div id="tipo" style="display:none;">
			
			</div>

@if (isset($issues))

		<h4><b>Hallazgo de evaluación para la {{ $kind }} del control {{ $control_name }}</b></h4>


		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				{!! link_to_route('create_hallazgo', $title = 'Agregar Hallazgo', $parameters = ['evaluation'=>$evaluation->id,'kind'=>'NULL'],$attributes = ['class'=>'btn btn-success'])!!}
			<?php break; ?>
			@endif
		@endforeach
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Estado<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Evidencia</th>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
					<th>Editar</th>
					<th>Eliminar</th>
				<?php break; ?>
			@endif
		@endforeach
				</thead>

				@foreach ($issues as $issue)
					<tr>
						<td>{{ $issue['name'] }}</td>
						<td>{{ $issue['classification'] }}</td>
						<td>{{ $issue['recommendations'] }}</td>
						<td>{{ $issue['plan'] }}</td>
						<td>{{ $issue['status'] }}</td>
						<td>{{ $issue['final_date'] }}</td>
						<td>
						@if ($issue['evidence'] == NULL)
							No tiene documentos
						@else
							<div style="cursor:hand" id="descargar_{{ $issue['id'] }}" onclick="descargar(2,'{{$issue['evidence'][0]['url'] }}')"><font color="CornflowerBlue"><u>Descargar</u></font></div>
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
							<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $issue['id'] }},2)">
							</br>
					<?php break; ?>
					@endif
				@endforeach
						@endif
				@foreach (Session::get('roles') as $role)
					@if ($role != 6)
						<td>{!! link_to_route('edit_hallazgo', $title = 'Editar', $parameters = ['org'=>$org_id,'id'=>$issue['id'],'evaluation'=>$evaluation],$attributes = ['class'=>'btn btn-success'])!!}</td>
						<td>
						<button class="btn btn-danger" onclick="eliminar2({{ $issue['id'] }},'{{ $issue['name'] }}','hallazgo','El hallazgo')">Eliminar</button></td>
					<?php break; ?>
					@endif
				@endforeach
					</tr>
				@endforeach
				</td></tr></table>
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
@endif
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')

@stop