@if (!isset($risk_id))
				<div class="form-group">
					{!!Form::label('Select risk',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						<select name="risk_id" id="risk_id" required="true">
							<option value="" selected disabled>- Select -</option>
							<option value="" disabled>- Process risks associated -</option>
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
								<option value="" disabled>There are no process risks associated</option>
							@endif

							@if ($objective_risk != null)
								<option value="" disabled>- Bussiness risks -</option>
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
								<option value="" disabled>No bussiness risks</option>
							@endif
						</select>
					</div>
				</div>
			@else
				{!!Form::hidden('risk_id',$risk_id)!!}
			@endif
				<div class="form-group">
					{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Kind',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
					{!!Form::select('type',['0'=>'Manual','1'=>'Automatic'], 
				 	   null, 
				 	   ['id' => 'el2','placeholder'=>'- Select -','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Periodicity',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::select('periodicity',['0'=>'Diary','1'=>'Weekly','2'=>'Monthly',
												'3'=>'Biannual','4'=>'Annual','5'=>'Each time it ocurrs'],null,
												['placeholder'=>'- Select -'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Measurement unit',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
					{!!Form::select('uni_med',['0'=>'Percentage','1'=>'Amount','2'=>'Quantity'], 
				 	   null, 
				 	   ['id' => 'uni_med','placeholder'=>'- Select -','required'=>'true'])!!}
					</div>
				</div>
			<div id="cotas" style="display:none;">
				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							<b>Specify whether the maximum numeric value will be green or red.</b><br>
							<br>
							<div class="radio-inline">
								<label>
									{!!Form::radio('min_max',1,['onchange'=>'ordenamiento()'])!!} Green Maximum
									
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>
									{!!Form::radio('min_max',2,['onchange'=>'ordenamiento()'])!!} Red Maximum
									<i class="fa fa-circle-o"></i>
								</label>
							</div>
						</center>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							<b>Input the intervals according to the following image</b>
						</center>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-10 control-label">
						<center>
							{!! HTML::image('assets/img/gradiente_kri.png',"Image not found", array('id' => 'gradiente', 'title' => 'Example of gradient','height'=>'95px','width'=>'450px')) !!}
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
					{!!Form::label('Green description',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_green',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_green'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Yellow description',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_yellow',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_yellow'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Red description',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description_red',null,['class'=>'form-control',
										'required'=>'true','rows'=>'3','id'=>'description_red'])!!}
					</div>
				</div>
			</div>
				<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-success','id'=>'guardar'])!!}
						</center>
				</div>