			@if (!isset($risk_id))
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
			@else
				{!!Form::hidden('risk_id',$risk_id)!!}
			@endif
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
				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							<b>Indique si el valor n&uacute;merico m&aacute;ximo lo poseer&aacute; el verde o el rojo</b><br>
							<br>
							<div class="radio-inline">
								<label>
									{!!Form::radio('min_max',1,['onchange'=>'ordenamiento()'])!!}  Verde m&aacute;ximo
									
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>
									{!!Form::radio('min_max',2,['onchange'=>'ordenamiento()'])!!}  Rojo m&aacute;ximo
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
						</center>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							<b>Ingrese los intervalos en concordancia a la imagen siguiente</b>
						</center>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							{!! HTML::image('assets/img/gradiente_kri.png',"Imagen no encontrada", array('id' => 'gradiente', 'title' => 'Gradiente de ejemplo','height'=>'95px','width'=>'450px')) !!}
						</center>
					</div>
				</div>

				<div class="form-group" id="div_green_min">
					<label for="green_min" class="col-sm-4 control-label"><img src="../public/assets/img/1.png" height="21px" width="21px"></label>
					<div class="col-sm-3">
						{!!Form::number('green_min',null,['class'=>'form-control','required'=>'true','id'=>'green_min','step'=>'0.1', 'onchange'=>'ordenamiento()'])!!}
					</div>
					<div id="error_min_green"></div>
				</div>

				<div class="form-group" id="div_interval_min">
					<label for="interval_min" class="col-sm-4 control-label"><img src="../public/assets/img/2.png" height="21px" width="21px"></label>
					<div class="col-sm-3">
						{!!Form::number('interval_min',null,['class'=>'form-control','required'=>'true','id'=>'interval_min','step'=>'0.1', 'onchange'=>'ordenamiento()'])!!}
					</div>
					<div id="error_interval_min"></div>
				</div>

				<div class="form-group" id="div_interval_max">
					<label for="interval_max" class="col-sm-4 control-label"><img src="../public/assets/img/3.png" height="21px" width="21px"></label>
					<div class="col-sm-3">
						{!!Form::number('interval_max',null,['class'=>'form-control','required'=>'true','id'=>'interval_max','step'=>'0.1', 'onchange'=>'ordenamiento()'])!!}
					</div>
					<div id="error_interval_max"></div>
				</div>

				<div class="form-group" id="div_red_max">
					<label for="red_max" class="col-sm-4 control-label"><img src="../public/assets/img/4.png" height="21px" width="21px"></label>
					<div class="col-sm-3">
						{!!Form::number('red_max',null,['class'=>'form-control','required'=>'true','id'=>'red_max','step'=>'0.1', 'onchange'=>'ordenamiento()'])!!}
					</div>
					<div id="error_max_red"></div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción verde',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_green',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_green'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción amarillo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_yellow',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_yellow'])!!}
					</div>
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