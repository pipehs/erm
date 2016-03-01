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
     <!-- Heatmap de última encuesta agregada -->
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Mapa de Calor para &uacute;ltima evaluaci&oacute;n generada</span>
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
	<div class="row">
    <p><b> Nombre Evaluaci&oacute;n:</b> {{ $nombre }}.</p>
           <p><b> Descripci&oacute;n:</b> {{ $descripcion }}.</p>
            <center>
            
              <div class="col-sm-1">
                  <div style="width: 5px; word-wrap: break-word; text-align: center">Impacto</div>
              </div>
              <div class="col-sm-6">
              <table style="text-align: center; font-weight: bold;">
              <tr>
                <td width="15%" bgcolor="#CCCCCC">
                  <table height="295px" width="100%" border="1">
                    <tr><td>5<br>Cr&iacute;tico</td></tr>
                    <tr><td>4<br>Alto</td></tr>
                    <tr><td>3<br>Moderado</td></tr>
                    <tr><td>2<br>Bajo</td></tr>
                    <tr><td>1<br>Menor</td></tr>
                  </table>
                </td>
                <td width="85%">
                  <table class="heatmap1" border="1">

                  <!-- damos por ahora los 5 niveles fijos de criticidad y probabilidad -->
                  @for ($i=0; $i<5; $i++)

                    <tr style="height: 20%; ">
                    @for ($j=1; $j<=5; $j++)
                        <td id="{{(5-$i)}}_{{($j)}}" style="width: 20%; text-align: center;"></td>
                    @endfor
                    </tr>
                    
                  @endfor

                </table>
                </td>
              </tr>
              <tr>
              <td width="15%"></td>
              <td width="85%" bgcolor="#CCCCCC">
                  <table height="50px" width="100%" border="1">
                      <td width="20%">1<br>Remoto</td>
                      <td width="20%">2<br>No probable</td>
                      <td width="20%">3<br>Probable</td>
                      <td width="20%">4<br>Altamente probable</td>
                      <td width="20%">5<br>Esperado</td>
                  </table>
              </td>
              </tr>
              </table>
                <br>
                <div style="letter-spacing:5px; text-align: center">Probabilidad</div>
                </center>
              
                
              <div class="col-sm-4">
                <div id="leyendas"> </div>
              </div>
     </div>
	</div>
	</div>
</div>
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

@section('scripts2')
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



      //---- HEATMAP ----//
      <?php $cont = 1; //contador de riesgos ?>
      //ciclo para rellenar tabla con riesgos
      @for($k=0; $k < count($riesgos); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($prom_criticidad[$k]) == (5-$i))
                      @if (intval($prom_proba[$k]) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}').append("<span class='circulo' title='{{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['subobj'] }}. Probabilidad: {{ number_format($prom_proba[$k],1) }} &nbsp; Impacto: {{ number_format($prom_criticidad[$k],1) }}'>{{ $cont }}</span>");

                         $('#leyendas').append("<p><small><span class='circulo-small'>{{ $cont }}</span> : {{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['subobj'] }}")
                       /*
                        $('#{{(5-$i)}}_{{(5-$j)}}').append("<img src='assets/img/circulo.png' height='20px' width='20px' title='{{ $riesgos[$k]['name'] }}. Probabilidad: {{ number_format($prom_proba[$k],1) }} &nbsp; Criticidad: {{ number_format($prom_criticidad[$k],1) }}'>");
                     
                        $('#{{(5-$i)}}_{{(5-$j)}}').append("<li><b>{{ $riesgos[$k]['name'] }}</b></li>");
                        $('#{{(5-$i)}}_{{(5-$j)}}').append("Probabilidad: {{ number_format($prom_proba[$k],'1') }}<br>");
                        $('#{{(5-$i)}}_{{(5-$j)}}').append("Criticidad: {{ number_format($prom_criticidad[$k],'1') }}");
                        */

                        <?php $cont += 1; ?>
                      @endif
                  @endif
              @endfor
          @endfor
      @endfor

    $( document ).tooltip(); 
    </script>
@stop

