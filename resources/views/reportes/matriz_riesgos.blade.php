@extends('master')

@section('title', 'Matriz de riesgos')

@section('content')
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="heatmap">Matriz de riesgos</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de riesgos</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz para los riesgos de negocio y/o de procesos. </p>

			{!! link_to_route('genmatrizriesgos', $title = 'Matriz Riesgos de Proceso', $parameters = 0, $attributes = ['class'=>'btn btn-primary']) !!}
					&nbsp;&nbsp;
			{!! link_to_route('genmatrizriesgos', $title = 'Matriz Riesgos de Negocio', $parameters = 1, $attributes = ['class'=>'btn btn-success']) !!}

		@if (isset($datos))
			<hr>
			<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">

			@if ($value == 0)
				<thead>
				<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>ID Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Categoría<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causas<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Efectos<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Pérdida esperada<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
					<td>{{$dato['Proceso']}}</td>
					<td>{{$dato['Subproceso']}}</td>
					<td>{{$dato['Riesgo']}}</td>
					<td>{{$dato['Descripción']}}</td>
					<td>{{$dato['Categoría']}}</td>
					<td>{{$dato['Causas']}}</td>
					<td>{{$dato['Efectos']}}</td>
					<td>{{$dato['Pérdida_esperada']}}</td>
					<td>{{$dato['Probabilidad']}}</td>
					<td>{{$dato['Impacto']}}</td>
					<td>{{$dato['Score']}}</td>
					<td>{{$dato['Fecha_expiración']}}</td>
					<td><ul>{{$dato['Controles']}}</ul></td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = 3, $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@elseif ($value == 1)
				<thead>
				<th>Organizaci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Objetivo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>ID Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripci&oacute;n Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Categoría<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Causas<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Efectos<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Pérdida esperada<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Impacto<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Probabilidad<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Fecha expiraci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Control<label><input type="text" placeholder="Filtrar" /></label></th>
				</thead>

				@foreach ($datos as $dato)
					<tr>
					<td>{{$dato['Organización']}}</td>
					<td>{{$dato['Objetivo']}}</td>
					<td>{{$dato['Riesgo']}}</td>
					<td>{{$dato['Descripción']}}</td>
					<td>{{$dato['Categoría']}}</td>
					<td>{{$dato['Causas']}}</td>
					<td>{{$dato['Efectos']}}</td>
					<td>{{$dato['Pérdida_esperada']}}</td>
					<td>{{$dato['Probabilidad']}}</td>
					<td>{{$dato['Impacto']}}</td>
					<td>{{$dato['Score']}}</td>
					<td>{{$dato['Fecha_expiración']}}</td>
					<td><ul>{{$dato['Controles']}}</ul></td>
					</tr>
				@endforeach

				</table>
		
				<div id="boton_exportar">
					{!! link_to_route('genexcel', $title = 'Exportar', $parameters = 4, $attributes = ['class'=>'btn btn-success']) !!}
				</div>
			@endif
		@endif

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
				if ($("#type").val() == 0) //Se seleccionó Riesgos / Procesos, por lo que se generará la matriz con estos riesgos
				{
					//reseteamos matriz

					$("#matrizriesgos").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>Proceso</th>";
					table_head += "<th >Subproceso(s)</th><th>ID Riesgo</th><th>Descripci&oacute;n Riesgo</th><th>Categoría</th>";
					table_head += "<th>Causa</th><th>Efecto</th><th>Pérdida esperada</th>Impacto<th>Probabilidad</th>";
					table_head += "<th>Impacto</th><th>Score</th><th>Fecha identificaci&oacute;n</th><th>Fecha expiraci&oacute;n</th><th>Control</th>";
					table_head += "</thead>";

					//Añadimos la imagen de carga en el contenedor
					$('#matrizriesgos').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP
      				
					$.get('genmatrizriesgos.'+$("#type").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#matrizriesgos").html(table_head);
							

							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							 
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los riesgos de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Proceso + '</td><td>' + this.Subproceso + '</td><td>' + this.Riesgo +'</td>';
								table_row += '<td>' + this.Descripción + '</td><td>' + this.Categoría + '<td>' + this.Causas + '</td><td>' + this.Efectos +'</td>';
								table_row += '<td>' + this.Pérdida_esperada + '</td><td>' + this.Probabilidad + '</td><td>' + this.Impacto +'</td>';
								table_row += '<td>' + this.Score +'</td><td>' + this.Fecha_identificación + '</td><td>' + this.Fecha_expiración + '</td><td>' + this.Controles +'</tr>';
							});

							$("#matrizriesgos").append(table_row);
					});
				}

				else if ($("#type").val() == 1) //Se seleccionó Riesgos / Objetivos
				{
					//reseteamos matriz

					$("#matrizriesgos").removeAttr("style").show();

					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>Organizaci&oacute;n</th><th>Objetivo</th><th>ID Riesgo</th>";
					table_head += "<th width='35%'>Descripción</th><th>Categoría</th><th>Causa(s)</th><th>Efecto(s)</th>";
					table_head += "<th>Pérdida esperada</th><th>Impacto</th><th>Probabilidad</th><th>Score</th><th>Controles</th></thead>";

					$('#matrizriesgos').html('<div><center><img src="../public/assets/img/loading.gif"/></center></div>');
					//generamos matriz a través de JSON y PHP

					//generamos matriz a través de JSON y PHP
					$.get('genmatrizriesgos.'+$("#type").val(), function (result) {

							//con la función html se BORRAN los datos existentes anteriormente (de existir)
							$("#matrizriesgos").html(table_head);
							
							var table_row ="";
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							//seteamos datos en tabla para riesgos a través de un ciclo por todos los controles de procesos
							$(datos).each( function() {	
								
								table_row += '<tr><td>' + this.Organización + '</td><td>' + this.Objetivo + '</td><td>' + this.Riesgo +'</td>';
								table_row += '<td>' + this.Descripción + '</td><td>' + this.Categoría + '</td><td>' + this.Causas +'</td>';
								table_row += '<td>' + this.Efectos + '</td><td>' + this.Pérdida_esperada + '</td><td>' + this.Impacto +'</td><td>' + this.Probabilidad + '</td>';
								table_row += '<td>' + this.Score + '</td><td>' + this.Controles + '</td></tr>';
							});

							$("#matrizriesgos").append(table_row);
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
			        window.location.href = "{{URL::to('genexcel.3')}}"
			        e.preventDefault();
			    });
			  }

			  else if (value == 1)
				{
					$("#btnExport").click(function(e) {
			        window.location.href = "{{URL::to('genexcel.4')}}"
			        e.preventDefault();
			    });
			  }
	    });

</script>
@stop