						{!!Form::hidden('audit_plans',$audit_plan,['id'=>'audit_plans'])!!}

						<div class="form-group">
							{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('type2',['1'=>'Prueba de control','2'=>'Prueba de riesgo',
																'3'=>'Prueba de subproceso'],$type2,
																['id'=>'type2_test_1','required'=>'true','onchange'=>'getType(1)','placeholder'=>'- Seleccione -','required'=>'true'])!!}
							</div>
						</div>

						<div id="categoria_test_1" style="display:none;"></div>

						<div class="form-group">
								{!!Form::label('Prueba: Nombre',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::text('name',null,['id'=>'name_test_1','class'=>'form-control',
																'required'=>'true'])!!}
								</div>
						</div>

						<div class="form-group">

								{!!Form::label('Descripción',null,['class'=>'col-sm-4 control-label'])!!}	
								<div class="col-sm-4">
									{!!Form::textarea('description',null,['id'=>'description_test_1','class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
								</div>

						</div>

						<div class="form-group">
							{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('type',['0'=>'Prueba de diseño','1'=>'Prueba de efectividad operativa',
															'2'=>'Prueba de cumplimiento','3'=>'Prueba sustantiva'],null,
															['id'=>'type','placeholder'=>'- Seleccione -'])!!}
							</div>
						</div>

						<div class="form-group">

							{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('stakeholder_id',$stakeholders,null,['required'=>'true',
								'placeholder'=>'- Seleccione -','id'=>'stakeholder_id','class'=>'first-disabled'])!!}
							</div>

						</div>

						<div class="form-group">

								{!!Form::label('Horas-hombre',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::number('hh',null,['id'=>'hh_test_1','class'=>'form-control','min'=>'1'])!!}
								</div>
						</div>

						<div class="form-group">
							<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
							<div class="col-sm-4">
								<input id="file-1" type="file" class="file" name="file_test[]" multiple=true data-preview-file-type="any">
							</div>
										
						</div>
