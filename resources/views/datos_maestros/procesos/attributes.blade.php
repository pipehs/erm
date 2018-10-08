@extends('master')

@section('title', 'Agregar Atributos Proceso')
@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Datos Maestros')!!}</li>
			<li>{!!Html::link('procesos','Procesos')!!}</li>
			<li><a href="procesos.attributes.{{$id}}">Asignar atributos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-folder"></i>
					<span>Asignar Atributos Proceso {{ $process }}</span>
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
			Ingrese los datos del proceso para cada organización.
				{!!Form::open(['route'=>'procesos.assign_attributes','method'=>'POST','class'=>'form-horizontal',
				'enctype'=>'multipart/form-data','onsubmit'=>'return checkSubmit();'])!!}
				<table class="table table-bordered table-striped table-hover table-heading">
					<thead>
						<th>Organización</th>
						<th>Responsable</th>
						<th>Proceso clave</th>
						<th>Criticidad proceso</th>
						<th>Documentos</th>
						<th>Documentos cargados</th>
					</thead>
					@foreach ($ops as $o)
						<tr>
							<td>{{ $o->org }}</td>
							<td>
								{!!Form::select('stakeholder_'.$o->organization_id,$stakeholders,$o->stakeholder_id,['id'=>$o->organization_id,'placeholder'=>'- Seleccione -'])!!}
							</td>
							<td>
								{!!Form::select('key_process_'.$o->organization_id,['1'=>'Si','0'=>'No'],$o->key_process,['placeholder'=>'- Seleccione -'])!!}
							</td>
							<td>
								{!!Form::select('criticality_'.$o->organization_id,['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'], $o->criticality, ['placeholder'=>'- Seleccione -','id' => 'critically'])!!}
							</td>
							<td>
								<input id="file-1" type="file" class="file" name="evidence_doc_{{$o->organization_id}}[]" multiple=true data-preview-file-type="any">
							</td>
							<td>
								@if (empty($o->files))
									No se han agregado documentos.
								@else
									<table class="table" style="font-size: 10px;">
									<thead>
										<th>Nombre archivo</th>
										<th>Fecha carga</th>
									</thead>
									@foreach ($o->files as $file)
										<tr>
											<td>
											<?php //pequeño módulo php para ver el tipo de archivo y nombre
												$archivo = explode('.',$file);
												$filename = explode('/',$archivo[0]);
												$id2 = $filename[1]; //id del elemento
												$kind3 = $filename[0]; //por ej. evidencias_hallazgos
												$filename = $filename[2];
												if (Storage::exists($file)) //Comprobación de existencia (probablemente innecesaria)
												{	
													//obtenemos modificación en formato UNIX
													$timestamp = Storage::lastModified($file);
													//Restamos segundos para estar en zona horaria
													$timestamp -= 14400;
													//Tranformamos fecha
													$timestamp = gmdate("Y-m-d \a \l\a\s H:i:s", $timestamp);
												}
											?>
										@if (isset($archivo[1]))
											@if ($archivo[1] == 'pdf')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@elseif ($archivo[1] == 'png')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
												<a href="downloadfile.{{$kind3}}.{{$id2}}.{{$filename}}.{{$archivo[1]}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@else {{-- No hay extensión --}}
												<a href="downloadfile2.{{$kind3}}.{{$id2}}.{{$filename}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
												{{ $filename }}
											@endif
										@else {{-- No hay extensión --}}
											<a href="downloadfile2.{{$kind3}}.{{$id2}}.{{$filename}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@endif
											@foreach (Session::get('roles') as $role)
												@if ($role == 1)
													<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $o->id }},10,'{{ $filename }}')"><br/>
													<?php break; //si es admin terminamos ciclo para no repetir menú ?>
												@endif
											@endforeach
											</td>
											<td>{{ $timestamp }}</td>
										</tr>
									@endforeach
									</table>
								@endif
							</td>
						</tr>
					@endforeach
				</table>
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

