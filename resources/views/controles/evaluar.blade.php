@extends('master')

@section('title', 'Controles')

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

				<table id="table_evaluacion" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none; vertical-align: top;">
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
{!!Html::script('assets/js/controls_by_org.js')!!}
<script>
$("#control_id").change(function() {
		
			if ($("#control_id").val() != '') //Si es que se ha seleccionado valor válido de control
			{
				//obtenemos issue (si es que hay) de control seleccionado
				$.get('controles.get_description.'+$("#control_id").val(), function (result) {
					descripcion = JSON.parse(result);
				
				$('#cargando').fadeIn(1);
				//Añadimos la imagen de carga en el contenedor
				$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

				$('#cargando').delay(400).fadeOut(5);

				$('#table_evaluacion').empty();
				$('#boton-guardar').empty();
				var table_row = '<b><p style="font-size:large; text-align:left;">Descripción: '+descripcion+'</p><hr><br>'; 
				table_row += '<center><b>Indique si desea modificar su última evaluación (si es que existe) o si desea agregar una nueva</b></center>';

				table_row += '<br><center><button type="button" name="new_eval" class="btn btn-success" onclick="newEval()">Nueva evaluación</button>';
				table_row += '&nbsp;&nbsp;&nbsp;&nbsp;';
				table_row += '<button type="button" name="edit_eval" class="btn btn-primary" onclick="editEval('+$("#control_id").val()+')">Editar evaluación</button></center>';

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
	var id_eval = "NULL"; //para identificar que se esta creando una nueva evaluación y no editando
	agregarCampos("diseno",id_eval);
}

function test_efectividad()
{
	var id_eval = "NULL";
	agregarCampos("efectividad",id_eval);
}

function test_sustantiva()
{
	var id_eval = "NULL";
	agregarCampos("sustantiva",id_eval)
}

function test_cumplimiento()
{
	var id_eval = "NULL";
	agregarCampos("cumplimiento",id_eval);
}

function test_diseno_edit(eval) 
{
	agregarCampos("diseno",eval);
}

function test_efectividad_edit(eval)
{
	agregarCampos("efectividad",eval);
}

function test_sustantiva_edit(eval)
{
	agregarCampos("sustantiva",eval)
}

function test_cumplimiento_edit(eval)
{
	agregarCampos("cumplimiento",eval);
}

//agrega campos a cada una de las pruebas
function agregarCampos(prueba,eval)
{
	if ($("#"+prueba).val() == 2)
	{	
		//obtenemos issue (si es que hay) de control seleccionado
		$.get('controles.get_issue.'+eval, function (result) {
			verificador = JSON.parse(result); //se usa en caso de que se cambie el resultado de efectivo a inefectivo
			//alert(result);
			//alert(verificador.issue);
			if (result == "null" || verificador.issue == null)
			{	
				inefectivo = '<br>';
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
			else
			{
				datos1 = JSON.parse(result);
				cont = 0;
				
				$(datos1).each(function() {
						inefectivo = '<br>';
						inefectivo += "<select name='clasificacion_"+prueba+"' style='width:180px;' class='form-control'>";

						if (this.issue.classification == 0)
						{
							inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
							inefectivo += "<option value='0' selected>Oportunidad de mejora</option>";
							inefectivo += "<option value='1'>Deficiencia</option>";
							inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
						}
						else if (this.issue.classification == 1)
						{
							inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
							inefectivo += "<option value='0'>Oportunidad de mejora</option>";
							inefectivo += "<option value='1' selected>Deficiencia</option>";
							inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
						}
						else if (this.issue.classification == 2)
						{
							inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
							inefectivo += "<option value='0'>Oportunidad de mejora</option>";
							inefectivo += "<option value='1'>Deficiencia</option>";
							inefectivo += "<option value='2' selected>Debilidad significativa</option></select><br>";
						}
						else
						{
							inefectivo += "<option value='' disabled selected>Seleccione Clasificación</option>";
							inefectivo += "<option value='0'>Oportunidad de mejora</option>";
							inefectivo += "<option value='1'>Deficiencia</option>";
							inefectivo += "<option value='2'>Debilidad significativa</option></select><br>";
						}

						inefectivo += '<input type="text" name="name_hallazgo_'+prueba+'" class="form-control" style="width:180px" value="'+this.issue.name+'" placeholder="Nombre hallazgo"><br>';
						inefectivo += '<textarea name="description_hallazgo_'+prueba+'" class="form-control" style="width:180px" placeholder="Descripción hallazgo">'+this.issue.description+'</textarea><br>';
						inefectivo += '<textarea name="recomendaciones_'+prueba+'" class="form-control" style="width:180px"  placeholder="Recomendaciones hallazgo">'+this.issue.recommendations+'</textarea><br>';

						identificador = "#datos_"+prueba;
						$(identificador).html(inefectivo);
						//plan de acción
						//alert("ID: "+this.issue.id);
						$.get('auditorias.get_action_plan.'+this.issue.id,function(result2) {
							//cont++;
							//alert(result2);
							if (result2 == "null")
							{
									inefectivo2 = '<b><span style="float: left;">Plan de acción: </span></b><br>';
									inefectivo2 += '<textarea name="plan_accion_'+prueba+'" class="form-control" rows="3"	 style="width:180px" placeholder="Ingrese plan de acción"></textarea><br>';

									inefectivo2 += '<input type="date" name="fecha_plan_'+prueba+'" class="form-control" style="width:180px" title="Ingrese fecha de término del plan"><br>';

									inefectivo2 += '<select name="responsable_plan_'+prueba+'" class="form-control" style="width:180px">';
									inefectivo2 += '<option value="" disabled selected>Responsable</option>';
									@foreach ($stakeholders as $stakeholder)
										inefectivo2 += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
									@endforeach

									inefectivo2 += '</select>';

							}
							else
							{

								datos2 = JSON.parse(result2);
								inefectivo2 = '<b><span style="float: left;">Plan de acción: </span></b><br>';
								inefectivo2 += '<textarea name="plan_accion_'+prueba+'" class="form-control" style="width:180px" rows="3" placeholder="Ingrese plan de acción">'+datos2.description+'</textarea><br>';

								inefectivo2 += '<input type="date" name="fecha_plan_'+prueba+'" class="form-control" style="width:180px" value="'+datos2.final_date+'" title="Ingrese fecha de término del plan"><br>';

								inefectivo2 += '<select name="responsable_plan_'+prueba+'" class="form-control" style="width:180px">';
								inefectivo2 += '<option value="" disabled>Responsable</option>';
								@foreach ($stakeholders as $stakeholder)
									if ({{ $stakeholder['id']}} == datos2.rut)
									{
										inefectivo2 += '<option value="{{ $stakeholder["id"] }}" selected>{{ $stakeholder["name"] }}</option>';
									}
									else
									{
										inefectivo2 += '<option value="{{ $stakeholder["id"] }}">{{ $stakeholder["name"] }}</option>';
									}
									
								@endforeach

								inefectivo2 += '</select>';
							}
							identificador = "#datos_"+prueba;
							$(identificador).append(inefectivo2);
							
						});

				//hacemos ciclo aunque es solo una evidencia (ya que en otros casos, como notas, pueden ser más de uno, por lo que se debe dejar abierto para la opción)

				$(this.evidence).each(function(i,arc) {

							if (arc == null) 
							//if (this.evidence == null)
							{
								inefectivo3 = '<br><input type="file" name="file_'+prueba+'" id="file'+prueba+'" class="inputfile" />';
								inefectivo3 += '<label for="file'+prueba+'">Cargue evidencia</label></div>';
							}
							else
							{
								inefectivo3 = '<div style="cursor:hand" id="descargar_'+arc.id+'" onclick="descargar(3,\''+arc.url+'\')"><font color="CornflowerBlue"><u>Descargar evidencia</u></font></div><br>';
							}

							identificador = "#datos_"+prueba;
							$(identificador).append(inefectivo3);
							$(identificador).fadeIn(500);
					});
				});

				
			}
		});
	}
	else if ($("#"+prueba).val() == 1)
	{
		//alert("holi "+eval);
		//obtenemos evaluación (si es que hay) de control seleccionado
		$.get('controles.get_evaluation2.'+eval, function (result) {
			//alert("holi 2"+eval);
			if (result != "null")
			{
				datos = JSON.parse(result);
				if (datos.comments == null)
				{
					var efectivo = '<br><textarea name="comentarios_'+prueba+'" class="form-control" style="width:180px" rows="3" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
				}
				else
				{
					var efectivo = '<br><textarea name="comentarios_'+prueba+'" class="form-control" style="width:180px" rows="3" placeholder="Ingrese comentarios (opcional)">'+datos.comments+'</textarea><br>';
				}
				
				$(datos.evidence).each(function(i,arc) {

					if (arc == null) 				
					{
						efectivo += '<br><input type="file" name="file_'+prueba+'" id="file'+prueba+'" class="inputfile" />';
						efectivo += '<label for="file'+prueba+'">Cargue evidencia</label></div>';
					}
					else
					{
						efectivo += '<div style="cursor:hand" id="descargar_'+arc.id+'" onclick="descargar(3,\''+arc.url+'\')"><font color="CornflowerBlue"><u>Descargar evidencia</u></font></div><br>';
					}
				});
			}
			else
			{
				var efectivo = '<br><textarea name="comentarios_'+prueba+'" class="form-control" style="width:180px" rows="3" placeholder="Ingrese comentarios (opcional)"></textarea><br>';
				efectivo += '<br><input type="file" name="file_'+prueba+'" id="file'+prueba+'" class="inputfile" />';
				efectivo += '<label for="file'+prueba+'">Cargue evidencia</label></div>';
			}
			
			identificador = "#datos_"+prueba;
			$(identificador).html(efectivo);
			$(identificador).fadeIn(500);
		});
	}
	else
	{
		identificador = "#datos_"+prueba;
		$(identificador).empty();
	}
}

function editEval(control_id)
{
	//obtenemos evaluación (si es que hay) de control seleccionado
	$.get('controles.get_evaluation.'+control_id, function (result) {

		if (result == "null")
		{
			swal('Error','No hay evaluaciones previas');
		}
		else
		{
			$('#table_evaluacion').empty();
			//Seteamos cabecera
			var table_head = "<thead>";
			table_head += "<th>Diseño</th><th>Efectividad operativa</th><th>Prueba sustantiva</th><th>Prueba de cumplimiento</th>";
			table_head += "</thead>";
			//seteremos todas las variables de cada una de las pruebas, para ver cuales tienen respuestas y cuales no


				diseno2 = "<td><select name='diseno' id='diseno' style='width:180px; vertical-align:top' class='form-control' onchange='test_diseno()'>";
				diseno2 += "<option value=''>Seleccione Resultado</option>";
				diseno2 += "<option value='1'>Efectivo</option>";
				diseno2 += "<option value='2'>Inefectivo</option></select>";
				diseno2 += "<div id='datos_diseno' style='display: none;'></div></td>";

				efectividad2 = "<td><select name='efectividad' style='width:180px' class='form-control' id='efectividad' onchange='test_efectividad()'>";
				efectividad2 += "<option value=''>Seleccione Resultado</option>";
				efectividad2 += "<option value='1'>Efectivo</option>";
				efectividad2 += "<option value='2'>Inefectivo</option></select>";
				efectividad2 += "<div id='datos_efectividad' style='display: none;'></div></td>";

				sustantiva2 = "<td><select name='sustantiva' style='width:180px' class='form-control' id='sustantiva' onchange='test_sustantiva()'>";
				sustantiva2 += "<option value=''>Seleccione Resultado</option>";
				sustantiva2 += "<option value='1'>Efectivo</option>";
				sustantiva2 += "<option value='2'>Inefectivo</option></select>";
				sustantiva2 += "<div id='datos_sustantiva' style='display: none;'></div></td>";

				cumplimiento2 = "<td><select name='cumplimiento' style='width:180px' class='form-control' id='cumplimiento' onchange='test_cumplimiento()'>";
				cumplimiento2 += "<option value=''>Seleccione Resultado</option>";
				cumplimiento2 += "<option value='1'>Efectivo</option>";
				cumplimiento2 += "<option value='2'>Inefectivo</option></select>";
				cumplimiento2 += "<div id='datos_cumplimiento' style='display: none;'></div></td>";
			

			var datos = JSON.parse(result);

			$(datos).each( function() {
					if (this.kind == 0)
					{
						diseno2 = "<td><select name='diseno' id='diseno' style='width:180px; vertical-align:top' class='form-control' onchange='test_diseno_edit("+this.id+")'>";

						if (this.results == 1)
						{
							diseno2  += "<option value=''>Seleccione Resultado</option>";
							diseno2 += "<option value='1' selected>Efectivo</option>";
							diseno2  += "<option value='2'>Inefectivo</option></select>";
						}
						else if (this.results == 2)
						{
							diseno2 += "<option value=''>Seleccione Resultado</option>";
							diseno2 += "<option value='1'>Efectivo</option>";
							diseno2 += "<option value='2' selected>Inefectivo</option></select>";
						}
						else
						{
							diseno2 += "<option value=''>Seleccione Resultado</option>";
							diseno2 += "<option value='1'>Efectivo</option>";
							diseno2 += "<option value='2'>Inefectivo</option></select>";
						}

						diseno2 += "<div id='datos_diseno' style='display: none;'></div></td>";
					}


					if (this.kind == 1)
					{
						efectividad2 = "<td><select name='efectividad' style='width:180px' class='form-control' id='efectividad' onchange='test_efectividad_edit("+this.id+")'>";

						if (this.results == 1)
						{
							efectividad2 += "<option value=''>Seleccione Resultado</option>";
							efectividad2 += "<option value='1' selected>Efectivo</option>";
							efectividad2 += "<option value='2'>Inefectivo</option></select>";
						}
						else if (this.results == 2)
						{
							efectividad2 += "<option value=''>Seleccione Resultado</option>";
							efectividad2+= "<option value='1'>Efectivo</option>";
							efectividad2+= "<option value='2' selected>Inefectivo</option></select>";
						}
						else
						{
							efectividad2+= "<option value=''>Seleccione Resultado</option>";
							efectividad2+= "<option value='1'>Efectivo</option>";
							efectividad2+= "<option value='2'>Inefectivo</option></select>";
						}

						efectividad2 += "<div id='datos_efectividad' style='display: none;'></div></td>";
					}

					if (this.kind == 2)
					{
						sustantiva2 = "<td><select name='sustantiva' style='width:180px' class='form-control' id='sustantiva' onchange='test_sustantiva_edit("+this.id+")'>";

						if (this.results == 1)
						{
							sustantiva2  += "<option value=''>Seleccione Resultado</option>";
							sustantiva2  += "<option value='1' selected>Efectivo</option>";
							sustantiva2  += "<option value='2'>Inefectivo</option></select>";
						}
						else if (this.results == 2)
						{
							sustantiva2 += "<option value=''>Seleccione Resultado</option>";
							sustantiva2 += "<option value='1'>Efectivo</option>";
							sustantiva2 += "<option value='2' selected>Inefectivo</option></select>";
						}
						else
						{
							sustantiva2 += "<option value=''>Seleccione Resultado</option>";
							sustantiva2 += "<option value='1'>Efectivo</option>";
							sustantiva2 += "<option value='2'>Inefectivo</option></select>";
						}

						sustantiva2 += "<div id='datos_sustantiva' style='display: none;'></div></td>";
					}
				
					if (this.kind == 3)
					{
						cumplimiento2 = "<td><select name='cumplimiento' style='width:180px' class='form-control' id='cumplimiento' onchange='test_cumplimiento_edit("+this.id+")'>";

						if (this.results == 1)
						{
							cumplimiento2 += "<option value=''>Seleccione Resultado</option>";
							cumplimiento2 += "<option value='1' selected>Efectivo</option>";
							cumplimiento2 += "<option value='2'>Inefectivo</option></select>";
						}
						else if (this.results == 2)
						{
							cumplimiento2 += "<option value=''>Seleccione Resultado</option>";
							cumplimiento2 += "<option value='1'>Efectivo</option>";
							cumplimiento2 += "<option value='2' selected>Inefectivo</option></select>";
						}
						else
						{
							cumplimiento2 += "<option value=''>Seleccione Resultado</option>";
							cumplimiento2 += "<option value='1'>Efectivo</option>";
							cumplimiento2 += "<option value='2'>Inefectivo</option></select>";
						}

						cumplimiento2 += "<div id='datos_cumplimiento' style='display: none;'></div></td>";
					}

				$('#table_evaluacion').html(table_head);
				var boton = '<center><button name="guardar" value=1 class="btn btn-success">Guardar actualización</button>';
				boton += '&nbsp;&nbsp;&nbsp;';
				boton += '<button name="nueva" value=1 class="btn btn-danger" onclick="newEval()">Agregar nueva evaluación</button></center>';
				$('#boton-guardar').html(boton);

				var pruebas = '<tr>';
				pruebas += diseno2;
				pruebas += efectividad2;
				pruebas += sustantiva2;
				pruebas += cumplimiento2;
				pruebas += '</tr>';

				$('#table_evaluacion').append(pruebas);
				$('#table_evaluacion').fadeIn(500);

			});
			
			//probamos diferencias en tiempo ya que algunos no los muestra por cargar muy rápido
			setTimeout(function() {
		         $('#diseno').change();
		    }, 200);
		    setTimeout(function() {
		         $('#efectividad').change();
		    }, 800);
		    setTimeout(function() {
		         $('#sustantiva').change();
		    }, 400);
		    setTimeout(function() {
		         $('#cumplimiento').change();
		    }, 600);
			//$('#diseno').change(200);
			//$('#efectividad').change(400);
			//$('#sustantiva').change(600);
			//$('#cumplimiento').change(800);
		}
	});
}
		
function newEval()
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
	var boton = '<center><button name="guardar" value=0 class="btn btn-success">Guardar evaluación</button>'
	boton += '&nbsp;&nbsp;&nbsp;';
	boton += '<button type="button" name="nueva" value=1 class="btn btn-danger" onclick="editEval('+$("#control_id").val()+')">Editar última evaluación</button></center>';
	$('#boton-guardar').html(boton);
	$('#table_evaluacion').append(table_row);
	$('#table_evaluacion').fadeIn(500)
}

</script>

{!!Html::script('assets/js/descargar.js')!!}
@stop

