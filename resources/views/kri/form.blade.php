				<div class="form-group">
					{!!Form::label('Seleccione riesgo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						<select name="risk_id" id="risk_id" required="true">
							<option value="" selected disabled>- Seleccione -</option>
							<option value="" disabled>- Riesgos de proceso asociados -</option>
							@if ($risk_subprocess != null)
								@foreach ($risk_subprocess as $risk)
									<!-- Buscamos Riesgo que corresponde en caso que sea editar -->
									@if (isset($kri) && $kri->risk_id == $risk['id'])
										<option value="{{ $risk['id'] }}" selected>
											{{ $risk['name'] }}
										</option>
									@else
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</option>
									@endif
								@endforeach
							@else
								<option value="" disabled>No hay riesgos de proceso asociados</option>
							@endif

							@if ($objective_risk != null)
								<option value="" disabled>- Riesgos de negocio -</option>
								@foreach ($objective_risk as $risk)
									<!-- Buscamos Riesgo que corresponde en caso que sea editar -->
									@if (isset($kri) && $kri->risk_id == $risk['id'])
										<option value="{{ $risk['id'] }}" selected>
											{{ $risk['name'] }}
										</option>
									@else
										<option value="{{ $risk['id'] }}">
											{{ $risk['name'] }}
										</ooption>
									@endif
								@endforeach
							@else
								<option value="" disabled>No hay riesgos de negocio</option>
							@endif
						</select>
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
					{!!Form::select('type',['0'=>'Manual','1'=>'Automático'], 
				 	   null, 
				 	   ['id' => 'el2','placeholder'=>'- Seleccione -','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Periodicidad',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::select('periodicity',['0'=>'Diario','1'=>'Semanal','2'=>'Mensual',
												'3'=>'Semestral','4'=>'Anual','5'=>'Cada vez que ocurra'],null,
												['placeholder'=>'- Seleccione -'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Unidad de medida',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
					{!!Form::select('uni_med',['0'=>'Porcentaje','1'=>'Monto','2'=>'Cantidad'], 
				 	   null, 
				 	   ['id' => 'uni_med','placeholder'=>'- Seleccione -','required'=>'true'])!!}
					</div>
				</div>
			<div id="cotas" style="display:none;">
				<div class="form-group" id="div_green_min">
					{!!Form::label('Cota mínima verde',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('green_min',null,['class'=>'form-control','required'=>'true','id'=>'green_min','step'=>'0.1'])!!}
					</div>
					<div id="error_min_green"></div>
				</div>

				<div class="form-group" id="div_green_max">
					{!!Form::label('Cota máxima verde',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('green_max',null,['class'=>'form-control','required'=>'true','id'=>'green_max','step'=>'0.1'])!!}
					</div>
					<div id="error_max_green"></div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción verde',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_green',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_green'])!!}
					</div>
				</div>

				<div class="form-group" id="div_yellow_min">
					{!!Form::label('Cota mínima amarillo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('yellow_min',null,['class'=>'form-control','required'=>'true','id'=>'yellow_min','step'=>'0.1'])!!}
					</div>
					<div id="error_min_yellow"></div>
				</div>

				<div class="form-group" id="div_yellow_max">
					{!!Form::label('Cota máxima amarillo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('yellow_max',null,['class'=>'form-control','required'=>'true','id'=>'yellow_max','step'=>'0.1'])!!}
					</div>
					<div id="error_max_yellow"></div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción amarillo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_yellow',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_yellow'])!!}
					</div>
				</div>

				<div class="form-group" id="div_red_min">
					{!!Form::label('Cota mínima roja',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('red_min',null,['class'=>'form-control','required'=>'true','id'=>'red_min','step'=>'0.1'])!!}
					</div>
					<div id="error_min_red"></div>
				</div>

				<div class="form-group" id="div_red_max">
					{!!Form::label('Cota máxima roja',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('red_max',null,['class'=>'form-control','required'=>'true','id'=>'red_max'])!!}
					</div>
					<div id="error_max_red"></div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción rojo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_red',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_red'])!!}
					</div>
				</div>
			</div>
				<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
				</div>