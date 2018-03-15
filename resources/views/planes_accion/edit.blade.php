@extends('master')

@section('title', 'Editar plan de acci&oacute;n')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Hallazgos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Editar Plan de acci&oacute;n</span>
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

			@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

			Ingrese los datos del hallazgo para la organizaci&oacute;n <b>{{ $org }}</b> <br><br>

			{!!Form::model($action_plan,['route'=>['action_plan.update',$action_plan['id']],'method'=>'PUT','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
					@include('planes_accion.form')
			{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
@if (strstr($_SERVER["REQUEST_URI"],'edit'))

	$('#issue_id').attr('disabled','disabled');
	$('#kind').attr('disabled','disabled');
	//QUEDE AQUÍ!!!! DEBO HACER DISABLED ´SELECT DE ISSUE
	
	@if ($action_plan != NULL)
		$(document).ready(function() {

			@if ($action_plan->stakeholder_id != NULL)
				$("select option[value='{{ $action_plan->stakeholder_id }}']").attr("selected","selected");
			@endif

			@if ($action_plan->status == 1)

				$('#description_plan2').val($('#description').val());
				$('#stakeholder_id2').val($('#stakeholder_id').val())
				$('#final_date2').val($('#final_date').val())
				$('#percentage2').val($('#percentage').val())
				$('#progress_comments2').val($('#progress_comments').val())

				$('#description').attr('disabled','disabled');
				$('#stakeholder_id').attr('disabled','disabled');
				$('#final_date').attr('disabled','disabled');
				$('#percentage').attr('disabled','disabled');
				$('#progress_comments').attr('disabled','disabled');

			@endif

		});

		$('#status').change(function() {

			if($('#status').is(':checked'))
			{
				$('#description_plan2').val($('#description').val());
				$('#stakeholder_id2').val($('#stakeholder_id').val())
				$('#final_date2').val($('#final_date').val())
				$('#percentage2').val($('#percentage').val())
				$('#progress_comments2').val($('#progress_comments').val())

				$('#description').attr('disabled','disabled');
				$('#stakeholder_id').attr('disabled','disabled');
				$('#final_date').attr('disabled','disabled');
				$('#percentage').attr('disabled','disabled');
				$('#progress_comments').attr('disabled','disabled');
			}
			else
			{
				$('#description').attr('disabled',false);
				$('#stakeholder_id').attr('disabled',false);
				$('#final_date').attr('disabled',false);
				$('#percentage').attr('disabled',false);
				$('#progress_comments').attr('disabled',false);
			}
		});

	@endif
@endif

$('#kind').change(function() {

	if ($('#kind').val() != '')
	{
		$("#issues").empty();
		//obtenemos todas las causas
		$.get('get_issues.'+$('#kind').val()+'.{{ $org_id }}', function (result) {
			//parseamos datos obtenidos
			var datos = JSON.parse(result);
			var issues = '<option value="" disabled selected>- Seleccione </option>';
			$(datos).each( function() {
				issues += '<option value='+this.id+'>'+this.name+'</option>';
			});

			$("#issues").append(issues);
			$("#issues").change();
		});
	}
		
});
</script>
@stop