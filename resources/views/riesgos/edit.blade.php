@extends('master')

@section('title', 'Editar Riesgo')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="riesgos">Identificaci&oacute;n de Riesgos</a></li>
			<li><a href="riesgos.edit.{{ $risk['id'] }}">Editar Riesgo</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Modificar Riesgo</span>
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
			Ingrese los datos del riesgo.
				{!!Form::model($risk,['route'=>['riesgos.update',$risk->id],'method'=>'PUT','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}
					@include('riesgos.form')
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
<script>
//bloqueamos opciones de llenado si es que se esta ingresando un riesgo tipo
	$("#risk_id").change(function() {

			if ($("#risk_id").val() != "")
			{
				$.get('riesgos.setriesgotipo.'+$("#risk_id").val(), function (result) {
						
						//alert(result);
						//parseamos datos obtenidos
						var datos = JSON.parse(result);
						//alert(datos.name);

						//seteamos datos
						$("#nombre").val(datos.name);
						$("#descripcion").val(datos.description);
						$("#categoria").val(datos.risk_category_id); 
						$("#categoria").change(); //cambiamos texto que muestra select
						$("#input_date2").val(datos.expiration_date);
						$("#cause_id").val(datos.cause_id);
						$("#cause_id").change();
						$("#effect_id").val(datos.effect_id);
						$("#effect_id").change();

				/*	Dejaremos que el usuario decida si quiere modificar algun dato de riesgo tipo
						//bloqueamos datos
						$("#nombre").prop("disabled",true);
						$("#nombre").prop("required",false);

						$("#descripcion").prop("disabled",true);
						$("#descripcion").prop("required",false);

						$("#categoria").prop("disabled",true);
						$("#categoria").prop("required",false);


						$("#input_date2").prop("disabled",true);
						$("#input_date2").prop("required",false);

						$("#cause_id").prop("disabled",true);
						$("#cause_id").prop("required",false);

						$("#effect_id").prop("disabled",true);
						$("#effect_id").prop("required",false);
				*/
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
			/*	Dejaremos que el usuario decida si quiere modificar algun dato de riesgo tipo
				$("#nombre").prop("disabled",false);
				$("#nombre").prop("required",true);

				$("#descripcion").prop("disabled",false);
				$("#descripcion").prop("required",true);

				$("#categoria").prop("disabled",false);
				$("#categoria").prop("required",true);


				$("#input_date2").prop("disabled",false);
				$("#input_date2").prop("required",true);

				$("#cause_id").prop("disabled",false);
				$("#cause_id").prop("required",true);

				$("#effect_id").prop("disabled",false);
				$("#effect_id").prop("required",true);
			*/
			}
			
	    });

	$("#agregar_causa").click(function() {
		$("#causa").empty();
		$("#causa").append('<div class="form-group">{!!Form::label("Causa",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("causa_nueva",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

	$("#agregar_efecto").click(function() {
		$("#efecto").empty();
		$("#efecto").append('<div class="form-group">{!!Form::label("Efecto",null,["class"=>"col-sm-4 control-label"])!!}<div class="col-sm-3">{!!Form::textarea("efecto_nuevo",null,["class"=>"form-control","rows"=>"3","cols"=>"4","required"=>"true"])!!}</div></div>');
		});

</script>

{!!Html::script('assets/js/create_edit_risks.js')!!}
@stop