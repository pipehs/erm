				@if (isset($processes))
					<div class="form-group">
						{!!Form::label('Seleccione proceso involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('process_id',$processes,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif (isset($subprocesses))
					<div class="form-group">
						{!!Form::label('Seleccione subproceso involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('subprocess_id',$subprocesses,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($controls))
					<div class="form-group">
						{!!Form::label('Seleccione control involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('control_id',$controls,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($audit_programs))
					<div class="form-group">
						{!!Form::label('Seleccione programa de auditoría involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_audit_plan_audit_program_id',$audit_programs,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@elseif(isset($audits))
					<div class="form-group">
						{!!Form::label('Seleccione auditoría involucrada',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_audit_plan_id',$audits,
							null,['placeholder'=>'- Seleccione -','required'=>'true'])!!}
						</div>
					</div>
				@endif
					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recomendaciones',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('recommendations',null,['id'=>'recommendations','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>
				

					<div class="form-group">
						{!!Form::label('Clasificaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('classification',['0'=>'Oportunidad de mejora','1'=>'Deficiencia','2'=>'Debilidad significativa'],null,
													['placeholder'=>'- Seleccione -'])!!}
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
						<label for="file" class="col-sm-4 control-label">Para mayor detalle del hallazgo, puede agregar archivos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-4">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
						
					</div>

				@if (!isset($action_plan) || $action_plan == NULL)
					<center><b><font color="blue">Opcionalmente puede agregar plan de acci&oacute;n</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description_plan',null,['id'=>'descripcion_plan','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha límite del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',
							null,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('percentage',['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'],null,['placeholder'=>'- Seleccione -','id' => 'percentage'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('progress_comments',null,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>

				@else
					<center><b><font color="blue">Opcionalmente puede agregar (o editar) plan de acci&oacute;n</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<textarea rows="6" cols="4" name="description_plan" class="form-control" id="description_plan">{{ $action_plan->description }}</textarea>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Seleccione -','id'=>'stakeholder_id'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha límite del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',$action_plan->final_date,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="status" class="control-label col-sm-4">Estado del plan</label>
						<div class="col-sm-4">
							<input type="checkbox" name="status" id="status" data-toggle="toggle" data-on="Cerrado" data-off="Abierto" data-width="100" data-offstyle="primary" data-onstyle="danger">
						</div>
					</div>

					@if (!empty($per) && $per != NULL)
						<div class="form-group">
							{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('percentage',['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'],$per->percentage,['placeholder'=>'- Seleccione -','id' => 'percentage'])!!}
							</div>
						</div>

						<div class="form-group">
							{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::textarea('progress_comments',$per->comments,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
							</div>
						</div>
					@else
						<div class="form-group">
							{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('percentage',['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'],null,['placeholder'=>'- Seleccione -','id' => 'percentage'])!!}
							</div>
						</div>

						<div class="form-group">
							{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::textarea('progress_comments',null,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
							</div>
						</div>
					@endif

					{!!Form::hidden('description_plan2',null,['id'=>'description_plan2'])!!}
					{!!Form::hidden('stakeholder_id2',null,['id'=>'stakeholder_id2'])!!}
					{!!Form::hidden('final_date2',null,['id'=>'final_date2'])!!}
					{!!Form::hidden('percentage2',null,['id'=>'percentage2'])!!}
					{!!Form::hidden('progress_comments2',null,['id'=>'progress_comments2'])!!}

				@endif

					<div class="form-group">
						<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-4">
							<input id="file-1" type="file" class="file" name="evidence_doc2[]" multiple=true data-preview-file-type="any">
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