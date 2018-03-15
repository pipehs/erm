@extends('master')

@section('title', 'Reporte de Gráficos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="graficos_controles">Gr&aacute;ficos Auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Auditor&iacute;as</span>
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
      		<p>En esta secci&oacute;n podr&aacute; ver distintos gr&aacute;ficos que permitan observar de mejor manera toda la informaci&oacute;n relacionada a las auditor&iacute;as ingresadas en el sistema.</p>

      		@if (!isset($audit_plans))
      		{!!Form::open(['url'=>'graficos_auditorias2.0.0','method'=>'GET','class'=>'form-horizontal'])!!}
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

@if(isset($audit_plans))
<!-- Gráfico de planes de auditoría abiertos, en ejecución o cerrados -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Estado de planes de auditor&iacute;a</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar el universo de planes de auditor&iacute;a para la organizaci&oacute;n seleccionada y el estado de estos, ya sea abierto, cerrado o en ejecuci&oacute;n.</p>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar la informaci&oacute;n de todas las pruebas de auditor&iacute;a para el plan seleccionado, con sus correspondientes estados de ejecuci&oacute;n.</p>

			<div id="cargando"><br></div>

			{!!Form::open()!!}
	      			<div class="form-group">
						{!!Form::label('Plan de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$planes_auditoria,null, 
							 	   ['id' => 'audit_plan_id','placeholder'=>'- Seleccione -'])!!}
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
			<div id="cuerpo" style="display: none;">
				<h1>Reporte de Auditorías</h1>
				<div id="grafico1"></div>
				<div id="grafico2"></div>
			</div>
			<div id="boton_exportar" style="display: none;">
			{!!Form::open(['route'=>'export_audit_graphics','method'=>'POST','class'=>'form-horizontal'])!!}
				{!!Form::hidden('cuerpo','',['id' => 'cuerpo2'])!!}
				<div class="form-group">
						<center>
						{!!Form::submit('Exportar PDF', ['class'=>'btn btn-danger'])!!}
						</center>
				</div>
			{!!Form::close()!!}
			</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endif				

@stop
@section('scripts2')
<!--<script type="text/javascript" src="assets/js/loader.js"></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
@if(isset($audit_plans))
	@if ($planes_ejec > 0 || $planes_abiertos > 0 || $planes_cerrados > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Planes de auditoría', 'Cantidad'],
          ['En ejecución',     {{ $planes_ejec }}],
          ['Abiertos',     {{ $planes_abiertos }}],
          ['Cerrados',     {{ $planes_cerrados }}]
        ]);

        var options = {
          title: 'Estado de planes de auditoría',
          is3D: false,
          colors: ['#D7DF01','#FF8000','#74DF00'] 
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));

        //agregamos imagen para reporte
        var my_div = document.getElementById('grafico1');
		google.visualization.events.addListener(chart, 'ready', function () {
			my_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
			//console.log(my_div.innerHTML);
		});

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
					var title = '<b>Planes Abiertos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Descripción</th><th>Auditorías</th><th>Programas</th><th>Pruebas</th></thead>';

					@foreach ($audit_plans as $plan)
						@if ($plan['abiertas'] >= 0 && $plan['ejecucion'] == 0 && $plan['status'] == 0)
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
					text += '<a class="btn btn-success" href="genexcelgraficos.5.{{$org}}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide',   
						html: true 
					});
				}
				else if (sel[0].row == 0) //planes en ejecución
				{
					var title = '<b>Planes en ejecución</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Descripción</th><th>Auditorías</th><th>Programas</th><th>Pruebas</th></thead>';

					@foreach ($audit_plans as $plan)
						@if ($plan['ejecucion'] > 0 && $plan['status'] == 0)
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
					text += '<a class="btn btn-success" href="genexcelgraficos.6.{{$org}}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide',   
						html: true 
					});
				}

				else if (sel[0].row == 2) //planes cerrados
				{
					var title = '<b>Planes Cerrados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Plan</th><th>Descripción</th><th>Auditorías</th><th>Programas</th><th>Pruebas</th></thead>';

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
					text += '<a class="btn btn-success" href="genexcelgraficos.7.{{$org}}">Exportar</a>'

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
    	$('#alternativo').html('<b>No existen planes de auditor&iacute;as pendientes ni en ejecuci&oacute;on</b>');
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
				          title: 'Estado de pruebas de auditoría',
				          is3D: false,
				          colors: ['#D7DF01','#FF8000','#74DF00'] 
				        };

				        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));

				        //agregamos imagen para reporte
				        var my_div = document.getElementById('grafico2');
						google.visualization.events.addListener(chart2, 'ready', function () {
							my_div.innerHTML = '<img src="' + chart2.getImageURI() + '">';
							//console.log(my_div.innerHTML);
						});
				        chart2.draw(data, options);

				        //asignamos valores a variable hidden
				        document.getElementById('cuerpo2').value = document.getElementById('cuerpo').innerHTML;
				        //hacemos visible botón exportar
				        //$('#boton_exportar').show(500);

				        //agregamos evento de click
				        google.visualization.events.addListener(chart2, 'select', clickHandler);

				      	function clickHandler(e) {
				      		var sel = chart2.getSelection();

				      		if (sel.length > 0)
							{
					        	if (sel[0].row == 1) //pruebas abiertas
								{
									var title = '<b>Pruebas abiertas</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Auditoría</th><th>Programa</th><th>Prueba</th><th>Descripción</th><th>Tipo</th><th>Resultado</th><th>Horas-hombre planificadas</th><th>Horas-hombre reales</th><th>Responsable</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 0)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Prueba de diseño</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Prueba de efectividad operativa</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Prueba de cumplimiento</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Prueba sustantiva</td>'
											}
											else
											{
												text += '<td>Tipo no definido</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Inefectiva</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Efectiva</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>En proceso</td>'
											}
											
											if (!test.hh_plan)
											{
												text += '<td>No se ha agregado planificación de horas hombre</td>'
											}
											else
											{
												text += '<td>'+test.hh_plan+'</td>'
											}

											if (!test.hh_real)
											{
												text += '<td>No se ha agregado horas hombre de ejecución</td>'
											}
											else
											{
												text += '<td>'+test.hh_real+'</td>'
											}
											
											text += '<td>'+test.stakeholder+'</td>'
											
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.1,'+$("#audit_plan_id").val()+'.{{$org}}">Exportar</a>'

									swal({   
										title: title,   
										text: text,
										customClass: 'swal-wide',   
										html: true 
									});
								}
								else if (sel[0].row == 0) //pruebas en ejecución
								{
									var title = '<b>Pruebas abiertas</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Auditoría</th><th>Programa</th><th>Prueba</th><th>Descripción</th><th>Tipo</th><th>Resultado</th><th>Horas-hombre planificadas</th><th>Horas-hombre reales</th><th>Responsable</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 1)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Prueba de diseño</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Prueba de efectividad operativa</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Prueba de cumplimiento</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Prueba sustantiva</td>'
											}
											else
											{
												text += '<td>Tipo no definido</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Inefectiva</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Efectiva</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>En proceso</td>'
											}
											else
											{
												text += '<td>Resultado no especificado</td>'
											}
											
											if (!test.hh_plan)
											{
												text += '<td>No se ha agregado planificación de horas hombre</td>'
											}
											else
											{
												text += '<td>'+test.hh_plan+'</td>'
											}

											if (!test.hh_real)
											{
												text += '<td>No se ha agregado horas hombre de ejecución</td>'
											}
											else
											{
												text += '<td>'+test.hh_real+'</td>'
											}

											text += '<td>'+test.stakeholder+'</td>'
											
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.2,'+$("#audit_plan_id").val()+'.{{$org}}">Exportar</a>'

									swal({   
										title: title,   
										text: text,
										customClass: 'swal-wide',   
										html: true 
									});
								}
								else if (sel[0].row == 2) //pruebas cerradas
								{
									var title = '<b>Pruebas cerradas</b>';

									var text ='<table class="table table-striped table-datatable"><thead><th>Auditoría</th><th>Programa</th><th>Prueba</th><th>Descripción</th><th>Tipo</th><th>Resultado</th><th>Horas-hombre planificadas</th><th>Horas-hombre reales</th><th>Responsable</th></thead>';

									$(datos.audit_tests).each( function(i,test) {
										if (test.status == 2)
										{
											text += '<tr><td>'+test.audit_name+'</td>'
											text += '<td>'+test.audit_program_name+'</td>'
											text += '<td>'+test.name+'</td>'
											text += '<td>'+test.description+'</td>'

											if (test.type == 0)
											{
												text += '<td>Prueba de diseño</td>'
											}
											else if (test.type == 1)
											{
												text += '<td>Prueba de efectividad operativa</td>'
											}
											else if (test.type == 2)
											{
												text += '<td>Prueba de cumplimiento</td>'
											}
											else if (test.type == 3)
											{
												text += '<td>Prueba sustantiva</td>'
											}
											else
											{
												text += '<td>Tipo no definido</td>'
											}
											
											if (test.results == 0)
											{
												text += '<td>Inefectiva</td>'
											}
											else if (test.results == 1)
											{
												text += '<td>Efectiva</td>'
											}
											else if (test.results == 2)
											{
												text += '<td>En proceso</td>'
											}
											
											if (!test.hh_plan)
											{
												text += '<td>No se ha agregado planificación de horas hombre</td>'
											}
											else
											{
												text += '<td>'+test.hh_plan+'</td>'
											}

											if (!test.hh_real)
											{
												text += '<td>No se ha agregado horas hombre de ejecución</td>'
											}
											else
											{
												text += '<td>'+test.hh_real+'</td>'
											}

											text += '<td>'+test.stakeholder+'</td>'
											
										}
									});
									

									text += '</table>'
									text += '<a class="btn btn-success" href="genexcelgraficosdinamicos.2,'+$("#audit_plan_id").val()+'.{{$org}}">Exportar</a>'

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
					$('#alternativo2').html('<b>No existen pruebas de auditor&iacute;as para el plan '+datos.audit_plan+'</b>');
				}	
		});
	}
	else
	{
		//$('#boton_exportar').hide(500);
	}
});    

@endif
</script>
@stop