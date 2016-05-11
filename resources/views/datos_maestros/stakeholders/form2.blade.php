					<div class="form-group">
			            {!!Form::label('Rut',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-2">
							{!!Form::text('id',null,
							['class'=>'form-control',$required,'input maxlength'=>'8',$disabled])!!}
						</div>
						<div class="col-sm-1">
						{!!Form::select('dv',$dv, 
					 	   null, 
					 	   ['id' => 'el2','placeholder'=>'-',$required,$disabled])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Apellidos',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('surnames',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div id="rol">
						<div class="form-group">
							{!!Form::label('Tipo',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('role_id[]',$roles, 
							 	   null, 
							 	   ['id' => 'el3','multiple'=>'true','required'=>'true'])!!}
							</div>
							<a href="#" id="agregar_rol">Agregar nuevo tipo</a> <br>
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Correo Electr&oacute;nico',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::email('mail',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Cargo (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('position',null,['class'=>'form-control'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Organizaci&oacute;n(es)',null,['class'=>'col-sm-4 control-label'])!!}
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