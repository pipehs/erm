					<div class="form-group">
						{!!Form::label('Organizaci&oacute;n(es) *',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="organization_id[]" multiple id="organization_id" required>
								@foreach ($organizaciones as $id=>$name)

									<?php $i = 0; //contador de organizaciones 
										  $cont = 0; //contador para ver si una organización está seleccionada ?>
									@while (isset($orgs_selected[$i]))
										@if ($orgs_selected[$i] == $id)
											<option value="{{ $id }}" selected>{{ $name }}</option>
											<?php $cont += 1; ?>
										@endif
										<?php $i += 1; ?>
									@endwhile

									@if ($cont == 0)
										<option value="{{ $id }}">{{ $name }}</option>
									@endif

								@endforeach
								</select>
							@else
								{!!Form::select('organization_id[]',$organizaciones,
								 	   null, 
								 	   ['id' => 'organization_id','multiple'=>'true','required'=>'true'])!!}
							@endif
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre *',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n *',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Proceso Involucrado *',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::select('process_id',$procesos,
							 	   null, ['id' => 'el2', 'required'=>'true', 'placeholder'=>'-Seleccione-'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('¿Depende de otro subproceso?',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::select('subprocess_id',$subprocesos,
							 	   null, 
							 	   ['id' => 'el2','placeholder'=>'No'])!!}
						</div>
					</div>

					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Sistemas / Plataformas',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('systems',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Habeas data',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('habeas_data',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Marco Regulatorio',null,['class'=>'col-sm-3 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('regulatory_framework',null,['class'=>'form-control'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="file" class="col-sm-3 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)<a href="#" class="popper" data-popbox="pop1">?</a></label>
						<div class="col-sm-5">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>
				