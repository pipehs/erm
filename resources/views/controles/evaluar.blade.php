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

				//obtenemos evaluación (si es que hay) de control seleccionado
				$.get('controles.get_evaluation.'+$("#control_id").val(), function (result) {

					if (result == "null")
					{
						$('#table_evaluacion').empty();
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

						$('#boton-guardar').html('<center><button name="guardar" class="btn btn-success">Guardar evaluación</button></center>');
					}
					else
					{
						var datos = JSON.parse(result);
						
						$(datos).each( function() {
							id_eval = this.id;
						});
						$('#table_evaluacion').empty();
						$('#boton-guardar').empty();
						var table_row = '<center><b>Indique si desea modificar su última evaluación o si desea agregar una nueva</b></center>';

						table_row += '<br><center><button name="new_eval" class="btn btn-success" onchange="newEval()">Nueva evaluación</button>';
						table_row += '&nbsp;&nbsp;&nbsp;&nbsp;';
						table_row += '<button name="edit_eval" class="btn btn-primary" onchange="editEval('+id_eval+')">Editar evaluación</button></center>';
					}
					
					$('#table_evaluacion').append(table_row);
					$('#table_evaluacion').fadeIn(500);

				});	
			}

			else
			{
				$('#table_evaluacion').fadeOut(500);
				$('#table_evaluacion').empty();
			}
});

function test_diseno() //ESTE SI FUNCIONA!!!!!!
{
	agregarCampos("diseno");
}

function test_efectividad()
{
	agregarCampos("efectividad");
}

function test_sustantiva()
{
	agregarCampos("sustantiva")
}

function test_cumplimiento()
{
	agregarCampos("cumplimiento");
}

//agrega campos a cada una de las pruebas
function agregarCampos(prueba)
{

	if ($("#"+prueba).val() == 2)
	{
		var inefectivo = '<br>';
		inefectivo += "<select name='clasificacion_"+prueba+"' style='width:180px;' class='form-control'>";
		inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
		inefectivo += "<option value='0'>Oportunidad de mejora</option>";
		inefectivo += "<option value='1'>Deficiencia</option>";
		inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";

		inefectivo += '<input type="text" name="name_hallazgo_'+prueba+'" class="form-control" style="width:180px" placeholder="Nombre hallazgo"><br>';
		inefectivo += '<textarea name="description_hallazgo_'+prueba+'" class="form-control" style="width:180px" placeholder="Descripción hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones_'+prueba+'" class="form-control" style="width:180px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<b><span style="float: left;">Plan de acción: </span></b><br>';
		inefectivo += '<textarea name="plan_accion_'+prueba+'" class="form-control" style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

		inefectivo += '<input type="date" name="fecha_plan_'+prueba+'" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"></textarea><br>';

		inefectivo += '<select name="responsable_plan_'+prueba+'" class="form-control" style="width:180px">';
		inefectivo += '<option value="" disabled selected>Responsable</option>';
		@foreach ($stakeholders as $stakeholder)
			inefectivo += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
		@endforeach

		inefectivo += '</select>';

		inefectivo += '<br><input type="file" name="file_'+prueba+'" id="file'+prueba+'" class="inputfile" />';
		inefectivo += '<label for="file'+prueba+'">Cargue evidencia</label></div>';

		identificador = "#datos_"+prueba;
		$(identificador).html(inefectivo);
		$(identificador).fadeIn(500);
	}
	else if ($("#"+prueba).val() == 1)
	{
		var efectivo = '<br><textarea name="comentarios_'+prueba+'" class="form-control" style="width:180px" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
		
		efectivo += '<br><input type="file" name="file_'+prueba+'" id="file'+prueba+'" class="inputfile" />';
		efectivo += '<label for="file'+prueba+'">Cargue evidencia</label></div>';
		identificador = "#datos_"+prueba;
		$(identificador).html(efectivo);
		$(identificador).fadeIn(500);
	}
	else
	{
		identificador = "#datos_"+prueba;
		$(identificador).empty();
	}
}

function editEval(id_eval)
{
	$('#table_evaluacion').empty();
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

						$('#boton-guardar').html('<center><button name="guardar" class="btn btn-success">Guardar evaluación</button></center>');
						$('#table_evaluacion').append(table_row);
						$('#table_evaluacion').fadeIn(500);
}

function newEval()
{

}

</script>
@stop

