				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						{!!Form::label('Seleccione si es control de negocio o proceso',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('subneg',['0'=>'Proceso','1'=>'Negocio'],null, 
							 	   ['id' => 'subneg','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@endif

					<div class="form-group" id="procesos" style="display: none;">
						{!!Form::label('Seleccione Riesgo(s)/Subproceso(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="select_procesos[]" id="select_procesos" multiple="multiple">
								<option value="">-Seleccione-</option>
								<!-- Aquí se agregarán los riesgos/subprocesos a través de Jquery (en caso de que el usuario lo solicite) -->
							</select>
						</div>
					</div>

					<div class="form-group" id="negocios" style="display: none;">
						{!!Form::label('Seleccione Riesgo(s)/Negocio(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select multiple name="select_objetivos[]" id="select_objetivos">
								<option value="">-Seleccione-</option>
								<!-- Aquí se agregarán los riesgos/objetivos a través de Jquery (en caso de que el usuario lo solicite) -->
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

					<div class="form-group">
						{!!Form::label('Cargar evidencia (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::file('evidence_doc',null)!!}
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>