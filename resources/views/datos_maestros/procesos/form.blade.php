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
	<div class="form-group">
		{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::text('expiration_date',null,['class'=>'form-control','id'=>'input_date2'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('¿Depende de otro proceso?',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::select('process_id',$procesos, 
			 	   null, 
			 	   ['id' => 'el2','placeholder'=>'No'])!!}
		</div>
	</div>
					
	<div class="form-group">
		<center>
		{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
		</center>
	</div>