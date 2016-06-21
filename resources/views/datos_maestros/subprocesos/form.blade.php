					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
						{!!Form::label('Organizaci&oacute;n(es)',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="row form-group">
						<div class="col-sm-3">
							{!!Form::select('organization_id[]',$organizaciones,
							 	   null, 
							 	   ['id' => 'el2','multiple'=>'true','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Proceso Involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('process_id',$procesos,
							 	   null, 
							 	   ['id' => 'el2','required'=>'true','placeholder'=>'-Seleccione-'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿Depende de otro subproceso?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('subprocess_id',$subprocesos,
							 	   null, 
							 	   ['id' => 'el2','placeholder'=>'No'])!!}
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
				