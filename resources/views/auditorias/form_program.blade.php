					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'name','class'=>'form-control',
														'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'description','class'=>'form-control',
							'rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

			@if (isset($audit_audit_plan_audit_program))
					<div class="form-group">
						{!!Form::label('Fecha fin',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('expiration_date',$audit_audit_plan_audit_program->expiration_date,['id'=>'expiration_date','class'=>'form-control'])!!}
						</div>
					</div>
			@else
					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha fin',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('expiration_date',null,['id'=>'expiration_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
			@endif

			<div class="form-group">
				<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
				<div class="col-sm-4">
					<input id="file-1" type="file" class="file" name="file_program[]" multiple=true data-preview-file-type="any">
				</div>
							
			</div>


					