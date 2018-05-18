@extends('master')

@section('title', 'Reporte Consolidado')

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
			<li><a href="#">Reportes</a></li>
			<li><a href="reporte_consolidado">Reporte Consolidado</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Reporte Consolidado</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver el reporte consolidado de Riesgos, con sus principales datos asociados, como son los procesos, subprocesos, controles, hallazgos y planes de acción asociados. </p>

			<hr>
			<table id="datatable-2" class="table-scroll table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

			<thead>
				<th>Organización<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Categoría Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subcategoría Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Correo Responsable Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Cargo Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causas<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Efectos<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Pérdida esperada<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad 1<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto 1<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha 1<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad 2<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto 2<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha 2<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad 3<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto 3<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha 3<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad 4<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto 4<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha 4<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad 5<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto 5<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha 5<label><input type="text" placeholder="Filtrar" /></label></th>
				<!--<th>Severidad<label><input type="text" placeholder="Filtrar" /></label></th>-->
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripción Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Correo Responsable Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Cargo Responsable Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Tipo Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Periodicidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Propósito<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Costo Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Evidencia Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Comentarios Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>% Contribución Control<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Exposición efectiva 1(Riesgo Residual)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Exposición efectiva 2(Riesgo Residual)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Exposición efectiva 3(Riesgo Residual)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Exposición efectiva 4(Riesgo Residual)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Exposición efectiva 5(Riesgo Residual)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripción Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Clasificación Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Recomendaciones Hallazgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Plan de acción<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Estado plan de acción<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Porcentaje avance<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Comentarios avance<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha avance<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha final Plan de Acción<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable Plan de Acción<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Correo Responsable Plan de Acción<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Cargo Responsable Plan de Acción<label><input type="text" placeholder="Filtrar" /></label></th>
			</thead>

			@foreach ($results as $r)
				<tr>
				<td>{{ $r['org'] }}</td>
				<td>{{ $r['process']->name }}</td>
				<td>{{ $r['subprocess']->name }}</td>
				<td>{{ $r['risk']->name }}</td>
				<td>{{ $r['risk']->description }}</td>
				<td>{{ $r['ppal_category'] }}</td>
				<td>{{ $r['risk_category'] }}</td>
				<td>{{ $r['risk_resp'] }}</td>
				<td>{{ $r['risk_resp_mail'] }}</td>
				<td>{{ $r['risk_resp_position'] }}</td>
				<td>
				@if (!empty($r['causes']))
					@foreach ($r['causes'] as $cause)
						<li>{{ $cause->name }} - {{ $cause->description }}</li>
					@endforeach
				@else
					No se han agregado causas
				@endif
				</td>
				<td>
				@if (!empty($r['effects']))
					@foreach ($r['effects'] as $effect)
						<li>{{ $effect->name }} - {{ $effect->description }}</li>
					@endforeach
				@else
					No se han agregado efectos
				@endif
				</td>
				<td>{{ $r['risk']->expected_loss }}</td>
				<td>{{ $r['eval'][0]->avg_probability }}</td>
				<td>{{ $r['eval'][0]->avg_impact }}</td>
				<td>{{ $r['eval'][0]->updated_at }}</td>
				<td>{{ $r['eval'][1]->avg_probability }}</td>
				<td>{{ $r['eval'][1]->avg_impact }}</td>
				<td>{{ $r['eval'][1]->updated_at }}</td>
				<td>{{ $r['eval'][2]->avg_probability }}</td>
				<td>{{ $r['eval'][2]->avg_impact }}</td>
				<td>{{ $r['eval'][2]->updated_at }}</td>
				<td>{{ $r['eval'][3]->avg_probability }}</td>
				<td>{{ $r['eval'][3]->avg_impact }}</td>
				<td>{{ $r['eval'][3]->updated_at }}</td>
				<td>{{ $r['eval'][4]->avg_probability }}</td>
				<td>{{ $r['eval'][4]->avg_impact }}</td>
				<td>{{ $r['eval'][4]->updated_at }}</td>
				

				

				<td>{{ $r['control']->name }}</td>
				<td>{{ $r['control']->description }}</td>
				<td>{{ $r['control_resp'] }} </td>
				<td>{{ $r['control_resp_mail'] }}</td>
				<td>{{ $r['control_resp_position'] }}</td>
				<td>{{ $r['control']->type }}</td>
				<td>{{ $r['control']->periodicity }}</td>
				<td>{{ $r['control']->purpose }}</td>
				<td>{{ $r['control']->expected_cost }}</td>
				<td>{{ $r['control']->evidence }}</td>
				<td>{{ $r['control']->comments }}</td>
				<td>{{ $r['control']->cont_percentage }}</td>
				<td>{{ $r['residual_risk'][0] }}</td>
				<td>{{ $r['residual_risk'][1] }}</td>
				<td>{{ $r['residual_risk'][2] }}</td>
				<td>{{ $r['residual_risk'][3] }}</td>
				<td>{{ $r['residual_risk'][4] }}</td>
				<td>{{ $r['issue']->name }}</td>
				<td>{{ $r['issue']->description }}</td>
				<td>{{ $r['issue']->classification }}</td>
				<td>{{ $r['issue']->recommendations }}</td>
				<td>{{ $r['action_plan']->description }}</td>
				<td>{{ $r['action_plan']->status }}</td>
				<td>{{ $r['percentage'] }}</td>
				<td>{{ $r['percentage_comments'] }}</td>
				<td>{{ $r['percentage_date'] }}</td>
				<td>{{ $r['action_plan']->final_date }}</td>
				<td>{{ $r['action_plan_resp'] }}</td>
				<td>{{ $r['action_plan_resp_mail'] }}</td>
				<td>{{ $r['action_plan_resp_position'] }}</td>
				</tr>
			@endforeach

			</table>
			
			<div id="boton_exportar">

			{!! link_to_route('genexcelconsolidado', $title = 'Exportar', $parameters = '', $attributes = ['class'=>'btn btn-success']) !!}

			</div>
			

			<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>

      </div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>
</script>
@stop