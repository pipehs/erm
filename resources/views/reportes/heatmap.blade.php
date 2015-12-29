@extends('master')

@section('title', 'Mapa de calor')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="heatmap">Mapa de Calor</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Mapa de Calor</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver los riesgos asociados a alguna encuesta de evaluaci&oacute;n o a alguna organizaci&oacute;n en particular. </p>
      @if (!isset($riesgos))
            {!!Form::open(['route'=>'heatmap2','method'=>'POST','class'=>'form-horizontal'])!!}
				   	
            <div class="form-group">
                {!!Form::label('Seleccione tipo',null,['class'=>'col-sm-4 control-label'])!!}
                <div class="col-sm-3">
                  {!!Form::select('tipo',(['1'=>'Por organización','2'=>'Por encuesta de evaluación']), 
                       null, 
                       ['id' => 'tipo','placeholder'=>'- Seleccione -','required'=>'true'])!!}
                </div>
            </div>

            <div class="form-group" id="eval" style="display: none;">
                {!!Form::label('Seleccione encuesta de evaluación',null,['class'=>'col-sm-4 control-label'])!!}
                <div class="col-sm-3">
                  {!!Form::select('evaluation_id',$encuestas, 
                       null, 
                       ['id' => 'eval2','placeholder'=>'- Seleccione -'])!!}
                </div>
            </div>

            <div class="form-group" id="org" style="display: none;">
                {!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
                <div class="col-sm-3">
                  {!!Form::select('organization_id',$organizaciones, 
                       null, 
                       ['id' => 'org2','placeholder'=>'- Seleccione -'])!!}
                </div>
            </div>

              <div class="form-group">
                <center>
                {!!Form::submit('Seleccionar', ['class'=>'btn btn-primary'])!!}
                </center>
              </div>
				    {!!Form::close()!!}
<!--
                <div id="container">
                </div>
-->
      @else

        <div class="row">
           <p><b> Nombre:</b> {{ $nombre }}.</p>
           <p><b> Descripci&oacute;n:</b> {{ $descripcion }}.</p>
            <center>
            
              <div class="col-sm-1">
                  <div style="width: 5px; word-wrap: break-word; text-align: center">Impacto</div>
              </div>
              <div class="col-sm-6">
                  <table class="matrix" border="1">

                  <!-- damos por ahora los 5 niveles fijos de criticidad y probabilidad -->
                  @for ($i=0; $i<5; $i++)

                    <tr style="height: 20%; ">
                    @for ($j=1; $j<=5; $j++)
                        <td id="{{(5-$i)}}_{{($j)}}" style="width: 20%; text-align: center;"></td>
                    @endfor
                    </tr>
                    
                  @endfor

                </table>
                <br>
                <div style="letter-spacing:5px; text-align: center">Probabilidad</div>
                </center>
              
                
              <div class="col-sm-4">
                <div id="leyendas"> </div>
              </div>

            </div>
            <br>
            <hr>
            <br>
            
                {!! link_to_route('heatmap', $title = 'Volver', $parameters = NULL,
                 $attributes = ['class'=>'btn btn-success'])!!}
            <center>
      @endif
			   </div>
		</div>
	</div>
</div>

@stop
@section('scripts2')
<script>

  @if (isset($riesgos))
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

  @endif

    $( document ).tooltip();

    $( "#tipo" ).change(function() {
      
        if ($("#tipo").val() == 1)
        {
          $("#org").removeAttr("style").show();
          $("#org2").attr("required","required");
          $("#eval").removeAttr("style").hide();
          $("#eval2").removeAttr("required");
        }

        else if ($("#tipo").val() == 2)
        {
          $("#org").removeAttr("style").hide();
          $("#org2").removeAttr("required");
          $("#eval").removeAttr("style").show();
          $("#eval2").attr("required","required");
        }
  });


</script>


@stop
