@extends('en.master')

@section('title', 'Graphic Reports')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="graficos_auditorias">Audit Graphics</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Audits</span>
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
			On this section you will be able to view different charts that allow you to see on a better way all the information related to the audits.</p>

		</div>
	</div>
</div>

<!-- Gráfico de planes de auditoría abiertos, en ejecución o cerrados -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Audit Plans status</span>
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
			<p align="justify">On this chart you will be able to see the universe of audit plans and the status of them for all the organizations on the system, either open, closed or on execution.</p>
			<br><br><br><br><br><br>
			<p id="alternativo"></p>
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de planes de auditoría abiertos, en ejecución o cerrados -->

<!-- Gráfico de pruebas de auditoría -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Gráfico Pruebas de auditoría</span>
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
			<p align="justify">On this chart you will be able to see the information of all the audit tests for the selected plan, with their correspondant execution states.</p>

			<div id="cargando"><br></div>

			{!!Form::open()!!}
	      			<div class="form-group">
						{!!Form::label('Audit Plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$planes_auditoria,null, 
							 	   ['id' => 'audit_plan_id','placeholder'=>'- Select -'])!!}
						</div>
					</div>
			{!!Form::close()!!}
			<br><br><br>
			<p id="alternativo2"><br></p>
			<div id="piechart2" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de pruebas de auditoría -->
				

@stop
@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

	@if ($planes_ejec > 0 || $planes_abiertos > 0 || $planes_cerrados > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Audit Plans', 'Amount'],
          ['On execution',     {{ $planes_ejec }}],
          ['Open',     {{ $planes_abiertos }}],
          ['Closed',     {{ $planes_cerrados }}]
        ]);

        var options = {
          title: 'Status of audit plans',
          is3D: false,
          colors: ['#FFFF00','#FF8000','#74DF00']
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
				if (sel[0].row == 1) //planes abiertos
				{
					var title = '<b>Open Plans</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Audits</th><th>Programs</th><th>Tests</th></thead>';

					@foreach ($audit_plans as $plan)
						@if ($plan['abiertas'] > 0 && $plan['ejecucion'] == 0)
							text += '<tr><td>{{$plan["name"]}}</td>';
							text += '<td>';
							@foreach ($plan['audits'] as $audit)
								text += '<li>{{$audit}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['programs'] as $program)
								text += '<li>{{$program}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['tests'] as $test)
								text += '<li>{{$test}}</li>';
							@endforeach
							text += '</td></tr>';
						@endif
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.5">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide',   
						html: true 
					});
				}
				else if (sel[0].row == 0) //planes en ejecución
				{
					var title = '<b>On execution Plans</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Audits</th><th>Programs</th><th>Tests</th></thead>';

					@foreach ($audit_plans as $plan)
						@if ($plan['ejecucion'] > 0)
							text += '<tr><td>{{$plan["name"]}}</td>';
							text += '<td>';
							@foreach ($plan['audits'] as $audit)
								text += '<li>{{$audit}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['programs'] as $program)
								text += '<li>{{$program}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['tests'] as $test)
								text += '<li>{{$test}}</li>';
							@endforeach
							text += '</td></tr>';
						@endif
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.6">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide',   
						html: true 
					});
				}
				else if (sel[0].row == 2) //planes cerrados
				{
					var title = '<b>Closed Plans</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Description</th><th>Audits</th><th>Programs</th><th>Tests</th></thead>';

					@foreach ($audit_plans as $plan)
						@if ($plan['status'] == 1)
							text += '<tr><td>{{$plan["name"]}}</td>';
							text += '<td>{{$plan["description"]}}</td>';
							text += '<td>';
							@foreach ($plan['audits'] as $audit)
								text += '<li>{{$audit}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['programs'] as $program)
								text += '<li>{{$program}}</li>';
							@endforeach
							text += '</td>';

							text += '<td>';
							@foreach ($plan['tests'] as $test)
								text += '<li>{{$test}}</li>';
							@endforeach
							text += '</td></tr>';
						@endif
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.7">Export</a>'

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
    	$('#alternativo').html('<b>There are not pending or in execution audit plans</b>');
    	//$('#alternativo2').html('<b>Aun no se han ejecutado controles</b>');
    @endif

//script para agregar gráfico de pruebas de auditoría
$("#audit_plan_id").change(function() {
	if ($("#audit_plan_id").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
	{
		//Añadimos la imagen de carga en el contenedor
		$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
		
		//se obtienen datos de pruebas de auditoría para el plan seleccionado
		$.get('auditorias.getpruebas.0,'+$("#audit_plan_id").val(), function (result) {
				$('#cargando').html('<br>')
				//alert(result)
				//parseamos datos obtenidos
				var datos = JSON.parse(result);

				if (datos.pruebas_abiertas > 0 || datos.pruebas_ejec > 0 || datos.pruebas_cerradas > 0)
				{
					$('#alternativo2').html('<br>')
				    google.charts.setOnLoadCallback(chart2);
				    function chart2() {
				        var data = google.visualization.arrayToDataTable([
				          ['Pruebas de auditoría', 'Cantidad'],
				          ['En ejecución',     datos.pruebas_ejec],
				          ['Abiertas',     datos.pruebas_abiertas],
				          ['Cerradas',     datos.pruebas_cerradas]
				        ]);

				        var options = {
				          title: 'Audit tests status',
				          is3D: false,
				          colors: ['#D7DF01','#FF8000','#74DF00'] 
				        };

				        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));
				        chart2.draw(data, options);

				        //agregamos evento de click
				        google.visualization.events.addListener(chart2, 'select', clickHandler);

				      	function clickHandler(e) {
				      		var sel = chart2.getSelection();

				      		if (sel.length > 0)
							{
					        	if (sel[0].row == 1) //pruebas abiertas
								{
									var title = '<b>Audit tests</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Audit</th><th>Program</th><th>Test</th><th>Description</th><th>Kind</th><th>Results</th><th>Hours-man</th><th>Responsable</th><th>Related objects</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 0)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Design test</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Operational effectiveness test</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Compliance test</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Sustantive test</td>'
											}
											else
											{
												text += '<td>Not defined</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Ineffective</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Effective</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>In process</td>'
											}
											
											text += '<td>'+test.hh+'</td>'
											text += '<td>'+test.stakeholder+'</td>'

											if (test.related_type == 1)
											{
												text += '<td>Risk: '+test.related+'</td>'
											}
											else if (test.related_type == 2)
											{
												text += '<td>Subprocess: '+test.related+'</td>'
											}
											else if (test.related_type == 3)
											{
												text += '<td>Control: '+test.related+'</td>'
											}
											
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.1,'+$("#audit_plan_id").val()+'">Export</a>'

									swal({   
										title: title,   
										text: text,
										customClass: 'swal-wide',   
										html: true 
									});
								}
								else if (sel[0].row == 0) //pruebas en ejecución
								{
									var title = '<b>Open tests</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Audit</th><th>Program</th><th>Test</th><th>Description</th><th>Kind</th><th>Results</th><th>Hours-man</th><th>Responsable</th><th>Related objects</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 1)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Design test</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Operational effectiveness test</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Compliance test</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Sustantive test</td>'
											}
											else
											{
												text += '<td>Not defined</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Ineffective</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Effective</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>In process</td>'
											}
											
											text += '<td>'+test.hh+'</td>'
											text += '<td>'+test.stakeholder+'</td>'

											if (test.related_type == 1)
											{
												text += '<td>Risk: '+test.related+'</td>'
											}
											else if (test.related_type == 2)
											{
												text += '<td>Subprocess: '+test.related+'</td>'
											}
											else if (test.related_type == 3)
											{
												text += '<td>Control: '+test.related+'</td>'
											}
											
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.2,'+$("#audit_plan_id").val()+'">Export</a>'

									swal({   
										title: title,   
										text: text,
										customClass: 'swal-wide',   
										html: true 
									});
								}
								else if (sel[0].row == 2) //pruebas cerradas
								{
									var title = '<b>Pruebas abiertas</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Audit</th><th>Program</th><th>Test</th><th>Description</th><th>Kind</th><th>Results</th><th>Hours-man</th><th>Responsable</th><th>Related objects</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 2)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Design test</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Operational effectiveness test</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Compliance test</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Sustantive test</td>'
											}
											else
											{
												text += '<td>Not defined</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Ineffective</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Effective</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>In process</td>'
											}
											
											text += '<td>'+test.hh+'</td>'
											text += '<td>'+test.stakeholder+'</td>'

											if (test.related_type == 1)
											{
												text += '<td>Risk: '+test.related+'</td>'
											}
											else if (test.related_type == 2)
											{
												text += '<td>Subprocess: '+test.related+'</td>'
											}
											else if (test.related_type == 3)
											{
												text += '<td>Control: '+test.related+'</td>'
											}				
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.2,'+$("#audit_plan_id").val()+'">Export</a>'

									swal({   
										title: title,   
										text: text,
										customClass: 'swal-wide',   
										html: true 
									});
								}
							}
						}
					}
				}
				else
				{
					$('#piechart2').html('');
					$('#alternativo2').html('<b>There is not audit tests for the audit plan '+datos.audit_plan+'</b>');
				}	
		});
	}
});
</script>
@stop