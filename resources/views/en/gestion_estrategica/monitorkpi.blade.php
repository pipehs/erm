@extends('en.master')

@section('title', 'Strategic Management - Monitor KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI Monitor')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Monitor KPI</span>
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
				<div class="alert alert-success alert-dismissible" role="alert">
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

			@if(Session::has('error'))
				<div class="alert alert-danger alert-dismissible" role="alert">
				{{ Session::get('error') }}
				</div>
			@endif

			On this section you will be able to monitor the different measurements for the KPI's of each organization on the system.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'monitor_kpi_2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'organization_id','required'=>'true','placeholder'=>'- Select -'])!!}
				</div>
			</div>

			<div class="form-group" id="kpi" style="display:none;">
				{!!Form::label('Select KPI',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					<select name="kpi_id" id="kpi_id" required="true">
						<!-- Aquí se agregarán los kpi de la org seleccionada a través de Jquery -->
					</select>
				</div>
			</div>
			<br>
			<div class="form-group">
						<center>
						{!!Form::submit('Select', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

@if (isset($kpi))
		@if (empty($measures))
			<center><b>Still haven't created a measurement for the KPI {{ $kpi->name }}.</b></center><br><br>
		@else
		<table width="100%">
		<tr><td width="40%" style="vertical-align:top;">
			<h5><b><u>{{ $org_selected }}</u></b></h5>
			<h4><b><u>{{ $kpi->name }}</u></b></h4>
			<ul style="text-align: left;">
				<li><b>Description: {{ $kpi->description }}</b></li>
				<li><b>Created date: {{ date_format($kpi->created_at,'d-m-Y') }}</b></li>
				@if ($kpi->calculation_method == NULL)
					<li><b>Calculation method: Not defined</b></li>
				@else
					<li><b>Calculation method: {{ $kpi['calculation_method'] }}</b></li>
				@endif
				
				@if ($kpi->periodicity == NULL)
					<li><b>Periodicity: Not defined</b></li>
				@else
					@if ($kpi->periodicity == 1)
						<li><b>Periodicity: Monthly</b></li>
					@elseif ($kpi->periodicity == 2)
						<li><b>Periodicity: Biannual</b></li>
					@elseif ($kpi->periodicity == 3)
						<li><b>Periodicity: Trimestral</b></li>
					@elseif ($kpi->periodicity == 4)
						<li><b>Periodicity: Annual</b></li>
					@endif
				@endif

				@if ($stake != NULL)
					<li><b>Responsable: {{ $stake->name.' '.$stake->surnames }}</b></li>
				@else
					<li><b>Responsable: Not defined</b></li>
				@endif
				
				@if ($kpi->initial_date == NULL)
					<li><b>Initial date: Not defined</b></li>
				@else
					<li><b>Initial date: {{ $kpi->initial_date }}</b></li>
				@endif

				@if ($kpi->final_date == NULL)
					<li><b>Final date: Not defined</b></li>
				@else
					<li><b>Final date: {{ $kpi->final_date }}</b></li>
				@endif

				@if ($kpi->initial_value == NULL)
					<li><b>Initial value: Not defined</b></li>
				@else
					@if ($kpi->initial_value == 1)
						<li><b>Initial value: Amount</b></li>
					@elseif ($kpi->initial_value == 2)
						<li><b>Initial value: Percentage</b></li>
					@elseif ($kpi->initial_value == 3)
						<li><b>Initial value: Quantity</b></li>
					@endif
				@endif

				@if ($kpi->goal == NULL)
					<li><b>KPI Goal: Not defined</b></li>
				@else
					<li><b>KPI Goal: {{ $kpi->goal }}</b></li>
				@endif 
			</ul>
		
		</td>
		<td style="vertical-align:top;">
		<center>
			<h4><b>Measurements</b></h4>
			<table class="table table-bordered table-striped table-hover table-heading table-datatable">
			<thead>
				<th>Period</th>
				<th>Value</th>
			</thead>
			@foreach($measures as $measure)
				<tr>
				<td width="50%">
				@if ($measure->month != NULL)
					{{ $meses[$measure->month-1] }} {{ $measure->year }}
				@elseif ($measure->trimester != NULL)
					{{ $trimestres[$measure->trimester-1] }} {{ $measure->year }}
				@elseif ($measure->semester != NULL)
					{{ $semestres[$measure->semester-1] }} {{ $measure->year }}
				@else
					{{ $measure->year }}
				@endif
				</td>
				<td width="50%">
					{{ $measure->value }}
				</td>
				</tr>
			@endforeach
			</table>
		</center></td></tr>
		</table>

		<div align="center">
				<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-circle"></i>
					<span>Historical metric graphic</span>
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
				 <div id="chart" style="width: 80%; height: 400px;"></div>
			</div>
		</div>
		</div>
	@endif
@endif

			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script type="text/javascript" src="assets/js/loader.js"></script>
<script>
//función que selecciona KPI de determinada organización
		$("#organization_id").change(function() {

			if ($("#organization_id").val() != "") //Si es que el se ha cambiado el valor a un valor válido
			{
				$("#kpi_id").empty();
				$("#kpi_id").change();
				//Añadimos la imagen de carga en el contenedor
				$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');

				//se obtienen stakeholders (menos el auditor jefe)
				$.get('getkpi.'+$("#organization_id").val(), function (result) {
						$("#cargando").html('<br>');
						//parseamos datos obtenidos
						var datos = JSON.parse(result);

						//seteamos datos en select de kpi
						$(datos).each( function() {
							$("#kpi_id").append('<option value="' + this.id + '">' + this.name +'</option>');
						});
				});

				$("#kpi").show(500);
			}
			else
			{
				$("#kpi_id").empty();
				$("#kpi_id").change();
			}
		});
 
@if(isset($measures))

    google.charts.load('current', {'packages':['bar']});
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Measurement period', 'Value'],
        @foreach ($measures as $measure)
        	@if ($measure->month != NULL)
				["{{ $meses[$measure->month-1] }} {{ $measure->year }}", {{ $measure->value }}],
			@elseif ($measure->trimester != NULL)
				["{{ $trimestres[$measure->trimester-1] }} {{ $measure->year }}", {{ $measure->value }}],
			@elseif ($measure->semester != NULL)
				["{{ $semestres[$measure->semester-1] }} {{ $measure->year }}", {{ $measure->value }}],
			@else
				["{{ $measure->year }}", {{ $measure->value }}],
			@endif
		@endforeach
        ]);

        var options = {
          title: 'Graphic Measurements',
          width: 700,
          legend: { position: 'none' },
          chart: { subtitle: '{{ $kpi->name }}' },
          series: {
            0: { axis: 'distance' }, // Bind series 0 to an axis named 'distance'.
            1: { axis: 'brightness' } // Bind series 1 to an axis named 'brightness'.
          },
          axes: {
            y: {
              distance: {label: 'Value'}, // Left y-axis.
              brightness: {side: 'right', label: 'Period'} // Right y-axis.
            }
          },
          bar: { groupWidth: "20%" }
        };

        var chart = new google.charts.Bar(document.getElementById('chart'));
        // Convert the Classic options to Material options.
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };
@endif
      
</script>
</script>
@stop