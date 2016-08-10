				@if (isset($processes))
					<div class="form-group">
						{!!Form::label('Select process involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('process_id',$processes,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>
				@elseif (isset($subprocesses))
					<div class="form-group">
						{!!Form::label('Select subprocess involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('subprocess_id',$subprocesses,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>
				@elseif(isset($controls))
					<div class="form-group">
						{!!Form::label('Select control involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('control_id',$controls,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>
				@elseif(isset($audit_programs))
					<div class="form-group">
						{!!Form::label('Seleccione audit program involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_audit_plan_audit_program_id',$audit_programs,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>
				@elseif(isset($audits))
					<div class="form-group">
						{!!Form::label('Seleccione audit involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('audit_audit_plan_id',$audits,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>
				@endif
					<div class="form-group">
						{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recommendations',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('recommendations',null,['id'=>'recommendations','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>
				

					<div class="form-group">
						{!!Form::label('Classification',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('classification',['0'=>'Oportunidad de mejora','1'=>'Deficience','2'=>'Significant weakness'],null,
													['placeholder'=>'- Select -'])!!}
						</div>
					</div>

				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					{!!Form::hidden('kind',$kind)!!}
				@endif

				@if (isset($org_id))
					{!!Form::hidden('org_id',$org_id)!!}
				@endif

					<div class="form-group">
						<label for="file_1" class="col-sm-4 control-label">For more detail on the issue, you could add a file (optional)</label>
						<div class="col-sm-4">
							<input type="file" name="evidence_doc" id="evidence_doc" class="inputfile" /><label for="evidence_doc">Upload Evidence</label>
						</div>
						
					</div>

				@if (!isset($action_plan) || $action_plan == NULL)
					<center><b><font color="blue">You can optionally create an action plan</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Plan description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description_plan',null,['id'=>'descripcion_plan','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Select responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Select -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Plan deadline',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',
							null,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

				@else
					<center><b><font color="blue">Optionally you can add (or edit) an action plan</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Plan description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<textarea rows="3" cols="4" name="description_plan" class="form-control" id="description_plan">{{ $action_plan->description }}</textarea>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Select responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Select -','id'=>'stakeholder_id'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Plan deadline',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',$action_plan->final_date,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="status" class="control-label col-sm-4">Plan status</label>
						<div class="col-sm-4">
							<input type="checkbox" name="status" id="status" data-toggle="toggle" data-on="Cerrado" data-off="Abierto" data-width="100" data-offstyle="primary" data-onstyle="danger">
						</div>
					</div>

					{!!Form::hidden('description_plan2',null,['id'=>'description_plan2'])!!}
					{!!Form::hidden('stakeholder_id2',null,['id'=>'stakeholder_id2'])!!}
					{!!Form::hidden('final_date2',null,['id'=>'final_date2'])!!}

				@endif


					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

				<center>
					{!! link_to_route('hallazgos', $title = 'Return', $parameters = NULL, $attributes = ['class'=>'btn btn-danger'])!!}
				<center>