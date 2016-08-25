@extends('en.master')

@section('title', 'Graphic Reports')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="graficos_controles">Control Graphics</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Controls</span>
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
      		<p>On this section you will be able to view different charts that allow to you see on different ways the execution of the internal control on the organization.</p>

		</div>
	</div>
</div>

<!-- Gráfico de controles ejecutados v/s no ejecutados -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Executed v/s Non Executed Controls</span>
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
			<p align="justify">
			On this chart you will observe the amount of controls which have been executed through a control evaluation or an audit test. Click on the graphic if you want to observe the information of the controls executed or pending.</p>
			<p id="alternativo"></p>
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de controles ejecutados v/s no ejecutados -->

<!-- Gráfico de controles efectivos v/s inefectivos -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Effective v/s Ineffective Controls</span>
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
			<p align="justify">
			On this chart you will observe the results obtained on the controls that have been executed on the system. Click on the graphic if you want to observe the information of the effective or ineffective controls.</p>
			<p id="alternativo2"></p>
			<div id="piechart2" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de controles efectivos v/s inefectivos -->

      		

				

@stop
@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

	@if ($cont_ejec > 0 || $cont_no_ejec > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      google.charts.setOnLoadCallback(chart2);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Controls', 'Amount'],
          ['Executed',     {{ $cont_ejec }}],
          ['Pending',     {{ $cont_no_ejec }}]
        ]);

        var options = {
          title: 'Executed v/s Pending',
          is3D: false,
          colors: ['#74DF00','#FF0000']
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
					var title = '<b>Controls Executed</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Updated</th></thead>';

					@foreach ($controls as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.1">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mostramos controles no ejecutados
				{
					var title = '<b>Controls Pending</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Updated</th></thead>';

					@foreach ($no_ejecutados as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.2">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}


			}
      		//console.log(sel);
		}
      }

      function chart2() {
        var data = google.visualization.arrayToDataTable([
          ['Result', 'Amount'],
          ['Effective',     {{ $efectivos }}],
          ['Ineffective',     {{ $inefectivos }}]
        ]);

        var options = {
          title: 'Effective v/s Ineffective',
          is3D: false,
          colors: ['#74DF00','#FF0000']
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart2.draw(data, options);

        //agregamos evento de click
        google.visualization.events.addListener(chart2, 'select', clickHandler2);

      	function clickHandler2(e) {
      		var sel = chart2.getSelection();

      		if (sel.length > 0)
			{
				//alert(sel[0].row);
				if (sel[0].row == 0) //mostramos controles efectivos
				{
					var title = '<b>Effective Controls</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Updated</th></thead>';

					@foreach ($controls as $control)
						@if ($control['results'] == 2)
							text += '<tr><td>{{$control["name"]}}</td>';
							text += '<td>{{$control["description"]}}</td>';
							text += '<td>{{$control["updated_at"]}}</td></tr>';
						@endif
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.3">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mostramos controles no ejecutados
				{
					var title = '<b>Ineffective Controls</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Updated</th></thead>';

					@foreach ($controls as $control)
						@if ($control['results'] == 1)
							text += '<tr><td>{{$control["name"]}}</td>';
							text += '<td>{{$control["description"]}}</td>';
							text += '<td>{{$control["updated_at"]}}</td></tr>';
						@endif
					@endforeach
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.4">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}


			}
      		//console.log(sel);
		}
      }
    @else
    	$('#alternativo').html('<b>Still have not implemented controls</b>');
    	$('#alternativo2').html('<b>Still have not implemented controls</b>');
    @endif
      
</script>
@stop