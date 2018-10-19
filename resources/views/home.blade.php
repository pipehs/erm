@extends('master')

@section('title', 'Home')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
		</ol>
	</div>
</div>
  @if ($m)
    {!! $m !!}
  @else
    <h3><b>Bienvenido al sistema B-GRC</b></h3>
  @endif

  @if ($d)
    <p>{!! $d !!}</p>
  @endif

    <br>

    @if(Session::has('message'))
      <div class="alert alert-success alert-dismissible" role="alert">
      {{ Session::get('message') }}
      </div>
    @endif

    @if(Session::has('error'))
      <div class="alert alert-danger alert-dismissible" role="alert">
      {{ Session::get('error') }}
      </div>
    @endif

<!-- Heatmap de categorías para evaluación de Riesgos -->
<div class="row">
	<div class="col-sm-6 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Mapa de Calor para evaluaciones de Riesgos</span>
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
      <center>
        <div class="col-sm-12">
            <table style="text-align: center; font-weight: bold; float: left; width: 100%;">
            <tr><td bgcolor="#000000" style="padding:5px;">
              <div  style="width: 11px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
                    </div></td>
                <td  bgcolor="#CCCCCC">
                  <table height="600px" width="100%" border="1" style="font-size:10px;">
                    <tr><td>5<br>Cr&iacute;tico</td></tr>
                    <tr><td>4<br>Alto</td></tr>
                    <tr><td>3<br>Moderado</td></tr>
                    <tr><td>2<br>Bajo</td></tr>
                    <tr><td>1<br>Menor</td></tr>
                  </table>
                </td>
                <td >
                  <table class="heatmap-inicio" border="1">

                  <!-- damos por ahora los 5 niveles fijos de criticidad y probabilidad -->
                  @for ($i=0; $i<5; $i++)

                    <tr style="height: 20%; ">
                    @for ($j=1; $j<=5; $j++)
                        <td id="{{(5-$i)}}_{{($j)}}_e" style="width: 16%; text-align: center;"></td>
                    @endfor
                    </tr>
                    
                  @endfor
                  </table>
                </td>
              </tr>
              <tr>
                    <td ></td>
                    <td ></td>
                    <td  bgcolor="#CCCCCC">
                        <table height="80px" width="100%" border="1" style="font-size:10px;">
                            <td width="20%" style="vertical-align:top;">1<br>Remoto</td>
                            <td width="20%" style="vertical-align:top;">2<br>No probable</td>
                            <td width="20%" style="vertical-align:top;">3<br>Probable</td>
                            <td width="20%" style="vertical-align:top;">4<br>Altamente probable</td>
                            <td width="20%" style="vertical-align:top;">5<br>Esperado</td>
                        </table>
                    </td>
              </tr>
                <tr>
                    <td ></td>
                    <td ></td>
                    <td bgcolor="#000000" style="letter-spacing:5px; text-align: center; color:white;">Probabilidad
                    </td>
                </tr>
              </table>
                </center>
              
              
              <div class="col-sm-5">
                <div id="leyendas"> </div>
              </div>
     </div>
	</div>
	</div>
</div>

<!-- Heatmap de categorías para evaluaciones manuales Riesgos -->
<div class="row">
  <div class="col-sm-6 col-m-6">
    <div class="box">
      <div class="box-header">
        <div class="box-name">
          <i class="fa fa-table"></i>
          <span>Mapa de Calor para evaluaciones manuales de Riesgos</span>
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
      <center>
        <div class="col-sm-12">
            <table style="text-align: center; font-weight: bold; float: left; width: 100%;">
            <tr><td bgcolor="#000000" style="padding:5px;">
              <div  style="width: 11px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
                    </div></td>
                <td  bgcolor="#CCCCCC">
                  <table height="600px" width="100%" border="1" style="font-size:10px;">
                    <tr><td>5<br>Cr&iacute;tico</td></tr>
                    <tr><td>4<br>Alto</td></tr>
                    <tr><td>3<br>Moderado</td></tr>
                    <tr><td>2<br>Bajo</td></tr>
                    <tr><td>1<br>Menor</td></tr>
                  </table>
                </td>
                <td >
                  <table class="heatmap-inicio" border="1">

                  <!-- damos por ahora los 5 niveles fijos de criticidad y probabilidad -->
                  @for ($i=0; $i<5; $i++)

                    <tr style="height: 20%; ">
                    @for ($j=1; $j<=5; $j++)
                        <td id="{{(5-$i)}}_{{($j)}}_m" style="width: 16%; text-align: center;"></td>
                    @endfor
                    </tr>
                    
                  @endfor
                  </table>
                </td>
              </tr>
              <tr>
                    <td ></td>
                    <td ></td>
                    <td  bgcolor="#CCCCCC">
                        <table height="80px" width="100%" border="1" style="font-size:10px;">
                            <td width="20%" style="vertical-align:top;">1<br>Remoto</td>
                            <td width="20%" style="vertical-align:top;">2<br>No probable</td>
                            <td width="20%" style="vertical-align:top;">3<br>Probable</td>
                            <td width="20%" style="vertical-align:top;">4<br>Altamente probable</td>
                            <td width="20%" style="vertical-align:top;">5<br>Esperado</td>
                        </table>
                    </td>
              </tr>
                <tr>
                    <td ></td>
                    <td ></td>
                    <td bgcolor="#000000" style="letter-spacing:5px; text-align: center; color:white;">Probabilidad
                    </td>
                </tr>
              </table>
                </center>
              
              
              <div class="col-sm-5">
                <div id="leyendas"> </div>
              </div>
     </div>
  </div>
  </div>
</div>


<!-- Riesgos por categoría -->
<!--
  <div class="col-sm-4 col-m-6">
    <div class="box">
      <div class="box-header">
        <div class="box-name">
          <i class="fa fa-table"></i>
          <span>Riesgos clasificados por categor&iacute;a</span>
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
        <p id="alternativo"></p>
         <div id="chart1" style="width: 100%; height: 250px;"></div>
      </div>
    </div>
  </div>
-->
<!-- FIN Riesgos por categoría -->

<!-- Riesgos críticos con o sin control -->
<!--
  <div class="col-sm-4 col-m-6">
    <div class="box">
      <div class="box-header">
        <div class="box-name">
          <i class="fa fa-table"></i>
          <span>Estado de Riesgos críticos</span>
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

        Hola
      </div>
    </div>
  </div>
-->
<!-- FIN Riesgos por categoría -->



</div> <!-- FIN row -->


@stop

@section('scripts2')
  <script type="text/javascript">


  @if (!empty($plans))
    @foreach ($plans as $p)
        swal('Atención!','El plan de acción descrito como "{{$p["description"]}}" se encuentra próximo a su fecha límite, o bien ésta ya pasó','warning');
    @endforeach
  @endif
  

  //---- HEATMAP por evaluaciones ----//
      <?php $cont = 1; //contador de Categorías de Riesgos ?>
      //ciclo para rellenar tabla con Categorías de Riesgos
      @for($k=0; $k < count($cats); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($cats[$k]['prom_eval_i']) == (5-$i))
                      @if (intval($cats[$k]['prom_eval_p']) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}_e').append("<span class='circulo' title='{{ $cats[$k]['name'] }}. Probabilidad: {{ number_format($cats[$k]['prom_eval_p'],1) }} &nbsp; Impacto: {{ number_format($cats[$k]['prom_eval_i'],1) }}'>{{ $cont }}</span>");

                        <?php $cont += 1; ?>
                      @endif
                  @endif
              @endfor
          @endfor
      @endfor

  //---- HEATMAP por evaluaciones manuales ----//
      <?php $cont = 1; //contador de Categorías de Riesgos ?>
      //ciclo para rellenar tabla con Categorías de Riesgos
      @for($k=0; $k < count($cats); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($cats[$k]['prom_eval_manual_i']) == (5-$i))
                      @if (intval($cats[$k]['prom_eval_manual_p']) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}_m').append("<span class='circulo' title='{{ $cats[$k]['name'] }}. Probabilidad: {{ number_format($cats[$k]['prom_eval_manual_p'],1) }} &nbsp; Impacto: {{ number_format($cats[$k]['prom_eval_manual_i'],1) }}'>{{ $cont }}</span>");

                        <?php $cont += 1; ?>
                      @endif
                  @endif
              @endfor
          @endfor
      @endfor

  $( document ).tooltip(); 
  </script>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script>
  @if (isset($categories) && !empty($categories))
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Riesgos', 'Cantidad',{ role: 'style' }],

          <?php $last = end($categories); $cont = 0; ?>
          @foreach ($categories as $cat)
            <?php $cont+=1; ?>
            @if ($cat == $last)
              ["{{ $cat['name']}}", {{ $cat['cont'] }},"{{ $cat['color'] }}"]
            @else
              ["{{$cat['name']}}", {{ $cat['cont'] }},"{{ $cat['color'] }}"],
            @endif
          @endforeach
        ]);

        var options = {
          title: 'Riesgos por categoría',
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart1'));
        chart.draw(data, options);

        //agregamos evento de click
        google.visualization.events.addListener(chart, 'select', clickHandler);

        function clickHandler(e) {
          var sel = chart.getSelection();
          <?php $i = 0; ?>
          
          if (sel.length > 0)
          {
            @while ($i < $cont)
                //alert(sel[0].row);
                if (sel[0].row == {{ $i }})
                {
                  var title = '<b>Categoría {{ $categories[$i]["name"]}}</b>';

                  var text ='<table class="table table-striped table-datatable"><thead><th>Subcategor&iacute;a</th><th>Riesgo</th><th>Descripci&oacute;n</th><th>Tipo</th><th>P&eacute;rdida esperada</th><th>Fecha expiraci&oacute;n</th></thead>';

                  @foreach ($categories[$i]['risks'] as $risk)
                    text += '<tr><td>{{$risk->risk_category}}</td>';
                    text += '<td>{{$risk->name}}</td>';
                    text += '<td>{{$risk->description}}</td>';

                    @if ($risk->type == 0)
                      text += '<td>Proceso</td>';
                    @else
                      text += '<td>Negocio</td>';
                    @endif

                    @if ($risk->expected_loss > 0 && $risk->expected_loss != NULL)
                        text += '<td>{{$risk->expected_loss}}</td>';
                    @else
                        text += '<td>No se ha definido</td>';
                    @endif
                    @if ($risk->expiration_date != NULL)
                      exp_date = '{{$risk->expiration_date}}'
                      exp_date = exp_date.split('-')
                      text += '<td>'+exp_date[2]+'-'+exp_date[1]+'-'+exp_date[0]+'</td></tr>';
                    @else
                      text += '<td>No se ha definido</td></tr>'
                    @endif
                  @endforeach
                  

                  text += '</table>'
                  text += '<a class="btn btn-success" href="genexcelgraficos.1.{{$org}}">Exportar</a>'

                  swal({   
                    title: title,   
                    text: text,
                    customClass: 'swal-wide2',   
                    html: true 
                  });
                }
                <?php $i += 1; ?>
            @endwhile
          
              console.log(sel);
          }
        }

        //guardamos imagen en form hidden para reporte
        //document.getElementById('grafico1').value = chart.getImageURI();
      }
  @else
    $('#alternativo').html('<b>Aun no se han creado riesgos, o éstos no se han categorizado</b>');
  @endif
  </script>
@stop

