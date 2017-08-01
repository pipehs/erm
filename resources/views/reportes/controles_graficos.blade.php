@extends('master')

@section('title', 'Reporte de Gráficos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="graficos_controles">Gr&aacute;ficos Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Controles</span>
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
      		<p>En esta secci&oacute;n podr&aacute; ver distintos gr&aacute;ficos que permitan observar de mejor manera la ejecuci&oacute;n del control interno en la empresa.</p>

      	@if (!isset($controls))
      		{!!Form::open(['url'=>'graficos_controles2.0.0','method'=>'GET','class'=>'form-horizontal'])!!}
			<div class="form-group">
				  <div class="row">
				    {!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
				    <div class="col-sm-4">
				      {!!Form::select('organization_id',$organizations, 
				           null, 
				          ['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
				    </div>
				 </div>
			</div>
			<br>
			<div class="form-group">
				<center>
					{!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
				</center>
			</div>
			{!!Form::close()!!}
		@endif
		</div>
	</div>
</div>

@if(isset($controls))
<!-- Gráfico de controles ejecutados v/s no ejecutados -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Controles ejecutados v/s No ejecutados</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar la cantidad de controles que han sido ejecutados a trav&eacute;s de una evaluaci&oacute;n de controles o de una prueba de auditor&iacute;a. Haga click en el gr&aacute;fico si desea observar la informaci&oacute;n de los controles ejecutados o pendientes.</p>
			<p id="alternativo"></p>
			<div id="piechart_3d" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de controles ejecutados v/s no ejecutados -->

<!-- Gráfico de controles efectivos v/s inefectivos -->
<div class="col-xs-12 col-sm-6">
	<div class="box">
		<div class="box-header">
			<div class="box-name">
				<i class="fa fa-circle"></i>
				<span>Controles efectivos v/s inefectivos</span>
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
			<p align="justify">En este gr&aacute;fico podr&aacute; observar los resultados obtenidos en los controles que han sido ejecutados en el sistema. Haga click en el gr&aacute;fico si desea observar la informaci&oacute;n de los controles efectivos o inefectivos.</p>
			<p id="alternativo2"></p>
			<div id="piechart2" style="width: 500px; height: 300px;"></div>
		</div>
	</div>
</div>
<!-- FIN Gráfico de controles efectivos v/s inefectivos -->
<!-- Botón exportar-->
<div class="row">
	<div class="col-xs-12 col-sm-12">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-circle"></i>
					<span>Exportar</span>
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
			<div id="boton_exportar">
			{!!Form::open(['route'=>'export_control_graphics','method'=>'POST','class'=>'form-horizontal'])!!}
				{!!Form::hidden('grafico1','',['id' => 'grafico1'])!!}
				{!!Form::hidden('grafico2','',['id' => 'grafico2'])!!}
				{!!Form::hidden('org',$org,['id' => 'org'])!!}
				<div class="form-group">
						<center>
						{!!Form::submit('Exportar a Word', ['class'=>'btn btn-info'])!!}
						</center>
				</div>
			{!!Form::close()!!}
			</div>
			</div>
			</div>
		</div>
	</div>
</div>
@endif
      		

				

@stop
@section('scripts2')
<!--<script type="text/javascript" src="assets/js/loader.js"></script>-->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>

@if (isset($controls))
	@if ($cont_ejec > 0 || $cont_no_ejec > 0)
      google.charts.load("visualization", "1", {packages:["corechart"]});
      google.charts.setOnLoadCallback(chart1);
      google.charts.setOnLoadCallback(chart2);
      function chart1() {
        var data = google.visualization.arrayToDataTable([
          ['Controles', 'Cantidad'],
          ['Ejecutados',     {{ $cont_ejec }}],
          ['Pendientes',     {{ $cont_no_ejec }}]
        ]);

        var options = {
          title: 'Ejecutados v/s Pendientes',
          is3D: false,
          colors: ['#74DF00','#FF0000']
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico1').value = chart.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart, 'select', clickHandler);

      	function clickHandler(e) {
      		var sel = chart.getSelection();

      		if (sel.length > 0)
			{
				//alert(sel[0].row);
				if (sel[0].row == 0) //mostramos controles ejecutados
				{
					var title = '<b>Controles Ejecutados</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($controls as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.1.{{$org}}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mostramos controles no ejecutados
				{
					var title = '<b>Controles Pendientes</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($no_ejecutados as $control)
						text += '<tr><td>{{$control["name"]}}</td>';
						text += '<td>{{$control["description"]}}</td>';
						text += '<td>{{$control["updated_at"]}}</td></tr>';
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.2.{{$org}}">Exportar</a>'
					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}


			}
      		//console.log(sel);
		}
      }

      function chart2() {
        var data = google.visualization.arrayToDataTable([
          ['Resultado', 'Cantidad'],
          ['Efectivos',     {{ $efectivos }}],
          ['Inefectivos',     {{ $inefectivos }}]
        ]);

        var options = {
          title: 'Efectivos v/s Inefectivos',
          is3D: false,
          colors: ['#74DF00','#FF0000']
        };

        var chart2 = new google.visualization.PieChart(document.getElementById('piechart2'));
        chart2.draw(data, options);

        //guardamos imagen en form hidden para reporte
        document.getElementById('grafico2').value = chart2.getImageURI();

        //agregamos evento de click
        google.visualization.events.addListener(chart2, 'select', clickHandler2);

      	function clickHandler2(e) {
      		var sel = chart2.getSelection();

      		if (sel.length > 0)
			{
				//alert(sel[0].row);
				if (sel[0].row == 0) //mostramos controles efectivos
				{
					var title = '<b>Controles Efectivos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($controls as $control)
						@if ($control['results'] == 2)
							text += '<tr><td>{{$control["name"]}}</td>';
							text += '<td>{{$control["description"]}}</td>';
							text += '<td>{{$control["updated_at"]}}</td></tr>';
						@endif
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.3.{{$org}}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}
				else if (sel[0].row == 1) //mostramos controles no ejecutados
				{
					var title = '<b>Controles Inefectivos</b>';

					var text ='<table class="table table-striped table-datatable"><thead><th>Nombre</th><th>Descripci&oacute;n</th><th>Actualizado</th></thead>';

					@foreach ($controls as $control)
						@if ($control['results'] == 1)
							text += '<tr><td>{{$control["name"]}}</td>';
							text += '<td>{{$control["description"]}}</td>';
							text += '<td>{{$control["updated_at"]}}</td></tr>';
						@endif
					@endforeach

					text += '</table>'
					text += '<a class="btn btn-success" href="genexcelgraficos.4.{{$org}}">Exportar</a>'

					swal({   
						title: title,   
						text: text,
						customClass: 'swal-wide2',   
						html: true 
					});
				}


			}
      		//console.log(sel);
		}
      }
    @else
    	$('#alternativo').html('<b>Aun no se han ejecutado controles</b>');
    	$('#alternativo2').html('<b>Aun no se han ejecutado controles</b>');
    @endif
@endif
</script>
@stop