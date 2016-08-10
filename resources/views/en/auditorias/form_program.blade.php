					<div class="form-group">
						{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'name','class'=>'form-control',
														'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'description','class'=>'form-control',
							'rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

			@if (isset($audit_audit_plan_audit_program))
					<div class="form-group">
						{!!Form::label('Final date',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('expiration_date',$audit_audit_plan_audit_program->expiration_date,['id'=>'expiration_date','class'=>'form-control', 'required'=>'true'])!!}
						</div>
					</div>
			@else
					<div id="exp_date" class="form-group">
						{!!Form::label('Final date',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('expiration_date',null,['id'=>'expiration_date','class'=>'form-control', 'required'=>'true','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
			@endif

			@if (isset($audit_audit_plan_audit_program))
					@if ($evidence == NULL)
						<div class="form-group">

							<label for="file_program" class="col-sm-4 control-label">Optionally you can add a file to the program</label>

							<div class="col-sm-4">
								<input type="file" name="file_program" id="fileprogram" class="inputfile" />
								<label for="fileprogram">Upload Evidence</label>
							</div>
						</div>
					@else
						<center>
						<a href="../storage/app/programas_auditoria/{{$evidence[0]['url'] }}" style="cursor:hand">
						<font color="CornflowerBlue"><u>Download Evidence</u></font></a><br>
						</center>
					@endif
			@else
					<div class="form-group">

							<label for="file_program" class="col-sm-4 control-label">Optionally you can add a file to the program</label>

							<div class="col-sm-4">
								<input type="file" name="file_program" id="fileprogram" class="inputfile" />
								<label for="fileprogram">Upload Evidence</label>
							</div>
					</div>
			@endif

					