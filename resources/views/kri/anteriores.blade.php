@extends('master')

@section('title', 'Evaluaciones anteriores KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li><a href="kri.veranteriores.{{ $id }}">Evaluaciones anteriores KRI</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluaciones KRI</span>
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

				@if(Session::has('error'))
					<div class="alert alert-danger alert-dismissible" role="alert">
					{{ Session::get('error') }}
					</div>
				@endif

				<b> KRI: {{ $name }}</b>.</br>
				<b> Descripci&oacute;n: {{ $description }}</b></br></br>
				

				<!--<div id="evaluaciones" style="float:left; width:50%; padding:20px; background-color: yellow;">-->
				<table>
				<tr>
				<td style="width:40%; padding:20px;">
				@if ($evaluations == null)
					<h3>No existen evaluaciones previas para el KRI {{ $name }}</h3>
				@else
					
					<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
					<th>Valor evaluaci&oacute;n</th>
					<th>Resultado</th>
					<th>Fecha evaluaci&oacute;n</th>
					<th>Intervalo evaluaci&oacute;n</th>
					</thead>

					@foreach ($evaluations as $eval)
						<tr><td>{{ $eval['value'] }}</td>
				
							@if ($eval['eval'] == 0)
								<td valign="top"><ul class="semaforo verde"><li></li><li></li><li></li></ul></td>
							@elseif ($eval['eval'] == 1)
								<td><ul class="semaforo amarillo"><li></li><li></li><li></li></ul></td>
							@elseif ($eval['eval'] == 2)
								<td><ul class="semaforo rojo"><li></li><li></li><li></li></ul></td>
							@else
								<td>{{$eval['eval']}}</td>
							@endif
							
							<td>{{$eval['date']}}</td>
							<td>{{$eval['date_min']}} - {{$eval['date_max']}}</td></tr>					
							
					@endforeach

						</table>
						
				@endif
				</td>
				<td style="width:60%; padding:20px;">
				<!--<div id="grafico" style="float:left; width:50%; padding:20px; background-color: red;">-->
				
  				<div id="chart_div" style="width: 100%; height: 400px;"></div>
				</td>
				</tr>
				</table>
				<center>
					{!! link_to_route('kri', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
				</div>

			
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
google.charts.load('current', {packages: ['corechart', 'line']});
google.charts.setOnLoadCallback(drawBackgroundColor);

function drawBackgroundColor() {
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'X');
      data.addColumn('number', 'Evolución KRI');

      data.addRows([
      @if ($evaluations != null)
      	@foreach ($evaluations as $eval)
        	["{{ $eval['fecha_array'] }}", {{ $eval['value'] }}],
        @endforeach
      @endif
      ]);

      var options = {
        hAxis: {
          title: 'Fecha'
        },
        vAxis: {
          title: 'Valor'
        },
        colors: ['#097138']
      };

      var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }

</script>
@stop

