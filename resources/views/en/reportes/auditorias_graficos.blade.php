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
			On this section you will be able to view different graphics that allow you to see on a better way all the information related to the audits.</p>

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
			<p align="justify">On this graphic you will be able to view the universe of audit plans and the status of them, either open, closed or on execution.</p>
			<p id="alternativo"></p>
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de planes de auditoría abiertos, en ejecución o cerrados -->



      		

				

@stop
@section('scripts2')
<script type="text/javascript" src="assets/js/loader.js"></script>
<script>

	@if ($planes_ejec > 0 || $planes_abiertos > 0 || $planes_cerrados > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      google.charts.setOnLoadCallback(chart2);
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
      
</script>
@stop