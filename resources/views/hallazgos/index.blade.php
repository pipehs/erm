@extends('master')

@section('title', 'Mantenedor de Hallazgos')

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

			En esta secci&oacute;n podr&aacute; crear, modificar y cerrar hallazgos para los distintos datos maestros relevantes a una organizaci&oacute;n. Estos hallazgos puedes estar relacionados a un proceso, subproceso, o a la organizaci&oacute;n misma<br><br>
			<div id="cargando"><br></div>
			{!!Form::open(['route'=>'hallazgos_lista','method'=>'POST','class'=>'form-horizontal'])!!}
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
					{!!Form::select('kind',['0'=>'De proceso','1'=>'De subproceso','2'=>'De organización','3'=>'Control de proceso','4'=>'De control de entidad','5'=>'De programa de auditoría','6'=>'De auditoría'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>
			<div class="form-group">
						<center>
						{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

			<div id="tipo" style="display:none;">
			
			</div>

			@if (isset($issues))

				@if ($kind == 0)
					<h4><b>{{ $org }}: Hallazgos de procesos</b></h4>
				@elseif($kind == 1)
					<h4><b>{{ $org }}: Hallazgos de subprocesos</b></h4>
				@elseif ($kind == 2)
					<h4><b>{{ $org }}: Hallazgos de organizaci&oacute;n</b></h4>
				@elseif ($kind == 3)
					<h4><b>{{ $org }}: Hallazgos de controles de procesos</b></h4>
				@elseif ($kind == 4)
					<h4><b>{{ $org }}: Hallazgos de controles de entidad</b></h4>
				@elseif ($kind == 5)
					<h4><b>{{ $org }}: Hallazgos de programas de auditor&iacute;a</b></h4>
				@elseif ($kind == 6)
					<h4><b>{{ $org }}: Hallazgos de auditor&iacute;a</b></h4>
				@endif

				{!! link_to_route('create_hallazgo', $title = 'Agregar Hallazgo', $parameters = ['org'=>$org_id,'kind'=>$kind],$attributes = ['class'=>'btn btn-success'])!!}
				<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
				<thead>
					@if ($kind == 0)
						<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif($kind == 1)
						<th>Subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 2)
						<th>Organizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 3 || $kind == 4)
						<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 5)
						<th>Programa de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@elseif ($kind == 6)
						<th>Plan de auditor&iacute;a - Auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
					@endif
					<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Estado<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
					<th>Evidencia</th>
					<th>Editar</th>
					<th>Eliminar</th>
				</thead>

				@foreach ($issues as $issue)
					<tr>
						<td>{{ $issue['origin'] }}</td>
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

							<img src="assets/img/btn_eliminar.png" height="40px" width="40px" onclick="eliminar_ev({{ $issue['id'] }},2)">

							</br>
						@endif
						<td>{!! link_to_route('edit_hallazgo', $title = 'Editar', $parameters = ['org'=>$org_id,'id'=>$issue['id']],$attributes = ['class'=>'btn btn-success'])!!}</td>
						<td>
						<button class="btn btn-danger" onclick="eliminar({{ $issue['id'] }},'{{ $issue['name'] }}','hallazgo','el hallazgo')">Eliminar</button></td>
					</tr>
				@endforeach

			@endif


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>

</script>
@stop