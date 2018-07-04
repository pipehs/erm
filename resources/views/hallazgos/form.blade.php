				@if (isset($processes))
					<div class="form-group">
						{!!Form::label('Seleccione proceso involucrado *',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('process_id',$processes,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif (isset($subprocesses))
					<div class="form-group">
						{!!Form::label('Seleccione subproceso involucrado *', null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('subprocess_id',$subprocesses,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($controls))
					<div class="form-group">
						{!!Form::label('Seleccione control involucrado *', null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('control_id',$controls,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($audit_programs))
					<div class="form-group">
						{!!Form::label('Seleccione programa de auditoría involucrado *',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('audit_audit_plan_audit_program_id',$audit_programs,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($audits))
					<div class="form-group">
						{!!Form::label('Seleccione auditoría involucrada *',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('audit_audit_plan_id',$audits,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($audit_tests))
					<div class="form-group">
						<label for="audit_test_id" class="col-sm-2 control-label">Seleccione prueba involucrada *</label>
						<div class="col-sm-8">
							<select name="audit_test_id" required="true">
								<option value="" selected disabled>- Seleccione -</option>

								@foreach ($audit_tests as $test2)
									<option value="{{ $test2->id }}">{{ $test2->audit_plan }} - {{ $test2->audit }} - {{ $test2->audit_program }} - {{ $test2->name }}</option>
								@endforeach
							</select>
						</div>
					</div>
				@elseif(isset($risks))
					<div class="form-group">
						<label for="audit_test_id" class="col-sm-2 control-label">Seleccione Riesgo(s) involucrado(s) *</label>
						<div class="col-sm-8">
							<select name="organization_risk_id[]" multiple="true">
							@foreach ($risks as $risk)

								<?php $i = 0; //contador de causas 
									$cont = 0; //contador para ver si una causa está seleccionada ?>
								@while (isset($risks_selected[$i]))
									@if ($risks_selected[$i]->id == $risk->id)
										<option value="{{ $risk->id }}" selected>{{ $risk->name }} - {{ $risk->description }}</option>
										<?php $cont += 1; ?>
									@endif
									<?php $i += 1; ?>
								@endwhile

								@if ($cont == 0)
									<option value="{{ $risk->id }}">{{ $risk->name }} - {{ $risk->description }}</option>
								@endif

							@endforeach
							</select>
						</div>
					 </div>
				@endif
					<div class="form-group">
						{!!Form::label('Nombre *',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recomendaciones',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('recommendations',null,['id'=>'recommendations','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>
				

					<div class="form-group">
						{!!Form::label('Clasificaci&oacute;n',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							<select name="classification" id="classification">
							<option value="" selected>- Seleccione -</option>
							@foreach ($classifications as $c)
								<option value="{{ $c->id }}">{{ $c->name }}</option>
							@endforeach
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Valor económico',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::number('economic_value',null,['id'=>'economic_value','class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Comentarios',null,['class'=>'col-sm-2 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('comments',null,['id'=>'comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>

				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					@if (isset($org))
						{!!Form::hidden('kind',$kind)!!}
					@endif
				@endif

				@if (isset($org_id))
					{!!Form::hidden('org_id',$org_id)!!}
				@endif

				@if (isset($test))
					{!!Form::hidden('test_id',$test_id)!!}
				@endif

				@if (isset($evaluation_id))
					{!!Form::hidden('evaluation_id',$evaluation_id)!!}
				@endif

				<div class="form-group">
					<label for="file" class="col-sm-2 control-label">Para mayor detalle del hallazgo, puede agregar archivos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
					<div class="col-sm-8">
						<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
					</div>
						
				</div>
	

				<div class="form-group">
					<center>
					{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
					</center>
				</div>

				@if (isset($test_id) AND $test_id != NULL)
					<center>
						{!! link_to_route('hallazgos_test', $title = 'Volver', $parameters = $test_id, $attributes = ['class'=>'btn btn-danger'])!!}		
					<center>
				@elseif (isset($evaluation_id))
					<center>
						{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}
					</center>
				@else

					{!!Form::hidden('kind',$kind)!!}
					{!!Form::hidden('organization_id',$org_id)!!}
					<center>
						{!! link_to_route('hallazgos_lista', $title = 'Volver', $parameters = ['organization_id' => $org_id, 'kind' => $kind], $attributes = ['class'=>'btn btn-danger'])!!}
					<center>
				@endif