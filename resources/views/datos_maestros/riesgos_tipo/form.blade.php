					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::textarea('descripcion',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('risk_category_id',$categorias,
							 	   null, 
							 	   ['id' => 'el2','required'=>'true','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Creaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_creacion',null,['class'=>'form-control','id'=>'input_date','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_expiracion',null,['class'=>'form-control','id'=>'input_date2'])!!}
						</div>
					</div>
					<div id="causa">
						<div class="form-group">
							{!!Form::label('Causa ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('cause_id',$causas,
								 	   null, 
								 	   ['id' => 'el2','placeholder'=>'No especifica'])!!}
							</div>
							<a href="#" id="agregar_causa">Agregar Nueva Causa</a> <br>
						</div>
					</div>
					<div id="efecto">
						<div class="form-group">
							{!!Form::label('Efecto ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('effect_id',$efectos,
								 	   null, 
								 	   ['id' => 'el2','placeholder'=>'No especifica'])!!}
							</div>
							<a href="#" id="agregar_efecto">Agregar Nuevo Efecto</a> <br>
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>