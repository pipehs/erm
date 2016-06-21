
					<div class="form-group">
						{!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione tipo de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="0">Auditor&iacute;a de Procesos
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="1">Auditor&iacute;a de Negocios
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="2">Auditor&iacute;a de Riesgos
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
						</div>
					</div>

					<b><font color="blue">Ingrese informaci&oacute;n para el plan de auditor&iacute;a</font></b></br>

					<div class="form-group" id="procesos" style="display: none;">
						{!!Form::label('Procesos de la organización',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="processes_id[]" id="processes_id" multiple="multiple">
								<!-- Aquí se agregarán los procesos de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="objetivos" style="display: none;">
						{!!Form::label('Objetivos de la organización',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="objectives_id[]" id="objectives_id" multiple="multiple">
								<!-- Aquí se agregarán los procesos de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_objetivos" style="display: none;">
						{!!Form::label('Riesgos relevantes (del negocio)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="objective_risk_id[]" id="objective_risk_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_procesos" style="display: none;">
						{!!Form::label('Riesgos relevantes (de proceso)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="risk_subprocess_id[]" id="risk_subprocess_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de proceso de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

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

					<div class="form-group">
						{!!Form::label('Horas-Hombre estimadas del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::number('HH_plan',null,['class'=>'form-control','id'=>'HH_plan','min'=>'0'])!!}
						</div>
					</div>

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

					<div id="contador_HH">
						<font color="red">Debe asignar horas hombre al plan</font>
					</div>
					<div class="form-group">
						<center>
							<div style="cursor:hand" id="agregar_auditoria">
								<font color="CornflowerBlue"><u>Agregar Nueva Auditor&iacute;a</u></font>
							</div>
						</center> <br>
						{!!Form::label('Auditor&iacute;as a realizar',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('audits[]',$audits,null,
													['multiple'=>'true','id'=>'auditorias','disabled'=>'true'])!!}
						</div>
					</div>

					<div id="info_auditorias"></div>

					<div id="info_new_auditorias"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'guardar','disabled'=>'true'])!!}
						</center>
					</div>