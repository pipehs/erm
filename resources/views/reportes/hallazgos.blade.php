@extends('master')

@section('title', 'Reporte de Hallazgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="planes_accion">Hallazgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Hallazgos</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver el reporte de hallazgos de cada organización con su información correspondiente.</p>

      	{!!Form::open()!!}
				<div class="form-group">
							{!!Form::label('Seleccione tipo',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('type',['0'=>'Hallazgos de procesos','1'=>'Hallazgos de objetivos'],
								 	   null, 
								 	   ['id' => 'type','placeholder'=>'- Seleccione -'])!!}
							</div>
				</div>

				{!!Form::close()!!}
				<br>
				<br>
				<hr>
				<table id="hallazgos" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>
		
				<div id="boton_exportar">
				</div>

      </div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>

//Mostraremos planes de accion
	$("#type").change(function() {


			if ($("#type").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
				if ($("#type").val() == 0) //Se seleccionó procesos
				{
					//reseteamos matriz

					$("#hallazgos").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = '<table class="table table-bordered table-striped table-hover table-heading table-datatable auditorias2" id="datatable-2" style="font-size:11px">';
					table_head += "<thead>";
					table_head += "<th>Hallazgo</th>";
					table_head += "<th>Riesgo</th>";
					table_head += "<th>Control</th>";
					table_head += "<th>Proceso</th>";
					table_head += "<th>Recomendación</th>";
					table_head += "<th>Plan de acción</th>";
					table_head += "<th>Responsable plan de acción</th>";
					table_head += "<th>Fecha límite plan de acción</th>";
					table_head += "<th>Plan de auditoría</th>";
					table_head += "<th>Auditoría</th>";



					//Añadimos la imagen de carga en el contenedor
					$('#hallazgos').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('genissues_report.'+$("#type").val(), function (result) {
						
							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#hallazgos").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);


							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
										
										table_row += '<tr><td>' + this.Hallazgo + '</td><td>' + this.Riesgo + '</td><td>';
										table_row += this.Control +'</td><td>';
										table_row += this.Proceso + '</td><td>' + this.Recomendación + '</td><td>' + this.Plan_de_acción;
										table_row += '</td><td>' + this.Responsable_plan +'</td>';
										table_row += '<td>' + this.Fecha_final_plan + '</td><td>';
										table_row += this.Auditoría + '</td><td>' + this.Plan_de_auditoría + '</td></tr>';
									});
					
									$("#hallazgos").append(table_row);
					});		
				}

				else if ($("#type").val() == 1) //Se seleccionó objetivos
				{
					//reseteamos matriz

					$("#hallazgos").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = '<table class="table table-bordered table-striped table-hover table-heading table-datatable auditorias2" id="datatable-2" style="font-size:11px">';
					table_head += "<thead>";
					table_head += "<th>Hallazgo</th>";
					table_head += "<th>Riesgo</th>";
					table_head += "<th>Control</th>";
					table_head += "<th>Objetivo</th>";
					table_head += "<th>Recomendación</th>";
					table_head += "<th>Plan de acción</th>";
					table_head += "<th>Responsable plan de acción</th>";
					table_head += "<th>Fecha límite plan de acción</th>";
					table_head += "<th>Plan de auditoría</th>";
					table_head += "<th>Auditoría</th>";



					//Añadimos la imagen de carga en el contenedor
					$('#hallazgos').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('genissues_report.'+$("#type").val(), function (result) {
						
							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#hallazgos").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);


							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
										
										table_row += '<tr><td>' + this.Hallazgo + '</td><td>' + this.Riesgo + '</td><td>';
										table_row += this.Control +'</td><td>';
										table_row += this.Objetivo + '</td><td>' + this.Recomendación + '</td><td>' + this.Plan_de_acción;
										table_row += '</td><td>' + this.Responsable_plan +'</td>';
										table_row += '<td>' + this.Fecha_final_plan + '</td><td>';
										table_row += this.Auditoría + '</td><td>' + this.Plan_de_auditoría + '</td></tr>';
									});
					
									$("#hallazgos").append(table_row);
					});	
				}

					var value = $("#type").val();
					//agregamos botón para exportar y array con datos
					var insert = "<input type='hidden' name='datos[]' value='" + $("#organization").val() + "'>";
					insert += '<button type="button" id="btnExport" class="btn btn-success">Exportar Excel</button>';
					$("#boton_exportar").html(insert);


					$("#btnExport").click(function(e) {
						
					        window.location.href = "genexcelissues."+value;
					        e.preventDefault();
					});
				
			}

			else
			{
				//REseteamos datos
			}

	    });

</script>
@stop