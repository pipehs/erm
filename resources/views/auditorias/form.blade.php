
					<div class="form-group">
						{!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>


					<div class="form-group">
						<center><a href="#" id="agregar_auditoria">Agregar Nueva Auditor&iacute;a</a></center> <br>
						{!!Form::label('Auditor&iacute;as a realizar',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('audits[]',$audits,null,
													['multiple'=>'true','id'=>'auditorias'])!!}
						</div>
					</div>

					<div id="info_auditorias"></div>

					<div id="info_new_auditorias"></div>

					<b><font color="blue">Ingrese informaci&oacute;n para el plan de auditor&iacute;a</font></b></br>

					<div class="form-group">
						{!!Form::label('Riesgos relevantes (del negocio)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="objective_risk_id[]" id="objective_risk_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Riesgos relevantes (de proceso)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="risk_subprocess_id[]"[] id="risk_subprocess_id" multiple="multiple">
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
																'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Alcances',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('scopes',null,['class'=>'form-control','rows'=>'3',
															'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recursos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('resources',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Auditor responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('stakeholder_id',$stakeholders,null,
													['placeholder'=>'- Seleccione -',
													'required'=>'true','id'=>'stakeholder'])!!}
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
																'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha Inicio',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('initial_date',null,['class'=>'form-control','id'=>'input_date2'
																,'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha t&eacute;rmino',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('final_date',null,['class'=>'form-control','id'=>'input_date'
															,'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Norma(s) asociada(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('rules',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::submit('Continuar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>