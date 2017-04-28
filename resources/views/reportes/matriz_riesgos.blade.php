@extends('master')

@section('title', 'Matriz de riesgos')

@section('content')

<style>
.form-horizontal .form-group.text-left{
    text-align: left !important;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="risk_matrix">Matriz de riesgos</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de riesgos</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz para los riesgos de negocio y/o de procesos de las distintas organizaciones ingresadas en el sistema. </p>

      @if (!isset($datos))
      	{!!Form::open(['route'=>'genriskmatrix','method'=>'GET','class'=>'form-horizontal'])!!}

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

                <div class="form-group" id="tipo">
	                <div class="row">
	                  {!!Form::label('Seleccione tipo de matriz',null,['class'=>'col-sm-4 control-label'])!!}
	                  <div class="col-sm-3">
	                    {!!Form::select('kind',(['0'=>'Riesgos de proceso','1'=>'Riesgos de negocio']), 
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
			<!--{!! link_to_route('genriskmatrix', $title = 'Matriz Riesgos de Proceso', $parameters = 0, $attributes = ['class'=>'btn btn-primary']) !!}
					&nbsp;&nbsp;
			{!! link_to_route('genriskmatrix', $title = 'Matriz Riesgos de Negocio', $parameters = 1, $attributes = ['class'=>'btn btn-success']) !!}
			-->

	{!!Form::close()!!}

	@else
			<hr>
			<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

			@if ($value == 0)
				<thead>
				<th>Proceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>ID Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Categoría<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causas<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Efectos<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Pérdida esperada<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
					<td>
					@if (!empty($dato['Procesos']))
						@foreach($dato['Procesos'] as $process)
							<li>{{$process}}</li>
						@endforeach
					@else
						No se han definido procesos
					@endif
					</td>
					<td>
					@if (!empty($dato['Subprocesos']))
						@foreach($dato['Subprocesos'] as $subprocess)
							<li>{{$subprocess}}</li>
						@endforeach
					@else
						No se han definido subprocesos
					@endif
					</td>
					<td>{{$dato['Riesgo']}}</td>
					<td> 
					@if ($dato['Descripción'] == NULL || $dato['Descripción'] == "")
						No se ha definido descripci&oacute;n
					@else
						@if (strlen($dato['Descripción']) > 100)
							<div id="description_{{$dato['id']}}" title="{{ $dato['Descripción'] }}">{{ $dato['short_des'] }}...
							<div style="cursor:hand" onclick="expandir({{ $dato['id'] }},'{{ $dato['Descripción'] }}','{{ $dato['short_des'] }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $dato['Descripción'] }}
						@endif
					@endif
					</td>
					<td>{{$dato['Categoría']}}</td>
					<td>
					@if (!empty($dato['Causas']))
						@foreach($dato['Causas'] as $c)
							<li>{{$c}}</li>
						@endforeach
					@else
						No se han definido causas
					@endif
					</td>
					<td>
					@if (!empty($dato['Efectos']))
						@foreach($dato['Efectos'] as $e)
							<li>{{$e}}</li>
						@endforeach
					@else
						No se han definido efectos
					@endif
					</td>
					<td>{{$dato['Pérdida_esperada']}}</td>
					<td>{{$dato['Probabilidad']}}</td>
					<td>{{$dato['Impacto']}}</td>
					<td>{{$dato['Score']}}</td>
					<td>{{$dato['Fecha_expiración']}}</td>
					<td>
					@if (!empty($dato['Controles']))
						@foreach($dato['Controles'] as $c)
							<li>{{$c}}</li>
						@endforeach
					@else
						No se han definido controles
					@endif	
					</td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = "3,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@elseif ($value == 1)
				<thead>
				<th>Organizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Objetivo(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>ID Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Categoría<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causas<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Efectos<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Pérdida esperada<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
					<td>{{$dato['Organización']}}</td>
					<td>
					@foreach($dato['Objetivos'] as $obj)
						<li>{{$obj}}</li>
					@endforeach
					</td>
					<td>{{$dato['Riesgo']}}</td>
					<td>
					@if ($dato['Descripción'] == NULL || $dato['Descripción'] == "")
						No se ha definido descripci&oacute;n
					@else
						@if (strlen($dato['Descripción']) > 100)
							<div id="description_{{$dato['id']}}" title="{{ $dato['Descripción'] }}">{{ $dato['short_des'] }}...
							<div style="cursor:hand" onclick="expandir({{ $dato['id'] }},'{{ $dato['Descripción'] }}','{{ $dato['short_des'] }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $dato['Descripción'] }}
						@endif
					@endif
					</td>
					<td>{{$dato['Categoría']}}</td>
					<td>
					@if (!empty($dato['Causas']))
						@foreach($dato['Causas'] as $c)
							<li>{{$c}}</li>
						@endforeach
					@else
						No se han definido causas
					@endif
					</td>
					<td>
					@if (!empty($dato['Efectos']))
						@foreach($dato['Efectos'] as $e)
							<li>{{$e}}</li>
						@endforeach
					@else
						No se han definido efectos
					@endif
					</td>
					<td>{{$dato['Pérdida_esperada']}}</td>
					<td>{{$dato['Probabilidad']}}</td>
					<td>{{$dato['Impacto']}}</td>
					<td>{{$dato['Score']}}</td>
					<td>{{$dato['Fecha_expiración']}}</td>
					<td>
					@if (!empty($dato['Controles']))
						@foreach($dato['Controles'] as $c)
							<li>{{$c}}</li>
						@endforeach
					@else
						No se han definido controles
					@endif
					</td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = "4,$org_selected", $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@endif

			<center>
					{!! link_to_route('risk_matrix', $title = 'Volver', $parameters = NULL,
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