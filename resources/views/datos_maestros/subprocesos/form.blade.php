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
						{!!Form::label('Fecha Creaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_creacion',null,['class'=>'form-control','id'=>'input_date','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_exp',null,['class'=>'form-control','id'=>'input_date2'])!!}
						</div>
					</div>
						{!!Form::label('Seleccione organizaciones involucradas 
						(para seleccionar m&aacute;s de una presione ctrl + click)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="row form-group">
						<div class="col-sm-3">
							{!!Form::select('organization_id[]',$organizaciones,
							 	   null, 
							 	   ['id' => 'el2', 'class' => 'form-control','multiple'=>'true','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Proceso Involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('process_id', 
							array('' => '- Seleccione -',
								  '1' => 'Proceso 1',
					 	  		  '2' => 'Proceso 2',
					 	  		  '3' => 'Proceso 3'),
							 	   null, 
							 	   ['id' => 'el2', 'class' => 'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿Depende de otro subproceso?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('subprocess_id',$subprocesos,
							 	   null, 
							 	   ['id' => 'el2', 'class' => 'form-control','placeholder'=>'No'])!!}
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
				