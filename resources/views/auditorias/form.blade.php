					
					<div class="form-group">
						{!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder' => '- Seleccione -'])!!}
						</div>
					</div>

					<b><font color="blue">Ingrese informaci&oacute;n para el plan de auditor&iacute;a</font></b></br>

					<div class="form-group">
						{!!Form::label('Nombre plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control',
														'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					
					<div class="form-group">
						{!!Form::label('Objetivos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('objectives',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Alcances',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('scopes',null,['class'=>'form-control','rows'=>'3',
															'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recursos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('resources',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>
<!--
					<div class="form-group">
						{!!Form::label('Horas-Hombre estimadas del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::number('HH_plan',null,['class'=>'form-control','id'=>'HH_plan','min'=>'0'])!!}
						</div>
					</div>
-->
					<div class="form-group">
						{!!Form::label('Auditor responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="stakeholder_id" id="stakeholder_id">
								<!-- Aquí se mostrarán todos los stakeholders de la organización seleccionada -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Equipo de auditores',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="stakeholder_team[]" id="stakeholder_team" multiple="multiple">
								<!-- Aquí se mostrarán todos los stakeholders exceptuando el auditor jefe -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Metodolog&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('methodology',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group" id="init_date">
						{!!Form::label('Fecha Inicio',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::date('initial_date',null,['class'=>'form-control'
																,'required'=>'true','id'=>'initial_date','onblur'=>'compararFechas(this.value,form.final_date.value)'])!!}
						</div>
					</div>

					<div class="form-group" id="fin_date">
						{!!Form::label('Fecha t&eacute;rmino',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::date('final_date',null,['class'=>'form-control'
															,'required'=>'true','id'=>'final_date'
																,'required'=>'true','onblur'=>'compararFechas(form.initial_date.value,this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Norma(s) asociada(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('rules',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<b><font color="blue">Ingrese informaci&oacute;n para cada auditor&iacute;a del plan</font></b></br>
<!--
					<div id="contador_HH">
						<font color="red">Debe asignar horas hombre al plan</font>
					</div>
-->
			@if (strstr($_SERVER["REQUEST_URI"],'edit'))
					<div class="form-group">
						{!!Form::label('Auditor&iacute;as seleccionadas previamente',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="audits_sel[]" multiple="true" disabled="true" id="auditorias_sel">
							@foreach ($audits_selected as $audit)
								<option value="{{$audit->id}}" selected="true">{{$audit->name}}</option>
							@endforeach
							</select>
						</div>
					</div>

					<div id="info_auditorias_sel"></div>
			@endif
				<div class="form-group">
						<center>
							<div style="cursor:hand" id="agregar_auditoria">
								<font color="CornflowerBlue"><u>Agregar Nueva Auditor&iacute;a</u></font>
							</div>
						</center> <br>
					@if (strstr($_SERVER["REQUEST_URI"],'edit'))
						<label for="audits" class="col-sm-4 control-label">Seleccione si desea agregar más Auditorías</label>
					@else
						{!!Form::label('Auditor&iacute;as a realizar',null,['class'=>'col-sm-4 control-label'])!!}
					@endif
						<div class="col-sm-8">
							{!!Form::select('audits[]',$audits,null,
													['multiple'=>'true','id'=>'auditorias'])!!}
						</div>
				</div>
					<div id="info_auditorias"></div>

					<div id="info_new_auditorias"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'guardar','disabled'=>'true'])!!}
						</center>
					</div>
					