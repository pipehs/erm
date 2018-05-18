@extends('master')

@section('title', 'Reporte de Hallazgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="planes_accion">Hallazgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
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
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
      <p>En esta secci&oacute;n podr&aacute; ver el reporte de hallazgos de cada organización con su información correspondiente.</p>

      	{!!Form::open(['route'=>'genissues_report','method'=>'GET','class'=>'form-horizontal'])!!}
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
					{!!Form::select('kind',['0'=>'De proceso','1'=>'De subproceso','2'=>'De organización','3'=>'Controles de proceso','4'=>'Controles de entidad','5'=>'De programa de auditoría','6'=>'De auditoría','7'=>'Prueba de auditoría','8'=>'Riesgos','9'=>'Compliance','10'=>'Canal de denuncia'],null, 
							 	   ['id' => 'kind','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>
				<div class="form-group">
						<center>
						{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
				</div>
		{!!Form::close()!!}
				
			@if (isset($issues) && isset($kind))

				@if ($kind == 0)
					<h4><b>{{ $org }}: Hallazgos de proceso</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['process'] }}</td>
							<td>{{ $issue['datos']['subprocesses'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 1)
					<h4><b>{{ $org }}: Hallazgos de subproceso</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['process'] }}</td>
							<td>{{ $issue['datos']['subprocess'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['controls'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 2)
					<h4><b>{{ $org }}: Hallazgos de organizaci&oacute;n</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Objetivo(s) de la organización<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['objectives'] }}</td>
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 3)
					<h4><b>{{ $org }}: Hallazgos de controles de procesos</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Proceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['processes'] }}</td>
							<td>{{ $issue['datos']['subprocesses'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['control'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>

						</tr>
					@endforeach

				@elseif ($kind == 4)
					<h4><b>{{ $org }}: Hallazgos de controles de entidad</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Objetivo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Control(es)<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['objectives'] }}</td>
							<td>{{ $issue['datos']['risks'] }}</td>
							<td>{{ $issue['datos']['control'] }}</td> 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 5)
					<h4><b>{{ $org }}: Hallazgos de programas de auditor&iacute;a</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Planes de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Auditor&iacute;as<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Programa de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['audit_plans'] }}</td>
							<td>{{ $issue['datos']['audits'] }}</td>
							<td>{{ $issue['datos']['audit_program']}} 
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 6)
					<h4><b>{{ $org }}: Hallazgos de auditor&iacute;a</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Planes de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Programas de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['audit_plans'] }}</td>
							<td>{{ $issue['datos']['audit'] }}</td>
							<td>{{ $issue['datos']['audit_programs'] }}</td>
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach

				@elseif ($kind == 7)
					<h4><b>{{ $org }}: Hallazgos de pruebas auditor&iacute;a</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Planes de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Programas de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Prueba de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>{{ $issue['datos']['audit_plans'] }}</td>
							<td>{{ $issue['datos']['audit'] }}</td>
							<td>{{ $issue['datos']['audit_programs'] }}</td>
							<td>{{ $issue['datos']['audit_test'] }}</td>
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['description'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach
				@elseif ($kind == 8)
					<h4><b>{{ $org }}: Hallazgos de Riesgos</b></h4>
					<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
						<th style="vertical-align:top;">Riesgos<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Clasificaci&oacute;n<label><input type="text" placeholder="Filtrar" /></lab style="vertical-align:top;"el></th>
						<th style="vertical-align:top;">Recomendaciones<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Plan de acci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Estado<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Fecha final plan<label><input type="text" placeholder="Filtrar" /></label></th>
						<th style="vertical-align:top;">Responsable plan<label><input type="text" placeholder="Filtrar" /></label></th>
					</thead>

				

					@foreach ($issues as $issue)
						<tr>
							<td>
							@foreach ($issue['datos']['risks'] as $r)
								<li>{{ $r->name }} - {{ $r->description }}</li>
							@endforeach
							</td>
							<td>{{ $issue['name'] }}</td>
							<td>{{ $issue['description'] }}</td>
							<td>{{ $issue['classification'] }}</td>
							<td>{{ $issue['recommendations'] }}</td>
							<td>{{ $issue['plan'] }}</td>
							<td>{{ $issue['status'] }}</td>
							<td>{{ $issue['final_date'] }}</td>
							<td>{{ $issue['responsable'] }}</td>
						</tr>
					@endforeach
				@endif
			</div>
				<div id="boton_exportar">
						<!--<input type="image" id="btnExport" src="assets/img/excel.jpg" width="70" height="70">-->
						{!! link_to_route('genexcelissues', $title = 'Exportar', $parameters = "$kind,$org_id", $attributes = ['class'=>'btn btn-success']) !!}
				</div>
		@endif
		
				

      
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>
@if (isset($kind))

	var value1 = {{ $kind }};
	var value2 = {{ $org_id }};
	$("#btnExport").click(function(e) {					
		window.location.href = "genexcelissues."+value1+","+value2;
		e.preventDefault();
	});
@endif
</script>
@stop