				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						<label for="subneg" class="col-sm-4 control-label">Seleccione si es control de negocio o proceso</label>
						<div class="col-sm-4">
							{!!Form::select('subneg',['0'=>'Proceso','1'=>'Negocio'],null, 
							 	   ['id' => 'subneg','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@endif

					<div class="form-group" id="riesgos" style="display: none;">
						<label for="select_riesgos" class="col-sm-4 control-label" id="label_riesgos">Seleccione Riesgo</label>
						<div class="col-sm-4">
							<select name="select_riesgos[]" id="select_riesgos" multiple="multiple">
								<option value="">-Seleccione-</option>
								<!-- Aquí se agregarán los riesgos/subprocesos a través de Jquery (en caso de que el usuario lo solicite) -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					
					<div class="form-group">
						{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('type',['0'=>'Manual','1'=>'Semi-Automático','2'=>'Automático'],
							null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Periodicidad',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('periodicity',['0'=>'Diario','1'=>'Semanal','2'=>'Mensual',
													'3'=>'Semestral','4'=>'Anual','5'=>'Cada vez que ocurra'],null,
													['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Prop&oacute;sito',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('purpose',['0'=>'Preventivo','1'=>'Detectivo','2'=>'Correctivo'],null,
													['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,null,
													['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Costo esperado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::number('expected_cost',null,['id'=>'expected_cost','class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Evidencia',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('evidence',null,['id'=>'nombre','class'=>'form-control'])!!}
						</div>
					</div>

					{!!Form::hidden('org_id',$org)!!}

					<div class="form-group">
						<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-4">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
					</div>
				<!--	
					<div class="form-group">
						<label for="evidence_doc" class="col-sm-4 control-label">
						Cargar Documentos (para seleccionar más de 1 use ctrl + click)</label>
						<div class="col-sm-4">
							<input type="file" name="evidence_doc" multiple>
						</div>
					</div>
				-->
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>