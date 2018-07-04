						{!!Form::hidden('audit_plans',$audit_plan,['id'=>'audit_plans'])!!}

						<div class="form-group">
							<label for="type1" class="col-sm-4 control-label">Tipo de prueba *</label>
							<div class="col-sm-6">
								{!!Form::select('type1',['1'=>'Prueba a nivel de procesos','2'=>'Prueba a nivel de entidad'], null,['id'=>'type1','required'=>'true','placeholder'=>'- Seleccione -','required'=>'true'])!!}
							</div>
						</div>


						<div id="categoria_test_1" style="display:none;"></div>

						<div class="form-group">
								{!!Form::label('Prueba: Nombre *',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-6">
									{!!Form::text('name',null,['id'=>'name_test_1','class'=>'form-control','required'=>'true'])!!}
								</div>
						</div>

						<div class="form-group">

								{!!Form::label('Descripción *',null,['class'=>'col-sm-4 control-label'])!!}	
								<div class="col-sm-6">
									{!!Form::textarea('description',null,['id'=>'description_test_1','class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
								</div>

						</div>

						<div class="form-group">
							{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-6">
							@if (!isset($test_selected))
								{!!Form::select('type',$evaluation_tests,null,['id'=>'type','placeholder'=>'- Seleccione -'])!!}
							@else
								<select name="type">
								<option value="">- Seleccione -</option>
								@foreach ($evaluation_tests as $id=>$name)
									@if ($id == $test_selected)
										<option value="{{$id}}" selected="true">{{$name}}</option>
									@else
										<option value="{{$id}}">{{$name}}</option>
									@endif
								@endforeach
								</select>
							@endif
							</div>
						</div>

						<div class="form-group">

							{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-6">
								{!!Form::select('stakeholder_id',$stakeholders,null,['placeholder'=>'- Seleccione -','id'=>'stakeholder_id','class'=>'first-disabled'])!!}
							</div>

						</div>

						<div class="form-group">

								{!!Form::label('Horas-hombre',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-6">
									{!!Form::number('hh',null,['id'=>'hh_test_1','class'=>'form-control','min'=>'1'])!!}
								</div>
						</div>

						<div class="form-group">
							<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
							<div class="col-sm-6">
								<input id="file-1" type="file" class="file" name="file_test[]" multiple=true data-preview-file-type="any">
							</div>
										
						</div>
