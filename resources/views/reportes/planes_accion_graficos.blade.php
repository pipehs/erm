@extends('master')

@section('title', 'Reporte de Gráficos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
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

		</div>
	</div>
</div>

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
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
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
			<div id="piechart2" style="width: 500px; height: 300px;"></div>
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
			<div id="piechart3" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de estado de planes de acción -->

@stop
@section('scripts2')
<script type="text/javascript" src="assets/js/loader.js"></script>
<script>

	@if ($cont_ctrl > 0 || $cont_audit > 0 || $others > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Planes de acción', 'Cantidad'],
          ['Evaluación de controles',     {{ $cont_ctrl }}],
          ['Ejecución de auditoría',     {{ $cont_audit }}],
          ['Otros',     {{ $others }}]
        ]);

        var options = {
          title: 'Tipo de planes de acción',
          is3D: false,
          colors: ['#0431B4', '#5882FA']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);

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
							text += '<td>{{$plan["program"]}}</td>';
							text += '<td>{{$plan["test"]}}</td>';
							text += '<td>{{ $plan["issue"] }}';
							text += '<td>{{ $plan["recommendations"]}}</td>';
							text += '<td>{{ $plan["description"] }}</td>';
							text += '<td>{{ $plan["status"] }}</td>';
							text += '<td>{{ $plan["final_date"] }}</td>';
							text += '<td>{{ $plan["stakeholder"] }}</td>';
							text += '</tr>';
					@endforeach
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
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
				else if (sel[0].row == 3) //op_mejora
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
    
      
</script>
@stop