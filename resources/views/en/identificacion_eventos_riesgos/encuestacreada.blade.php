@extends('master')

@section('title', 'Identification of Risk Events')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Identification of risk events</a></li>
			<li><a href="crear_encuesta">Create poll</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-8 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-check"></i>
					<span>Create poll</span>
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

<div class="has-success has-feedback">
	<label class="control-label">Poll successfully created</label>
</div>


<!-- Mostramos la encuesta recien ingresada -->

<h4> Poll: {{ @$post['nombre'] }} </h4>

@for ($i=1; $i<=$_POST['contpreguntas']; $i++)
	<div class="form-group">
	<br><br>
	@if (isset($_POST['pregunta'.$i]))
		<p><b>{{ $i }}.- {{ $post['pregunta'.$i] }}</b></p>

		@if ($_POST['tipo_respuesta'.$i] == 0)

				<div class="col-sm-6">
	                <input type="text" class="form-control" placeholder="Ingrese respuesta" />
	            </div>

		@elseif ($_POST['tipo_respuesta'.$i] == 1)
			<?php $j = 1; ?>
			
			@while(isset($_POST['pregunta'.$i.'_alternativa'.$j]) AND $_POST['pregunta'.$i.'_alternativa'.$j] != "")
				<div class="radio-inline">
					<label>
						<input type="radio" name="radio-inline" checked> {{ $_POST['pregunta'.$i.'_alternativa'.$j] }}
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				<?php $j++; ?>
			@endwhile

		@elseif ($_POST['tipo_respuesta'.$i] == 2)
			<?php $j = 1; ?>
		
			@while(isset($_POST['pregunta'.$i.'_alternativa'.$j]) AND $_POST['pregunta'.$i.'_alternativa'.$j] != "")
				<div class="checkbox-inline">
					<label>
						<input type="checkbox"> {{ $_POST['pregunta'.$i.'_alternativa'.$j] }}
						<i class="fa fa-square-o"></i>
					</label>
				</div>
				<?php $j++; ?>
			@endwhile
		@endif
	@endif
	</div>
@endfor

			<center>
				{!!Form::open(['url'=>'crear_encuesta','method'=>'POST'])!!}
					{!!Form::submit('Return', ['name'=>'volver','class'=>'btn btn-danger'])!!}
				{!!Form::close()!!}
			<center>
	</div>
</div>
<script>
// Run Datables plugin and create 3 variants of settings
function AllTables(){
	TestTable1();
	TestTable2();
	TestTable3();
	LoadSelect2Script(MakeSelect2);
}
function MakeSelect2(){
	$('select').select2();
	$('.dataTables_filter').each(function(){
		$(this).find('label input[type=text]').attr('placeholder', 'Search');
	});
}

$(document).ready(function() {
	// Load Datatables and run plugin on tables 
	LoadDataTablesScripts(AllTables);
	// Add Drag-n-Drop feature
	WinMove();

});
</script>

@stop
