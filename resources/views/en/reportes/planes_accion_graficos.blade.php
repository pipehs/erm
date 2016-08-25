@extends('en.master')

@section('title', 'Graphic Reports')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="graficos_controles">Action Plan Graphics</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Action Plans</span>
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
			<p>On this section you will be able to see different charts that allow you to see all the information related to action plans entered on the system.</p>

		</div>
	</div>
</div>

<!-- Gráfico de planes accion de controles, auditorias u otros -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Action Plans kind</span>
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
			On this chart you will see the universe of action plans with their kind, whether if it are of controls assessment, audits or others.</p>
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
				<span>Issues Classification</span>
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
			On this chart you will see the different issues registered on the system with their correspondant information, and with their action plans (if their have).</p>
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
				<span>Action plan status</span>
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
			On this chart you will observe the status of each action plan (open, close, next to close, deadline passed and still open).</p>
			<p id="alternativo3"></p>
			<div id="piechart3" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de estado de planes de acción -->

@stop
@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

	@if ($cont_ctrl > 0 || $cont_audit > 0 || $others > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Action Plans', 'Amount'],
          ['Controls Assesment',     {{ $cont_ctrl }}],
          ['Audit Execution',     {{ $cont_audit }}],
          ['Others',     {{ $others }}]
        ]);

        var options = {
          title: 'Kind of action plan',
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
					var title = '<b>Actions plan created on control assessment</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Control</th><th>Issue</th><th>Recommendations</th><th>Plan</th><th>Status</th><th>Plan Deadline</th><th>Responsable</th></thead>';

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
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.8">Export</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide3',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //auditorías
				{
					var title = '<b>Action plans created on audit execution</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Audit Plan</th><th>Audit</th><th>Program</th><th>Test</th><th>Issue</th><th>Recommendations</th><th>Plan</th><th>Status</th><th>Plan Deadline</th><th>Responsable</th></thead>';

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
					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.9">Export</a>'
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
    	$('#alternativo').html('<b>There are no audit plans pending or on execution</b>');
    	//$('#alternativo2').html('<b>Aun no se han ejecutado controles</b>');
    @endif

    @if ($op_mejora > 0 || $deficiencia > 0 || $deb_significativa > 0)
    //aqui otro gráfico
    	google.charts.setOnLoadCallback(chart2);
      function chart2() {
        var data = google.visualization.arrayToDataTable([
          ['Classification', 'Amount'],
          ['Improvement Opportunity',     {{ $op_mejora }}],
          ['Deficience',     {{ $deficiencia }}],
          ['Significant Weakness',     {{ $deb_significativa }}]
        ]);

        var options = {
          title: 'Issues Classification',
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
					var title = '<b>Improvement Opportunities</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Recommendations</th><th>Updated</th><th>Action Plan</th><th>Plan Deadline</th><th>Status</th><th>Responsable</th></thead>';

						@foreach ($issues_om as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.10">Export</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide3',

							html: true 
						});
				}
	      		else if (sel[0].row == 1) //deficiencia
				{
					var title = '<b>Deficiences</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Recommendations</th><th>Updated</th><th>Action plan</th><th>Plan Deadline</th><th>Status</th><th>Responsable</th></thead>';

						@foreach ($issues_def as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.11">Export</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide3',

							html: true 
						});
				}
				else if (sel[0].row == 2) //debilidad
				{
					var title = '<b>Significant Weakness</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Name</th><th>Description</th><th>Recommendations</th><th>Updated</th><th>Action Plan</th><th>Plan deadline</th><th>Status</th><th>Responsable</th></thead>';

						@foreach ($issues_deb as $issue)
								text += '<tr><td>{{$issue["name"]}}</td>';
								text += '<td>{{$issue["description"]}}</td>';
								text += '<td>{{$issue["recommendations"]}}</td>';
								text += '<td>{{$issue["updated_at"]}}</td>';

								@if ($issue['action_plan'] == NULL)
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
									text += '<td>Plan has not been added</td>';
								@else
									text += '<td>{{$issue["action_plan"]["description"]}}</td>';
									text += '<td>{{$issue["action_plan"]["final_date"]}}</td>';
									text += '<td>{{$issue["action_plan"]["status"]}}</td>';
									text += '<td>{{$issue["action_plan"]["stakeholder"]}}</td>';
								@endif

								text += '</tr>';
						@endforeach
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.12">Export</a>'
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
    	$('#alternativo2').html('<b>No issues on the system</b>');
    @endif


    @if ($cont_open > 0 || $cont_warning > 0 || $cont_danger > 0 || $cont_closed > 0)
    //aqui otro gráfico
    	google.charts.setOnLoadCallback(chart3);
      function chart3() {
        var data = google.visualization.arrayToDataTable([
          ['Status', 'Amount'],
          ['Open',     {{ $cont_open }}],
          ['Close to deadline',     {{ $cont_warning }}],
          ['Deadline passed and still open',     {{ $cont_danger }}],
          ['Closed',     {{ $cont_closed }}],
        ]);

        var options = {
          title: 'Action plan status',
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
					var title = '<b>Action Plans open</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Issue</th><th>Recommendations</th><th>Action Plans</th><th>Status</th><th>Updated date</th><th>Plan Deadline</th><th>Responsable</th></thead>';

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
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.13">Export</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
	      		else if (sel[0].row == 1) //planes warning
				{
					var title = '<b>Action plans near to closed</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Issue</th><th>Recommendations</th><th>Action plan</th><th>Status</th><th>Updated date</th><th>Plan Deadline</th><th>Responsable</th></thead>';

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
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.14">Export</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
				else if (sel[0].row == 2) //planes danger
				{
					var title = '<b>Action Plans Deadline passed and still open</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Issue</th><th>Recommendations</th><th>Action Plan</th><th>Status</th><th>Updated date</th><th>Plan deadline</th><th>Responsable</th></thead>';

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
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.15">Export</a>'
						swal({   
							title: title,   
							text: text,
							customClass: 'swal-wide',

							html: true 
						});
				}
				else if (sel[0].row == 3) //op_mejora
				{
					var title = '<b>Action Plans Closed</b>';

						var text ='<table class="table table-striped table-datatable"><thead><th>Issue</th><th>Recommendations</th><th>Action Plan</th><th>Status</th><th>Updated date</th><th>Plan Deadline</th><th>Responsable</th></thead>';

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
						text += '</table>'
						text += '<a class="btn btn-success" href="genexcelgraficos.16">Export</a>'
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
    	$('#alternativo3').html('<b>No action plans have been created</b>');
    @endif
    
      
</script>
@stop