@extends('master')

@section('title', 'Agregar Control')

@section('content')
<link href="assets/css/uploadfile.css" rel="stylesheet">
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
					{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
				<center>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')

<script src="assets/js/jquery.uploadfile.min.js"></script>
<script>

	$(document).ready(function () {
		$("#subneg").change();
	})
//bloqueamos opciones de llenado si es que se esta ingresando un riesgo tipo
	$("#subneg").change(function() {

			if ($("#subneg").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					$.get('controles.subneg.'+$("#subneg").val()+'.{{$org}}', function (result) {

							$("#riesgos").removeAttr("style").show(500); //hacemos visible riesgos objetivos
							$("#select_riesgos").empty();
							$("#select_riesgos").prop('required',true);
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {

								if (this.description == null)
								{
									$("#select_riesgos").append('<option value="' + this.id + '">' + this.risk_name + ' - (Sin descripción)</option>');
								}
								else
								{
									$("#select_riesgos").append('<option value="' + this.id + '">' + this.risk_name + ' - ' + this.description +'</option>');
								}
								
							});
					});
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

	$("#upload").uploadFile({
	  url:"cargar",
	  multiple: true,
	  allowedTypes: "jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx",
	  doneStr:"Cargado !",
	  extErrorStr:"Solo puedes realizar carga de archivos! ",
	  uploadErrorStr:"Ocurrio un error al carga. Intentelo de nuevo!"
	});

</script>
@stop

