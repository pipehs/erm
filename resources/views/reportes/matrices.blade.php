@extends('master')

@section('title', 'Matrices de control')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="heatmap">Matriz de control</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de control</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz de control para riesgos de negocio o de procesos. 
      En caso de que desee ver la matriz de control para los riesgos de negocio, deber&aacute; especificar si desea ver
      la matriz para todas las organizaciones o para alguna en espec&iacute;fica.</p>

      	{!!Form::open()!!}
				<div class="form-group">
							{!!Form::label('Seleccione tipo',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('type',['0'=>'Matriz para controles de procesos','1'=>'Matriz para controles de negocio'],
								 	   null, 
								 	   ['id' => 'type','placeholder'=>'- Seleccione -'])!!}
							</div>
				</div>

				{!!Form::close()!!}
				<br>
				<br>
				<hr>
				<table id="matrizcontrol" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
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

//Mostraremos matriz de controles para riesgos de procesos o de negocio
	$("#type").change(function() {


			if ($("#type").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
				if ($("#type").val() == 0) //Se seleccionó Riesgos / Procesos, por lo que se generará la matriz con estos controles
				{
					//reseteamos matriz

					$("#matrizcontrol").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>ID Control</th><th>Descripci&oacute;n Control</th><th>Responsable</th>";
					table_head += "<th>Tipo</th><th>Periodicidad</th><th>Propósito</th><th>Costo control</th><th>Evidencia</th>";
					table_head += "<th>Riesgo(s) / Subproceso(s)</th></thead>";

					//Añadimos la imagen de carga en el contenedor
					$('#matrizcontrol').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					
      				
					$.get('genmatriz.'+$("#type").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#matrizcontrol").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Control + '</td><td>' + this.Descripción + '</td><td>';
								table_row += this.Responsable + '</td><td>' + this.Tipo + '</td><td>' + this.Periodicidad +'</td>';
								table_row += '<td>' + this.Propósito + '</td><td>' + this.Costo_control +'</td>';
								table_row += '<td>' + this.Evidencia + '</td><td>' + this.Riesgo_Subproceso_Organización +'</td></tr>';
							});

							$("#matrizcontrol").append(table_row);
					});
				}

				else if ($("#type").val() == 1) //Se seleccionó Riesgos / Objetivos
				{
					//reseteamos matriz

					$("#matrizcontrol").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>ID Control</th><th>Descripci&oacute;n Control</th><th>Responsable</th>";
					table_head += "<th>Tipo</th><th>Periodicidad</th><th>Propósito</th><th>Costo control</th><th>Evidencia</th>";
					table_head += "<th>Riesgo(s) / Objetivos(s) / Organizaci&oacute;n</th></thead>";

					$('#matrizcontrol').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					//generamos matriz a través de JSON y PHP
					$.get('genmatriz.'+$("#type").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#matrizcontrol").html(table_head);
							
							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Control + '</td><td>' + this.Descripción + '</td><td>';
								table_row += this.Riesgo_Objetivo_Organización + '</td><td>' + this.Tipo + '</td><td>' + this.Periodicidad +'</td>';
								table_row += '<td>' + this.Propósito + '</td><td>' + this.Responsable + '</td><td>' + this.Evidencia +'</td>';
								table_row += '<td>' + this.Costo_esperado + '</td><td>' + this.Riesgo_Objetivo_Organización + '</td></tr>';
							});

							$("#matrizcontrol").append(table_row);
					});
				}
				
			}

			else
			{
				//REseteamos datos
			}

			var value = $("#type").val();
			//agregamos botón para exportar y array con datos
			var insert = "<input type='hidden' name='datos[]' value='" + $("#type").val() + "'>";
			insert += '<button type="button" id="btnExport" class="btn btn-success">Exportar Excel</button>';
			$("#boton_exportar").html(insert);

				if (value == 0)
				{
					$("#btnExport").click(function(e) {
			        window.location.href = "{{URL::to('genexcel.0')}}"
			        e.preventDefault();
			    });
			  }

			  else if (value == 1)
				{
					$("#btnExport").click(function(e) {
			        window.location.href = "{{URL::to('genexcel.1')}}"
			        e.preventDefault();
			    });
			  }
	    });

</script>
@stop