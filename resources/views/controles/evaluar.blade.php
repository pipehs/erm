@extends('master')

@section('title', 'Controles')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="evaluar_controles">Evaluar Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluar Controles</span>
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


		{!!Form::open(['route'=>'control.guardar_evaluacion','method'=>'POST','class'=>'form-horizontal',
						'enctype'=>'multipart/form-data'])!!}
				<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Seleccione control',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('control_id',$controls,null, 
							 	   ['id' => 'control_id','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

				<table id="table_evaluacion" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>

				<div id="boton-guardar">
					<!-- Añadiremos aquí botón para guardar evaluación -->
				</div>

		{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
$("#control_id").change(function() {
	
			if ($("#control_id").val() != '') //Si es que se ha seleccionado valor válido de control
			{
				$('#cargando').fadeIn(1);
				//Añadimos la imagen de carga en el contenedor
				$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

				$('#cargando').delay(400).fadeOut(5);

				//Seteamos cabecera
				var table_head = "<thead>";
				table_head += "<th>Diseño</th><th>Efectividad operativa</th><th>Prueba sustantiva</th><th>Prueba de cumplimiento</th>";
				table_head += "</thead>";

				

				var table_row = "<tr>";
				table_row += "<td><select name='diseno' id='diseno' style='width:180px; vertical-align:top' class='form-control' onchange='test_diseno()'>";
				table_row += "<option value=''>Seleccione Resultado</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='2'>Inefectivo</option></select>";
				table_row += "<div id='datos_diseno' style='display: none;'></div></td>";

				table_row += "<td><select name='efectividad' style='width:180px' class='form-control' id='efectividad' onchange='test_efectividad()'>";
				table_row += "<option value='' selected>Seleccione Resultado</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='2'>Inefectivo</option></select>";
				table_row += "<div id='datos_efectividad' style='display: none;'></div></td>";

				table_row += "<td><select name='sustantiva' style='width:180px' class='form-control' id='sustantiva' onchange='test_sustantiva()'>";
				table_row += "<option value='' selected>Seleccione Resultado</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='2'>Inefectivo</option></select>";
				table_row += "<div id='datos_sustantiva' style='display: none;'></div></td>";

				table_row += "<td><select name='cumplimiento' style='width:180px' class='form-control' id='cumplimiento' onchange='test_cumplimiento()'>";
				table_row += "<option value='' selected>Seleccione Resultado</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='2'>Inefectivo</option></select>";
				table_row += "<div id='datos_cumplimiento' style='display: none;'></div></td>";

				table_row += "</tr>";

				$('#table_evaluacion').html(table_head);
				$('#table_evaluacion').append(table_row);
				$('#table_evaluacion').fadeIn(500);

				$('#boton-guardar').html('<center><button name="guardar" class="btn btn-success">Guardar evaluación</button></center>');

			}

			else
			{
				$('#table_evaluacion').fadeOut(500);
				$('#table_evaluacion').empty();
			}
});

function test_diseno() //ESTE SI FUNCIONA!!!!!!
{
	if ($("#diseno").val() == 2)
	{
		var inefectivo = '<br>';
		inefectivo += "<select name='clasificacion_diseno' style='width:180px;' class='form-control'>";
		inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
		inefectivo += "<option value='0'>Oportunidad de mejora</option>";
		inefectivo += "<option value='1'>Deficiencia</option>";
		inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";

		inefectivo += '<textarea name="hallazgo_diseno" class="form-control" style="width:180px" placeholder="Ingrese hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones_diseno" class="form-control" style="width:180px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<b><span style="float: left;">Plan de acción: </span></b><br>';
		inefectivo += '<textarea name="plan_accion_diseno" class="form-control" style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

		inefectivo += '<input type="date" name="fecha_plan_diseno" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"></textarea><br>';

		inefectivo += '<select name="responsable_plan_diseno" class="form-control" style="width:180px">';
		inefectivo += '<option value="" disabled selected>Responsable</option>';
		@foreach ($stakeholders as $stakeholder)
			inefectivo += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
		@endforeach

		inefectivo += '</select>';

		inefectivo += '<br><input type="file" name="file_diseno" id="file1" class="inputfile" />';
		inefectivo += '<label for="file1">Cargue evidencia</label></div>';


		$("#datos_diseno").html(inefectivo);
		$("#datos_diseno").fadeIn(500);
	}
	else if ($("#diseno").val() == 1)
	{
		var efectivo = '<br><textarea name="comentarios_diseno" class="form-control" style="width:180px" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
		
		efectivo += '<br><input type="file" name="file_diseno" id="file1" class="inputfile" />';
		efectivo += '<label for="file1">Cargue evidencia</label></div>';

		$("#datos_diseno").html(efectivo);
		$("#datos_diseno").fadeIn(500);
	}
	else
	{
		$("#datos_diseno").empty();
	}
}

function test_efectividad()
{
	if ($("#efectividad").val() == 2)
	{
		var inefectivo = '<br>';
		inefectivo += "<select name='clasificacion_efectividad' style='width:180px;' class='form-control'>";
		inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
		inefectivo += "<option value='0'>Oportunidad de mejora</option>";
		inefectivo += "<option value='1'>Deficiencia</option>";
		inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
		inefectivo += '<textarea name="hallazgo_efectividad" class="form-control" style="width:180px" placeholder="Ingrese hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones_efectividad" class="form-control" style="width:180px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<b><span style="float: left;">Plan de acción: </span></b><br>';
		inefectivo += '<textarea name="plan_accion_efectividad" class="form-control" style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

		inefectivo += '<input type="date" name="fecha_plan_efectividad" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"></textarea><br>';

		inefectivo += '<select name="responsable_plan_efectividad" class="form-control" style="width:180px">';
		inefectivo += '<option value="" disabled selected>Responsable</option>';
		@foreach ($stakeholders as $stakeholder)
			inefectivo += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
		@endforeach

		inefectivo += '</select>';
		inefectivo += '<br><input type="file" name="file_efectividad" id="file2" class="inputfile" />';
		inefectivo += '<label for="file2">Cargue evidencia</label></div>';


		$("#datos_efectividad").html(inefectivo);
		$("#datos_efectividad").fadeIn(500);
	}
	else if ($("#efectividad").val() == 1)
	{
		var efectivo = '<br><textarea name="comentarios_efectividad" class="form-control" style="width:180px" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
		
		efectivo += '<br><input type="file" name="file_efectividad" id="file2" class="inputfile" />';
		efectivo += '<label for="file2">Cargue evidencia</label></div>';

		$("#datos_efectividad").html(efectivo);
		$("#datos_efectividad").fadeIn(500);
	}
	else
	{
		$("#datos_efectividad").empty();
	}
}

function test_sustantiva()
{
	if ($("#sustantiva").val() == 2)
	{
		var inefectivo = '<br>';
		inefectivo += "<select name='clasificacion_sustantiva' style='width:180px;' class='form-control'>";
		inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
		inefectivo += "<option value='0'>Oportunidad de mejora</option>";
		inefectivo += "<option value='1'>Deficiencia</option>";
		inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
		inefectivo += '<textarea name="hallazgo_sustantiva" class="form-control" style="width:180px" placeholder="Ingrese hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones_sustantiva" class="form-control" style="width:180px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<b><span style="float: left;">Plan de acción: </span></b><br>';
		inefectivo += '<textarea name="plan_accion_sustantiva" class="form-control" style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

		inefectivo += '<input type="date" name="fecha_plan_sustantiva" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"></textarea><br>';

		inefectivo += '<select name="responsable_plan_sustantiva" class="form-control" style="width:180px">';
		inefectivo += '<option value="" disabled selected>Responsable</option>';
		@foreach ($stakeholders as $stakeholder)
			inefectivo += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
		@endforeach

		inefectivo += '</select>';
		
		inefectivo += '<br><input type="file" name="file_sustantiva" id="file3" class="inputfile" />';
		inefectivo += '<label for="file3">Cargue evidencia</label></div>';


		$("#datos_sustantiva").html(inefectivo);
		$("#datos_sustantiva").fadeIn(500);
	}
	else if ($("#sustantiva").val() == 1)
	{
		var efectivo = '<br><textarea name="comentarios_sustantiva" class="form-control" style="width:180px" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
		
		efectivo += '<br><input type="file" name="file_sustantiva" id="file3" class="inputfile" />';
		efectivo += '<label for="file3">Cargue evidencia</label></div>';

		$("#datos_sustantiva").html(efectivo);
		$("#datos_sustantiva").fadeIn(500);
	}
	else
	{
		$("#datos_sustantiva").empty();
	}
}

function test_cumplimiento()
{
	if ($("#cumplimiento").val() == 2)
	{
		var inefectivo = '<br>';
		inefectivo += "<select name='clasificacion_cumplimiento' style='width:180px;' class='form-control'>";
		inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
		inefectivo += "<option value='0'>Oportunidad de mejora</option>";
		inefectivo += "<option value='1'>Deficiencia</option>";
		inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
		inefectivo += '<textarea name="hallazgo_cumplimiento" class="form-control" style="width:180px" placeholder="Ingrese hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones_cumplimiento" class="form-control" style="width:180px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<b><span style="float: left;">Plan de acción: </span></b><br>';
		inefectivo += '<textarea name="plan_accion_cumplimiento" class="form-control" style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

		inefectivo += '<input type="date" name="fecha_plan_cumplimiento" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"></textarea><br>';

		inefectivo += '<select name="responsable_plan_cumplimiento" class="form-control" style="width:180px">';
		inefectivo += '<option value="" disabled selected>Responsable</option>';
		@foreach ($stakeholders as $stakeholder)
			inefectivo += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
		@endforeach

		inefectivo += '</select>';
		
		inefectivo += '<br><input type="file" name="file_cumplimiento" id="file4" class="inputfile" />';
		inefectivo += '<label for="file4">Cargue evidencia</label></div>';


		$("#datos_cumplimiento").html(inefectivo);
		$("#datos_cumplimiento").fadeIn(500);
	}
	else if ($("#cumplimiento").val() == 1)
	{
		var efectivo = '<br><textarea name="comentarios_cumplimiento" class="form-control" style="width:180px" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
		
		efectivo += '<br><input type="file" name="file_cumplimiento" id="file4" class="inputfile" />';
		efectivo += '<label for="file4">Cargue evidencia</label></div>';

		$("#datos_cumplimiento").html(efectivo);
		$("#datos_cumplimiento").fadeIn(500);
	}
	else
	{
		$("#datos_cumplimiento").empty();
	}
}

</script>
@stop

