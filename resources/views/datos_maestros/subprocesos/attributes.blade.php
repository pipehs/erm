@extends('master')

@section('title', 'Agregar Atributos Subproceso')
@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('subprocesos','Subprocesos')!!}</li>
			<li><a href="subprocesos.attributes.{{$id}}">Asignar atributos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Asignar Atributos a Subproceso {{ $subprocess }}</span>
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
			Ingrese los datos del subproceso para cada organización.
				{!!Form::open(['route'=>'subprocesos.assign_attributes','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
				<table class="table table-bordered table-striped table-hover table-heading">
					<thead>
						<th>Organización</th>
						<th>Responsable</th>
						<th>Subproceso clave</th>
						<th>Criticidad subproceso</th>
						<th>Documentos</th>
					</thead>
					@foreach ($os as $o)
						<tr>
							<td>{{ $o->org }}</td>
							<td>
								{!!Form::select('stakeholder_'.$o->organization_id,$stakeholders,$o->stakeholder_id,['id'=>$o->organization_id,'placeholder'=>'- Seleccione -'])!!}
							</td>
							<td>
								{!!Form::select('key_subprocess_'.$o->organization_id,['1'=>'Si','0'=>'No'],$o->key_subprocess,['placeholder'=>'- Seleccione -'])!!}
							</td>
							<td>
								{!!Form::select('criticality_'.$o->organization_id,['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'], $o->criticality, ['placeholder'=>'- Seleccione -','id' => 'criticality'])!!}
							</td>
							<td>
								<input id="file-1" type="file" class="file" name="evidence_doc_{{$o->organization_id}}[]" multiple=true data-preview-file-type="any">
							</td>
						</tr>
					@endforeach
				</table>
					{!!Form::hidden('subprocess_id',$id)!!}
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

