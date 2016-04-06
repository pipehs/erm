@extends('master')

@section('title', 'Controles')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="evaluar_controles">Evaluar Controles</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Evaluar Controles</span>
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

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif


		{!!Form::open(['route'=>'control.guardar_evaluacion','method'=>'POST','class'=>'form-horizontal'])!!}
				<div id="cargando"><br></div>

					<div class="form-group">
						{!!Form::label('Seleccione control',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('control_id',$controls,null, 
							 	   ['id' => 'control_id','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

				<input type="file" name="file[]" id="file" class="inputfile" data-multiple-caption="{count} files selected" multiple />
				<label for="file"><span>Cargue evidencia</span></label>
				<table id="table_evaluacion" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>

		{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
$("#control_id").change(function() {
	
			if ($("#control_id").val() != '') //Si es que se ha seleccionado valor válido de control
			{
				$('#cargando').fadeIn(1);
				//Añadimos la imagen de carga en el contenedor
				$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

				$('#cargando').delay(400).fadeOut(5);

				//Seteamos cabecera
				var table_head = "<thead>";
				table_head += "<th>Diseño</th><th>Efectividad operativa</th><th>Prueba sustantiva</th><th>Prueba de cumplimiento</th>";
				table_head += "</thead>";

				

				var table_row = "<tr>";
				table_row += "<td><div style='text-align:center'><select name='diseno' id='diseno' style='width:150px;' class='form-control' onchange='test_diseno()'>";
				table_row += "<option value='' disabled selected>Seleccione</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='0'>Inefectivo</option></select></div>";
				table_row += "<div id='datos_diseno' style='display: none;'></div></td>";

				table_row += "<td><select name='efect_operativa' style='width:150px' class='form-control' id='efect_operativa'>";
				table_row += "<option value='' disabled selected>Seleccione</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='0'>Inefectivo</option></select>";
				table_row += "<div id='datos_efectividad'></div></td>";

				table_row += "<td><select name='sustantiva' style='width:150px' class='form-control' id='sustantiva'>";
				table_row += "<option value='' disabled selected>Seleccione</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='0'>Inefectivo</option></select>";
				table_row += "<div id='datos_sustantiva'></div></td>";

				table_row += "<td><select name='cumplimiento' style='width:150px' class='form-control' id='cumplimiento'>";
				table_row += "<option value='' disabled selected>Seleccione</option>";
				table_row += "<option value='1'>Efectivo</option>";
				table_row += "<option value='0'>Inefectivo</option></select>";
				table_row += "<div id='datos_cumplimiento'></div></td>";

				table_row += "</tr>";

				$('#table_evaluacion').html(table_head);
				$('#table_evaluacion').append(table_row);
				$('#table_evaluacion').fadeIn(500);

			}

			else
			{
				$('#table_evaluacion').fadeOut(500);
				$('#table_evaluacion').empty();
			}
});

function test_diseno() //ESTE SI FUNCIONA!!!!!!
{
	if ($("#diseno").val() == 0)
	{
		var inefectivo = '<br>';
		inefectivo += '<textarea name="hallazgo" class="form-control" style="width:150px" placeholder="Ingrese hallazgo"></textarea><br>';
		inefectivo += '<textarea name="recomendaciones" class="form-control" style="width:150px" placeholder="Ingrese recomendaciones"></textarea><br>';
		inefectivo += '<textarea name="plan_accion" class="form-control" style="width:150px" placeholder="Ingrese plan de acción"></textarea><br>';
		
		


		$("#datos_diseno").html(inefectivo);
		$("#datos_diseno").fadeIn(500);
	}
	else
	{
		$("#datos_diseno").empty();
	}
}

function test_efectividad()
{

}

function test_sustantiva()
{
	
}

function test_cumplimiento()
{
	
}

$(function(){
    $(".custom-input-file input:file").change(function(){
        $(this).parent().find(".archivo").html($(this).val());
    }).css('border-width',function(){
        if(navigator.appName == "Microsoft Internet Explorer")
            return 0;
    });
});

</script>
@stop

