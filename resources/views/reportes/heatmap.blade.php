@extends('master')

@section('title', 'Mapa de calor')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="heatmap">Mapa de Calor</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-12">
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
            {!!Form::open(['route'=>'heatmap2','method'=>'GET','class'=>'form-horizontal'])!!}

            <div class="form-group">
                <div class="row">
                  {!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('organization_id',$organizaciones, 
                         null, 
                         ['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
                  </div>
                </div>
            </div>

            <div class="form-group">
               <div class="row">
                 <label for="risk_category_id" class='col-sm-4 control-label'>Seleccione categor&iacute;a de Riesgo (opcional)</label>
                 <div class="col-sm-3">
                   {!!Form::select('risk_category_id',$categories, 
                        null, 
                       ['id' => 'risk_category_id','placeholder'=>'- Seleccione -'])!!}
                 </div>
              </div>
            </div>

            <div class="form-group">
               <div class="row">
                 <label for="risk_subcategory_id" class='col-sm-4 control-label'>Seleccione sub-categor&iacute;a de Riesgo o Riesgo General (opcional)</label>
                 <div class="col-sm-3">
                    <select id="risk_subcategory_id" name="risk_subcategory_id"></select>
                 </div>
              </div>
            </div>


            <div class="form-group">
               <div class="row">
                 <label for="sub_organizations" class='col-sm-4 control-label'>Active si desea ver tambi&eacute;n los riesgos de las organizaciones dependientes de la seleccionada</label>
                 <div class="col-sm-3">
                   <input type="checkbox" name="sub_organizations" id="sub_organizations" data-toggle="toggle" data-on="Organización + filiales" data-off="Solo organización" data-width="200" data-offstyle="primary" data-onstyle="success">
                 </div>
              </div>
            </div>

            <div class="form-group" id="tipo" style="display: none;">
                <div class="row">
                  {!!Form::label('Seleccione tipo de heatmap',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('kind',(['0'=>'Riesgos de proceso','1'=>'Riesgos de negocio']), 
                         null, 
                         ['id' => 'kind','placeholder'=>'- Seleccione -','required'=>'true'])!!}
                  </div>
                </div>
            </div>

            <div class="form-group" id="tipo2" style="display: none;">
                <div class="row">
                  <label for="kind2" class="col-sm-4 control-label">Seleccione si desea ver otro mapa además de residual</label>
                  <div class="col-sm-3">
                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="kind2_1">
                        <i class="fa fa-square-o"></i>% Contribución acciones mitigantes
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="kind2_2">
                        <i class="fa fa-square-o"></i>Evaluación de controles
                      </label>
                    </div>

                    <div class="checkbox">
                      <label>
                        <input type="checkbox" name="kind2_3">
                        <i class="fa fa-square-o"></i>Evaluación residual manual
                      </label>
                    </div>
                  </div>
                </div>
            </div>

                <div class="row">
                  <div class="form-group">
                    {!!Form::label('Seleccione mes Y año (si desea ver heatmap de todo un año el mes debe quedar en blanco)',
                    null,['class'=>'col-sm-4 control-label'])!!}
                    <div class="col-sm-1">
                      {!!Form::number('ano',null,
                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
                    </div>
                    <div class="col-sm-1">
                      {!!Form::number('mes',null,['class'=>'form-control','input maxlength'=>'2',
                       'placeholder'=>'MM','min'=>'01','max'=>'12'])!!}
                    </div>
                    <div class="col-sm-1">
                    {!!Form::number('dia',null,
                      ['class'=>'form-control','input maxlength'=>'2',
                       'placeholder'=>'DD','min'=>'01','max'=>'31'])!!}
                    </div>
                  </div>
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
           
               <!-- Heatmap riesgo inherente -->
        <center> <!--Centra títulos -->
          <div class="col-sm-5">
            <div class="row">
                <h4><b>&nbsp;&nbsp;Evaluación de Riesgo Inherente</b></h4>
            </div>
            <table style="text-align: center; font-weight: bold; float: left;">
              <tr><td bgcolor="#000000" style="padding:5px;">
                  <div  style="width: 12px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
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
                    @for ($i=0; $i < 5; $i++)

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
                          <tr>
                            <td width="20%" style="vertical-align:top;">1<br>Remoto</td>
                            <td width="20%" style="vertical-align:top;">2<br>No probable</td>
                            <td width="20%" style="vertical-align:top;">3<br>Probable</td>
                            <td width="20%" style="vertical-align:top;">4<br>Altamente probable</td>
                            <td width="20%" style="vertical-align:top;">5<br>Esperado</td>
                          </tr>
                        </table>
                    </td>
              </tr>
              <tr>
                    <td ></td>
                    <td ></td>
                    <td bgcolor="#000000" style="letter-spacing:5px; text-align: center; color:white;">Probabilidad</td>
              </tr>
            </table>
          </div>

        @if ($kind2_1)
               <!-- Heatmap riesgo controlado -->
            <?php $sev = ['25','20','16','15','12','10','9','8','6','5','4','3','2','1']; ?>
          <div class="col-sm-5">
            <div class="row">
              <h4><b>Riesgo Residual (Exposición efectiva al riesgo)</b></h4>
            </div>
            <table style="text-align: center; font-weight: bold; float: left; margin-left: 50px;">
                <tr><td bgcolor="#000000" style="padding:5px;">
                  <div  style="width: 12px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Severidad
                        </div></td>
                    <td  bgcolor="#CCCCCC">
                      <table height="400px" width="100%" border="1">
                        @foreach ($sev as $s)
                          <tr><td>{{ $s }}</td></tr>
                        @endforeach
                      </table>
                    </td>
                    <td >
                      <table class="heatmap2" border="1">

                      <!-- Damos niveles de severidad y exposición efectiva de Coca Cola 25 x 100 -->

                      @foreach ($sev as $s)

                        <tr>
                        @for ($j=1; $j<=4; $j++)
                            <td id="{{ $s }}_{{ $j }}_ctrl" style="width: 25%; text-align: center; height: 29px;"></td>
                        @endfor
                        </tr>
                        
                      @endforeach
                      </table>
                    </td>
                </tr>
                <tr>
                    <td ></td>
                    <td ></td>
                    <td  bgcolor="#CCCCCC">
                        <table height="50px" width="100%" border="1">
                            <td style="min-width: 95px; vertical-align:top;">95 - 100 % <br>&Oacute;ptima</td>
                            <td style="min-width: 95px; vertical-align:top;">85 - 94 %<br>Buena</td>
                            <td style="min-width: 95px; vertical-align:top;">50 - 84 %<br>Media</td>
                            <td style="min-width: 95px; vertical-align:top;">0 - 49 %<br>Deficiente</td>


                        </table>
                    </td>
                </tr>
                <tr>
                    <td ></td>
                    <td ></td>
                    <td bgcolor="#000000" style="letter-spacing:4px; text-align: center; color:white;">Contribuci&oacute;n acciones mitigantes
                    </td>
                </tr>
            </table>
          </div> 
      @endif
      @if ($kind2_2)
          <div class="col-sm-5">
            <div class="row">
              <h4><b>Riesgo Residual (Evaluación de controles)</b></h4>
            </div>
            <table style="text-align: center; font-weight: bold; float: left;">
                <tr><td bgcolor="#000000" style="padding:5px;">
                  <div  style="width: 12px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
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
                      @for ($i=0; $i < 5; $i++)

                        <tr style="height: 20%; ">
                        @for ($j=1; $j<=5; $j++)
                            <td id="{{(5-$i)}}_{{($j)}}_2" style="width: 20%; text-align: center;"></td>
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
                          <tr>
                            <td width="20%" style="vertical-align:top;">1<br>Remoto</td>
                            <td width="20%" style="vertical-align:top;">2<br>No probable</td>
                            <td width="20%" style="vertical-align:top;">3<br>Probable</td>
                            <td width="20%" style="vertical-align:top;">4<br>Altamente probable</td>
                            <td width="20%" style="vertical-align:top;">5<br>Esperado</td>
                          </tr>
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
          </div>
      @endif
      @if ($kind2_3)
        <div class="col-sm-5">
          <div class="row">
              <h4><b>Riesgo Residual (Evaluación residual manual)</b></h4>
          </div>
            <table style="text-align: center; font-weight: bold; float: left;">
                <tr><td bgcolor="#000000" style="padding:5px;">
                  <div  style="width: 12px; background-color:#000000; word-wrap: break-word; text-align: center; color:white;">Impacto
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
                      @for ($i=0; $i < 5; $i++)

                        <tr style="height: 20%; ">
                        @for ($j=1; $j<=5; $j++)
                            <td id="{{(5-$i)}}_{{($j)}}_3" style="width: 20%; text-align: center;"></td>
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
                          <tr>
                            <td width="20%" style="vertical-align:top;">1<br>Remoto</td>
                            <td width="20%" style="vertical-align:top;">2<br>No probable</td>
                            <td width="20%" style="vertical-align:top;">3<br>Probable</td>
                            <td width="20%" style="vertical-align:top;">4<br>Altamente probable</td>
                            <td width="20%" style="vertical-align:top;">5<br>Esperado</td>
                          </tr>
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
          </div>
      @endif
             </center>
      @if (!$kind2_1 && !$kind2_2 && !$kind2_3)
            <div class="row">
              <div class="col-sm-5">
                <div id="leyendas"> </div>
              </div>
            </div>
      @endif
      </div>   
      </div>
      <br>
        <hr>
      <br>
            
                {!! link_to_route('heatmap', $title = 'Volver', $parameters = NULL,
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
  <?php 
    $cont = 1; //contador de riesgos
    $verificador = array(); //Verificador para ver si ya existe una agrupación de Riesgos en el cuadrante
    $verificador2 = array(); //Verificador para agrupación de Riesgos residuales (% de contribución)
    $verificador3 = array(); //Verificador para agrupación de Riesgos residuales (Eval. Controles)
  ?>
      //ciclo para rellenar tabla con riesgos INHERENTES
    @foreach ($riesgos as $r)
        @if ($cont2[intval($r['impact_in'])][intval($r['proba_in'])] > 5)
          @if (!isset($verificador[intval($r['impact_in'])][intval($r['proba_in'])]))
            $('#{{$r["impact_in"]}}_{{$r["proba_in"]}}').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos({{intval($r['impact_in'])}},{{intval($r['proba_in'])}})'>N{{$cont2[intval($r['impact_in'])][intval($r['proba_in'])]}}</span>");
            <?php $verificador[intval($r['impact_in'])][intval($r['proba_in'])] = 1; ?>
          @endif
        @else
            $('#{{$r["impact_in"]}}_{{$r["proba_in"]}}').append("<span class='circulo' title='{{ $r['description'] }}. Probabilidad: {{ number_format($r['proba_in'],1) }} &nbsp; Impacto: {{ number_format($r['impact_in'],1) }}'>{{ $cont }}</span>");

              @if (!$kind2_1 && !$kind2_2 && !$kind2_3)
                  var leyendas = "<p><ul><li><small><span class='circulo-small'>{{ $cont }}</span> : <b>Riesgo:</b>";
                  leyendas += "{{ $r['name'] }}</li>";

                  if ({{ $kind }} == 0)
                  {
                    leyendas += "<li><b>Subproceso afectado: </b></li> "

                    @foreach ($r['subobj'] as $sub)
                    {
                        leyendas +="<li> {{ $sub->name }}</li>";
                    }
                    @endforeach
                  }
                  else
                  {
                    leyendas += "<li><b>Objetivo(s) afectado(s): </b>";

                    @foreach ($r['subobj'] as $obj)
                    {
                      leyendas += "<li> {{ $obj->name }} </li>";
                    }
                    @endforeach
                  }

                  leyendas += "</ul>";
                  $('#leyendas').append(leyendas);
              @endif
        @endif
          
        
    

        @if ($kind2_1)
            //% de contribución

            @foreach ($sev as $s)
                @if (intval($r['impact_ctrl1']) == $s)
                    @if ($r['proba_ctrl1'] <= 0.05 && $r['proba_ctrl1'] >= 0)
                        @if ($cont_ctrl[$s][1] > 4)
                            @if (!isset($verificador2[$s][1]))
                                $('#{{($s)}}_1_ctrl').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos2({{$s}},1)'>N{{$cont_ctrl[$s][1]}}</span>");
                                <?php $verificador2[$s][1] = 1; ?>
                            @endif
                        @else
                              $('#{{($s)}}_1_ctrl').append("<span class='circulo' title='{{ $r['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($r['proba_ctrl1'],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$r['proba_ctrl1'])*100) }}% &nbsp; &nbsp; Severidad: {{ $r['impact_ctrl1'] }}'>{{ $cont }}</span>");
                        @endif
                    @elseif ($r['proba_ctrl1'] <= 0.15 && $r['proba_ctrl1'] > 0.05)
                        @if ($cont_ctrl[$s][2] > 4)
                          @if (!isset($verificador2[$s][2]))
                                $('#{{($s)}}_2_ctrl').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos2({{$s}},2)'>N{{$cont_ctrl[$s][2]}}</span>");
                                <?php $verificador2[$s][2] = 1; ?>
                          @endif
                        @else
                              $('#{{($s)}}_2_ctrl').append("<span class='circulo' title='{{ $r['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($r['proba_ctrl1'],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$r['proba_ctrl1'])*100) }}% &nbsp; &nbsp; Severidad: {{ $r['impact_ctrl1'] }}'>{{ $cont }}</span>");
                        @endif
                    @elseif ($r['proba_ctrl1'] <= 0.5 && $r['proba_ctrl1'] > 0.15)
                        @if ($cont_ctrl[$s][3] > 4)
                          @if (!isset($verificador2[$s][3]))
                                $('#{{($s)}}_3_ctrl').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos2({{$s}},3)'>N{{$cont_ctrl[$s][3]}}</span>");
                                <?php $verificador2[$s][3] = 1; ?>
                          @endif
                        @else
                              $('#{{($s)}}_3_ctrl').append("<span class='circulo' title='{{ $r['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($r['proba_ctrl1'],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$r['proba_ctrl1'])*100) }}% &nbsp; &nbsp; Severidad: {{ $r['impact_ctrl1'] }}'>{{ $cont }}</span>");
                        @endif
                    @elseif ($r['proba_ctrl1'] <= 1 && $r['proba_ctrl1'] > 0.5)
                        @if ($cont_ctrl[$s][4] > 4)
                          @if (!isset($verificador2[$s][4]))
                                $('#{{($s)}}_4_ctrl').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos2({{$s}},4)'>N{{$cont_ctrl[$s][4]}}</span>");
                                <?php $verificador2[$s][4] = 1; ?>
                          @endif
                        @else
                              $('#{{($s)}}_4_ctrl').append("<span class='circulo' title='{{ $r['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($r['proba_ctrl1'],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$r['proba_ctrl1'])*100) }}% &nbsp; &nbsp; Severidad: {{ $r['impact_ctrl1'] }}'>{{ $cont }}</span>");
                        @endif
                    @endif
                @endif
            @endforeach
            
        @endif

        @if ($kind2_2) //Eval de controles
            @if (intval($r['impact_ctrl2']) > 0 && intval($r['proba_ctrl2']) > 0)
                @if ($cont3[intval($r['impact_ctrl2'])][intval($r['proba_ctrl2'])] > 5)
                    @if (!isset($verificador3[intval($r['impact_ctrl2'])][intval($r['impact_ctrl2'])]))
                      $('#{{$r["impact_ctrl2"]}}_{{$r["proba_ctrl2"]}}_2').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos({{intval($r['impact_ctrl2'])}},{{intval($r['proba_ctrl2'])}})'>N{{$cont2[intval($r['impact_ctrl2'])][intval($r['proba_ctrl2'])]}}</span>");
                      <?php $verificador3[intval($r['impact_ctrl2'])][intval($r['proba_ctrl2'])] = 1; ?>
                    @endif
                @else
                    $('#{{$r["impact_ctrl2"]}}_{{$r["proba_ctrl2"]}}_2').append("<span class='circulo' title='{{ $r['description'] }}. Probabilidad: {{ number_format($r['proba_ctrl2'],1) }} &nbsp; Impacto: {{ number_format($r['impact_ctrl2'],1) }}'>{{ $cont }}</span>");
                @endif
            @endif
        @endif

        @if ($kind2_3) //Eval residual manual
            @if (intval($r['impact_ctrl3']) > 0 && intval($r['proba_ctrl3']) > 0)
                @if ($cont4[intval($r['impact_ctrl3'])][intval($r['proba_ctrl3'])] > 5)
                    @if (!isset($verificador4[intval($r['impact_ctrl3'])][intval($r['impact_ctrl3'])]))
                      $('#{{$r["impact_ctrl3"]}}_{{$r["proba_ctrl3"]}}_3').append("<span class='circulo' title='Haga click para ver detalles' onclick='getRiesgos({{intval($r['impact_ctrl3'])}},{{intval($r['proba_ctrl3'])}})'>N{{$cont2[intval($r['impact_ctrl3'])][intval($r['proba_ctrl3'])]}}</span>");
                      <?php $verificador4[intval($r['impact_ctrl3'])][intval($r['proba_ctrl3'])] = 1; ?>
                    @endif
                @else
                    $('#{{$r["impact_ctrl3"]}}_{{$r["proba_ctrl3"]}}_3').append("<span class='circulo' title='{{ $r['description'] }}. Probabilidad: {{ number_format($r['proba_ctrl3'],1) }} &nbsp; Impacto: {{ number_format($r['impact_ctrl3'],1) }}'>{{ $cont }}</span>");
                @endif
            @endif
        @endif
      <?php $cont += 1; ?> 
    @endforeach
@endif

  $(function() {
    $( document ).tooltip();
  })
  

 $("#org").change(function() {
    if ($("#org").val() != '')
    {
        $("#tipo").show(500);
        $("#tipo2").show(500);
    }
    else
    {
        $("#tipo").hide(500);
        $("#tipo2").hide(500);
    }
    
 });

//ACT 10-01-18: Obtiene riesgos agrupados
function getRiesgos(imp,proba)
{
    var title = '<b>Riesgos en cuadrante '+proba+','+imp+'</b>';
    @if (isset($riesgos))
      if ({{ $kind }} == 0)
      {
        var text ='<table class="table table-striped table-datatable"><thead><th>Subproceso(s)</th><th>Riesgo</th><th>Descripci&oacute;n</th><th>Probabilidad</th><th>Impacto</th></thead>';
      }
      else
      {
        var text ='<table class="table table-striped table-datatable"><thead><th>Objetivo(s)</th><th>Riesgo</th><th>Descripci&oacute;n</th><th>Probabilidad</th><th>Impacto</th></thead>';
      }

      @foreach($riesgos as $r)
          if ({{intval($r["proba_in"])}} == proba && {{intval($r["impact_in"])}} == imp)
          {
            text += '<tr><td>'
            @foreach ($r['subobj'] as $subobj)
              text += '<li> {{ $subobj->name }}</li>';
            @endforeach
            text += '</td>'
            text += '<td>{{$r["name"]}}</td>';
            text += '<td>{{$r["description"]}}</td>';
            text += '<td>{{$r["proba_in"]}}</td>';
            text += '<td>{{$r["impact_in"]}}</td></tr>';
          }
      @endforeach
            text += '</table>'

            swal({   
              title: title,   
              text: text,
              customClass: 'swal-wide3',   
              html: true 
            });
    @endif
}

function getRiesgos2(s,cuadrante)
{
    var title = '<b>Riesgos en cuadrante '+s+','+cuadrante+'</b>';
    @if (isset($riesgos))
      if ({{ $kind }} == 0)
      {
        var text ='<table class="table table-striped table-datatable"><thead><th>Subproceso(s)</th><th>Riesgo</th><th>Descripci&oacute;n</th><th>Exposición efectiva al riesgo</th><th>Severidad</th></thead>';
      }
      else
      {
        var text ='<table class="table table-striped table-datatable"><thead><th>Objetivo(s)</th><th>Riesgo</th><th>Descripci&oacute;n</th><th>Exposición efectiva al riesgo</th><th>Severidad</th></thead>';
      }

      @if ($kind2_1 != NULL)
        @foreach($riesgos as $r)
            if ({{intval($r["impact_ctrl1"])}} == s)
            {
              if (cuadrante == 1)
              {
                @if ($r["proba_ctrl1"] <= 0.05 && $r["proba_ctrl1"] >= 0)
                  text += '<tr><td>'
                  @foreach ($r['subobj'] as $subobj)
                    text += '<li> {{ $subobj->name }}</li>';
                  @endforeach
                  text += '</td>'
                  text += '<td>{{$r["name"]}}</td>';
                  text += '<td>{{$r["description"]}}</td>';
                  text += '<td>{{round($r["proba_ctrl1"],2) * intval($r["impact_ctrl1"])}}</td>';
                  text += '<td>{{$r["impact_ctrl1"]}}</td></tr>';
                @endif  
              }

              else if (cuadrante == 2)
              {
                @if ($r["proba_ctrl1"] <= 0.15 && $r["proba_ctrl1"] > 0.05)
                  text += '<tr><td>'
                  @foreach ($r['subobj'] as $subobj)
                    text += '<li> {{ $subobj->name }}</li>';
                  @endforeach
                  text += '</td>'
                  text += '<td>{{$r["name"]}}</td>';
                  text += '<td>{{$r["description"]}}</td>';
                  text += '<td>{{round($r["proba_ctrl1"],2) * intval($r["impact_ctrl1"])}}</td>';
                  text += '<td>{{$r["impact_ctrl1"]}}</td></tr>';
                @endif  
              }

              else if (cuadrante == 3)
              {
                @if ($r["proba_ctrl1"] <= 0.5 && $r["proba_ctrl1"] > 0.15)
                  text += '<tr><td>'
                  @foreach ($r['subobj'] as $subobj)
                    text += '<li> {{ $subobj->name }}</li>';
                  @endforeach
                  text += '</td>'
                  text += '<td>{{$r["name"]}}</td>';
                  text += '<td>{{$r["description"]}}</td>';
                  text += '<td>{{round($r["proba_ctrl1"],2) * intval($r["impact_ctrl1"])}}</td>';
                  text += '<td>{{$r["impact_ctrl1"]}}</td></tr>';
                @endif  
              }

              else if (cuadrante == 4)
              {
                @if ($r["proba_ctrl1"] <= 1 && $r["proba_ctrl1"] > 0.5)
                  text += '<tr><td>'
                  @foreach ($r['subobj'] as $subobj)
                    text += '<li> {{ $subobj->name }}</li>';
                  @endforeach
                  text += '</td>'
                  text += '<td>{{$r["name"]}}</td>';
                  text += '<td>{{$r["description"]}}</td>';
                  text += '<td>{{round($r["proba_ctrl1"],2) * intval($r["impact_ctrl1"])}}</td>';
                  text += '<td>{{$r["impact_ctrl1"]}}</td></tr>';
                @endif  
              }
            }
        @endforeach
      @endif
            text += '</table>'

            swal({   
              title: title,   
              text: text,
              customClass: 'swal-wide3',   
              html: true 
            });
    @endif
}

</script>


@stop
