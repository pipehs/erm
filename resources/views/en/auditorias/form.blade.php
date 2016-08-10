<div class="form-group">
						{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('organization_id',$organizations,null, 
							 	   ['id' => 'orgs','required'=>'true','placeholder'=>'- Select -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Select audit type',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="0">Process Audit
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="1">Bussiness Audit
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
							<div class="radio">
								<label>
									<input type="radio" onchange="kind(this.value)" name="type" id="type" value="2">Risk Audit
									<i class="fa fa-circle-o small"></i>
								</label>
							</div>
						</div>
					</div>

					<b><font color="blue">Input information for the audit plan</font></b></br>

					<div class="form-group" id="procesos" style="display: none;">
						{!!Form::label('Processes of the organization',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="processes_id[]" id="processes_id" multiple="multiple">
								<!-- Aquí se agregarán los procesos de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="objetivos" style="display: none;">
						{!!Form::label('Objectives of the organization',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="objectives_id[]" id="objectives_id" multiple="multiple">
								<!-- Aquí se agregarán los procesos de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_objetivos" style="display: none;">
						{!!Form::label('Relevant risks (bussiness)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="objective_risk_id[]" id="objective_risk_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de negocio de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group" id="riesgos_procesos" style="display: none;">
						{!!Form::label('Relevant risks (process)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="risk_subprocess_id[]" id="risk_subprocess_id" multiple="multiple">
								<!-- Aquí se agregarán los riesgos de proceso de la org seleccionada a través de Jquery -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Plan name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control',
														'required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					
					<div class="form-group">
						{!!Form::label('Objectives',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('objectives',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Scopes',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('scopes',null,['class'=>'form-control','rows'=>'3',
															'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Resources',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('resources',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Hours-Man estimated',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::number('HH_plan',null,['class'=>'form-control','id'=>'HH_plan','min'=>'0'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Audit responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="stakeholder_id" id="stakeholder_id">
								<!-- Aquí se mostrarán todos los stakeholders de la organización seleccionada -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Audit team',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							<select name="stakeholder_team[]" id="stakeholder_team" multiple="multiple">
								<!-- Aquí se mostrarán todos los stakeholders exceptuando el auditor jefe -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Methodology',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('methodology',null,['class'=>'form-control','rows'=>'3',
																'cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group" id="init_date">
						{!!Form::label('Initial date',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::date('initial_date',null,['class'=>'form-control'
																,'required'=>'true','id'=>'initial_date','onblur'=>'compararFechas(this.value,form.final_date.value)'])!!}
						</div>
					</div>

					<div class="form-group" id="fin_date">
						{!!Form::label('Final date',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::date('final_date',null,['class'=>'form-control'
															,'required'=>'true','id'=>'final_date'
																,'required'=>'true','onblur'=>'compararFechas(form.initial_date.value,this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Rules associated',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('rules',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<b><font color="blue">Input information for each audit on the plan</font></b></br>

					<div id="contador_HH">
						<font color="red">You must to assign hours man to the plan</font>
					</div>
					<div class="form-group">
						<center>
							<div style="cursor:hand" id="agregar_auditoria">
								<font color="CornflowerBlue"><u>Add New Audit</u></font>
							</div>
						</center> <br>
						{!!Form::label('Audits to perform',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('audits[]',$audits,null,
													['multiple'=>'true','id'=>'auditorias','disabled'=>'true'])!!}
						</div>
					</div>

					<div id="info_auditorias"></div>

					<div id="info_new_auditorias"></div>

					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-success','id'=>'guardar','disabled'=>'true'])!!}
						</center>
					</div>