@extends('master')

@section('title', 'Agregar Control')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('controles','Controles')!!}</li>
			<li>{!!Html::link('controles.create','Agregar Control')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Control</span>
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

			Ingrese la informaci&oacute;n asociada al nuevo control
				{!!Form::open(['route'=>'controles.store','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data'])!!}
					@include('controles.form')
				{!!Form::close()!!}

				<center>
					{!! link_to_route('controles', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

	$(document).ready(function () {
		$("#subneg").change();
	})
//bloqueamos opciones de llenado si es que se esta ingresando un riesgo tipo
	$("#subneg").change(function() {

			if ($("#subneg").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
				if ($("#subneg").val() == 0) //Se seleccionó Riesgos / Procesos
				{
					$.get('controles.subneg.'+$("#subneg").val(), function (result) {

							$("#procesos").removeAttr("style").show(); //hacemos visible riesgos / procesos
							$("#negocios").removeAttr("style").hide(); //ocultamos riesgos / objetivos
							$("#select_procesos").empty();
							$("#select_objetivos").empty();
							$("#select_objetivos").prop('required',false);
							$("#select_procesos").prop('required',true);
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#select_procesos").append('<option value="' + this.id + '">' + this.risk_name + ' - ' + this.subprocess_name +'</option>');
							});
					});
				}

				else if ($("#subneg").val() == 1) //Se seleccionó Riesgos / Objetivos
				{
					$.get('controles.subneg.'+$("#subneg").val(), function (result) {

							$("#negocios").removeAttr("style").show(); //hacemos visible riesgos / objetivos
							$("#procesos").removeAttr("style").hide(); //ocultamos riesgos / procesos
							$("#select_procesos").empty();
							$("#select_objetivos").empty();
							$("#select_objetivos").prop('required',true);
							$("#select_procesos").prop('required',false);
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#select_objetivos").append('<option value="' + this.id + '">' + this.risk_name + ' - ' + this.objective_name +' - ' + this.organization_name + '<option>');
							});
					});
				}
				
			}

			else
			{
				//REseteamos datos
						$("#nombre").val("");
						$("#descripcion").val("");
						$("#categoria").val(""); 
						$("#categoria").change(); //cambiamos texto que muestra select
						$("#input_date2").val("");
						$("#cause_id").val("");
						$("#cause_id").change();
						$("#effect_id").val("");
						$("#effect_id").change();
			}
			
	    });

</script>
@stop

