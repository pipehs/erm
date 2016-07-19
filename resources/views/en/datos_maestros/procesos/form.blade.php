	<div class="form-group">	
	{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
		</div>
					</div>

	<div class="form-group">
		{!!Form::label('Description',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
		</div>
	</div>
	<div id="exp_date" class="form-group">
		{!!Form::label('Expiration Date',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('¿Depends on other process?',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-3">
			{!!Form::select('process_id',$procesos, 
			 	   null, 
			 	   ['id' => 'el2','placeholder'=>'No'])!!}
		</div>
	</div>
					
	<div class="form-group">
		<center>
		{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
		</center>
	</div>