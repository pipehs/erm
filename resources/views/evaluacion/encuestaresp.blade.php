@extends('master2')

@section('title', 'Encuesta respondida')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-lg-12">
		<ol class="breadcrumb">
			<li><a href="#">Evaluación</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">

			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Evaluación</span>
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

		     <!-- Heatmap de última encuesta agregada -->
<div class="row">
	<div class="col-sm-12 col-m-6">
	<div class="box">

	<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">
	<div class="row">
  @if (isset($riesgos))
          @if (isset($org))
            <p><b>Organizaci&oacute;n involucrada:</b> {{ $org }}</p>
          @endif
            <center>
      <div class="col-sm-5">
            <table style="text-align: center; font-weight: bold; float: left;">
            <tr><td bgcolor="#000000" style="padding:5px;">
              <div  style="width: 11px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
                    </div></td>
                <td  bgcolor="#CCCCCC">
                  <table height="395px" width="100%" border="1">
                    <tr><td>5<br>Cr&iacute;tico</td></tr>
                    <tr><td>4<br>Alto</td></tr>
                    <tr><td>3<br>Moderado</td></tr>
                    <tr><td>2<br>Bajo</td></tr>
                    <tr><td>1<br>Menor</td></tr>
                  </table>
                </td>
                <td >
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
                    <td ></td>
                    <td ></td>
                    <td  bgcolor="#CCCCCC">
                        <table height="50px" width="100%" border="1">
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
              
              
              <div class="col-sm-4">
                <div id="leyendas"> </div>
              </div>
     		</div>
  @endif
		</div>
	</div>
</div>


		@if (Auth::guest())

		@else
			<center>
				{!! link_to_route('home', $title = 'Ir al inicio', $parameters = NULL,
				 $attributes = ['class'=>'btn btn-success'])!!}
			<center>
        @endif

			</div>
		</div>
	</div>
</div>
@stop
@section('scripts2')
<script type="text/javascript">
  @if (isset($riesgos))
  //---- HEATMAP ----//
      <?php $cont = 1; //contador de riesgos ?>
      //ciclo para rellenar tabla con riesgos
      @for($k=0; $k < count($riesgos); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($prom_criticidad[$k]) == (5-$i))
                      @if (intval($prom_proba[$k]) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}').append("<span class='circulo' title='{{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['description'] }}. Probabilidad: {{ number_format($prom_proba[$k],1) }} &nbsp; Impacto: {{ number_format($prom_criticidad[$k],1) }}'>{{ $cont }}</span>");

                         $('#leyendas').append("<p><small><span class='circulo-small'>{{ $cont }}</span> : {{ $riesgos[$k]['name'] }} - {{ $riesgos[$k]['description'] }}")

                        <?php $cont += 1; ?>
                      @endif
                  @endif
              @endfor
          @endfor
      @endfor
  @endif
  $( document ).tooltip(); 
  </script>
@stop

