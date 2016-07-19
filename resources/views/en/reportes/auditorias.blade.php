@extends('master')

@section('title', 'Auditor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="planes_accion">Auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Auditor&iacute;as</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver las auditor&iacute;as de cada organización con su información correspondiente.</p>

      	{!!Form::open()!!}
				<div class="form-group">
							{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('organization',$organizations,
								 	   null, 
								 	   ['id' => 'organization','placeholder'=>'- Seleccione -'])!!}
							</div>
				</div>

				{!!Form::close()!!}
				<br>
				<br>
				<hr>
				<div id="auditorias" style="display:none;">
					
				</div>
		
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
	$("#organization").change(function() {


			if ($("#organization").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					//reseteamos matriz

					$("#auditorias").removeAttr("style").show();

					//Seteamos cabecera
					var table_row = '<table class="table table-bordered table-striped table-hover table-heading table-datatable auditorias2" id="datatable-2" style="font-size:11px">';
					table_row += "<thead>";
					table_row += "<th>Plan de auditoría<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Auditoría<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Descripción auditoría<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Fecha inicio<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Fecha fin<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Proceso / Objetivo<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Riesgo<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Hallazgo<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Recomendación<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Plan de acción<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Estado plan de acción<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "<th>Fecha final plan acción<label><input type='text' placeholder='Filtrar' /></label></th>";
					table_row += "</thead>";

					//Añadimos la imagen de carga en el contenedor
					$('#auditorias').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('genaudit_report.'+$("#organization").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							//$("#auditorias").html(table_head);
							

							//var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);


							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Plan_de_auditoría + '</td><td>' + this.Auditoría + '</td><td>';
								table_row += this.Descripción_auditoría + '</td><td>' + this.Fecha_inicio + '</td><td>' + this.Fecha_fin;
								table_row += '</td><td>' + this.Proceso_Objetivo +'</td><td>' + this.Riesgo;
								table_row += '</td><td>' + this.Hallazgo + '</td><td>' + this.Recomendación + '</td><td>';
								table_row += this.Plan_de_acción + '</td><td>' + this.Estado + '</td><td>' + this.Fecha_final_plan + '</td></tr>';
							});
							$("#auditorias").empty();
							$("#auditorias").append(table_row);
					});

					var value = $("#organization").val();
					//agregamos botón para exportar y array con datos
					var insert = "<input type='hidden' name='datos[]' value='" + $("#organization").val() + "'>";
					insert += '<button type="button" id="btnExport" class="btn btn-success">Exportar Excel</button>';
					$("#boton_exportar").html(insert);


					$("#btnExport").click(function(e) {
						
					        window.location.href = "genexcelaudit."+value;
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