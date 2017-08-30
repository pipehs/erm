@extends('master')

@section('title', 'Reporte de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="semaforo">Reporte de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Reporte de Riesgos</span>
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
      		<p>En esta secci&oacute;n podr&aacute; ver el reporte de Riesgos en formato de sem&aacute;foro, donde se puede observar el nivel de exposici&oacute;n ponderado por Riesgo general y tambi&eacute;n visualizar los riesgos espec&iacute;ficos.</p>

		@if(!isset($riesgos))
			{!!Form::open(['url'=>'reporte_riesgos2.0.0','method'=>'GET','class'=>'form-horizontal'])!!}
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
			<!-- Por ahora sólo será de procesos -->
			{!!Form::hidden('kind',0)!!}
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

@if (isset($riesgos))
<!-- Datos de los riesgos por categoría -->
		<!-- Para exportación en Word -->
			<div id="cuerpo" style="display: none;">
				<h1>Reporte de Auditorías</h1>
				<div id="grafico1"></div>
				<div id="grafico2"></div>
			</div>
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Datos de Riesgos de {{ $organization }}</span>
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
			
			<table class="table">
			<thead>
				<th>Categoría</th>
				<th bgcolor="#FF0000"></th>
				<th bgcolor="#FFFF00"></th>
				<th bgcolor="#00FF00"></th>
				<th>Total</th>
			</thead>
			<?php $verdes_total = 0; $amarillos_total = 0; $rojos_total = 0; ?>
			@foreach ($categories as $category)
			<?php $verdes = 0; $amarillos = 0; $rojos = 0; ?>
			<tr>
				@foreach ($riesgos as $riesgo)
					@if ($riesgo['exposicion'] != 0)
						@if ($riesgo['risk_category_id'] == $category['id'])
							@if ($riesgo['exposicion'] <= 3.75)
								<?php $verdes += 1; $verdes_total += 1; ?>
							@elseif ($riesgo['exposicion'] > 3.75 && $riesgo['exposicion'] < 8)
								<?php $amarillos += 1; $amarillos_total += 1; ?>
							@elseif ($riesgo['exposicion'] >= 8)
								<?php $rojos += 1; $rojos_total += 1; ?>
							@endif
						@endif
					@endif
				@endforeach
								
				<td>{{ $category['name'] }}</td>
				<td>{{ $rojos }}</td>
				<td>{{ $amarillos }}</td>
				<td>{{ $verdes }}</td>
				<td>{{ $verdes+$amarillos+$rojos }}</td>
			</tr>
			@endforeach
			</table>
		</div>
	</div>
</div>
<!-- FIN datos -->

<!-- Gráfico de riesgos -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Gráfico Riesgos {{ $organization }}</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar los distintos riesgos de la organizaci&oacute;n seg&uacute;n su nivel de exposici&oacute;n.</p>
			<p id="alternativo"></p>
			<div id="piechart" style="width: 100%; height: 300px;"></div>
			<br><br><br><br><br><br><br><br><br>
		</div>
	</div>
</div>
<!-- FIN Gráfico de riesgos -->
</div>
<!-- Datos de los riesgos por categoría -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Datos de Riesgos Consolidados</span>
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
			
			<table class="table">
			<thead>
				<th>Categoría</th>
				<th bgcolor="#FF0000"></th>
				<th bgcolor="#FFFF00"></th>
				<th bgcolor="#00FF00"></th>
				<th>Total</th>
			</thead>
			<?php $verdes_total_c = 0; $amarillos_total_c = 0; $rojos_total_c = 0; ?>
			@foreach ($categories as $category)
			<?php $verdes_c = 0; $amarillos_c = 0; $rojos_c = 0; ?>
			<tr>
				@foreach ($riesgos_consolidados as $riesgo)
					@if ($riesgo['exposicion'] != 0)
						@if ($riesgo['risk_category_id'] == $category['id'])
							@if ($riesgo['exposicion'] <= 3.75)
								<?php $verdes_c += 1; $verdes_total_c += 1; ?>
							@elseif ($riesgo['exposicion'] > 3.75 && $riesgo['exposicion'] < 8)
								<?php $amarillos_c += 1; $amarillos_total_c += 1; ?>
							@elseif ($riesgo['exposicion'] >= 8)
								<?php $rojos_c += 1; $rojos_total_c += 1; ?>
							@endif
						@endif
					@endif
				@endforeach
								
				<td>{{ $category['name'] }}</td>
				<td>{{ $rojos_c }}</td>
				<td>{{ $amarillos_c }}</td>
				<td>{{ $verdes_c }}</td>
				<td>{{ $verdes_c+$amarillos_c+$rojos_c }}</td>
			</tr>
			@endforeach
			</table>
		</div>
	</div>
</div>
<!-- FIN datos consolidados -->
<!-- Gráfico de riesgos -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Gráfico Riesgos Consolidados</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar los distintos riesgos para todas las organizaciones ingresadas en el sistema, seg&uacute;n su nivel de exposici&oacute;n.</p>
			<p id="alternativo2"></p>
			<div id="piechart2" style="width: 100%; height: 300px;"></div>
			<br><br><br><br><br><br><br><br><br>
		</div>
	</div>
</div>
<!-- FIN Gráfico de riesgos -->
@endif
@stop

@section('scripts2')
<!--<script type="text/javascript" src="assets/js/loader.js"></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

@if (isset($riesgos))
	@if ($verdes_total > 0 || $amarillos_total > 0 || $rojos_total > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Riesgos', 'Cantidad'],
          ['Mitigados',     {{ $verdes_total }}],
          ['Mitigados con reparo',     {{ $amarillos_total }}],
          ['No mitigados',     {{ $rojos_total }}]
        ]);

        var options = {
          title: 'Riesgos mitigados de organización',
          is3D: true,
          colors: ['#00FF00','#FFFF00','#FF0000']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));
        chart.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico1').value = chart.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart, 'select', clickHandler);

      	function clickHandler(e) {
      		var sel = chart.getSelection();

      		if (sel.length > 0)
			{
				if (sel[0].row == 0) //mitigados
				{
					var title = '<b>Riesgos Mitigados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable</th></thead>';

					@foreach ($riesgos as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if ($riesgo['exposicion'] <= 3.75)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>{{ $riesgo["responsable"]}}</td>';
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mitigados con reparo
				{
					var title = '<b>Riesgos Mitigados con reparo</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable</th></thead>';

					@foreach ($riesgos as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if($riesgo['exposicion'] > 3.75 && $riesgo['exposicion'] < 8)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>{{ $riesgo["responsable"]}}</td>';
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 2) //no mitigados
				{
					var title = '<b>Riesgos No mitigados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable</th></thead>';

					@foreach ($riesgos as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if($riesgo['exposicion'] >= 8)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>{{ $riesgo["responsable"]}}</td>';
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

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
		$('#alternativo').html('<b>No existen riesgos mitigados en {{ $organization }}</b>');
    @endif

    @if ($verdes_total_c > 0 || $amarillos_total_c > 0 || $rojos_total_c > 0)
    	google.charts.setOnLoadCallback(chart2);
      function chart2() {
        var data = google.visualization.arrayToDataTable([
          ['Riesgos', 'Cantidad'],
          ['Mitigados',     {{ $verdes_total_c }}],
          ['Mitigados con reparo',     {{ $amarillos_total_c }}],
          ['No mitigados',     {{ $rojos_total_c }}]
        ]);

        var options = {
          title: 'Riesgos mitigados de organización',
          is3D: true,
          colors: ['#00FF00','#FFFF00','#FF0000']
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
				if (sel[0].row == 0) //mitigados
				{
					var title = '<b>Riesgos Mitigados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable(s)</th></thead>';

					@foreach ($riesgos_consolidados as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if ($riesgo['exposicion'] <= 3.75)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>'
								@if (!empty($riesgo['responsable']))
								@foreach ($riesgo['responsable'] as $responsable)
									text += '<li>{{$responsable->name}} {{$responsable->surnames}} - {{$responsable->organization}}</li>';
								@endforeach
								@else
									text += 'No se ha definido'
								@endif
								text += '</td>'
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mitigados con reparo
				{
					var title = '<b>Riesgos Mitigados con reparo</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable(s)</th></thead>';

					@foreach ($riesgos_consolidados as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if($riesgo['exposicion'] > 3.75 && $riesgo['exposicion'] < 8)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>'
								@if (!empty($riesgo['responsable']))
								@foreach ($riesgo['responsable'] as $responsable)
									text += '<li>{{$responsable->name}} {{$responsable->surnames}} - {{$responsable->organization}}</li>';
								@endforeach
								@else
									text += 'No se ha definido'
								@endif
								text += '</td>'
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 2) //no mitigados
				{
					var title = '<b>Riesgos No mitigados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Subprocesos</th><th>Riesgo</th><th>Descripción</th><th>Categoría</th><th>Comentarios</th><th>Exposición</th><th>Responsable(s)</th></thead>';

					@foreach ($riesgos_consolidados as $riesgo)
						@if ($riesgo['exposicion'] != 0)
							@if($riesgo['exposicion'] >= 8)
								text += '<tr><td>'
								@foreach ($riesgo['subobj'] as $subobj)
									text += '<li>{{$subobj->name}}</li>'
								@endforeach
								text += '</td>'
								text += '<td>{{$riesgo["name"]}}</td>';
								text += '<td>{{$riesgo["description"]}}</td>';
								text += '<td>{{$riesgo["risk_category"]}}</td>';
								@if ($riesgo['comments'] == NULL || $riesgo['comments'] == '')
									text += '<td>No se han agregado</td>';
								@else
									text += '<td>{{$riesgo["comments"]}}</td>';
								@endif
								text += '<td>{{ $riesgo["exposicion"] }}';
								text += '<td>'
								@if (!empty($riesgo['responsable']))
								@foreach ($riesgo['responsable'] as $responsable)
									text += '<li>{{$responsable->name}} {{$responsable->surnames}} - {{$responsable->organization}}</li>';
								@endforeach
								@else
									text += 'No se ha definido'
								@endif
								text += '</td>'
								text += '</tr>';
							@endif
						@endif
					@endforeach

					text += '</table>'
					

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
    	$('#alternativo2').html('<b>Aun no se han ejecutado controles</b>');
    @endif
@endif
</script>
@stop