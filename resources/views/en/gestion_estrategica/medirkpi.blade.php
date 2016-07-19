@extends('en.master')

@section('title', 'Measure KRI')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kpi','KPI')!!}</li>
			<li>{!!Html::link('kpi.evaluate.$kpi->id','Measure KPI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Measure KPI</span>
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

			Ingrese la medici&oacute;n para el KPI.

			@if ($kpi->periodicity == NULL)
				<h4><b>The selected KPI doesn't have a defined periodicity. Please update it to perform the measure.</b></h4><br><br>
			@else
				@if ($last_eval == NULL)
					<h4><center><b>No previous evaluations validated.</b></center></h4>
				@elseif ($kpi->periodicity == 1) {{-- Mensual: obtenemos meses --}}
					<?php $meses = ['January','February','March','April','May','June','July','August','September','October','November','December']; ?>

					<h4><center><b>The last measurement was conducted on {{ $meses[$last_eval-1] }} {{ $year }}.</b></center></h4>
				@elseif ($kpi->periodicity == 2) {{-- Semestre --}}
					@if ($last_eval == 1)
						<h4><center><b>The last measurement was conducted on the first half of {{ $year }}</b></center></h4>
					@else
						<h4><center><b>The last measurement was conducted on the second half of {{ $year }}</b></center></h4>
					@endif
				@elseif ($kpi->periodicity == 3) {{-- Trimestral --}}
					@if ($last_eval == 1)
						<h4><center><b>The last measurement was conducted on the first quarter of {{ $year }}</b></center></h4>
					@elseif ($last_eval == 2)
						<h4><center><b>The last measurement was conducted on the second quarter of {{ $year }}</b></center></h4>
					@elseif ($last_eval == 3)
						<h4><center><b>The last measurement was conducted on the third quarter of {{ $year }}</b></center></h4>
					@elseif ($last_eval == 4)
						<h4><center><b>The last measurement was conducted on the fourth quarter of {{ $year }}</b></center></h4>
					@endif
				@elseif ($kpi->periodicity == 4) {{-- Anual --}}
					<h4><center><b>The last measurement was conducted on the year {{ $year }}</b></center></h4>
				@endif
				<hr>

				{!!Form::open(['route'=>'kpi.store_eval','method'=>'POST','class'=>'form-horizontal'])!!}

				@if ($kpi->periodicity == 1) {{-- Mensual --}}
					<h5><center><b>KPI periodicity: Monthly</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Select year and month',null,['class'=>'col-sm-5 control-label'])!!}

	                    @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                    	<div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'YYYY','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-1">
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
		                       'placeholder'=>'YYYY','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-1">
		                    {!!Form::number('mes',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'MM','min'=>'01','max'=>'12','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 2) {{-- Semestral --}}
					<h5><center><b>KPI periodicity: Biannual</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Select year and semester (1 or 2)',null,['class'=>'col-sm-5 control-label'])!!}
	                    @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
		                    <div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'YYYY','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-1">
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
		                       'placeholder'=>'YYYY','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-1">
		                    {!!Form::number('semestre',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Sem','min'=>'01','max'=>'02','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 3) {{-- Trimestral --}}
					<h5><center><b>KPI periodicity: Quarterly</b></center></h5>
					<div class="row">
	                  <div class="form-group">
	                    {!!Form::label('Select year and trimester (del 1 al 4)',null,['class'=>'col-sm-5 control-label'])!!}

	                     @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                     	<div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'YYYY','min'=>'2016','disabled'])!!}
		                    </div>
		                    <div class="col-sm-1">
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
		                       'placeholder'=>'YYYY','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                    <div class="col-sm-1">
		                    {!!Form::number('trimestre',null,
		                      ['class'=>'form-control','input maxlength'=>'2',
		                       'placeholder'=>'Trim','min'=>'01','max'=>'04','required' => 'true'])!!}
		                    </div>
		                @endif
	                  </div>
	                </div>
				@elseif ($kpi->periodicity == 4) {{-- Anual --}}
					<h5><center><b>KPI periodicity: Annual</b></center></h5>
					<div class="form-group">
	                    {!!Form::label('Select year',
	                    null,['class'=>'col-sm-5 control-label'])!!}
		                @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
		                    <div class="col-sm-2">
		                      {!!Form::number('ano_muestra',$eval->year,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'YYYY','min'=>'2016'])!!}
		                    </div>
		                    {!!Form::hidden('ano',$eval->year)!!}
		                @else
		                	<div class="col-sm-2">
		                      {!!Form::number('ano',null,
		                      ['id'=>'ano','class'=>'form-control','input maxlength'=>'4',
		                       'placeholder'=>'YYYY','min'=>'2016','required'=>'true'])!!}
		                    </div>
		                @endif
	                </div>
				@endif


				<div class="form-group">
	                {!!Form::label('Value',
	                null,['class'=>'col-sm-5 control-label'])!!}
	                <div class="col-sm-2">
	                @if (isset($eval)) {{-- Si se está editando no se puede editar el periodo --}}
	                	{!!Form::number('value',$eval->value,
	                  	['id'=>'value','class'=>'form-control','requierd'=>true])!!}
	                @else
	                	{!!Form::number('value',null,
	                  	['id'=>'value','class'=>'form-control','required'=>'true'])!!}
	               	@endif
	                </div>
	            </div>

	            {!!Form::hidden('kpi_id',$kpi->id)!!}
	            {!!Form::hidden('org_id',$org_id)!!}
	            <div class="form-group">
					<center>
						{!!Form::submit('Save', ['class'=>'btn btn-success'])!!}
					</center>
				</div>

				{!! Form::close() !!}
			@endif

			<center>
				{!! link_to_route('kpi2', $title = 'Return', $parameters = ['organization_id'=>$org_id],
                 	$attributes = ['class'=>'btn btn-danger'])!!}
			<center>



			
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
@stop