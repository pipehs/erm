@extends('master')

@section('title', 'Medir KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI')!!}</li>
			<li>{!!Html::link('kpi.evaluate.$kpi->id','Mediciones KPI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-6 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Medir KPI</span>
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

			Ingrese una nueva medici&oacute;n para el KPI.

			@if ($kpi->periodicity == NULL)
				<h4><b>El KPI seleccionado no tiene una periodicidad definida. Por favor ingresela para poder realizar la medici&oacute;n.</b></h4><br><br>
			@else

				<!--<h4><center>&Uacute;ltima Medici&oacute;n: <b>{{ $last_eval }}</b></center></h4>-->

				<hr>

				{!!Form::open(['route'=>'kpi.store_eval','method'=>'POST','class'=>'form-horizontal'])!!}
					<h4 style="color:blue;"><center><b>Meta del KPI: {{ $kpi->goal }} {{ $kpi->initial_value }}</b></center></h4>
					<h5><center><b>Fecha final del KPI: {{ $final_date }}</b></center></h5>
				@if ($kpi->periodicity == 1) {{-- Mensual --}}
					
					<h5><center><b>Periodicidad del KPI: Mensual</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Seleccione año y mes',null,['class'=>'col-sm-5 control-label'])!!}

	                    @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                    	<div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('mes_muestra',$eval->month,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'MM','min'=>'01','max'=>'12','disabled'])!!}
		                    </div>
		                    {!!Form::hidden('mes',$eval->month)!!}
		                    {!!Form::hidden('ano',$eval->year)!!}
	                    @else
		                    <div class="col-sm-2">
		                      {!!Form::number('ano',null,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('mes',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'MM','min'=>'01','max'=>'12','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 2) {{-- Semestral --}}
					<h5><center><b>Periodicidad del KPI: Semestral</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Seleccione año y semestre (1 o 2)',null,['class'=>'col-sm-5 control-label'])!!}
	                    @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
		                    <div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('semestre_muestra',$eval->semester,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Sem','min'=>'01','max'=>'02','disabled'])!!}
		                    </div>
		                    {!!Form::hidden('semestre',$eval->semester)!!}
		                    {!!Form::hidden('ano',$eval->year)!!}
		                @else
		                	<div class="col-sm-2">
		                      {!!Form::number('ano',null,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('semestre',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Sem','min'=>'01','max'=>'02','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 3) {{-- Trimestral --}}
					<h5><center><b>Periodicidad del KPI: Trimestral</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Seleccione año y trimestre (del 1 al 4)',null,['class'=>'col-sm-5 control-label'])!!}

	                     @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                     	<div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('trimestre_muestra',$eval->trimester,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Trim','min'=>'01','max'=>'04','disabled'])!!}
		                    </div>
		                    {!!Form::hidden('trimestre',$eval->trimester)!!}
		                    {!!Form::hidden('ano',$eval->year)!!}
		                @else
		                    <div class="col-sm-2">
		                      {!!Form::number('ano',null,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-2">
		                    {!!Form::number('trimestre',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Trim','min'=>'01','max'=>'04','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 4) {{-- Anual --}}
					<h5><center><b>Periodicidad del KPI: Anual</b></center></h5>
					<div class="form-group">
	                    {!!Form::label('Seleccione año',
	                    null,['class'=>'col-sm-5 control-label'])!!}
		                @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
		                    <div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016'])!!}
		                    </div>
		                    {!!Form::hidden('ano',$eval->year)!!}
		                @else
		                	<div class="col-sm-2">
		                      {!!Form::number('ano',null,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'AAAA','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                @endif
	                </div>
				@endif


				<div class="form-group">
	                {!!Form::label('Valor de medición',
	                null,['class'=>'col-sm-5 control-label'])!!}
	                <div class="col-sm-4">
	                @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                	{!!Form::number('value',$eval->value,
	                  	['id'=>'value','class'=>'form-control','requierd'=>'true'])!!}
	                @else
	                	{!!Form::number('value',null,
	                  	['id'=>'value','class'=>'form-control','required'=>'true'])!!}
	               	@endif
	                </div>
	            </div>

	            {!!Form::hidden('kpi_id',$kpi->id)!!}

	            @if (isset($org_id))
	            	{!!Form::hidden('org_id',$org_id)!!}
	            @elseif (isset($obj_id))
	            	{!!Form::hidden('obj_id',$obj_id)!!}
	            @endif
	            <div class="form-group">
					<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success'])!!}
					</center>
				</div>

				{!! Form::close() !!}
			@endif

			<center>
				@if (isset($org_id))
					{!! link_to_route('kpi2', $title = 'Volver', $parameters = ['organization_id'=>$org_id],$attributes = ['class'=>'btn btn-danger'])!!}
				@elseif (isset($obj_id))
					{!! link_to_route('objective_kpi', $title = 'Volver', $parameters = $obj_id,$attributes = ['class'=>'btn btn-danger'])!!}
				@endif
			<center>		
			</div>
		</div>
	</div>

	<div class="col-sm-6 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Mediciones anteriores</span>
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
			@if (isset($measurements) && $measurements != "0")

				<div id="table-wrapper">
  					<div id="table-scroll">
						<table class="table table-bordered table-striped table-hover table-heading table-datatable table-scroll ">
						<thead><th>Peri&oacute;do de evaluaci&oacute;n</th><th>Valor de la medici&oacute;n</th></thead>
						<tbody>
						@foreach ($measurements as $measure)
						<tr>
							<td>
							@if ($kpi->periodicity == 1)
								@if ($measure->month != NULL)
									<?php $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; ?>

									{{ $meses[$measure->month-1] }} de {{ $measure->year }}
								@endif
							@elseif($kpi->periodicity == 2)
								@if ($measure->semester != NULL)
									<?php $semestres = ['Primer semestre','Segundo semestre']; ?>

									{{ $semestres[$measure->semester-1] }} de {{ $measure->year }}
								@endif
							@elseif($kpi->periodicity == 3)
								@if ($measure->trimester != NULL)
									<?php $trimestres = ['Primer trimestre','Segundo trimestre','Tercer trimestre','Cuarto trimestre']; ?>

									{{ $trimestres[$measure->trimester-1] }} de {{ $measure->year }}
								@endif
							@elseif($kpi->periodicity == 4)
								{{ $measure->year }}
							@endif
							</td>
							<td>
								{{ $measure->value }}
							</td>
						</tr>
						@endforeach
						</tbody>
						</table>
					</div>
				</div>
			@else
				Aun no se han agregado mediciones
			@endif
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Gr&aacute;fico de mediciones</span>
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
				<div id="chart_div" style="width: 100%; height: 400px;"></div>
			</div>
		</div>
	</div>
</div>


@stop


@section('scripts2')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
@if(isset($measurements2) && $measurements2 != "0")

    google.charts.load('current', {packages: ['corechart', 'line']});
	google.charts.setOnLoadCallback(drawBackgroundColor);
    
    function drawBackgroundColor() {
        var data = new google.visualization.arrayToDataTable([
          ['Periódo medición', 'Valor medición'],
        @foreach ($measurements2 as $measure)
        	@if (isset($measure->month))
				["{{ $meses[$measure->month-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@elseif (isset($measure->trimester))
				["{{ $trimestres[$measure->trimester-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@elseif (isset($measure->semester))
				["{{ $semestres[$measure->semester-1] }} de {{ $measure->year }}", {{ $measure->value }}],
			@else
				["{{ $measure->year }}", {{ $measure->value }}],
			@endif
		@endforeach
        ]);

        var options = {
        hAxis: {
          title: 'Periódo medición'
        },
        vAxis: {
          title: 'Valor de medición'
        },
        colors: ['#097138']
      };
        var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
      	chart.draw(data, options);
      };

  
@endif
      
</script>
@stop