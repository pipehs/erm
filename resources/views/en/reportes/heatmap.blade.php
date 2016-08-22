@extends('en.master')

@section('title', 'Heat Map')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="heatmap">Heat Map</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Heat Map</span>
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
      <p>On this section you will be able to see the heat map for the risk assessment of each organization on the system.</p>
      @if (!isset($riesgos))
            {!!Form::open(['route'=>'heatmap2','method'=>'GET','class'=>'form-horizontal'])!!}
				   	<!--
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
            </div>-->

            <div class="form-group">
                <div class="row">
                  {!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('organization_id',$organizaciones, 
                         null, 
                         ['id' => 'org','placeholder'=>'- Select -'])!!}
                  </div>
                </div>
            </div>

            <div class="form-group" id="tipo" style="display: none;">
                <div class="row">
                  {!!Form::label('Kind of heat map',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('kind',(['0'=>'Process Risk','1'=>'Bussiness Risks']), 
                         null, 
                         ['id' => 'kind','placeholder'=>'- Select -'])!!}
                  </div>
                </div>
            </div>

            <div class="form-group" id="tipo2" style="display: none;">
                <div class="row">
                  {!!Form::label('Inherent or controlled Risks',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('kind2',(['0'=>'Inherent Risks','1'=>'Inherent Risks v/s Controlled Risks']), 
                         null, 
                         ['id' => 'kind2','placeholder'=>'- Select -'])!!}
                  </div>
                </div>
            </div>

                <div class="row">
                  <div class="form-group">
                    {!!Form::label('Select month and year (if you want to see the heat map of a whole year, month must be blank)',null,['class'=>'col-sm-4 control-label'])!!}
                    <div class="col-sm-2">
                      {!!Form::number('ano',null,
                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
                       'placeholder'=>'YYYY','min'=>'2016','required'=>'true'])!!}
                    </div>
                    <div class="col-sm-1">
                    {!!Form::number('mes',null,
                      ['class'=>'form-control','input maxlength'=>'2',
                       'placeholder'=>'MM','min'=>'01','max'=>'12'])!!}
                    </div>
                  </div>
                </div>

            </div>

              <div class="form-group">
                <center>
                {!!Form::submit('Select', ['class'=>'btn btn-primary'])!!}
                </center>
              </div>
				    {!!Form::close()!!}

      @else

        <div class="row">
           <p><b> Name:</b> {{ $nombre }}.</p>
           <p><b> Description:</b> {{ $descripcion }}.</p>
            <center>
               <!-- Heatmap riesgo inherente -->
              <div class="row">
                <div class="col-sm-6">
                  <h4><b>Inherent Risk Assessment</b></h4>
                </div>
              @if ($kind2 == 1)
                <div class="col-sm-6">
                  <h4><b>Controlled Risk Assessment</b></h4>
                </div>
              @endif
              </div>

              <div style="width: 3%; float:left; padding-top: 10%;">
                  <div style="width: 1px; word-wrap: break-word; text-align: center"><b>Impact</b></div>
              </div>
              <div style="width: 47%; float: left;">
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
                      <tr>
                        <td width="15%">
                        </td>
                        <td width="85%" bgcolor="#000000" style="letter-spacing:5px; text-align: center; color:white;">Probability
                        </td>
                      </tr>
                  </table>
                  
              </div>

    @if ($kind2)
               <!-- Heatmap riesgo controlado -->

              <div style="width: 3%; float: left; padding-top: 10%;">
                  <div style="width: 1px; word-wrap: break-word; text-align: center;"><b>Impact</b></div>
              </div>
              <div style="width: 47%; float: left;">
                  <table style="text-align: center; font-weight: bold; float:left;">
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
                            <td id="{{(5-$i)}}_{{($j)}}_ctrl" style="width: 20%; text-align: center;"></td>
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
                  <tr>
                  <td width="15%">
                  </td>
                  <td width="85%" bgcolor="#000000" style="letter-spacing:5px; text-align: center; color:white;">Probability
                  </td>
                  </tr>
                  </table>
              </div>
            </div>

    @endif
             </center>

            <div class="row">
              <div class="col-sm-5">
                <div id="leyendas"> </div>
              </div>
            </div>   
            </div>
            <br>
            <hr>
            <br>
            
                {!! link_to_route('heatmap', $title = 'Return', $parameters = NULL,
                 $attributes = ['class'=>'btn btn-danger'])!!}
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
      //ciclo para rellenar tabla con riesgos INHERENTES
      @for($k=0; $k < count($riesgos); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($prom_criticidad_in[$k]) == (5-$i))
                      @if (intval($prom_proba_in[$k]) == (5-$j))
                        riesgo = "<span class='circulo'";
                        riesgo += "title='{{ $riesgos[$k]['description'] }}. Probability: {{ number_format($prom_proba_in[$k],1) }} &nbsp; Impact: {{ number_format($prom_criticidad_in[$k],1) }}'>{{ $cont }}</span>";

                         $('#{{(5-$i)}}_{{(5-$j)}}').append(riesgo);

                         var leyendas = "<p><ul><li><small><span class='circulo-small'>{{ $cont }}</span> : <b>Risk:</b>";
                         leyendas += "{{ $riesgos[$k]['name'] }}</li>";

                         if ({{ $kind }} == 0)
                         {
                            leyendas += "<li><b>Subprocess(es) involved: </b></li> "

                            @foreach ($riesgos[$k]['subobj'] as $sub)
                            {
                              leyendas +="<li> {{ $sub->name }}</li>";
                            }
                            @endforeach
                         }
                         else
                         {
                            leyendas += "<li><b>Objective(s) involved: </b>";

                            @foreach ($riesgos[$k]['subobj'] as $obj)
                            {
                              leyendas += "<li> {{ $obj->name }} </li>";
                            }
                            @endforeach
                         }

                         leyendas += "</ul>";
                         $('#leyendas').append(leyendas);
                      @endif
                  @endif

                @if ($kind2 == 1)
                  //controlado
                  @if (intval($prom_criticidad_ctrl[$k]) == (5-$i))
                      @if (intval($prom_proba_ctrl[$k]) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}_ctrl').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. Probability: {{ number_format($prom_proba_ctrl[$k],1) }} &nbsp; Impact: {{ number_format($prom_criticidad_ctrl[$k],1) }}'>{{ $cont }}</span>");
                      @endif
                  @endif
                @endif
              @endfor
          @endfor

          <?php $cont += 1; ?>
      @endfor
  @endif

  $(function() {
    $( document ).tooltip();
  })
  
/*
    $( "#tipo" ).change(function() {
      
        //Se seleccionó heatmap por organización
        if ($("#tipo").val() == 1)
        {
          $("#org").removeAttr("style").show();
          $("#org2").attr("required","required");
          $("#ano").attr("required","required");
          $("#eval").removeAttr("style").hide();
          $("#eval2").removeAttr("required");
        }

        //Se seleccionó heatmap por encuesta de evaluación
        else if ($("#tipo").val() == 2)
        {
          $("#org").removeAttr("style").hide();
          $("#org2").removeAttr("required");
          $("#ano").removeAttr("required");
          $("#eval").removeAttr("style").show();
          $("#eval2").attr("required","required");
        }
  });
 */

 $("#org").change(function() {
    $("#tipo").show(500);
    $("#tipo2").show(500);
 });



</script>


@stop
