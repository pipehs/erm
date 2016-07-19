@extends('en.master')

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
    <h3><b>Welcome to B-GRC system</b>  </h3>
    <br>

    @if(Session::has('message'))
      <div class="alert alert-success alert-dismissible" role="alert">
      {{ Session::get('message') }}
      </div>
    @endif

     <!-- Heatmap de última encuesta agregada -->
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
          <span>Heatmap for the last generated risk evaluation</span>
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
    <p><b> Evaluation Name:</b> {{ $nombre }}.</p>
           <p><b> Description:</b> {{ $descripcion }}.</p>
            <center>
            
               <div style="width: 3%; float:left; padding-top: 10%;">
                  <div style="width: 1px; word-wrap: break-word; text-align: center">Impact</div>
              </div>
              <div class="col-sm-5">
              <table style="text-align: center; font-weight: bold; float: left;">
              <tr>
                <td width="15%" bgcolor="#CCCCCC">
                  <table height="395px" width="100%" border="1">
                    <tr><td>5<br>Very high</td></tr>
                    <tr><td>4<br>High</td></tr>
                    <tr><td>3<br>Medium</td></tr>
                    <tr><td>2<br>Low</td></tr>
                    <tr><td>1<br>Very low</td></tr>
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
                      <td width="20%" style="vertical-align:top;">1<br>Very low</td>
                      <td width="20%" style="vertical-align:top;">2<br>Low</td>
                      <td width="20%" style="vertical-align:top;">3<br>Medium</td>
                      <td width="20%" style="vertical-align:top;">4<br>High</td>
                      <td width="20%" style="vertical-align:top;">5<br>Very high</td>
                  </table>
              </td>
              </tr>
              </table>
                <br>
                <div style="letter-spacing:5px; text-align: center">Probability</div>
                </center>
              
              
              <div class="col-sm-5">
                <div id="leyendas"> </div>
              </div>
     </div>
	</div>
	</div>
</div>
<!--
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
  -->
</div>


@stop

@section('scripts2')
<script>
//---- HEATMAP ----//
      <?php $cont = 1; //contador de riesgos ?>
      //ciclo para rellenar tabla con riesgos
      @for($k=0; $k < count($riesgos); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($prom_criticidad[$k]) == (5-$i))
                      @if (intval($prom_proba[$k]) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}').append("<span class='circulo' title='{{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['subobj'] }}. Probability: {{ number_format($prom_proba[$k],1) }} &nbsp; Impact: {{ number_format($prom_criticidad[$k],1) }}'>{{ $cont }}</span>");

                         $('#leyendas').append("<p><small><span class='circulo-small'>{{ $cont }}</span> : {{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['subobj'] }}")

                        <?php $cont += 1; ?>
                      @endif
                  @endif
              @endfor
          @endfor
      @endfor

    $( document ).tooltip(); 
</script>
@stop

