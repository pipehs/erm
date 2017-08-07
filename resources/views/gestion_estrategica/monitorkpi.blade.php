@extends('master')

@section('title', 'Gesti&oacute;n Estrat&eacute;gica - Monitor KPI')

@section('content')

{!!Html::style('assets/css/mapas.css')!!}

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','Monitor KPI')!!}</li>
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

			En esta secci&oacute;n podr&aacute; monitorear las distintas mediciones para los KPI de cada organizaci&oacute;n ingresada en el sistema.<br><br>
			<div id="cargando"><br></div>

			{!!Form::open(['route'=>'monitor_kpi_2','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'organization_id','required'=>'true','placeholder'=>'- Seleccione -'])!!}
				</div>
			</div>

			<div class="form-group" id="kpi" style="display:none;">
				{!!Form::label('Seleccione KPI',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					<select name="kpi_id" id="kpi_id" required="true">
						<!-- Aquí se agregarán los kpi de la org seleccionada a través de Jquery -->
					</select>
				</div>
			</div>
			<br>
			<div class="form-group">
						<center>
						{!!Form::submit('Seleccionar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
			</div>
			{!!Form::close()!!}

@if (isset($kpi))
		@if (empty($measures))
			<center><b>Aun no se han creado ninguna medici&oacute;n para el KPI {{ $kpi->name }}.</b></center><br><br>
		@else
		<table width="100%">
		<tr><td width="40%" style="vertical-align:top;">
			<h5><b><u>{{ $org_selected }}</u></b></h5>
			<h4><b><u>{{ $kpi->name }}</u></b></h4>
			<ul style="text-align: left;">
				<li><b>Descripci&oacute;n: {{ $kpi->description }}</b></li>
				<li><b>Fecha creaci&oacute;n: {{ date_format($kpi->created_at,'d-m-Y') }}</b></li>
				@if ($kpi->calculation_method == NULL)
					<li><b>M&eacute;todo de c&aacute;lculo: No definido</b></li>
				@else
					<li><b>M&eacute;todo de c&aacute;lculo: {{ $kpi['calculation_method'] }}</b></li>
				@endif
				
				@if ($kpi->periodicity == NULL)
					<li><b>Periodicidad: No definida</b></li>
				@else
					@if ($kpi->periodicity == 1)
						<li><b>Periodicidad: Mensual</b></li>
					@elseif ($kpi->periodicity == 2)
						<li><b>Periodicidad: Semestral</b></li>
					@elseif ($kpi->periodicity == 3)
						<li><b>Periodicidad: Trimestral</b></li>
					@elseif ($kpi->periodicity == 4)
						<li><b>Periodicidad: Anual</b></li>
					@endif
				@endif

				@if ($stake != NULL)
					<li><b>Responsable: {{ $stake->name.' '.$stake->surnames }}</b></li>
				@else
					<li><b>Responsable: No definido</b></li>
				@endif
				
				@if ($kpi->initial_date == NULL)
					<li><b>Fecha inicial: No definida</b></li>
				@else
					<li><b>Fecha inicial: {{ $kpi->initial_date }}</b></li>
				@endif

				@if ($kpi->final_date == NULL)
					<li><b>Fecha final: No definida</b></li>
				@else
					<li><b>Fecha final: {{ $kpi->final_date }}</b></li>
				@endif

				@if ($kpi->initial_value == NULL)
					<li><b>Valor inicial: No definido</b></li>
				@else
					@if ($kpi->initial_value == 1)
						<li><b>Valor inicial: Monto</b></li>
					@elseif ($kpi->initial_value == 2)
						<li><b>Valor inicial: Porcentaje</b></li>
					@elseif ($kpi->initial_value == 3)
						<li><b>Valor inicial: Cantidad</b></li>
					@endif
				@endif

				@if ($kpi->goal == NULL)
					<li><b>Meta del KPI: No definida</b></li>
				@else
					<li><b>Meta del KPI: {{ $kpi->goal }}</b></li>
				@endif 
			</ul>
		@endif
	</td>
	<td style="vertical-align:top;">
	<center>
		<h4><b>Mediciones</b></h4>
		<table class="table table-bordered table-striped table-hover table-heading table-datatable">
		<thead>
			<th>Peri&oacute;do</th>
			<th>Medici&oacute;n</th>
		</thead>
		@foreach($measures as $measure)
			<tr>
			<td width="50%">
			@if ($measure->month != NULL)
				{{ $meses[$measure->month-1] }} de {{ $measure->year }}
			@elseif ($measure->trimester != NULL)
				{{ $trimestres[$measure->trimester-1] }} de {{ $measure->year }}
			@elseif ($measure->semester != NULL)
				{{ $semestres[$measure->semester-1] }} de {{ $measure->year }}
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
				<span>Gr&aacute;fico de mediciones hist&oacute;ricas</span>
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


			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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
          ['Periódo medición', 'Valor medición'],
        @foreach ($measures as $measure)
        	@if ($measure->month != NULL)
				["{{ $meses[$measure->month-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@elseif ($measure->trimester != NULL)
				["{{ $trimestres[$measure->trimester-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@elseif ($measure->semester != NULL)
				["{{ $semestres[$measure->semester-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@else
				["{{ $measure->year }}", {{ $measure->value }}],
			@endif
		@endforeach
        ]);

        var options = {
          title: 'Gráfico de mediciones',
          width: 700,
          legend: { position: 'none' },
          chart: { subtitle: '{{ $kpi->name }}' },
          series: {
            0: { axis: 'distance' }, // Bind series 0 to an axis named 'distance'.
            1: { axis: 'brightness' } // Bind series 1 to an axis named 'brightness'.
          },
          axes: {
            y: {
              distance: {label: 'Valor medición'}, // Left y-axis.
              brightness: {side: 'right', label: 'Periódo'} // Right y-axis.
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