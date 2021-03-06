@extends('master')

@section('title', 'Ver documentos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('documentos','Ver documentos')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Documentos</span>
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

			@if (isset($kind2)) {{-- Documentos de hallazgos --}}
				@if ($kind2 == 0)
					<h3 style="color:#0B0B61;">Hallazgos de Proceso</h3>
				@elseif ($kind2 == 1)
					<h3 style="color:#0B0B61;">Hallazgos de Subproceso</h3>
				@elseif ($kind2 == 2)
					<h3 style="color:#0B0B61;">Hallazgos de Organizaci&oacute;n</h3>
				@elseif ($kind2 == 3)
					<h3 style="color:#0B0B61;">Hallazgos de Controles de proceso</h3>
				@elseif ($kind2 == 4)
					<h3 style="color:#0B0B61;">Hallazgos de Controles de entidad</h3>
				@elseif ($kind2 == 5)
					<h3 style="color:#0B0B61;">Hallazgos de Programas de auditor&iacute;a</h3>
				@elseif ($kind2 == 6)
					<h3 style="color:#0B0B61;">Hallazgos de Auditor&iacute;a</h3>
				@elseif ($kind2 == 7)
					<h3 style="color:#0B0B61;">Hallazgos de Riesgo</h3>
				@endif

				@if ($kind2 == 0 || $kind2 == 1 || $kind2 == 3 || $kind2 == 4 || $kind2 == 5 || $kind2 == 6 || $kind2 == 7)

					@if (empty($elements))
						<br/><br/><h4><center><b>No hay documentos asociados.</b></center></h4><br/><br/>
					@else
						@foreach ($elements as $element)
							@if (isset($element['audit_plan']))
								<h3 style="color:#0B0B61;">Plan de auditor&iacute;a: {{ $element['audit_plan'] }}</h3>
							
								<h4 style="color:#0B0B61;">Auditor&iacute;a: {{ $element['name'] }}</h3>
							@endif
							<p><b>{{ $element['description'] }}</b></p>
							@if (isset($element['process']))
								<p><b><u>Proceso afectado: {{ $element['process']}}</u></b></p>
							@endif
							
							@foreach ($element['issues'] as $issue)
							<table class="table" border="1">
							<thead>
							<th style="padding-left:5%; width:35%;">Hallazgo</th>
							<th style="padding-left:5%; width:65%;">Documentos</th>
							</thead>
							<tr>
								<td style="padding-left:5%">
								<p style="color:#3A01DF"><b>Nombre: {{ $issue['name'] }}</b></p>
								<p style="color:#3A01DF"><b>Descripción: {{ $issue['description'] }}</b></p>
								<p style="color:#3A01DF"><b>Recomendaciones: {{ $issue['recommendations'] }}</b></p>
								</td>
								<td style="padding-left:5%">
									<table class="table">
									<thead>
										<th>Nombre archivo</th>
										<th>Fecha carga</th>
									</thead>
									@foreach ($issue['files'] as $file)
									<tr>
										<td>
										<?php //pequeño módulo php para ver el tipo de archivo y nombre
											$archivo = explode('.',$file);
											$ext = $archivo[1]; //tipo archivo
											$filename = explode('/',$archivo[0]);
											$id = $filename[1]; //id del elemento
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
										@if ($archivo[1] == 'pdf')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx' || $archivo[1] == 'DOC' || $archivo[1] == 'DOCX')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx' || $archivo[1] == 'XLS' || $archivo[1] == 'XLSX')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx' || $archivo[1] == 'PPT' || $archivo[1] == 'PPTX')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@elseif ($archivo[1] == 'png' || $archivo[1] == 'PNG')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg' || $archivo[1] == 'JPG' || $archivo[1] == 'JPEG')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@else
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
											{{ $filename }}
										@endif

										@foreach (Session::get('roles') as $role)
											@if ($role == 1)
												<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $issue['id'] }},2,'{{ $filename }}')"><br/>
												<?php break; //si es admin terminamos ciclo para no repetir menú ?>
											@endif
										@endforeach
										</td>
										<td>
											{{ $timestamp }}
										</td>
									</tr>
									@endforeach
									</table>
									</td>
								</tr>
							</table>
	
								@if (!empty($issue['action_plans']))
									@foreach ($issue['action_plans'] as $ap)
										<table class="table" style="background-color: #BCF3C5;" border="1">
										<thead>
											<th style="padding-left:5%; width:35%;">Plan de acción: {{ $ap->description }}</th>
											<th style="padding-left:5%; width:65%;">Documentos</th>
										</thead>
										
										<tr>
											<td style="padding-left:5%">
											<p style="color:#3A01DF"><b>
											@if ($ap->status == 0)
												Estado: En progreso
											@elseif ($ap->status == 1)
												Estado: Cerrado
											@endif
											</b></p>
											</td>
											<td style="padding-left:5%">
											<table class="table" style="background-color: #BCF3C5;">
											<thead>
												<th>Nombre archivo</th>
												<th>Fecha carga</th>
											</thead>
											@foreach ($ap->files as $file2)
											<tr><td>
												<?php //pequeño módulo php para ver el tipo de archivo y nombre
													$archivo = explode('.',$file2);
													$ext = $archivo[1]; //tipo archivo
													$filename = explode('/',$archivo[0]);
													$id = $filename[1]; //id del elemento
													$kind3 = $filename[0]; //por ej. evidencias_hallazgos
													$filename = $filename[2];
													if (Storage::exists($file2)) //Comprobación de existencia (probablemente innecesaria)
													{	
														//obtenemos modificación en formato UNIX
														$timestamp = Storage::lastModified($file2);
														//Restamos segundos para estar en zona horaria
														$timestamp -= 14400;
														//Tranformamos fecha
														$timestamp = gmdate("Y-m-d \a \l\a\s H:i:s", $timestamp);

													}
												?>
												
												@if ($archivo[1] == 'pdf')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'png')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@else
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@endif
												@foreach (Session::get('roles') as $role)
													@if ($role == 1)
														<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $ap->id }},7,'{{ $filename }}')"><br/>
													<?php break; //si es admin terminamos ciclo para no repetir menú ?>
													@endif
												@endforeach
											</td>
											<td>{{ $timestamp }}</td>
											</tr>
											@endforeach
											</table>
										</table>
									@endforeach
								@else
									<table class="table" style="background-color: #BCF3C5;" border="1">
									<tr><td>
									<p style="background-color: #BCF3C5; padding:1%"><b>Hallazgo sin plan de acción aún</b></p></td></tr>
									</table>
								@endif

							@endforeach
							
						@endforeach
					@endif
				@elseif ($kind2 == 2) {{-- Hallazgos de organización --}}
						<p><b>{{ $org_description }}</b></p>

						<table class="table">
						<thead>
							<th style="padding-left:5%;">Hallazgo</th>
							<th style="padding-left:5%;">Documentos</th>
						</thead>
						@foreach ($issues as $issue)
						<tr>
							<td style="padding-left:5%">
							<p style="color:#3A01DF"><b>Nombre: {{ $issue['name'] }}</b></p>
							<p style="color:#3A01DF"><b>Descripción: {{ $issue['description'] }}</b></p>
							<p style="color:#3A01DF"><b>Recomendaciones: {{ $issue['recommendations'] }}</b></p>
							</td>
							<td style="padding-left:5%">
							<table class="table">
							<thead>
								<th>Nombre archivo</th>
								<th>Fecha carga</th>
							</thead>
							@foreach ($issue['files'] as $file)
								<tr><td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$ext = $archivo[1]; //tipo archivo
									$filename = explode('/',$archivo[0]);
									$id = $filename[1]; //id del elemento
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
								
								@if ($archivo[1] == 'pdf')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@elseif ($archivo[1] == 'png')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@else
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $filename }}
								@endif
								@foreach (Session::get('roles') as $role)
									@if ($role == 1)
										<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $issue['id'] }},2,'{{ $filename }}')"><br/>
										<?php break; //si es admin terminamos ciclo para no repetir menú ?>
									@endif
								@endforeach
								</td>
								<td>{{ $timestamp }}</td>
								</tr>
							@endforeach
							</table>
						</tr>
						@endforeach
						</table>
						
						@if (!empty($issue['action_plans']))
									@foreach ($issue['action_plans'] as $ap)
										<table class="table" style="background-color: #BCF3C5;" border="1">
										<thead>
											<th style="padding-left:5%; width:35%;">Plan de acción: {{ $ap->description }}</th>
											<th style="padding-left:5%; width:65%;">Documentos</th>
										</thead>
										
										<tr>
											<td style="padding-left:5%">
											<p style="color:#3A01DF"><b>
											@if ($ap->status == 0)
												Estado: En progreso
											@elseif ($ap->status == 1)
												Estado: Cerrado
											@endif
											</b></p>
											</td>
											<td style="padding-left:5%">
											<table class="table" style="background-color: #BCF3C5;">
											<thead>
												<th>Nombre archivo</th>
												<th>Fecha carga</th>
											</thead>
											@foreach ($ap->files as $file2)
											<tr><td>
												<?php //pequeño módulo php para ver el tipo de archivo y nombre
													$archivo = explode('.',$file2);
													$ext = $archivo[1]; //tipo archivo
													$filename = explode('/',$archivo[0]);
													$id = $filename[1]; //id del elemento
													$kind3 = $filename[0]; //por ej. evidencias_hallazgos
													$filename = $filename[2];
													if (Storage::exists($file2)) //Comprobación de existencia (probablemente innecesaria)
													{	
														//obtenemos modificación en formato UNIX
														$timestamp = Storage::lastModified($file2);
														//Restamos segundos para estar en zona horaria
														$timestamp -= 14400;
														//Tranformamos fecha
														$timestamp = gmdate("Y-m-d \a \l\a\s H:i:s", $timestamp);

													}
												?>
												
												@if ($archivo[1] == 'pdf')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'png')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@else
													<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
													{{ $filename }}
												@endif
												@foreach (Session::get('roles') as $role)
													@if ($role == 1)
														<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $ap->id }},7,'{{ $filename }}')"><br/>
													<?php break; //si es admin terminamos ciclo para no repetir menú ?>
													@endif
												@endforeach
											</td>
											<td>{{ $timestamp }}</td>
											</tr>
											@endforeach
											</table>
										</table>
									@endforeach
								@else
									<table class="table" style="background-color: #BCF3C5;" border="1">
									<tr><td>
									<p style="background-color: #BCF3C5; padding:1%"><b>Hallazgo sin plan de acción aún</b></p></td></tr>
									</table>
								@endif
				@endif
			@else
				@if ($kind == 1) {{-- Documentos de controles --}}
					<p>Documentos asociados al control {{ $control['name'] }}. </p>
						<h3 style="color:#0B0B61;">Control: {{ $control['name'] }}</h3><br/>
						<table class="table">
						<thead>
						<th style="padding-left:5%;">Información</th>

						<th style="padding-left:5%;">Documentos</th>
						</thead>

						<tr>
							<td style="padding-left:5%">
							<p ><b style="color:#3A01DF">Descripci&oacute;n control:</b> {{ $control['description'] }}</p>
								<p style="color:#3A01DF"><b>Riesgos involucrados:</b></p>
								<ul>
								@foreach ($risks as $risk)
									<li><b>{{ $risk->name }} - {{ $risk->description }}</b></li>
								@endforeach
								</ul>
							</td>
							<td style="padding-left:5%">
							<table class="table">
							<thead>
								<th>Nombre archivo</th>
								<th>Fecha carga</th>
							</thead>
							@if (empty($files))
								No se han agregado documentos.
							@else
								@foreach ($files as $file)
								<tr><td>
									<?php //pequeño módulo php para ver el tipo de archivo y nombre
										$archivo = explode('.',$file);
										$ext = $archivo[1]; //tipo archivo
										$filename = explode('/',$archivo[0]);
										$id = $filename[1]; //id del elemento
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
									
									@if ($ext == 'pdf' || $ext == 'PDF')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($ext == 'doc' || $ext == 'docx' || $ext == 'DOC' || $ext == 'DOCX')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($ext == 'xls' || $ext == 'xlsx' || $ext == 'XLS' || $ext == 'XLSX')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($ext == 'ppt' || $ext == 'pptx' || $ext == 'PPT' || $ext == 'PPTX')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($ext == 'png' || $ext == 'PNG')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@else
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@endif
									@foreach (Session::get('roles') as $role)
										@if ($role == 1)
											<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $co->id }},3,'{{ $filename }}')"><br/>
											<?php break; //si es admin terminamos ciclo para no repetir menú ?>
										@endif
									@endforeach
									</td>
									<td>{{ $timestamp }}</td>
									</tr>
								@endforeach
							@endif
							</table>
						</tr>
						</table>
				@elseif ($kind == 3) {{-- Documentos de notas (y respuestas de notas si es que hay) --}}
					<p>Documentos de notas y sus posibles respuestas por parte del auditor</p>
						
					@foreach ($elements as $element)
					<table class="table" style="background-color: #CEECF5;">
					<thead>
					<th style="padding-left:5%; width:35%;" >Nota</th>
					<th style="padding-left:5%;">Documentos</th>
					</thead>
					<tr>
						<td style="padding-left:5%; width:35%;">
						<p style="color:#3A01DF"><b>Nombre: {{ $element['name'] }}</b></p>
						<p style="color:#3A01DF"><b>Descripción: {{ $element['description'] }}</b></p>
						</td>
						<td style="padding-left:5%; width:65%;">
						<table class="table" style="background-color: #CEECF5;">
						<thead>
							<th>Nombre archivo</th>
							<th>Fecha carga</th>
						</thead>
						@foreach ($element['files'] as $file)	
							<tr><td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$ext = $archivo[1]; //tipo archivo
									$filename = explode('/',$archivo[0]);
									$id = $filename[1]; //id del elemento
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
								
								@if ($archivo[1] == 'pdf')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@else
									<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $filename }}<br/>
								@endif
								@foreach (Session::get('roles') as $role)
									@if ($role == 1)
										<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $element['id'] }},4,'{{ $filename }}')"><br/>
										<?php break; //si es admin terminamos ciclo para no repetir menú ?>
									@endif
								@endforeach
								</td>
								<td>{{ $timestamp }}</td>
							</tr>
							@endforeach
							</table>
						</tr></table>

							@if (!empty($element['answers']))
								<table class="table" style="background-color: #BCF3C5;">
								<thead>
								<th style="padding-left:5%;">Respuestas de nota {{ $element['name'] }}</th>
								<th style="padding-left:5%;">Documentos</th>
								</thead>
								
								@foreach ($element['answers'] as $ans)
								<tr>
									<td style="padding-left:5%">
									<p style="color:#3A01DF"><b>Respuesta: {{ $ans['answer'] }}</b></p>
									<p style="color:#3A01DF"><b>Fecha: {{ $ans['created_at'] }}</b></p>
									</td>
									<td style="padding-left:5%">
									<table class="table" style="background-color: #BCF3C5;">
									<thead>
										<th>Nombre archivo</th>
										<th>Fecha carga</th>
									</thead>
									@foreach ($ans['files'] as $file2)
									<tr><td>
										<?php //pequeño módulo php para ver el tipo de archivo y nombre
											$archivo = explode('.',$file2);
											$filename = explode('/',$archivo[0]);
											$filename = $filename[2];
											if (Storage::exists($file2)) //Comprobación de existencia (probablemente innecesaria)
											{	
												//obtenemos modificación en formato UNIX
												$timestamp = Storage::lastModified($file2);
												//Restamos segundos para estar en zona horaria
												$timestamp -= 14400;
												//Tranformamos fecha
												$timestamp = gmdate("Y-m-d \a \l\a\s H:i:s", $timestamp);
											}
										?>
										
										@if ($archivo[1] == 'pdf')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@elseif ($archivo[1] == 'png')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@else
											<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
											{{ $filename }}<br/>
										@endif
										@foreach (Session::get('roles') as $role)
											@if ($role == 1)
												<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $ans['id'] }},7,'{{ $filename }}')"><br/>
												<?php break; //si es admin terminamos ciclo para no repetir menú ?>
											@endif
										@endforeach
										</td>
										<td>{{ $timestamp }}</td>
									</tr>
									@endforeach
									</table>
								</tr>
								@endforeach
								</table>
							@else
								<p style="background-color: #BCF3C5; padding:1%"><b>Nota aun sin respuestas</b></p>
							@endif
						@endforeach
				@elseif ($kind == 4 || $kind == 5) {{-- Documentos de programas o pruebas --}}
					@if ($kind == 4)
						<p>Documentos de programas de auditor&iacute;a asociados al plan {{ $audit_plan }}</p>
					@elseif ($kind == 5)
						<p>Documentos de pruebas de auditor&iacute;a asociados al plan {{ $audit_plan }}</p>
					@endif
					<h3 style="color:#0B0B61;">Plan de auditor&iacute;a: {{ $audit_plan }}</h3><br/>

						<table class="table">
						<thead>
						@if ($kind == 4)
							<th style="padding-left:5%;">Programa</th>
						@elseif ($kind == 5)
							<th style="padding-left:5%;">Prueba</th>
						@endif
						<th style="padding-left:5%;">Documentos</th>
						</thead>
						@foreach ($elements as $element)
						<tr>
							<td style="padding-left:5%">
							<p style="color:#3A01DF"><b>Auditor&iacute;a a la que pertenece: {{ $element['audit'] }}</b></p>
							@if ($kind == 5)
								<p style="color:#3A01DF"><b>Programa al que pertenece: {{ $element['program'] }}</b></p>
							@endif
								<p style="color:#3A01DF"><b>Nombre: {{ $element['name'] }}</b></p>
							<p style="color:#3A01DF"><b>Descripción: {{ $element['description'] }}</b></p>
							</td>
							<td style="padding-left:5%">
							<table class="table">
								<thead>
									<th>Nombre archivo</th>
									<th>Fecha carga</th>
								</thead>
							@foreach ($element['files'] as $file)
							<tr><td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);

									if (isset($archivo[1])) //si es que tiene extensión
									{
										$ext = $archivo[1]; //tipo archivo
										$filename = explode('/',$archivo[0]);
										$id = $filename[1]; //id del elemento
										$kind3 = $filename[0]; //por ej. evidencias_hallazgos
										$filename = $filename[2];
									}
									else
									{
										$filename = explode('/',$archivo[0]);
										$id = $filename[1]; //id del elemento
										$kind3 = $filename[0]; //por ej. evidencias_hallazgos
										$filename = $filename[2];
									}
									
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
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'png')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@else
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/rar.jpg" width="30" height="30" /></a><br/>
										{{ $filename }}
									@endif
								@else {{-- No hay extensión --}}
									<a href="downloadfile2.{{$kind3}}.{{$id}}.{{$filename}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
										{{ $filename }}
								@endif

								@if ($kind == 4)
									@foreach (Session::get('roles') as $role)
										{{ $role }}
										@if ($role == 1 || $role == 4)
											<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $element['id'] }},1,'{{ $filename }}')"><br/>
											<?php break; //si es admin terminamos ciclo para no repetir menú ?>
										@endif
									@endforeach
								@elseif ($kind == 5)
									@foreach (Session::get('roles') as $role)
									{{ $role }}
										@if ($role == 1 || $role == 4)
											<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $element['id'] }},0,'{{ $filename }}')"><br/>
											<?php break; //si es admin terminamos ciclo para no repetir menú ?>
										@endif
									@endforeach
								@endif
								</td>
								<td>{{ $timestamp }}</td>
							</tr>
							@endforeach
							</table>
						</tr>
						@endforeach
						</table>
				@elseif ($kind == 6) {{-- Documentos de riesgos --}}
					<p>Documentos asociados al riesgo {{ $risk->name }}</p>
						<h3 style="color:#0B0B61;">Riesgo: {{ $risk->name }}</h3><br/>
						<table class="table">
						<thead>
						<th style="padding-left:5%; width:35%;">Información</th>

						<th style="padding-left:5%; width:65%;">Documentos</th>
						</thead>

						<tr>
							<td style="padding-left:5%">
							<p ><b style="color:#3A01DF">Descripci&oacute;n del riesgo:</b> {{ $risk->description }}</p>
								<p style="color:#3A01DF"><b>Controles asociados:</b></p>
								<ul>
								@foreach ($controls as $control)
									<li><b>{{ $control->name }} - {{ $control->description }}</b></li>
								@endforeach
								</ul>
							</td>
							<td style="padding-left:5%">
							<table class="table">
							<thead>
								<th>Nombre archivo</th>
								<th>Fecha carga</th>
							</thead>
							@if (empty($files))
								No se han agregado documentos.
							@else
								@foreach ($files as $file)
									<tr><td>
									<?php //pequeño módulo php para ver el tipo de archivo y nombre
										$archivo = explode('.',$file);
										$ext = $archivo[1]; //tipo archivo
										$filename = explode('/',$archivo[0]);
										$id = $filename[1]; //id del elemento
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
									
									@if ($archivo[1] == 'pdf')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/word.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'png')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/png.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@else
										<a href="downloadfile.{{$kind3}}.{{$id}}.{{$filename}}.{{$ext}}"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
										{{ $filename }}
									@endif
									@foreach (Session::get('roles') as $role)
										@if ($role == 1)
											<img src="assets/img/btn_eliminar2.png" height="20px" width="20px" onclick="eliminar_ev({{ $risk->id }},6,'{{ $filename }}')"><br/>
											<?php break; //si es admin terminamos ciclo para no repetir menú ?>
										@endif
									@endforeach
									</td>
									<td>{{ $timestamp }}</td>
								</tr>
								@endforeach
							@endif
							</table>
						</tr>
						</table>
				@endif
			@endif
			<center>
				<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
			<center>
		</div>
	</div>
</div>

			
@stop

@section('scripts2')
{!!Html::script('assets/js/type_documents.js')!!}
@stop