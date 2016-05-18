@extends('master')

@section('title', 'Reporte de Gráficos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="graficos_controles">Gr&aacute;ficos Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Controles</span>
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
      		<p>En esta secci&oacute;n podr&aacute; ver distintos gr&aacute;ficos que permitan observar de mejor manera la ejecuci&oacute;n del control interno en la empresa.</p>

		</div>
	</div>
</div>

<!-- Gráfico de controles ejecutados v/s no ejecutados -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Controles ejecutados v/s No ejecutados</span>
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
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de controles ejecutados v/s no ejecutados -->

      		

				

@stop
@section('scripts2')
<script type="text/javascript" src="assets/js/loader.js"></script>
<script>
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Controles', 'Cantidad'],
          ['Ejecutados',     {{ $cont_ejec }}],
          ['Pendientes',     {{ $cont_no_ejec }}]
        ]);

        var options = {
          title: 'Ejecutados v/s Pendientes',
          is3D: true,
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
				if (sel[0].row == 0) //mostramos controles ejecutados
				{
					var title = '<b>Controles Ejecutados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($controls as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach
					swal({   
						title: title,   
						text: text,   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mostramos controles no ejecutados
				{
					var title = '<b>Controles Pendientes</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($no_ejecutados as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach
					swal({   
						title: title,   
						text: text,   
						html: true 
					});
				}


			}
      		//console.log(sel);
		}
      }

      
</script>
@stop