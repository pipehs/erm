				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						{!!Form::label('Select whether is bussiness or process control',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('subneg',['0'=>'Process','1'=>'Bussiness'],null, 
							 	   ['id' => 'subneg','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@endif

					<div class="form-group" id="procesos" style="display: none;">
						{!!Form::label('Select Risk / Subprocess',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select name="select_procesos[]" id="select_procesos" multiple="multiple">
								<option value="">- Select -</option>
								<!-- Aquí se agregarán los riesgos/subprocesos a través de Jquery (en caso de que el usuario lo solicite) -->
							</select>
						</div>
					</div>

					<div class="form-group" id="negocios" style="display: none;">
						{!!Form::label('Select Risk / Objective',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select multiple name="select_objetivos[]" id="select_objetivos">
								<option value="">- Select -</option>
								<!-- Aquí se agregarán los riesgos/objetivos a través de Jquery (en caso de que el usuario lo solicite) -->
							</select>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>
					
					<div class="form-group">
						{!!Form::label('Kind',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('type',['0'=>'Manual','1'=>'Semi-Automatic','2'=>'Automatic'],
							null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Periodicidad',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('periodicity',['0'=>'Daily','1'=>'Weekly','2'=>'Monthly',
													'3'=>'Biannual','4'=>'Annual','5'=>'Each time it occurs'],null,
													['placeholder'=>'- Select -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Purpose',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('purpose',['0'=>'Preventive','1'=>'Detective','2'=>'Corrective'],null,
													['placeholder'=>'- Select -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Owner',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,null,
													['placeholder'=>'- Select -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Expected cost',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::number('expected_cost',null,['id'=>'expected_cost','class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Evidence',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('evidence',null,['id'=>'nombre','class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Upload evidence (optional)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::file('evidence_doc',null)!!}
						</div>
					</div>

					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>