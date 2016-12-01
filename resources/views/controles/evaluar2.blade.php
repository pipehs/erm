@extends('master')

@section('title', 'Controles')

@section('content')
<style>
.body {
    display: table;
}

.left-side {
    float: none;
    display: table-cell;
}

.right-side {
    float: none;
    display: table-cell;

}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="evaluar_controles">Evaluar Control</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-5 col-m-6">
	<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Informaci&oacute;n del Control</span>
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

		<h3>{{ $control->name }}</h3>
		<hr>
		<table class="table table-bordered table-striped table-datatable" style="font-size:12px; width: 100%;">
			<tr>
			<td><b>Descripci&oacute;n del control</b></td>
			<td>
				@if ($control->description == NULL)
					No se agreg&oacute; descripci&oacute;n
				@else
					{{ $control->description }}
				@endif
			</td>
			</tr>
			<tr><td><b>Tipo</td>
			<td>
				@if ($control->type == 0)
					Manual
				@elseif ($control->type == 1)
					Semi-autom&aacute;tico
				@elseif ($control->type == 2)
					Autom&aacute;tico
				@else
					No se agreg&oacute; tipo	
				@endif
			</td>
			</tr>
			<tr><td><b>Periodicidad</td>
			<td>
				@if ($control->periodicity == 0)
					Diario
				@elseif ($control->periodicity == 1)
					Semanal
				@elseif ($control->periodicity == 2)
					Mensual
				@elseif ($control->periodicity == 3)
					Semestral
				@elseif ($control->periodicity == 4)
					Anual
				@elseif ($control->periodicity == 5)
					Cada vez que ocurra
				@else
					No se agreg&oacute; periodicidad
				@endif
			</td></tr>
			<tr><td><b>Evidencia</td>
			<td>
				@if ($control->evidence == NULL)
					No se agreg&oacute; evidencia
				@else
					{{ $control->evidence }}
				@endif
			</td>
			</tr>
			<tr><td><b>Prop&oacute;sito</td>
			<td>
				@if ($control->purpose == 0)
					Preventivo
				@elseif ($control->purpose == 1)
					Detectivo
				@elseif ($control->purpose == 2)
					Correctivo
				@else
					No se agreg&oacute; prop&oacute;sito
				@endif
			</td></tr>
			<tr><td><b>Costo esperado</td>
			<td>
				@if ($control->expected_cost == NULL)
					No se agreg&oacute; costo esperado
				@else
					{{ $control->expected_cost }}
				@endif
			</td>
			</tr>
			<tr><td><b>Responsable</td>
			<td>
				@if ($stakeholder == NULL)
					No se agreg&oacute; responsable
				@else
					{{ $stakeholder }}
				@endif
			</td>
			</tr>
			<tr>
			<td><b>Riesgos involucrados</b></td>
			<td><ul>
			@foreach ($risks as $risk)
				<li>{{ $risk->name }}</li>
			@endforeach
			</ul></td>
		</table>
			</div>
		</div>
	</div>

	<div class="col-sm-7 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluar Control</span>
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
			
				<p>Seleccione el tipo de prueba que desea agregar o editar para el control {{ $control->name }}.</p>
				<hr>
				<table class="table table-bordered table-striped table-heading table-datatable" style="font-size:12px; width: 100%;">
				<thead>
				<th colspan="3">Prueba de diseño</th>
				</thead>
				<tr>
				<td style="width:60%;">
				@if (!isset($last_diseno) || $last_diseno == NULL) 
					Sin informaci&oacute;n previa
					</td>
					<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.0" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
				@else
					<center><b><u>&Uacute;ltima evaluaci&oacute;n</u></b></center>
					<ul>
						<li>Descripci&oacute;n: {{ $last_diseno['description'] }}</li>
						<li>Resultado: 
							@if ($last_diseno['results'] == 1)
								Efectiva
							@else
								Inefectiva
							@endif
						</li>
						<li>&Uacute;ltima actualizaci&oacute;n: 
							{{ $last_diseno['updated_at'] }}
						</li>
						<li>
						@if ($last_diseno['comments'] != NULL) {{-- Es efectiva--}}
							Comentarios: {{ $last_diseno['comments'] }}
						@elseif ($last_diseno['issues'] != NULL)
							Hallazgo(s) encontrado(s):
							<ul>
							@foreach ($last_diseno['issues'] as $i)
								<li>Nombre: {{ $i->name }}</li>
								<li>Descripci&oacute;n: {{ $i->description }}</li>
								<li>Clasificaci&oacute;n:
									@if ($i->classification == 0)
										Oportunidad de mejora
									@elseif ($i->classification == 1)
										Deficiencia
									@elseif ($i->classification == 2)
										Debilidad significativa
									@else
										No se pudo obtener la clasificaci&oacute;n
									@endif
								</li>
								<li>Recomendaciones: {{ $i->recommendations }}</li>
							@endforeach
							</li>
						@else
							No hay hallazgos ni comentarios.
						@endif
						</li>
						<li>Estado:
							@if ($last_diseno['status'] == 1)
								Abierta
							@else
								Cerrada
							@endif
						</li>
					</ul>
					</td>
					@if ($last_diseno['status'] == 2)
						<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.0" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
					@else
						<td>{!! link_to_route('editar_evaluacion', $title = 'Editar evaluaci&oacute;n', $parameters = $last_diseno['id'],
				                 		$attributes = ['class'=>'btn btn-primary'])!!}</td>
						<td><button class="btn btn-warning" onclick="cerrar_evaluacion({{ $last_diseno['id'] }},'{{ $control['name'] }}','La prueba de diseño')">Cerrar prueba</button></td>
				    @endif
				@endif
				
				</tr>
				<thead>
				<th colspan="3">Prueba de efectividad operativa</th>
				</thead>
				<tr>
				<td>
				@if (!isset($last_efectividad) || $last_efectividad == NULL)
					Sin informaci&oacute;n previa
					</td>
					<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.1" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
				@else
					<center><b><u>&Uacute;ltima evaluaci&oacute;n</u></b></center>
					<ul>
						<li>Descripci&oacute;n: {{ $last_efectividad['description'] }}</li>
						<li>Resultado: 
							@if ($last_efectividad['results'] == 1)
								Efectiva
							@else
								Inefectiva
							@endif
						</li>
						<li>&Uacute;ltima actualizaci&oacute;n: 
							{{ $last_efectividad['updated_at'] }}
						</li>
						<li>
						@if ($last_efectividad['comments'] != NULL) {{-- Es efectiva--}}
							Comentarios: {{ $last_efectividad['comments'] }}
						@elseif ($last_efectividad['issues'] != NULL)
							Hallazgo(s) encontrado(s):
							<ul>
							@foreach ($last_efectividad['issues'] as $i)
								<li>Nombre: {{ $i->name }}</li>
								<li>Descripci&oacute;n: {{ $i->description }}</li>
								<li>Clasificaci&oacute;n:
									@if ($i->classification == 0)
										Oportunidad de mejora
									@elseif ($i->classification == 1)
										Deficiencia
									@elseif ($i->classification == 2)
										Debilidad significativa
									@else
										No se pudo obtener la clasificaci&oacute;n
									@endif
								</li>
								<li>Recomendaciones: {{ $i->recommendations }}</li>
							@endforeach
							</li>
						@else
							No hay hallazgos ni comentarios.
						@endif
						</li>
						<li>Estado:
							@if ($last_efectividad['status'] == 1)
								Abierta
							@else
								Cerrada
							@endif
						</li>
					</ul>
					</td>
					@if ($last_efectividad['status'] == 2)
						<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.1" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
					@else
						<td>{!! link_to_route('editar_evaluacion', $title = 'Editar evaluaci&oacute;n', $parameters = $last_efectividad['id'],$attributes = ['class'=>'btn btn-primary'])!!}</td>
						<td><button class="btn btn-warning" onclick="cerrar_evaluacion({{ $last_efectividad['id'] }},'{{ $control['name'] }}','La prueba de efectividad operativa')">Cerrar prueba</button></td>
				    @endif
				@endif
				
				</tr>
				<thead>
				<th colspan="3">Prueba sustantiva</th>
				</thead>
				<tr>
				<td>
				@if (!isset($last_sustantiva) || $last_sustantiva == NULL)
					Sin informaci&oacute;n previa
					</td>
					<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.2" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
				@else
					<center><b><u>&Uacute;ltima evaluaci&oacute;n</u></b></center>
					<ul>
						<li>Descripci&oacute;n: {{ $last_sustantiva['description'] }}</li>
						<li>Resultado: 
							@if ($last_sustantiva['results'] == 1)
								Efectiva
							@else
								Inefectiva
							@endif
						</li>
						<li>&Uacute;ltima actualizaci&oacute;n: 
							{{ $last_sustantiva['updated_at'] }}
						</li>
						<li>
						@if ($last_sustantiva['comments'] != NULL) {{-- Es efectiva--}}
							Comentarios: {{ $last_sustantiva['comments'] }}
						@elseif ($last_sustantiva['issues'] != NULL)
							Hallazgo(s) encontrado(s):
							<ul>
							@foreach ($last_sustantiva['issues'] as $i)
								<li>Nombre: {{ $i->name }}</li>
								<li>Descripci&oacute;n: {{ $i->description }}</li>
								<li>Clasificaci&oacute;n:
									@if ($i->classification == 0)
										Oportunidad de mejora
									@elseif ($i->classification == 1)
										Deficiencia
									@elseif ($i->classification == 2)
										Debilidad significativa
									@else
										No se pudo obtener la clasificaci&oacute;n
									@endif
								</li>
								<li>Recomendaciones: {{ $i->recommendations }}</li>
							@endforeach
							</li>
						@else
							No hay hallazgos ni comentarios.
						@endif
						</li>
						<li>Estado:
							@if ($last_sustantiva['status'] == 1)
								Abierta
							@else
								Cerrada
							@endif
						</li>
					</ul>
					</td>
					@if ($last_sustantiva['status'] == 2)
						<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.2" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
					@else
						<td>{!! link_to_route('editar_evaluacion', $title = 'Editar evaluaci&oacute;n', $parameters = $last_sustantiva['id'],$attributes = ['class'=>'btn btn-primary'])!!}</td>
						<td><button class="btn btn-warning" onclick="cerrar_evaluacion({{ $last_sustantiva['id'] }},'{{ $control['name'] }}','La prueba sustantiva')">Cerrar prueba</button></td>
				    @endif
				@endif
				</tr>
				<thead>
				<th colspan="3">Prueba de cumplimiento</th>
				</thead>
				<tr>
				<td>
				@if (!isset($last_cumplimiento) || $last_cumplimiento == NULL)
					Sin informaci&oacute;n previa
					</td>
					<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.3" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
				@else
					<center><b><u>&Uacute;ltima evaluaci&oacute;n</u></b></center>
					<ul>
						<li>Descripci&oacute;n: {{ $last_cumplimiento['description'] }}</li>
						<li>Resultado: 
							@if ($last_cumplimiento['results'] == 1)
								Efectiva
							@else
								Inefectiva
							@endif
						</li>
						<li>&Uacute;ltima actualizaci&oacute;n: 
							{{ $last_cumplimiento['updated_at'] }}
						</li>
						<li>
						@if ($last_cumplimiento['comments'] != NULL) {{-- Es efectiva--}}
							Comentarios: {{ $last_diseno['comments'] }}
						@elseif ($last_cumplimiento['issues'] != NULL)
							Hallazgo(s) encontrado(s):
							<ul>
							@foreach ($last_cumplimiento['issues'] as $i)
								<li>Nombre: {{ $i->name }}</li>
								<li>Descripci&oacute;n: {{ $i->description }}</li>
								<li>Clasificaci&oacute;n:
									@if ($i->classification == 0)
										Oportunidad de mejora
									@elseif ($i->classification == 1)
										Deficiencia
									@elseif ($i->classification == 2)
										Debilidad significativa
									@else
										No se pudo obtener la clasificaci&oacute;n
									@endif
								</li>
								<li>Recomendaciones: {{ $i->recommendations }}</li>
							@endforeach
							</li>
						@else
							No hay hallazgos ni comentarios.
						@endif
						</li>
						<li>Estado:
							@if ($last_cumplimiento['status'] == 1)
								Abierta
							@else
								Cerrada
							@endif
						</li>
					</ul>
					</td>
					@if ($last_cumplimiento['status'] == 2)
						<td colspan="2"><a href="agregar_evaluacion.{{$control->id}}.3" class="btn btn-negro">Nueva evaluaci&oacute;n</a></td>
					@else
						<td>{!! link_to_route('editar_evaluacion', $title = 'Editar evaluaci&oacute;n', $parameters = $last_cumplimiento['id'],$attributes = ['class'=>'btn btn-primary'])!!}</td>
						<td><button class="btn btn-warning" onclick="cerrar_evaluacion({{ $last_cumplimiento['id'] }},'{{ $control['name'] }}','La prueba de cumplimiento')">Cerrar prueba</button></td>
				    @endif
				@endif
				</tr>
				</table>
				
			</div>
		</div>
	</div>
</div>
<div class="col-sm-12 col-m-6">
	<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">

		<center>
			{!! link_to_route('evaluar_controles', $title = 'Volver', $parameters = NULL,
                 $attributes = ['class'=>'btn btn-danger'])!!}
		<center>
	</div>
</div>
@stop

@section('scripts2')

@stop

