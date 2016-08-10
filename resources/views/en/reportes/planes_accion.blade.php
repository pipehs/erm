@extends('en.master')

@section('title', 'Action Plans')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="planes_accion">Action Plans</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Action Plans</span>
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
	<p>On this section you will be able to see the action plans for each organization with their corresponding information</p>

      	{!!Form::open()!!}
				<div class="form-group">
							{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('organization',$organizations,
								 	   null, 
								 	   ['id' => 'organization','placeholder'=>'- Select -'])!!}
							</div>
				</div>

				{!!Form::close()!!}
				<br>
				<br>
				<hr>
				<table id="planesaccion" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
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
	$("#organization").change(function() {


			if ($("#organization").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					//reseteamos matriz
					$("#planesaccion").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>Audit Plan</th><th>Audit</th><th>Issue founded</th>";
					table_head += "<th>Action Plan</th><th>Status</th><th>Creation date</th><th>Plan Deadline</th>";
					table_head += "</thead>";

					//Añadimos la imagen de carga en el contenedor
					$('#planesaccion').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('genplanes_accion.'+$("#organization").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#planesaccion").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);


							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Plan_de_auditoría + '</td><td>' + this.Auditoría + '</td><td>';
								table_row += this.Debilidad + '</td><td>' + this.Plan_de_acción + '</td><td>' + this.Estado;
								table_row += '</td><td>' + this.Fecha_creación +'</td><td>' + this.Fecha_final + '</td></tr>';
							});

							$("#planesaccion").append(table_row);
					});

					var value = $("#organization").val();
					//agregamos botón para exportar y array con datos
					var insert = "<input type='hidden' name='datos[]' value='" + $("#organization").val() + "'>";
					insert += '<button type="button" id="btnExport" class="btn btn-success">Export Excel</button>';
					$("#boton_exportar").html(insert);


					$("#btnExport").click(function(e) {
						
					        window.location.href = "genexcelplan."+value;
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