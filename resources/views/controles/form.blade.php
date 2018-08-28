				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						<label for="subneg" class="col-sm-4 control-label">Seleccione si es control de negocio o proceso *</label>
						<div class="col-sm-8">
							{!!Form::select('subneg',['0'=>'Proceso','1'=>'Negocio'],null, 
							 	   ['id' => 'subneg','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('risk_category_id',$categories,
							 	   null, 
							 	   ['id'=>'risk_category_id','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
		               <div class="row">
		                 <label for="risk_subcategory_id" class='col-sm-4 control-label'>Sub-categor&iacute;a</label>
		                 <div class="col-sm-8">
		                    <select id="risk_subcategory_id" name="risk_subcategory_id"></select>
		                 </div>
		              </div>
		            </div>

					<div class="form-group" id="riesgos" style="display: none;">
						<label for="select_riesgos" class="col-sm-4 control-label" id="label_riesgos">Seleccione Riesgo *</label>
						<div class="col-sm-8">
							<select name="select_riesgos[]" id="select_riesgos" multiple="multiple">
								<option value="">-Seleccione-</option>
								<!-- Aquí se agregarán los riesgos/subprocesos a través de Jquery (en caso de que el usuario lo solicite) -->
							</select>
						</div>
					</div>
				@endif
					<div class="form-group">
						{!!Form::label('Nombre *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					
				@if (Session::get('org') == 'Parque Arauco' || Session::get('org') == 'Parque Arauco (testing)')
					
					<div class="form-group">
						{!!Form::label('Establecimiento',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('establishment',null,['id'=>'establishment','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Aplicaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('application',null,['id'=>'application','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Supervisi&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('supervision',null,['id'=>'supervision','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>
				@else
					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>
				@endif
					<div class="form-group">
						{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('type',['0'=>'Manual','1'=>'Semi-Automático','2'=>'Automático'],null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Periodicidad',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('periodicity',['0'=>'Diario','1'=>'Semanal','2'=>'Mensual','6' => 'Trimestral','3'=>'Semestral','4'=>'Anual','5'=>'Cada vez que ocurra'],null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Prop&oacute;sito',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('purpose',['0'=>'Preventivo','1'=>'Detectivo','2'=>'Correctivo'],null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							@if (isset($stakeholder) && $stakeholder != NULL)
								<select name="stakeholder_id">
									<option value="">- Seleccione -</option>
									@foreach ($stakeholders as $id => $name)
										@if ($id == $stakeholder->id)
											<option value="{{$id}}" selected="true">{{$name}}</option>
										@else
											<option value="{{$id}}">{{$name}}</option>
										@endif
									@endforeach
								</select>
							@else
								{!!Form::select('stakeholder_id',$stakeholders,null, ['placeholder'=>'- Seleccione -'])!!}
							@endif
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Costo esperado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::number('expected_cost',null,['id'=>'expected_cost','class'=>'form-control'])!!}
						</div>
					</div>

				@if (isset($control_org))
					<div class="form-group">
						{!!Form::label('Porcentaje de contribución',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('porcentaje_cont',['0'=>'0% - Deficiente','50'=>'50% - Media','85'=>'85% - Buena','95'=>'95% - Óptima'],$control_org->cont_percentage,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@else
					<div class="form-group">
						{!!Form::label('Porcentaje de contribución',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('porcentaje_cont',['0'=>'0% - Deficiente','50'=>'50% - Media','85'=>'85% - Buena','95'=>'95% - Óptima'],null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@endif

					<div class="form-group">
						{!!Form::label('Control clave',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::select('key_control',['1'=>'Si','0'=>'No'],null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="objective" class="col-sm-4 control-label">Objetivo de control</label>
						<div class="col-sm-8">
							{!!Form::textarea('objective',null,['id'=>'objective','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="test_plan" class="col-sm-4 control-label">Plan de pruebas</label>
						<div class="col-sm-8">
							{!!Form::textarea('test_plan',null,['id'=>'test_plan','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Cuenta(s) Contable(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-6">
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
							<select name="financial_statement_id[]" multiple id="financial_statement_id">
							@foreach ($financial_statements as $id=>$name)

								<?php $i = 0; //contador de fs
										 $cont = 0; //contador para ver si una fs está seleccionada ?>
								@while (isset($fs_selected[$i]))
									@if ($fs_selected[$i]->id == $id)
										<option value="{{ $id }}" selected>{{ $name }}</option>
										<?php $cont += 1; ?>
									@endif
									<?php $i += 1; ?>
								@endwhile

								@if ($cont == 0)
									<option value="{{ $id }}">{{ $name }}</option>
								@endif

							@endforeach
							</select>
						@else
							{!!Form::select('financial_statement_id[]',$financial_statements,null,['id' => 'financial_statement_id','multiple'=>'true'])!!}
						@endif
						</div>
						<div style="cursor:hand" onclick="add_fs()">
							<button type="button" class="btn btn-primary btn-app-sm btn-circle">
								<i class="fa fa-plus"></i>
							</button>
						</div> 
						<br>
					</div>

					<div id="new_fs">
					</div>

				@if (isset($control_org))
					<div class="form-group">
						{!!Form::label('Evidencia',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('evidence',$control_org->evidence,['id'=>'evidence','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="comments" class="col-sm-4 control-label">Comentarios del control</label>
						<div class="col-sm-8">
							{!!Form::textarea('comments',$control_org->comments,['id'=>'comments','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>
				@else
					<div class="form-group">
						{!!Form::label('Evidencia',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-8">
							{!!Form::textarea('evidence',null,['id'=>'evidence','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="comments" class="col-sm-4 control-label">Comentarios del control</label>
						<div class="col-sm-8">
							{!!Form::textarea('comments',null,['id'=>'comments','class'=>'form-control','rows'=>'5','cols'=>'4'])!!}
						</div>
					</div>
				@endif

				{!!Form::hidden('org_id',$org)!!}

					<div class="form-group">
						<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-8">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
					</div>
				<!--	
					<div class="form-group">
						<label for="evidence_doc" class="col-sm-4 control-label">
						Cargar Documentos (para seleccionar más de 1 use ctrl + click)</label>
						<div class="col-sm-8">
							<input type="file" name="evidence_doc" multiple>
						</div>
					</div>
				-->
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
						</center>
					</div>