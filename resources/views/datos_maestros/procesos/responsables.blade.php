@extends('master')

@section('title', 'Agregar Proceso')
@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('procesos','Procesos')!!}</li>
			<li><a href="procesos.responsables.{{$id}}">Asignar Responsables</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Asignar Responsables Proceso {{ $process }}</span>
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
			Ingrese los responsables para cada organización.
				{!!Form::open(['route'=>'procesos.agregar_resp','method'=>'POST','class'=>'form-horizontal','onsubmit'=>'return checkSubmit();'])!!}

					@foreach ($ops as $o)
						<div class="form-group">	
						{!!Form::label($o->org,null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('stakeholder_'.$o->organization_id,$stakeholders,$o->stakeholder_id, 
							 	   ['id'=>$o->organization_id,'placeholder'=>'- Seleccione -'])!!}
							</div>
						</div>
					@endforeach

					{!!Form::hidden('process_id',$id)!!}
					<br>
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>

				{!!Form::close()!!}
			<hr>
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
			</div>
		</div>
	</div>
</div>
@stop

