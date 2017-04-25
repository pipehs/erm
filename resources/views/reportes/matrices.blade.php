@extends('master')

@section('title', 'Matrices de control')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="heatmap">Matriz de control</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de control</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz de control para riesgos de negocio o de procesos. 
      En caso de que desee ver la matriz de control para los riesgos de negocio, deber&aacute; especificar si desea ver
      la matriz para todas las organizaciones o para alguna en espec&iacute;fica.</p>

      @if (!isset($datos))
      	{!!Form::open(['route'=>'genmatriz','method'=>'GET','class'=>'form-horizontal'])!!}

      			<div class="form-group">
                 	<div class="row">
                  		{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
                  		<div class="col-sm-3">
                    		{!!Form::select('organization_id',$organizations, 
                         		null, 
                         	['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
                  		</div>
                	</div>
                </div>

                <div class="form-group" id="tipo">
	                <div class="row">
	                  {!!Form::label('Seleccione tipo de matriz',null,['class'=>'col-sm-4 control-label'])!!}
	                  <div class="col-sm-3">
	                    {!!Form::select('kind',(['0'=>'Controles de proceso','1'=>'Controles de negocio']), 
	                         null, 
	                         ['id' => 'kind','placeholder'=>'- Seleccione -','required'=>'true'])!!}
	                  </div>
	                </div>
	            </div>

           		<div class="form-group">
	                <center>
	                {!!Form::submit('Seleccionar', ['class'=>'btn btn-primary'])!!}
	                </center>
	              </div>
				<br>
				<br>
				<hr>
		@else
			<hr>
			<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

				<thead>
				<th>ID Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Tipo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Periodicidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Prop&oacute;sito<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Costo control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Evidencia<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Riesgo(s)<label><input type="text" placeholder="Filtrar" /></label></th>

			@if ($value == 0)
				<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			@elseif ($value == 1)
				<th>Objetivos(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			@endif	
				</thead>
				
				
				@foreach ($datos as $dato)
					<tr>
						<td>{{$dato['Control']}}</td>
						<td>{{$dato['Descripción']}}</td>
						<td>{{$dato['Responsable']}}</td>
						<td>{{$dato['Tipo']}}</td>
						<td>{{$dato['Periodicidad']}}</td>
						<td>{{$dato['Propósito']}}</td>
						<td>{{$dato['Costo_control']}}</td>
						<td>{{$dato['Evidencia']}}</td>
						<td>
						@foreach ($dato['Riesgos'] as $risk)
							<li>{{ $risk }}</li>
						@endforeach
						</td>
						<td>
						@if ($value == 0)
							@foreach ($dato['Subprocesos'] as $sub)
								<li>{{ $sub }}</li>
							@endforeach
						@elseif ($value == 1)
							@foreach ($dato['Objetivos'] as $obj)
								<li>{{ $obj }}</li>
							@endforeach
						@endif
						</td>
					</tr>
				@endforeach
				</table>
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = "1,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>

				<center>
					{!! link_to_route('matrices', $title = 'Volver', $parameters = NULL,
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
</script>
@stop