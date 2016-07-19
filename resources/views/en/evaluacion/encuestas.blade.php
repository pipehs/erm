@extends('en.master')

@section('title', 'Risk Assessments')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Risk Assessments</a></li>
			<li><a href="evaluacion_agregadas">Polls</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Assessment Polls</span>
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

		@if(Session::has('error'))
			<div class="alert alert-danger alert-dismissible" role="alert">
			@foreach (Session::get('error') as $error)
				{{ $error }}
				<br>
			@endforeach
			</div>
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<p>Select the poll that you want to view, send, consolidate or delete.</p>
			<?php break; ?>
		@else
			<p>Select the poll that you want to view.<p>
		@endif
	@endforeach
		<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="margin: 0 auto;">
			<thead>
			<th>Name</th>
			<th>Final date</th>
			<th>View</th>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
			<th>Send</th>
			<th>Consolidate</th>
			<th>Delete</th>
		<?php break; ?>
		@endif
	@endforeach

			</thead>
				@foreach ($encuestas as $encuesta)
					<tr>
					<td>{{ $encuesta['name'] }}</td>
					@foreach ($fecha as $f)
						@if ($f['evaluation_id'] == $encuesta['id'])
							<td>{{ $f['expiration_date'] }}</td>
						@endif
					@endforeach
					<td>
					 {!! link_to_route('evaluacion.show', $title = 'View', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-success']) !!}
					 </td>
	@foreach (Session::get('roles') as $role)
		@if ($role != 6)
					<td>
					{!! link_to_route('evaluacion.enviar', $title = 'Send', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-primary']) !!}
					</td>
					<td>
					{!! link_to_route('evaluacion.consolidar', $title = 'Consolidate', $parameters = $encuesta['id'], $attributes = ['class'=>'btn btn-default']) !!}
					</td>
					<td>
					<button class="btn btn-danger" onclick="eliminar({{ $encuesta['id'] }})">Delete</button>
					</td>
		<?php break; ?>
		@endif
	@endforeach
					 </tr>
				@endforeach
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
function eliminar(id)
{
	swal({   title: "Warning!",
		   text: "Are you sure to delete this poll?",
		   type: "warning",   
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Delete",
		   cancelButtonText: "Cancel",   
		   closeOnConfirm: false }, 
		   function(){
		   		$.get('evaluacion_delete.'+id, function (result) {

		   			if (result == 0)
		   			{
		   				swal({   title: "",
		   			   text: "The poll was successfully deleted ",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Accept",   
		   			   closeOnConfirm: false }, 
		   			   function(){   
		   			   	location.reload();
		   			   });
		   			}
		   			else
		   			{
			   			swal({   title: "",
			   			   text: "The poll can not been deleted. Probably it already has answers. For more information contact with administration",
			   			   type: "error",   
			   			   showCancelButton: false,   
			   			   confirmButtonColor: "#31B404",   
			   			   confirmButtonText: "Accept",   
			   			   closeOnConfirm: false }, 
			   			   function(){   
			   			   	location.reload();
			   			   });
			   			}
		   			});
		   		 
		   	});
	//confirm("Esta seguro de bloquear "+type+" "+name+"?")
}
</script>
@stop