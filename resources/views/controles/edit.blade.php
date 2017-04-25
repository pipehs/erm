@extends('master')

@section('title', 'Editar Control')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('controles','Controles')!!}</li>
			<li>{!!Html::link('controles.edit','Editar Control')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Editar Control</span>
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

			Ingrese la informaci&oacute;n que desea modificar del control.
				{!!Form::model($control,['route'=>['controles.update',$control->id],'method'=>'PUT','class'=>'form-horizontal','enctype'=>'multipart/form-data'])!!}
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
<script>

$(document).ready(function() {

	if ({{ $control->type2 }} == 0) //control de proceso
	{
			$.get('controles.subneg.0.{{$org}}', function (result) {

							$("#riesgos").removeAttr("style").show(); //hacemos visible riesgos
							$("#select_riesgos").empty();
							$("#select_riesgos").prop('required',true);

							$("#select_riesgos").append('<option value="" disabled>- Seleccione -</option>')
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#select_riesgos").append('<option value="' + this.id + '">' + this.risk_name + ' - ' + this.description + '<option>');
							});

				//riesgos seleccionados
				var risks = JSON.parse('{{ $risks_selected }}');
				$(risks).each( function() {
					$("#select_riesgos option[value='" + this + "']").attr("selected",true);
				});

				$("#select_riesgos").change();

			});
	}
	else if ({{ $control->type2 }} == 1) //riesgos de negocio
	{
			$.get('controles.subneg.1.{{$org}}', function (result) {

							$("#riesgos").removeAttr("style").show(); //hacemos visible riesgos
							$("#select_riesgos").empty();
							$("#select_riesgos").prop('required',true);

							$("#select_riesgos").append('<option value="" disabled>- Seleccione -</option>')
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							
							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#select_riesgos").append('<option value="' + this.id + '">' + this.risk_name + ' - ' + this.description + '<option>');
							});
			

				//riesgos seleccionados
				var risks = JSON.parse('{{ $risks_selected }}');
				$(risks).each( function() {
					$("#select_riesgos option[value='" + this + "']").attr("selected",true);
				});

				$("#select_riesgos").change();

			});
	}
});


</script>
@stop

