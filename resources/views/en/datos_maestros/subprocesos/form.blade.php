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
						{!!Form::label('Expiration date',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Organization(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="organization_id[]" multiple required id="el3">
								@foreach ($organizaciones as $id=>$name)

									<?php $i = 0; //contador de orgs del subproceso 
										  $cont = 0; //contador para ver si es que una org está seleccionada ?>
									@while (isset($orgs_selected[$i]))
										@if ($orgs_selected[$i] == $id)
											<option value="{{ $id }}" selected>{{ $name }}</option>
											<?php $cont += 1; ?>
										@endif
										<?php $i += 1; ?>
									@endwhile

									@if ($cont == 0) //no estaba seleccionada
										<option value="{{ $id }}">{{ $name }}</option>
									@endif

								@endforeach
								</select>
						@else
							{!!Form::select('organization_id[]',$organizaciones, 
						 	   null, 
						 	   ['id' => 'el3','multiple'=>'true','required'=>'true'])!!}
						@endif
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Process involved',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('process_id',$procesos,
							 	   null, 
							 	   ['id' => 'el2','required'=>'true','placeholder'=>'-Seleccione-'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿It depends on other subprocess?',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('subprocess_id',$subprocesos,
							 	   null, 
							 	   ['id' => 'el2','placeholder'=>'No'])!!}
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Save', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>
				