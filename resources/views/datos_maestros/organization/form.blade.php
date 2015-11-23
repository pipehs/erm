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

					

					<div class="form-group">
						{!!Form::label('¿Depende de otra organizaci&oacute;n?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
						{!!Form::select('organization_id',$organizations, 
					 	   null, 
					 	   ['id' => 'el2', 'class' => 'form-control','placeholder'=>'No'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿Es una organizaci&oacute;n de servicios compartidos?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							<div class="radio-inline">
								<label>
								{!!Form::radio('serv_compartidos',0,true)!!}  No
								<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>
								{!!Form::radio('serv_compartidos',1)!!}  Si
								<i class="fa fa-circle-o"></i>
								</label>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>