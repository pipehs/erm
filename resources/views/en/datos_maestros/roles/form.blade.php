
					<div class="form-group">
						{!!Form::label('Name',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>



					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>