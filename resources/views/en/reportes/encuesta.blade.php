@extends('master')

@section('title', 'Encuestas de evaluación de Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes básicos</a></li>
			<li><a href="encuestas">Encuestas</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Encuesta {{ $encuesta }}</span>
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

			<div class="row">
				<div class="col-sm-6">
				<p><b>Datos de stakeholder</b></p>
				
					<table class="table table-striped" style="font-size:11px">
					<tr>
					<td>Rut: </td><td>{{ $stakeholder['id'] }}-{{ $stakeholder['dv'] }}</td>
					</tr>
					<tr>
					<td>Nombre: </td><td>{{ $stakeholder['name']}} {{ $stakeholder['surnames'] }}</td>
					</tr>
					<tr>
					<td>Rol(es): </td>
					<td><ul>
					@foreach ($roles as $role)
						<li>{{ $role['name'] }}</li>
					@endforeach
					</ul></td>
					</tr>
					<tr>
					<td>Correo: </td><td>{{ $stakeholder['mail'] }}</td>
					</tr>
					</table>
				</div>
			</div>

		<?php $i = 1; //número de preguntas ?>

		@foreach ($questions as $question)
			<b>	{{ $i }}.- {{ $question->question }} </b>
				<ul>
			@foreach ($answers as $answers2)
				<!-- vemos en todas las respuestas si corresponden a la pregunta, si es asi la mostramos -->
				@foreach ($answers2 as $answer)
					@if ($answer->question_id == $question->id)
						<li>{{ $answer->answer }}</li>
					@endif
				@endforeach
			@endforeach
				</ul>
			<?php $i += 1; ?>
			<hr>
		@endforeach

		<center>
			{!! link_to_route('encuestas', $title = 'Volver', 
				$parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		</center>

			</div>
		</div>
	</div>
</div>

@stop

@section('scripts')


<script>
// Run Select2 on element
function Select2Test(){
	$("#el2").select2();
	$("#el3").select2();
}

function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}
$(document).ready(function() {
	// Load script of Select2 and run this
	LoadSelect2Script(Select2Test);
	// Add Drag-n-Drop feature
	WinMove();
});
</script>

@stop