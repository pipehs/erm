@extends('master')

@section('title', 'Reportes')

@section('content')
<style>
.popper {
    border-radius: 100%;
    padding: 2px 6px;
    background: #4132bc;
    color: white !important;
    margin-left: 10px;
}

.popbox {
    display: none;
    position: absolute;
    z-index: 99999;
    width: 400px;
    padding: 10px;
    background: #4132bc;
    color: white;
    border: 1px solid #4D4F53;
    border-radius:3px;
    margin: 0px;
    -webkit-box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
    box-shadow: 0px 0px 5px 0px rgba(164, 164, 164, 1);
}

.popbox p{
	margin:0;
}

.popbox h2
{
    background-color: #070664;
    font-weight: bold;
    color:  #E3E5DD;
    font-size: 14px;
    display: block;
    width: 100%;
    margin: -10px 0px 8px -10px;
    padding: 5px 10px;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="denuncias.registro">Reportes</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-lg-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Reportes</span>
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
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			<div class="row-fluid">
	<div id="dashboard_links" class="col-xs-12 col-sm-2 pull-right">
		<ul class="nav nav-pills nav-stacked">
			<li class="active"><a href="#" class="tab-link" id="overview">Casos por mes</a></li>
			<li><a href="#" class="tab-link" id="clients">Tipos de casos</a></li>
			<li><a href="#" class="tab-link" id="graph">Clasificación de denuncias</a></li>
			<li><a href="#" class="tab-link" id="servers">Estado de denuncias</a></li>
		</ul>
	</div>
	<div id="dashboard_tabs" class="col-lg-12 col-lg-10">
		<!--Start Dashboard Tab 1-->
		<div id="dashboard-overview" class="row" style="visibility: visible; position: relative;">
			<div id="ow-marketplace" class="col-sm-12 col-md-10">
				<!--
				<div id="ow-setting">
					<a href="#"><i class="fa fa-folder-open"></i></a>
					<a href="#"><i class="fa fa-credit-card"></i></a>
					<a href="#"><i class="fa fa-ticket"></i></a>
					<a href="#"><i class="fa fa-bookmark-o"></i></a>
					<a href="#"><i class="fa fa-globe"></i></a>
				</div>
			-->
				<h4 class="page-header">Casos por mes</h4>

				<div class="col-sm-12 col-m-12">
				        <p id="alternativo"></p>
				         <div id="chart1" style="width: 100%; height: 400px;"></div>
			  	</div> 
				
			</div>
			<!--
			<div class="col-sm-12 col-md-10">
				<div id="ow-activity" class="row">
					
					<div class="col-xs-2 col-sm-1 col-md-2">
						<div class="v-txt">ACTIVITY</div>
					</div>-
					
				</div>
			</div>-->
		</div>
		<!--End Dashboard Tab 1-->
		<!--Start Dashboard Tab 2-->
		<div id="dashboard-clients" class="row" style="visibility: hidden; position: absolute;">
			
			<div class="col-sm-12 col-m-12">
				<p id="alternativo"></p>
				<div id="chart2" style="width: 100%; height: 400px;"></div>
			</div> 
		</div>
		<!--End Dashboard Tab 2-->
		<!--Start Dashboard Tab 3-->
		<div id="dashboard-graph" class="row" style="width:100%; visibility: hidden; position: absolute;" >

		</div>
		<!--End Dashboard Tab 3-->
		<!--Start Dashboard Tab 4-->
		<div id="dashboard-servers" class="row" style="visibility: hidden; position: absolute;">
			
		</div>
		<!--End Dashboard Tab 4-->
	</div>
	<div class="clearfix"></div>
</div>
		</div>
	</div>
</div>

<div id="pop1" class="popbox">
	<h2>Denuncia</h2>
	<p>Una denuncia consiste en una acusación anónima o no, de alguna situación o circunstancia que usted considere haya infringido las normas de conducta o las leyes del país. Por ejemplo: Acoso por parte de un compañero de trabajo.</p><br>
	<h2>Reclamo</h2>
	<p>Un reclamo implica alguna situación o hecho que no sea de su agrado, aunque no constituye necesariamente una falta a las normas y leyes. Por ejemplo, compra de bebida con poca cantidad de gas o sin gas.</p><br>
	<h2>Consulta</h2>
	<p>Corresponde a cualquier duda o comentario que quiera realizar a través del sistema. Por ejemplo, quisiera saber cuál es el procedimiento para ser un pequeño comerciante de productos CCU.</p><br>
</div>
@stop

@section('scripts2')
<script>
// Array for random data for Sparkline
var sparkline_arr_1 = SparklineTestData();
var sparkline_arr_2 = SparklineTestData();
var sparkline_arr_3 = SparklineTestData();
$(document).ready(function() {
	// Make all JS-activity for dashboard
	DashboardTabChecker();
	// Load Knob plugin and run callback for draw Knob charts for dashboard(tab-servers)
	LoadKnobScripts(DrawKnobDashboard);
	// Load Sparkline plugin and run callback for draw Sparkline charts for dashboard(top of dashboard + plot in tables)
	LoadSparkLineScript(DrawSparklineDashboard);
	// Load Morris plugin and run callback for draw Morris charts for dashboard
	LoadMorrisScripts(MorrisDashboard);
	// Make beauty hover in table
	$("#ticker-table").beautyHover();
});
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load("visualization", "1", {packages:["corechart"]});
google.charts.setOnLoadCallback(chart1);
function chart1() 
{
	var data = google.visualization.arrayToDataTable([
	  ['Mes', 'Cantidad'],
	  ["Enero", 1],
	  ["Febrero", 0],
	  ["Marzo", 3]
	]);

        var options = {
          title: 'Casos 2018',
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart1'));
        chart.draw(data, options);

        //agregamos evento de click
        google.visualization.events.addListener(chart, 'select', clickHandler);

        function clickHandler(e) 
        {
			var sel = chart.getSelection();
      	}
}

google.charts.setOnLoadCallback(chart2);
function chart2() 
{
  var data = google.visualization.arrayToDataTable([
    ['Tipo de caso', 'Cantidad'],
    ['Consulta',     1],
    ['Reclamo',     1],
    ['Denuncia',     3]
  ]);

  var options = {
          title: 'Tipos de casos',
          is3D: false,
          colors: ['#D7DF01', '#FF8000', '#FF0000']
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('chart2'));
        chart2.draw(data, options);

        //agregamos evento de click
        google.visualization.events.addListener(chart2, 'select', clickHandler2);

      	function clickHandler2(e) 
      	{
      		var sel = chart2.getSelection();
      	}
}
</script>
@stop