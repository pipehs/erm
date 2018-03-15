					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'5','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Misi&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('mision',null,['class'=>'form-control','rows'=>'4','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Visi&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('vision',null,['class'=>'form-control','rows'=>'4','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Cliente objetivo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('target_client',null,['class'=>'form-control','rows'=>'2','cols'=>'4'])!!}
						</div>
					</div>

					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>	

					<div class="form-group">
						{!!Form::label('¿Depende de otra organizaci&oacute;n?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
						{!!Form::select('organization_id',$organizations, 
					 	   null, 
					 	   ['id' => 'el2','placeholder'=>'No'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿Es una organizaci&oacute;n de servicios compartidos?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							<div class="radio-inline">
								<label>
								{!!Form::radio('shared_services',0,true)!!}  No
								<i class="fa fa-circle-o"></i>
								</label>
							</div>
							<div class="radio-inline">
								<label>
								{!!Form::radio('shared_services',1)!!}  Si
								<i class="fa fa-circle-o"></i>
								</label>
							</div>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('EBT',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::number('ebt',null,['id'=>'ebt','class'=>'form-control','min'=>'0'])!!}
						</div>
						<div class="col-sm-2">
							{!!Form::select('kind_ebt',$kinds,null,['id'=>'kind_ebt','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['id'=>'submit','class'=>'btn btn-success','id'=>'btnsubmit'])!!}
						</center>
					</div>