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
                  {!!Form::label('Riesgo inherente o residual',null,['class'=>'col-sm-4 control-label'])!!}
                  <div class="col-sm-3">
                    {!!Form::select('kind2',(['0'=>'Riesgos inherentes','1'=>'Riesgo inherente v/s Riesgo residual']), 
                         null, 
                         ['id' => 'kind2','placeholder'=>'- Seleccione -','required'=>'true'])!!}
                  </div>
                </div>
            </div>

                <div class="row">
                  <div class="form-group">
                    {!!Form::label('Seleccione mes Y año (si desea ver heatmap de todo un año el mes debe quedar en blanco)',
                    null,['class'=>'col-sm-4 control-label'])!!}
                    <div class="col-sm-2">
                      {!!Form::number('ano',null,
                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
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
              <div class="row">
                <div class="col-sm-6">
                  <h4><b>&nbsp;&nbsp;Evaluación de Riesgo Inherente</b></h4>
                </div>
              @if ($kind2 == 1)
                <div class="col-sm-6">
                  <h4><b>Riesgo Residual (Exposición efectiva al riesgo)</b></h4>
                </div>
              @endif
              </div>
               <center>
            <div class="col-sm-5">
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

              </div>

        @if ($kind2)
               <!-- Heatmap riesgo controlado -->
            <?php $sev = ['25','20','16','15','12','10','9','8','6','5','4','3','2','1']; ?>
            <div class="col-sm-5">
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
      <?php $cont = 1; //contador de riesgos ?>
      //ciclo para rellenar tabla con riesgos INHERENTES
      @for($k=0; $k < count($riesgos); $k++)
          @for($i=0; $i < 5; $i++)
              @for ($j=0; $j < 5; $j++)
                  @if (intval($prom_criticidad_in[$k]) == (5-$i))
                      @if (intval($prom_proba_in[$k]) == (5-$j))
                         $('#{{(5-$i)}}_{{(5-$j)}}').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. Probabilidad: {{ number_format($prom_proba_in[$k],1) }} &nbsp; Impacto: {{ number_format($prom_criticidad_in[$k],1) }}'>{{ $cont }}</span>");

                         var leyendas = "<p><ul><li><small><span class='circulo-small'>{{ $cont }}</span> : <b>Riesgo:</b>";
                         leyendas += "{{ $riesgos[$k]['name'] }}</li>";

                         if ({{ $kind }} == 0)
                         {
                            leyendas += "<li><b>Subproceso afectado: </b></li> "

                            @foreach ($riesgos[$k]['subobj'] as $sub)
                            {
                              leyendas +="<li> {{ $sub->name }}</li>";
                            }
                            @endforeach
                         }
                         else
                         {
                            leyendas += "<li><b>Objetivo(s) afectado(s): </b>";

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
              @endfor
          @endfor
        <?php $cont += 1; ?>
    @endfor

                @if ($kind2 == 1)
                  <?php $cont = 1; //contador de riesgos ?>
                  //controlado
                  @for($k=0; $k < count($riesgos); $k++)
                    @foreach ($sev as $s)
                      @if (intval($prom_criticidad_ctrl[$k]) == $s)

                          @if ($prom_proba_ctrl[$k] <= 0.05 && $prom_proba_ctrl[$k] >= 0)
                              $('#{{($s)}}_1_ctrl').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($prom_proba_ctrl[$k],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$prom_proba_ctrl[$k])*100) }}% &nbsp; &nbsp; Severidad: {{ $prom_criticidad_ctrl[$k] }}'>{{ $cont }}</span>");
                          @elseif ($prom_proba_ctrl[$k] <= 0.15 && $prom_proba_ctrl[$k] > 0.05)
                              $('#{{($s)}}_2_ctrl').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($prom_proba_ctrl[$k],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$prom_proba_ctrl[$k])*100) }}% &nbsp; &nbsp; Severidad: {{ $prom_criticidad_ctrl[$k] }}'>{{ $cont }}</span>");
                          @elseif ($prom_proba_ctrl[$k] <= 0.5 && $prom_proba_ctrl[$k] > 0.15)
                              $('#{{($s)}}_3_ctrl').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($prom_proba_ctrl[$k],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$prom_proba_ctrl[$k])*100) }}% &nbsp; &nbsp; Severidad: {{ $prom_criticidad_ctrl[$k] }}'>{{ $cont }}</span>");
                          @elseif ($prom_proba_ctrl[$k] <= 1 && $prom_proba_ctrl[$k] > 0.5)
                              $('#{{($s)}}_4_ctrl').append("<span class='circulo' title='{{ $riesgos[$k]['description'] }}. &nbsp; &nbsp; Exposición efectiva al riesgo: {{ round($prom_proba_ctrl[$k],2) * $s }} &nbsp; &nbsp; Contribución acciones mitigantes: {{ intval((1-$prom_proba_ctrl[$k])*100) }}% &nbsp; &nbsp; Severidad: {{ $prom_criticidad_ctrl[$k] }}'>{{ $cont }}</span>");
                          @endif
        
                      @endif
                    @endforeach
                    <?php $cont += 1; ?>
                  @endfor
                @endif

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

$('#risk_category_id').change(function() {

  if ($('#risk_category_id').val() != '')
  {
    $('#risk_subcategory_id').empty();
    var options = '<option value="" selected disabled>- Seleccione -</option>'
    //obtenemos todas las causas
    $.get('get_subcategories.'+$('#risk_category_id').val(), function (result) {
      //parseamos datos obtenidos
      var datos = JSON.parse(result);

      $(datos).each( function() {
        options += '<option value='+this.id+'>'+this.name+'</option>';
      });

      $('#risk_subcategory_id').html(options)

    });
    
  }
  else
  {
    $('#risk_subcategory_id').html('')
  }
});

</script>


@stop
