@extends('master')

@section('title', 'Gestionar KRI')

@section('content')
<!-- INDEX PARA PANTALLAZOS -->

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li>{!!Html::link('kri.enlazar','Gestionar KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Gestionar KRI</span>
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

			En esta secci&oacute;n podr&aacute; crear y/o modificar los indicadores para los riesgos relevantes al negocio,
			y adem&aacute;s evaluar los mismos.

				{!!Form::open(['route'=>'kri.guardar_enlace','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data'])!!}
				<div id="cargando"></div>
				<div id="risks" style="float: center;">
					<div class="form-group">
						{!!Form::label('Seleccione riesgo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="risk_id" id="risk_idTEMP" required="true">
								<option value="" selected disabled>- Seleccione -</option>
								<option value="" disabled>- Riesgos de proceso asociados -</option>
								@if ($risk_subprocess != null)
									@foreach ($risk_subprocess as $risk)
										<option value="{{ $risk['id'] }}_sub">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No hay riesgos de proceso asociados</option>
								@endif

								@if ($objective_risk != null)
									<option value="" disabled>- Riesgos de negocio asociados -</option>
									@foreach ($objective_risk as $risk)
										<option value="{{ $risk['id'] }}_obj">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No hay riesgos de negocio</option>
								@endif
									<option value="">Desviación de los recursos de caja de la compañia</option>
							</select>
						</div>
					</div>
				</div>

				<div id="info_kri" style="float: center;">
				<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
					<thead>
					<th>KRI</th>
					<th width="35%">Descripci&oacute;n</th>
					<th>Evaluaci&oacute;n</th>
					<th>Descripci&oacute;n de la evaluaci&oacute;n</th>
					<th>Riesgo</th>
					<th>Responsable del riesgo</th>
					<th>Fecha evaluaci&oacute;n</th>
					</thead>

					<tr>
					<td>Avances a proveedores</td>
					<td>Exigir la composición de la cuenta "avances al proveedor" y verificar si es que hay algún avance
					antiguo abierto (pendientes por más de un mes, sin su correspondiente material de recepción o servicio efectuado,
					o fuera del tiempo de liquidación). Exigir justificación por casos de desviación detectados.
					Adicionalmente, para reforzar la excepción, verificar si es que hubo algún pago total a proveedores con
					antiguos avances abiertos. 
					</td>
					<td>
						<ul class="semaforo verde">
							<li></li>
							<li></li>
							<li></li>
						</ul>
					</td>
					<td>Todos los antiguos o con fecha de liquidación terminada se encuentran cerrados.</td>
					<td>Desviación de los recursos de caja de la compañia (proceso) - Comportamiento Anti-ético o fraude (estratégico)</td>
					<td>Felipe Herrera</td>
					<td>20-03-2016</td>
					</tr>
					<tr>
					<td>Productos a reparar en el largo plazo</td>
					<td>Se debe exigir la lista de productos que han presentado alguna falla en el último tiempo, y verificar
					que si estos aun no han sido reparados, no deben tener su fecha de liquidación vencida.</td>
					<td>
						<ul class="semaforo rojo">
							<li></li>
							<li></li>
							<li></li>
						</ul>
					</td>
					<td>Él 55% de productos a reparar se encuentra pasado su fecha de liquidación y aun no han sido reparados.</td>
					<td>Desviación de los recursos de caja de la compañia (proceso) - Comportamiento Anti-ético o fraude (estratégico)</td>
					<td>Eugenio Salcedo</td>
					<td>12-02-2016</td>
					</tr>
					<tr>
					<td>Stock en tránsito</td>
					<td>Se debe verificar la cuenta de stock en tránsito, con el fin de revisar los productos que según su fecha de ingreso
					ya deberían encontrarse en stock de libre utilización. Además, se debe verificar que todo el stock presente en la cuenta
					stock en tránsito, se encuentre físicamente en el almacen.</td>
					<td>
						<ul class="semaforo amarillo">
							<li></li>
							<li></li>
							<li></li>
						</ul>
					</td>
					<td>Todos los productos en stock en tránsito se encuentran en el almacen, sin embargo el 20% de éstos ya debió
					haber sido enviado a libre utilización.</td>
					<td>Desviación de los recursos de caja de la compañia (proceso) - Comportamiento Anti-ético o fraude (estratégico)</td>
					<td>Víctor Cortes</td>
					<td>24-03-2016</td>
					</tr>
					</table>
				</div>
				</br>

				{!!Form::close()!!}

				<center>
					{!! link_to_route('kri', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop


@section('scripts2')
<script>
$("#risk_id").change(function() {
if ($("#risk_id").val() != '') //Si es que se ha seleccionado valor válido de plan
{
	//Añadimos la imagen de carga en el contenedor
	$('#cargando').html('<div><center><img src="/bgrcdemo2/assets/img/loading.gif" width="19" height="19"/></center></div>');
	//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
	//primero obtenemos controles asociados a los riesgos de negocio

	//obtenemos pruebas relacionadas a la auditoría seleccionada
	$.get('get_kri.'+$("#risk_id").val(), function (result) {
			
			$("#cargando").html('<br>');
			$("#info_kri").empty();

			if (result == "null")
			{
				var info = "<center>Aun no se ha creado indicador para el riesgo ";
				info += $("#risk_id option:selected").text() + ".<br><br></center>";
				info += '<center><a href="kri.create.'+$("#risk_id").val()+'" class="btn btn-success">Crear KRI</a</center>';
				$("#info_kri").append(info);
			}

			//parseamos datos obtenidos
			var datos = JSON.parse(result);
			
			//seteamos datos
			$(datos).each( function() {

			
					});
			});

}
else
{
	$("#info_kri").empty();
}

});
</script>
@stop