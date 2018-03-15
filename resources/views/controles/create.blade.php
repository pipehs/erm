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
	<div class="col-sm-7 col-m-6">
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
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					@include('controles.form')
				{!!Form::close()!!}

				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
			</div>
		</div>
	</div>

	<div class="col-sm-5 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Información Porcentaje de contribución</span>
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

			<table class="table table-bordered table-heading" style="font-size:12px">
			<thead>
				<th style="width:13%;">Categor&iacute;a</th><th style="width:12%;">Porcentaje</th><th style="width:75%;">Descripci&oacute;n</th>
			</thead>
				<tr>
					<td style="background-color: #04B404; font-size:13px;" ><b>&Oacute;ptima</b></td>
					<td>95%</td>
					<td>La acción mitiga razonablemente el riesgo y cumple con requisitos tales como: documentación, registro oportuno y adecuado, autorización, formalización, segregación de funciones, supervisión, capacitación del personal y, en el caso de seguros, cobertura, etc.</td>
				</tr>
				<tr>
					<td style="background-color: #FFFF00; font-size:13px;"><b>Buena</b></td>
					<td>85%</td>
					<td>La acción mitiga razonablemente el riesgo, sin embargo, presenta debilidades de forma que podrían afectar su continuidad y calidad, por ejemplo, en caso de un cambio en la estructura organizacional.</td>
				</tr>
				<tr>
					<td style="background-color: #FF0000; font-size:13px;"><b>Media</b></td>
					<td>50%</td>
					<td>La acción NO mitiga razonablemente el riesgo, sin embargo, presenta ciertos elementos positivos respecto de su diseño y operación.</td>
				</tr>
				<tr>
					<td style="background-color: #FF0000; font-size:13px;"><b>Deficiente</b></td>
					<td>0%</td>
					<td>No hay acción mitigante o la existente debe ser rediseñada desde su origen para mitigar razonablemente el riesgo.</td>
				</tr>
			</table>
			
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
				riesgos = new Array()
					$.get('controles.subneg.'+$("#subneg").val()+'.{{$org}}', function (result) {

							$("#riesgos").removeAttr("style").show(500); //hacemos visible riesgos objetivos
							$("#select_riesgos").empty();
							$("#select_riesgos").prop('required',true);
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							var i = 0
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								riesgos[i] = {id: this.org_risk_id, name: this.risk_name,description: this.description,risk_category_id: this.risk_category_id};
								i++
								if (this.description == null)
								{
									$("#select_riesgos").append('<option value="' + this.org_risk_id + '">' + this.risk_name + ' - (Sin descripción)</option>');
								}
								else
								{
									$("#select_riesgos").append('<option value="' + this.org_risk_id + '">' + this.risk_name + ' - ' + this.description +'</option>');
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
						$('#select_riesgos').empty();
						$('#select_riesgos').change();
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



//ACTUALIZACIÓN 21-08-17: Filtramos por categoría
$("#risk_category_id").change(function()
{
	if ($("#subneg").val() != '' && $("#risk_category_id").val() != '')
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();


		$(riesgos).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#select_riesgos").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})
	}
	else if ($("#subneg").val() != '' && $("#risk_category_id").val() == '')
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();

		$(riesgos).each(function() {

			$("#select_riesgos").append('<option value="' + this.id + '">' + this.name +'</option>');

		})
	}
	else if ($("#subneg").val() == '' && $("#risk_category_id").val() == '')
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();
	}
});

$("#risk_subcategory_id").change(function()
{
	if ($("#subneg").val() != '' && $("#risk_subcategory_id").val() != '')
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();


		$(riesgos).each(function() {
			//agregamos sólo los riesgos de la subcategoría correspondiente
			if (this.risk_category_id == $("#risk_subcategory_id").val())
			{
				$("#select_riesgos").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})
	}
	else if ($("#risk_subcategory_id").val() == '' && $("#risk_category_id").val() != '')
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();


		$(riesgos).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#select_riesgos").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})
	}
	else
	{
		$("#select_riesgos").empty();
		$("#select_riesgos").change();

		$(riesgos).each(function() {
			$("#select_riesgos").append('<option value="' + this.id + '">' + this.name +'</option>');
		})
	}
});

</script>

@stop

