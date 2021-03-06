@extends('master')

@section('title', 'Enlazar Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('riesgo_kri','Riesgo - KRI')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Riesgo - KRI</span>
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

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			En esta secci&oacute;n podr&aacute; ver, crear, modificar y/o evaluar los indicadores filtrando &eacute;stos por el riesgo de negocio o de proceso enlazado.

				{!!Form::open(['route'=>'kri.guardar_enlace','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data'])!!}
				<div id="cargando"></div>
				<div id="risks" style="float: center;">
					<div class="form-group">
						{!!Form::label('Seleccione riesgo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="risk_id" id="risk_id" required="true">
								<option value="" selected disabled>- Seleccione -</option>
								<option value="" disabled>- Riesgos de proceso asociados -</option>
								@if ($risk_subprocess != null)
									@foreach ($risk_subprocess as $risk)
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No hay riesgos de proceso asociados</option>
								@endif

								@if ($objective_risk != null)
									<option value="" disabled>- Riesgos de negocio -</option>
									@foreach ($objective_risk as $risk)
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</option>
									@endforeach
								@else
									<option value="" disabled>No hay riesgos de negocio</option>
								@endif
							</select>
						</div>
					</div>
				</div>

				<div id="info_kri" style="float: center;">

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
	if ($("#risk_id").val() != '') //Si es que se ha seleccionado valor válido de riesgo
	{
		//Añadimos la imagen de carga en el contenedor
		$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
		//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
		//primero obtenemos controles asociados a los riesgos de negocio

		//obtenemos kri del riesgo seleccionado
		$.get('get_kri.'+$("#risk_id").val(), function (result) {
				$("#cargando").html('<br>');
				$("#info_kri").empty();

				if (result == "null")
				{
					var info = "<center>Aun no se ha creado indicador para el riesgo ";
					info += $("#risk_id option:selected").text() + ".<br><br></center>";
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					info += '<center><a href="kri.create2.'+$("#risk_id").val()+'" class="btn btn-success">Crear KRI</a</center>';
				<?php break; ?>
				@endif
			@endforeach
					$("#info_kri").append(info);
				}

				else
				{

					var table_row= '<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">';
					table_row += '<thead>';
					table_row += '<th>KRI</th>';
					table_row += '<th >Descripci&oacute;n</th>';
					table_row += '<th>Unidad de medida de evaluaci&oacute;n';
					table_row += '<th>Evaluaci&oacute;n</th>';
					table_row += '<th>Resultado</th>';
					table_row += '<th>Descripci&oacute;n de la evaluaci&oacute;n</th>';
					table_row += '<th>Riesgo</th>';
					table_row += '<th>Responsable</th>';
					table_row += '<th>Fecha evaluaci&oacute;n</th>';
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					table_row += '<th>Acci&oacute;n</th>';
					table_row += '<th>Acci&oacute;n</th>';
					table_row += '<th>Acci&oacute;n</th>';
				<?php break; ?>
				@endif
			@endforeach
					table_row += '</thead>';

					

					//parseamos datos obtenidos
					var datos = JSON.parse(result);
					
					//seteamos datos
					$(datos).each( function() {
							table_row += '<tr><td>'+this.name+'</td><td>'+this.description+'</td>';
							if (this.uni_med == 0)
								uni_med = "Porcentaje"
							else if (this.uni_med == 1)
								uni_med = "Monto"
							else if (this.uni_med == 2)
								uni_med = "Cantidad"

							table_row += '<td>'+uni_med+'</td>';

							table_row += '<td>'+this.last_eval+'</td>';

							//mostramos evaluación
							if (this.eval == 0)
							{
								table_row += '<td><ul class="semaforo verde"><li></li><li></li><li></li></ul></td>';	
							}
							else if (this.eval == 1)
							{
								table_row += '<td><ul class="semaforo amarillo"><li></li><li></li><li></li></ul></td>';	
							}
							else if (this.eval == 2)
							{
								table_row += '<td><ul class="semaforo rojo"><li></li><li></li><li></li></ul></td>';	
							}
							else
							{
								table_row += '<td>'+this.eval+'</td>';
							}
							

							table_row += '<td>'+this.description_eval+'</td>';
							table_row += '<td>'+ $("#risk_id option:selected").text() +'</td>';

							table_row += '<td>';

							if (this.s_name != 'NULL')
							{
								table_row += this.s_name+' '+this.s_surnames
							}
							else
							{
								table_row += "Ninguno"
							}
							
							table_row += '</td>'
							table_row += '<td>'+this.date_last+'</td>';
					@foreach (Session::get('roles') as $role)
						@if ($role != 6)
							table_row += '<td><a href="kri.edit.'+this.id+'" class="btn btn-primary">Editar</a></td>';
							table_row += '<td><a href="kri.evaluar.'+this.id+'" class="btn btn-success">Evaluar</a></td>';
							table_row += '<td><a href="kri.veranteriores.'+this.id+'" class="btn btn-info">Monitorear</a></td>';
							<?php break; ?>
						@endif
					@endforeach
							table_row += '</tr>';

					
					});

					table_row += '</table>';
			@foreach (Session::get('roles') as $role)
				@if ($role != 6)
					table_row += '<center><a href="kri.create2.'+$("#risk_id").val()+'" class="btn btn-success">Agregar nuevo KRI</a</center>';
				<?php break; ?>
				@endif
			@endforeach
					$("#info_kri").html(table_row);

				}
		});
	}
	else
	{
		$("#info_kri").empty();
	}

});
</script>
@stop