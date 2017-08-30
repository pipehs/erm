@extends('master')

@section('title', 'Reporte de Gráficos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="graficos_controles">Gr&aacute;ficos Planes de Acci&oacute;n</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Planes de Acci&oacute;n</span>
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
      		<p>En esta secci&oacute;n podr&aacute; ver distintos gr&aacute;ficos que permitan observar de mejor manera toda la informaci&oacute;n relacionada a los planes de acci&oacute;n ingresados en el sistema.</p>

		@if(!isset($issues_om))
			{!!Form::open(['url'=>'graficos_planes_accion2.0.0','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				  <div class="row">
				    {!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
				    <div class="col-sm-4">
				      {!!Form::select('organization_id',$organizations, 
				           null, 
				          ['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
				    </div>
				 </div>
			</div>
			<br>
			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
				</center>
			</div>
			{!!Form::close()!!}
		@endif
		</div>
	</div>
</div>

@if (isset($issues_om))
<!-- Gráfico de planes accion de controles, auditorias u otros -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Tipo de planes de acci&oacute;n</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar del universo de planes de acci&oacute;n, de que tipo son estos, ya sean planes de acci&oacute;n para evaluaci&oacute;n de controles, auditor&iacute;s u otros.</p>
			<p id="alternativo"></p>
			<div id="piechart_3d" style="width: 100%; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de planes accion de controles, auditorias u otros -->

<!-- Gráfico de hallazgos -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Clasificaci&oacute;n de hallazgos</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar los distintos hallazgos registrados en el sistema, además de la informaci&oacute;n correspondiente a los planes de acci&oacute;n enlazados a los mismos (siempre que estos tengan un plan de acci&oacute;n registrado).</p>
			<p id="alternativo2"></p>
			<div id="piechart2" style="width: 100%; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de hallazgos -->

<!-- Gráfico de estado de planes de acción -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Estado de planes de acción</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar el estado de los distintos planes de acci&oacute;n, si es que est&aacute;n cerrados, pr&oacute;ximos a cerrar, o aquellos que est&aacute;n pasados en su fecha final y aun no se han cerrado.</p>
			<p id="alternativo3"></p>
			<div id="piechart3" style="width: 100%; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de estado de planes de acción -->

<!-- Gráfico de porcentaje de avances -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Porcentaje de avances de planes de acción</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar el porcentaje de avance de los distintos planes de acci&oacute;n ingresados en el sistema.</p>
			<br>
			<p id="alternativo4"></p>
			<div id="piechart4" style="width: 100%; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de porcentaje de avances -->
</div>
<div class="row">
<div class="col-xs-12 col-sm-12">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
					<i class="fa fa-circle"></i>
					<span>Exportar</span>
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
			<div id="boton_exportar">
			{!!Form::open(['route'=>'export_action_plan_graphics','method'=>'POST','class'=>'form-horizontal'])!!}
				{!!Form::hidden('grafico1','',['id' => 'grafico1'])!!}
				{!!Form::hidden('grafico2','',['id' => 'grafico2'])!!}
				{!!Form::hidden('grafico3','',['id' => 'grafico3'])!!}
				{!!Form::hidden('grafico4','',['id' => 'grafico4'])!!}
				{!!Form::hidden('org',$org,['id' => 'org'])!!}
				<div class="form-group">
						<center>
						{!!Form::submit('Exportar a Word', ['class'=>'btn btn-info'])!!}
						</center>
				</div>
			{!!Form::close()!!}
			</div>
			</div>
		</div>
	</div>
</div>
</div>
<!-- FIN Gráfico de estado de planes de acción -->
@endif
@stop
@section('scripts2')
<!--<script type="text/javascript" src="assets/js/loader.js"></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

@if (isset($issues_om))
	@if ($cont_ctrl > 0 || $cont_audit > 0 || $cont_audit_plan > 0 || $cont_program > 0 || $cont_org > 0 || $cont_subprocess > 0 || $cont_process > 0 || $cont_process_ctrl > 0 || $cont_bussiness_ctrl > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Planes de acción', 'Cantidad'],
          ['Evaluación de controles',     {{ $cont_ctrl }}],
          ['Ejecución de auditorías',     {{ $cont_audit }}],
          ['Planes de auditoría',     {{ $cont_audit_plan }}],
          ['Programas de auditoría',     {{ $cont_program }}],
          ['Organización',     {{ $cont_org }}],
          ['Subprocesos',     {{ $cont_subprocess }}],
          ['Procesos',     {{ $cont_process }}],
          ['Controles de proceso',     {{ $cont_process_ctrl }}],
          ['Controles de entidad',     {{ $cont_bussiness_ctrl }}]
        ]);

        var options = {
          title: 'Tipos de planes de acción',
          is3D: false,
          colors: ['#0431B4','#5882FA','#ACD0FB','#967FD9','#85D4A1','#8A64AE','#64AE90','#C4D551','#D58651']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico1').value = chart.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart, 'select', clickHandler);

      	function clickHandler(e) {
      		var sel = chart.getSelection();

      		if (sel.length > 0)
			{
				//alert(sel[0].row);
				if (sel[0].row == 0) //controles
				{
					var title = '<b>Planes de acción creados al evaluar controles</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Control</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_ctrl as $plan)
							text += '<tr><td>{{$plan["control"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.8.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //auditorías
				{
					var title = '<b>Planes de acción creados al ejecutar auditoría</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan de auditoría</th><th>Auditoría</th><th>Programa</th><th>Prueba</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_audit as $plan)
							text += '<tr><td>{{$plan["audit_plan"]}}</td>';
							text += '<td>{{$plan["audit"]}}</td>';
							text += '<td>{{$plan["audit_program"]}}</td>';
							text += '<td>{{$plan["audit_test"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.9.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 2) //planes de auditorías
				{
					var title = '<b>Planes de acción creados para auditorías</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan de auditoría</th><th>Auditoría</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_audit_plan as $plan)
							text += '<tr><td>{{$plan["audit_plan"]}}</td>';
							text += '<td>{{$plan["audit"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.17.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 3) //programa de auditoría
				{
					var title = '<b>Planes de acción creados para programas de auditoría</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan de auditoría</th><th>Auditoría</th><th>Programa</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_program as $plan)
							text += '<tr><td>{{$plan["audit_plan"]}}</td>';
							text += '<td>{{$plan["audit"]}}</td>';
							text += '<td>{{$plan["audit_program"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.18.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 4) //organización
				{
					var title = '<b>Planes de acción creados para organización</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Organización</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_org as $plan)
							text += '<tr><td>{{$plan["organization"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.19.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 5) //subprocesos
				{
					var title = '<b>Planes de acción creados para subprocesos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Proceso</th><th>Subproceso</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_subprocess as $plan)
							text += '<tr><td>{{$plan["process"]}}</td>';
							text += '<td>{{$plan["subprocess"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.20.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 6) //procesos
				{
					var title = '<b>Planes de acción creados para procesos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Proceso</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_process as $plan)
							text += '<tr><td>{{$plan["process"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.21.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				if (sel[0].row == 7) //controles de proceso
				{
					var title = '<b>Planes de acción creados para controles de procesos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Control</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_process_ctrl as $plan)
							text += '<tr><td>{{$plan["control"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.22.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				if (sel[0].row == 8) //controles de negocio
				{
					var title = '<b>Planes de acción creados para controles de negocio</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Control</th><th>Hallazgo</th><th>Recomendaciones</th><th>Plan</th><th>Estado</th><th>Fecha final</th><th>Responsable</th></thead>';

					@foreach ($action_plans_bussiness_ctrl as $plan)
							text += '<tr><td>{{$plan["control"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.23.{{ $org }}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}

			}
      		console.log(sel);
		}
      }
    @else
    	$('#alternativo').html('<b>No existen planes de auditor&iacute;as pendientes ni en ejecuci&oacute;on</b>');
    	//$('#alternativo2').html('<b>Aun no se han ejecutado controles</b>');
    @endif

    @if ($op_mejora > 0 || $deficiencia > 0 || $deb_significativa > 0)
    //aqui otro gráfico
    	google.charts.setOnLoadCallback(chart2);
      function chart2() {
        var data = google.visualization.arrayToDataTable([
          ['Clasificación', 'Cantidad'],
          ['Oportunidad de mejora',     {{ $op_mejora }}],
          ['Deficiencia',     {{ $deficiencia }}],
          ['Debilidad significativa',     {{ $deb_significativa }}]
        ]);

        var options = {
          title: 'Clasificación de hallazgos',
          is3D: false,
          colors: ['#74DF00', '#FF8000', '#FF0000']
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart2.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico2').value = chart2.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart2, 'select', clickHandler2);

      	function clickHandler2(e) {
      		var sel = chart2.getSelection();

      		if (sel.length > 0)
			{
	      		if (sel[0].row == 0) //op_mejora
				{
					var title = '<b>Oportunidades de mejora</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Recomendaciones</th><th>Actualizado</th><th>Plan de acción</th><th>Fecha final plan</th><th>Estado plan</th><th>Responsable plan</th></thead>';

						@foreach ($issues_om as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.10.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide3',

							html: true 
						});
				}
	      		else if (sel[0].row == 1) //deficiencia
				{
					var title = '<b>Deficiencias</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Recomendaciones</th><th>Actualizado</th><th>Plan de acción</th><th>Fecha final plan</th><th>Estado plan</th><th>Responsable plan</th></thead>';

						@foreach ($issues_def as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.11.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide3',

							html: true 
						});
				}
				else if (sel[0].row == 2) //debilidad
				{
					var title = '<b>Debilidades significativas</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Recomendaciones</th><th>Actualizado</th><th>Plan de acción</th><th>Fecha final plan</th><th>Estado plan</th><th>Responsable plan</th></thead>';

						@foreach ($issues_deb as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
									text += '<td>No se ha agregado plan</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.12.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide3',

							html: true 
						});
				}
			}


      		//console.log(sel);
		}
		
      }
    @else
    	$('#alternativo2').html('<b>No existen hallazgos en el sistema</b>');
    @endif


    @if ($cont_open > 0 || $cont_warning > 0 || $cont_danger > 0 || $cont_closed > 0)
    //aqui otro gráfico
    	google.charts.setOnLoadCallback(chart3);
      function chart3() {
        var data = google.visualization.arrayToDataTable([
          ['Estado', 'Cantidad'],
          ['Abierto',     {{ $cont_open }}],
          ['Cerca de fecha final',     {{ $cont_warning }}],
          ['Fecha final terminada y aun abierto',     {{ $cont_danger }}],
          ['Cerrado',     {{ $cont_closed }}],
        ]);

        var options = {
          title: 'Estado de planes de acción',
          is3D: false,
          colors: ['#D7DF01', '#FF8000', '#FF0000','#74DF00']
        };

        var chart3 = new google.visualization.PieChart(document.getElementById('piechart3'));
        chart3.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico3').value = chart3.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart3, 'select', clickHandler3);

      	function clickHandler3(e) {
      		var sel = chart3.getSelection();

     		if (sel.length > 0)
			{
	      		if (sel[0].row == 0) //planes abierto
				{
					var title = '<b>Planes de acci&oacute;n abierto</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Hallazgo</th><th>Recomendaciones</th><th>Plan de acci&oacute;n</th><th>Estado</th><th>Fecha actualizado</th><th>Fecha final</th><th>Responsable</th></thead>';

						@foreach ($action_plans_open as $plan)
								text += '<tr><td>{{$plan["issue"]}}</td>';
								text += '<td>{{$plan["recommendations"]}}</td>';
								text += '<td>{{$plan["description"]}}</td>';
								text += '<td>{{$plan["status"]}}</td>';
								text += '<td>{{$plan["updated_at"]}}</td>';
								text += '<td>{{$plan["final_date"]}}</td>';
								text += '<td>{{$plan["stakeholder"]}}</td>';

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.13.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
	      		else if (sel[0].row == 1) //planes warning
				{
					var title = '<b>Planes de acci&oacute;n cercanos a cerrar</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Hallazgo</th><th>Recomendaciones</th><th>Plan de acci&oacute;n</th><th>Estado</th><th>Fecha actualizado</th><th>Fecha final</th><th>Responsable</th></thead>';

						@foreach ($action_plans_warning as $plan)
								text += '<tr><td>{{$plan["issue"]}}</td>';
								text += '<td>{{$plan["recommendations"]}}</td>';
								text += '<td>{{$plan["description"]}}</td>';
								text += '<td>{{$plan["status"]}}</td>';
								text += '<td>{{$plan["updated_at"]}}</td>';
								text += '<td>{{$plan["final_date"]}}</td>';
								text += '<td>{{$plan["stakeholder"]}}</td>';

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.14.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
				else if (sel[0].row == 2) //planes danger
				{
					var title = '<b>Planes de acci&oacute;n fecha terminada aun abiertos</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Hallazgo</th><th>Recomendaciones</th><th>Plan de acci&oacute;n</th><th>Estado</th><th>Fecha actualizado</th><th>Fecha final</th><th>Responsable</th></thead>';

						@foreach ($action_plans_danger as $plan)
								text += '<tr><td>{{$plan["issue"]}}</td>';
								text += '<td>{{$plan["recommendations"]}}</td>';
								text += '<td>{{$plan["description"]}}</td>';
								text += '<td>{{$plan["status"]}}</td>';
								text += '<td>{{$plan["updated_at"]}}</td>';
								text += '<td>{{$plan["final_date"]}}</td>';
								text += '<td>{{$plan["stakeholder"]}}</td>';

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.15.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
				else if (sel[0].row == 3) //cerrados
				{
					var title = '<b>Planes de acci&oacute;n cerrados</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Hallazgo</th><th>Recomendaciones</th><th>Plan de acci&oacute;n</th><th>Estado</th><th>Fecha actualizado</th><th>Fecha final</th><th>Responsable</th></thead>';

						@foreach ($action_plans_closed as $plan)
								text += '<tr><td>{{$plan["issue"]}}</td>';
								text += '<td>{{$plan["recommendations"]}}</td>';
								text += '<td>{{$plan["description"]}}</td>';
								text += '<td>{{$plan["status"]}}</td>';
								text += '<td>{{$plan["updated_at"]}}</td>';
								text += '<td>{{$plan["final_date"]}}</td>';
								text += '<td>{{$plan["stakeholder"]}}</td>';

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.16.{{ $org }}">Exportar</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
			}
      		//console.log(sel);
		}
      }
    @else
    	$('#alternativo3').html('<b>No existen planes de acci&oacute;n</b>');
    @endif

    @if ($cont_progress_percentage10 > 0 || $cont_progress_percentage20 > 0 || $cont_progress_percentage30 > 0 || $cont_progress_percentage40 > 0 || $cont_progress_percentage50 > 0 || $cont_progress_percentage60 > 0 || $cont_progress_percentage70 > 0 || $cont_progress_percentage80 > 0 || $cont_progress_percentage90 > 0 || $cont_progress_percentage100 > 0)

    	google.charts.setOnLoadCallback(chart4);
      	function chart4() {
        var data = google.visualization.arrayToDataTable([
          ['Porcentaje', 'Cantidad'],
          ['0 %',     {{ $cont_progress_percentage0 }}],
          ['10 %',     {{ $cont_progress_percentage10 }}],
          ['20 %',     {{ $cont_progress_percentage20 }}],
          ['30 %',     {{ $cont_progress_percentage30 }}],
          ['40 %',     {{ $cont_progress_percentage40 }}],
          ['50 %',     {{ $cont_progress_percentage50 }}],
          ['60 %',     {{ $cont_progress_percentage60 }}],
          ['70 %',     {{ $cont_progress_percentage70 }}],
          ['80 %',     {{ $cont_progress_percentage80 }}],
          ['90 %',     {{ $cont_progress_percentage90 }}],
          ['100 %',    {{ $cont_progress_percentage100 }}],
        ]);

        var options = {
          title: 'Porcentaje de avances de planes de acción',
          is3D: false,
          colors: ['#8A0808','#DF0000','#DF4E00','#DF6400', '#DFB600','#CCDF00','#BDDF00','#BDF025','#69F025','#25F062','#25F0AC']
        };

        var chart4 = new google.visualization.PieChart(document.getElementById('piechart4'));
        chart4.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico4').value = chart4.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart4, 'select', clickHandler4);

        function clickHandler4(e) {
      		var sel = chart4.getSelection();

      		if (sel.length > 0)
			{
				//hacemos ciclo para no repetir
				@for($i=0;$i<=10;$i++)
					if (sel[0].row == {{ $i }}) //0
					{
						@if ($i == 0)
							var title = '<b>Planes de acci&oacute;n porcentaje avance {{$i}}0%</b>';
						@else
							var title = '<b>Planes de acci&oacute;n porcentaje avance {{$i}}0%</b>';
						@endif

							var text ='<table class="table table-striped table-datatable"><thead><th>Plan de acci&oacute;n</th><th>Estado</th><th>Fecha final</th><th>Responsable</th><th>Comentarios de avance</th></thead>';

							@foreach ($action_plans_progress_percentage as $plan)
								@if ($plan['progress_percentage'] == $i*10)
									text += '<td>{{$plan["description"]}}</td>';
									text += '<td>{{$plan["status"]}}</td>';
									text += '<td>{{$plan["final_date"]}}</td>';
									text += '<td>{{$plan["stakeholder"]}}</td>';
									@if ($plan['progress_comments'] != NULL)
										text += '<td>{{$plan["progress_comments"]}}</td>';
									@else
										text += '<td>Sin comentarios</td>';
									@endif

									text += '</tr>';
								@endif
							@endforeach
							text += '</table>'
							text += '<a class="btn btn-success" href="genexcelgraficos.13.{{ $org }}">Exportar</a>'
							swal({   
								title: title,   
								text: text,
								customClass: 'swal-wide',

								html: true 
							});
					}
				@endfor
				
			}
			//console.log(sel);
      	}
      }
    @else
    	$('#alternativo4').html('<b>No existen planes de acci&oacute;n</b>');
    @endif
@endif  
      
</script>
@stop