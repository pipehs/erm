@extends('master')

@section('title', 'Evaluar KRI')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('kri','KRI')!!}</li>
			<li><a href="kri.evaluar.{{ $id }}">Evaluar KRI</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluar KRI</span>
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

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			Ingrese la evaluaci&oacute;n para el KRI: <b>{{ $name }}</b>.</br></br>

			@if ($uni_med == 0)
				Recuerde que la unidad de medida para este KRI es en porcentaje, por lo que su evaluaci&oacute;n debe ser igual o menor a 100.
			@endif

			
			{!!Form::open(['route'=>'kri.guardar_evaluacion','method'=>'POST','class'=>'form-horizontal'])!!}
			<div class="form-group">
				{!!Form::label('Evaluación',null,['class'=>'col-sm-4 control-label'])!!}
				<div class="col-sm-3">
					{!!Form::number('evaluation',null,['class'=>'form-control','required'=>'true','id'=>'evaluation','step'=>'0.1'])!!}
				</div>
			</div>

			<div class="form-group">
				<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
				</center>
			</div>

			<div class="form-group">
				<center>
					<a href="#" class="btn btn-warning" id="ver_evaluaciones">Ver evaluaciones anteriores</a>
				</center>
			</div>

			<div id="cargando"></div>

			<div id="evaluaciones" style="display:none;"></div>

			
			<input type="hidden" name="id" id="risk_id" value="{{ $id }}">

			{!!Form::close()!!}

				<center>
					{!! link_to_route('kri', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>

			
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

$("#evaluation").change(function() {

	if ({{ $uni_med }} == 0)
	{
		$("#evaluation").attr('max','100');
	}
});

$("#ver_evaluaciones").click(function() {

	//Añadimos la imagen de carga en el contenedor
	$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
	//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
	//primero obtenemos controles asociados a los riesgos de negocio

	//obtenemos kri del riesgo seleccionado
	$.get('get_kri_evaluations.'+$("#risk_id").val(), function (result) {
			
			$("#cargando").html('<br>');
			$("#evaluaciones").empty();

			if (result == "null")
			{
				var info = "<center>Aun no se han agregado evaluaciones para el riesgo ";
				info += "{{ $name }}" + ".<br><br></center>";
				$("#evaluaciones").append(info);
			}

			else
			{
				var table_row = '<div width="50%">';
				table_row += '<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">';
				table_row += '<thead>';
				table_row += '<th>Valor evaluaci&oacute;n</th>';
				table_row += '<th>Resultado</th>';
				table_row += '<th >Fecha evaluaci&oacute;n</th>';
				table_row += '</thead>';

				//parseamos datos obtenidos
				var datos = JSON.parse(result);
					
				//seteamos datos
				$(datos).each( function() {
						table_row += '<tr><td>'+this.value+'</td>';
						//mostramos evaluación
						if (this.eval == 0)
						{
							table_row += '<td><ul class="semaforo verde"><li></li><li></li><li></li></ul></td>';	
						}
						else if (this.eval == 1)
						{
							table_row += '<td><ul class="semaforo amarillo"><li></li><li></li><li></li></ul></td>';	
						}
						else if (this.eval == 2)
						{
							table_row += '<td><ul class="semaforo rojo"><li></li><li></li><li></li></ul></td>';	
						}
						else
						{
							table_row += '<td>'+this.eval+'</td>';
						}
						table_row += '<td>'+this.date+'</td></tr>';				
					});

					table_row += '</table>';
					table_row += '</div>';
					$("#evaluaciones").html(table_row);
					$("#evaluaciones").fadeIn(500);

				}
		});
});
</script>
@stop