@extends('master')

@section('title', 'Home')

@stop

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
		</ol>
	</div>
</div>
    <h4>Bienvenido al sistema ERM</h4>
    <br>
<div class="row">
    <div class="col-xs-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-circle"></i>
					<span>Controles del mes de octubre</span>
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

	<div class="col-xs-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-circle"></i>
					<span>Riesgos Identificados v/s Controles Ingresados</span>
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
				 <div id="columnchart_material" style="width: 500px; height: 300px;"></div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Controles', 'Cantidad'],
          ['Ejecutados',     8],
          ['Pendientes',      4],
          ['En Proceso',  2]
        ]);

        var options = {
          title: 'Controles',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }


    //Código JS para gráfico de barra

    google.load("visualization", "1.1", {packages:["bar"]});
    google.setOnLoadCallback(drawChart2);
      function drawChart2() {
        var data = google.visualization.arrayToDataTable([
          ['Mes', 'Controles', 'Riesgos'],
          ['Julio', 1, 4],
          ['Agosto', 2, 5],
          ['Septiembre', 4, 6],
          ['Octubre', 6, 6]
        ]);

        var options = {
          chart: {
            title: 'Riesgos v/s Controles',
            subtitle: 'Total de Riesgos identificados v/s Controles Ingresados los últimos 4 meses.',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('columnchart_material'));

        chart.draw(data, options);
      }
    </script>
@stop

