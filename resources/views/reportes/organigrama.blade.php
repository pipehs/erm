@extends('master')

@section('title', 'Auditor&iacute;as')

@section('content')
<style>
#chart_div {
	overflow-x: scroll;
	overflow-y: scroll;     
	width: 1000px;
	height: 500px;
	background-color: #DAE4E4;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="organigrama">Organigrama</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Organigrama</span>
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
      		
      			<div id="chart_div">
      			</div>


      		</div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script>
		$(document).ready(function () {
			$.get('get_organizations', function (result) {
				data1 = new Array();

				var datos = JSON.parse(result);
				$(datos).each( function() {
					array = new Array();
					array = [this.name, this.org_father, this.description];
					data1.push(array);
				});
				google.charts.load('current', {packages:["orgchart"]});
				google.charts.setOnLoadCallback(drawChart);

				function drawChart() {
					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Nombre');
					data.addColumn('string', 'Organización padre');
					data.addColumn('string', 'Descripción');	
					// For each orgchart box, provide the name, manager, and tooltip to show.
					data.addRows(data1);

					var options = {
			           explorer: {axis: 'horizontal'},
			           allowHtml:true,
			           allowCollapse:true,
			           size:'medium',
			         };

        	// Create the chart.
        	var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        	// Draw the chart, setting the allowHtml option to true for the tooltips.
        	chart.draw(data, options);
      }
				
			});
		});
	</script>
@stop