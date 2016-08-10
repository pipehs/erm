@extends('en.master')

@section('title', 'Create Risk')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('riesgos','Risk Identification')!!}</li>
			<li>{!!Html::link('riesgos.create','Create Risk')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Create Risk</span>
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
			Input the data for the new risk.
				{!!Form::open(['route'=>'riesgos.store','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('en.riesgos.form')
				{!!Form::close()!!}

				<center>
					{!! link_to_route('riesgos.index2', $title = 'Return', $parameters = ['organization_id' => $org_id], $attributes = ['class'=>'btn btn-danger']) !!}
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

						$(datos.causes).each(function(i,cause) {

							//recorremos select
							$("#cause_id option").each(function(){
							   if ($(this).attr('value') == cause.id)
							   {
							   		$(this).attr('selected','selected');
							   }
							});

							$("#cause_id").change();

						});

						$(datos.effects).each(function(i,effect) {

							//recorremos select
							$("#effect_id option").each(function(){
							   if ($(this).attr('value') == effect.id)
							   {
							   		$(this).attr('selected','selected');
							   }
							});

							$("#effect_id").change();

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

			}
			
	    });

</script>

{!!Html::script('assets/js/en/create_edit_risks.js')!!}
@stop

