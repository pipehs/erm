
					<div class="form-group">
			            {!!Form::label('Rut',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-2">
							{!!Form::text('id',null,
							['class'=>'form-control',$required,'input maxlength'=>'8',$disabled])!!}
						</div>
						<div class="col-sm-1">
						{!!Form::select('dv',array('0','1','2','3','4','5','6','7','8','9','k'), 
					 	   null, 
					 	   ['id' => 'el2','placeholder'=>'-',$required,$disabled])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Apellidos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('apellidos',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Agregado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_creacion',null,['class'=>'form-control','id'=>'input_date','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('tipo',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Correo Electr&oacute;nico',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::email('correo',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Cargo (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('cargo',null,['class'=>'form-control'])!!}
						</div>
					</div>
					{!!Form::label('Organizaci&oacute;n(es)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="row form-group">
						<div class="col-sm-3">
						{!!Form::select('organization_id[]',$organizations, 
					 	   null, 
					 	   ['id' => 'el3','multiple'=>'true','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>