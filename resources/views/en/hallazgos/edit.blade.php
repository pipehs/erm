@extends('en.master')

@section('title', 'Issues Management')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('hallazgos','Issues')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Edit issue</span>
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

			Change the data that you want to update from the issue <br><br>

			{!!Form::model($issue,['route'=>['update_hallazgo',$issue->id],'method'=>'PUT','class'=>'form-horizontal','id'=>'form','enctype'=>'multipart/form-data'])!!}
					@include('en.hallazgos.form')
			{!!Form::close()!!}

			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
@if (strstr($_SERVER["REQUEST_URI"],'edit'))
	@if ($action_plan != NULL)
		$(document).ready(function() {

			@if ($action_plan->stakeholder_id != NULL)
				$("select option[value='{{ $action_plan->stakeholder_id }}']").attr("selected","selected");
			@endif

			@if ($action_plan->status == 1)
				$('#status').bootstrapToggle('on')

				$('#description_plan').attr('disabled','disabled');
				$('#stakeholder_id').attr('disabled','disabled');
				$('#final_date').attr('disabled','disabled');

			@endif

		});

		$('#status').change(function() {

			if($('#status').is(':checked'))
			{
				$('#description_plan2').val($('#description_plan').val());
				$('#stakeholder_id2').val($('#stakeholder_id').val())
				$('#final_date2').val($('#final_date').val())

				$('#description_plan').attr('disabled','disabled');
				$('#stakeholder_id').attr('disabled','disabled');
				$('#final_date').attr('disabled','disabled');
			}
			else
			{
				$('#description_plan').attr('disabled',false);
				$('#stakeholder_id').attr('disabled',false);
				$('#final_date').attr('disabled',false);
			}
		});

	@endif
@endif
</script>
@stop