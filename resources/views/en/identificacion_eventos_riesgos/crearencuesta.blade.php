@extends('en.master')

@section('title', 'Identification of risk events')

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
	<div class="col-sm-12 col-m-6">
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

On this section you will be able to create polls for the identification of risk events.

{!!Form::open(['url'=>'crear_encuesta','method'=>'POST','class'=>'form-horizontal'])!!}

					<div class="form-group">
						{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Enter number of questions',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::number('cantidad_preguntas',null,
							['class'=>'form-control','id'=>'cantidad_preguntas','required'=>'true','min'=>'1'])!!}
						</div>
					</div>

					<div id="preguntas">
					</div>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
<script>

	$("#cantidad_preguntas").change(function() {
		//primero vaciamos las preguntas
			$("#preguntas").empty();

			i = 0;
			if ($("#cantidad_preguntas").val() == 1)
			{
				$("#preguntas").append('<b>Below enter the question:</b>');
			}
			else
			{
				$("#preguntas").append('<b>Below enter the '+$("#cantidad_preguntas").val()+' questions:</b>');	
			}
			while (i < $("#cantidad_preguntas").val())
			{
				if (i == 0) //sólo si es la primera pregunta será required="true"
				{
					$("#preguntas").append('<div class="form-group"><div class="col-sm-4 control-label"><label for="pregunta'+(i+1)+'">Question '+(i+1)+'</label></div><div class="col-sm-3"><input type="text" class="form-control" required name="pregunta'+(i+1)+'"></div></div>');
	    		}
	    		else
	    		{
	    			$("#preguntas").append('<div class="form-group"><div class="col-sm-4 control-label"><label for="pregunta'+(i+1)+'">Question '+(i+1)+'</label></div><div class="col-sm-3"><input type="text" class="form-control" name="pregunta'+(i+1)+'"></div></div>');
	    		}
	    		i++;
			}

			$("#preguntas").append('<center><div class="form-group">{!!Form::submit("Add answers and kind of answers", ["class"=>"btn btn-primary","name"=>"agregar"])!!}</div></center>');
			$("#preguntas").append('{!!Form::close()!!}')

			
	    });
</script>

@stop
