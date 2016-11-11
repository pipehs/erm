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
				@if ($kind2 == 0 || $kind2 == 1 || $kind2 == 3 || $kind2 == 4 || $kind2 == 5 || $kind2 == 6)
					@foreach ($elements as $element)
						@if (isset($element['audit_plan']))
							<h3 style="color:#0B0B61;">Plan de auditor&iacute;a: {{ $element['audit_plan'] }}</h3>
						@endif
						<h4 style="color:#0B0B61;">Auditor&iacute;a: {{ $element['name'] }}</h3>
						<p><b>{{ $element['description'] }}</b></p>
						@if (isset($element['process']))
							<p><b><u>Proceso afectado: {{ $element['process']}}</u></b></p>
						@endif
						<table class="table" >
						<thead>
						<th style="padding-left:5%; width:35%;">Hallazgo</th>
						<th style="padding-left:5%; width:65%;">Documentos</th>
						</thead>
						@foreach ($element['issues'] as $issue)
						<tr>
							<td style="padding-left:5%">
							<p style="color:#3A01DF"><b>Nombre: {{ $issue['name'] }}</b></p>
							<p style="color:#3A01DF"><b>Descripción: {{ $issue['description'] }}</b></p>
							<p style="color:#3A01DF"><b>Recomendaciones: {{ $issue['recommendations'] }}</b></p>
							</td>
							<?php $cont = 0; ?>
							<td style="padding-left:5%">
							<table class="table">
							<tr>
							@foreach ($issue['files'] as $file)

							
								
								<td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$file_name = explode('/',$archivo[0]);
									$file_name = $file_name[2];
									$cont += 1;
								?>
								
								@if ($archivo[1] == 'pdf')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@else
									<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@endif

								@if ($cont == 4)
									</td></tr>
									<?php $cont = 0; ?>
								@else
									</td>
								@endif
							@endforeach
							</table>
						</tr>
						@endforeach
						</table>
					@endforeach
				@elseif ($kind2 == 2) {{-- Hallazgos de organización --}}
					<h3 style="color:#0B0B61;">{{ $org_name }}</h3>
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
							<?php $cont = 0; ?>
							<td style="padding-left:5%">
							<table class="table">
							<tr>
							@foreach ($issue['files'] as $file)

							
								
								<td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$file_name = explode('/',$archivo[0]);
									$file_name = $file_name[2];
									$cont += 1;
								?>
								
								@if ($archivo[1] == 'pdf')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@else
									<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@endif

								@if ($cont == 4)
									</td></tr>
									<?php $cont = 0; ?>
								@else
									</td>
								@endif
							@endforeach
							</table>
						</tr>
						@endforeach
						</table>
	
				@endif
			@else
				@if ($kind == 1) {{-- Documentos de controles --}}
					<p>Documentos de controles</p>
					@if ($control_type == 0)
						<h3 style="color:#0B0B61;">Documentos de controles de proceso de organizaci&oacute;n: {{ $org_name }}</h3><br/>
					@elseif ($control_type == 1)
						<h3 style="color:#0B0B61;">Documentos de controles de negocio de organizaci&oacute;n: {{ $org_name }}</h3><br/>
					@endif
						<table class="table">
						<thead>
						<th style="padding-left:5%;">Control</th>

						<th style="padding-left:5%;">Documentos</th>
						</thead>
						@foreach ($elements as $element)
						<tr>
							<td style="padding-left:5%">
							<p style="color:#3A01DF"><b>Nombre: {{ $element['name'] }}</b></p>
							@if ($kind == 5)
								<p style="color:#3A01DF"><b>Descripci&oacute;n: {{ $element['description'] }}</b></p>
							@endif
								<p style="color:#3A01DF"><b>Riesgos involucrados:</b></p>
								<ul>
								@foreach ($element['risks'] as $risk)
									<li><b>{{ $risk->name }} - {{ $risk->description }}</b></li>
								@endforeach
								</ul>
							</td>
							<?php $cont = 0; ?>
							<td style="padding-left:5%">
							<table class="table">
							<tr>
							@foreach ($element['files'] as $file)
								<td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$file_name = explode('/',$archivo[0]);
									$file_name = $file_name[2];
									$cont += 1;
								?>
								
								@if ($archivo[1] == 'pdf')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@else
									<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@endif

								@if ($cont == 4)
									</td></tr>
									<?php $cont = 0; ?>
								@else
									</td>
								@endif
							@endforeach
							</table>
						</tr>
						@endforeach
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
						<?php $cont = 0; ?>
						<td style="padding-left:5%; width:65%;">
						<table class="table" style="background-color: #CEECF5;">
						<tr>
						@foreach ($element['files'] as $file)	
								<td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$file_name = explode('/',$archivo[0]);
									$file_name = $file_name[2];
									$cont += 1;
								?>
								
								@if ($archivo[1] == 'pdf')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@else
									<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@endif

								@if ($cont == 4)
									</td></tr>
									<?php $cont = 0; ?>
								@else
									</td>
								@endif
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
									<?php $cont = 0; ?>
									<td style="padding-left:5%">
									<table class="table" style="background-color: #BCF3C5;">
									<tr>
									@foreach ($ans['files'] as $file2)
										<td>
										<?php //pequeño módulo php para ver el tipo de archivo y nombre
											$archivo = explode('.',$file2);
											$file_name = explode('/',$archivo[0]);
											$file_name = $file_name[2];
											$cont += 1;
										?>
										
										@if ($archivo[1] == 'pdf')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@elseif ($archivo[1] == 'png')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
											<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@else
											<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
											{{ $file_name }}<br/>
										@endif

										@if ($cont == 4)
											</td></tr>
											<?php $cont = 0; ?>
										@else
											</td>
										@endif
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
					<p>Documentos de programas de auditor&iacute;a</p>
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
							<?php $cont = 0; ?>
							<td style="padding-left:5%">
							<table class="table">
							<tr>
							@foreach ($element['files'] as $file)
								<td>
								<?php //pequeño módulo php para ver el tipo de archivo y nombre
									$archivo = explode('.',$file);
									$file_name = explode('/',$archivo[0]);
									$file_name = $file_name[2];
									$cont += 1;
								?>
								
								@if ($archivo[1] == 'pdf')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'doc' || $archivo[1] == 'docx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/word.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'xls' || $archivo[1] == 'xlsx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/excel.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'ppt' || $archivo[1] == 'pptx')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'png')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/png.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@elseif ($archivo[1] == 'jpg' || $archivo[1] == 'jpeg')
									<a href="../storage/app/{{$file}}" download><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@else
									<a href="../storage/app/{{$file}}" download><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>
									{{ $file_name }}<br/>
								@endif

								@if ($cont == 4)
									</td></tr>
									<?php $cont = 0; ?>
								@else
									</td>
								@endif
							@endforeach
							</table>
						</tr>
						@endforeach
						</table>
				@endif
			@endif
			<center>
					{!! link_to_route('documentos', $title = 'Volver', $parameters=NULL, $attributes = ['class'=>'btn btn-danger'])!!}
			<center>
		</div>
	</div>
</div>

			
@stop

@section('scripts2')
{!!Html::script('assets/js/type_documents.js')!!}
@stop