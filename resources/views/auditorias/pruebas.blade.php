@extends('master')

@section('title', 'Auditor&iacute;as - Pruebas de auditor&iacute;a')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_auditoria">Auditor&iacute;as</a></li>
			<li><a href="pruebas">Pruebas de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Pruebas de auditor&iacute;as</span>
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
	      <p>En esta secci&oacute;n podr&aacute; generar reportes de las pruebas de auditor&iacute;a generadas en el sistema.</p>

				@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

		{!! link_to_route('crear_pruebas', $title = 'Agregar Nueva prueba', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

	{!!Form::open()!!}
				<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Seleccione plan de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_plan_id',$audit_plans,null, 
							 	   ['id' => 'audit_plans','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
					<br>
					<div class="form-group">
						{!!Form::label('Seleccione auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="audit" id="audit">
								<!-- Aquí se agregarán las auditorías relacionadas al plan seleccionado a través de Jquery -->
							</select>
						</div>
					</div>

				{!!Form::close()!!}
				<br>
				<br>
				<hr>
				<table id="matrizpruebas" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>
		
				<div id="boton_exportar">
				</div>

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/audits.js')!!}
<script>
$("#audit").change(function() {
			if ($("#audit").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{

					//reseteamos matriz

					$("#matrizpruebas").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>Plan de auditor&iacute;a</th><th>Auditor&iacute;a</th><th>Nombre prueba</th>";
					table_head += "<th>Descripci&oacute;n</th>Actividades</th>";
					table_head += "</thead>";

					//Añadimos la imagen de carga en el contenedor
					$('#matrizpruebas').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('auditorias.getpruebas.'+$("#audit").val(), function (result2) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#matrizpruebas").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos2 = JSON.parse(result2);
							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos2).each( function() {	
								
								table_row += '<tr><td>' + this.audit_plan_name + '</td><td>' + this.audit_name + '</td><td>' + this.name +' / ';
								table_row += this.description + '</td><td>' + this.activities + '</td>';
							});

							$("#matrizpruebas").append(table_row);
					});
			}
});
</script>
@stop