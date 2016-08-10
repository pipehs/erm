
						{!!Form::hidden('audit_plans',$audit_plan,['id'=>'audit_plans'])!!}

						<div class="form-group">
							{!!Form::label('Category',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('type2',['1'=>'Control test','2'=>'Risk test',
																'3'=>'Subprocess test'],$type2,
																['id'=>'type2_test_1','required'=>'true','onchange'=>'getType(1)','placeholder'=>'- Select -','required'=>'true'])!!}
							</div>
						</div>

						<div id="categoria_test_1" style="display:none;"></div>

						<div class="form-group">
								{!!Form::label('Test: Name',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::text('name',null,['id'=>'name_test_1','class'=>'form-control',
																'required'=>'true'])!!}
								</div>
						</div>

						<div class="form-group">

								{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}	
								<div class="col-sm-4">
									{!!Form::textarea('description',null,['id'=>'description_test_1','class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
								</div>

						</div>

						<div class="form-group">
							{!!Form::label('Kind',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('type',['0'=>'Design test','1'=>'Operational effectiveness test',
															'2'=>'Compliance test','3'=>'Substantive test'],null,
															['id'=>'type','placeholder'=>'- Select -'])!!}
							</div>
						</div>

						<div class="form-group">

							{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-4">
								{!!Form::select('stakeholder_id',$stakeholders,null,['required'=>'true',
								'placeholder'=>'- Select -','id'=>'stakeholder_id','class'=>'first-disabled'])!!}
							</div>

						</div>

						<div class="form-group">

								{!!Form::label('Hours-Man',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-4">
									{!!Form::number('hh',null,['id'=>'hh_test_1','class'=>'form-control','min'=>'1'])!!}
								</div>
						</div>

						@if (!isset($evidence) || $evidence == NULL)
							<div class="form-group">

								<label for="file_1" class="col-sm-4 control-label">For more detail of the test, you can add a file (optional)</label>

								<div class="col-sm-4">
								<input type="file" name="file_1" id="file1" class="inputfile" />
								<label for="file1">Upload Evidence</label>
							</div>
							</div>
						@else
							<center>
							<a href="../storage/app/pruebas_auditoria/{{$evidence[0]['url'] }}" style="cursor:hand">
							<font color="CornflowerBlue"><u>Download Evidence</u></font></a>
							<br>
							</center>
						@endif