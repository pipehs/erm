@if (strstr($_SERVER["REQUEST_URI"],'create'))
<!-- Actualización 20-08-17: Rut chileno o DNI Extranjero -->
			<div class="form-group">
				<label class="col-sm-4 control-label">Nacionalidad *</label>
				<div class="col-sm-3">
				<div class="radio-inline">
					<label> 
						<input type="radio" name="nacionalidad" id="chileno" value="chileno" onclick="nac()">Chileno
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				<div class="radio-inline">
					<label> 
						<input type="radio" name="nacionalidad" id="extranjero" value="extranjero" onclick="nac()">Extranjero
						<i class="fa fa-circle-o"></i>
					</label>
				</div>
				</div>
			</div>
			
			<div id="rut" style="display:none;">
				<div class="form-group">
			        {!!Form::label('Rut *',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-2">
						{!!Form::text('id',null,
						['class'=>'form-control','input maxlength'=>'8','input minlength'=>'7', $disabled])!!}
					</div>
					<div class="col-sm-1">
					{!!Form::select('dv',$dv, 
				 	   null, 
				 	   ['id' => 'el2','placeholder'=>'-',$disabled])!!}
					</div>
				</div>
			</div>
			<div id="dni" style="display:none;">
				<div class="form-group">
			        {!!Form::label('DNI *',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('id2',null,
						['class'=>'form-control','input minlength'=>'7', $disabled])!!}
					</div>
				</div>
			</div>
@endif

					<div class="form-group">
						{!!Form::label('Nombre *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Apellidos *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('surnames',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div id="rol">
						<div class="form-group">
							{!!Form::label('Tipo *',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="role_id[]" multiple required id="el3">
								@foreach ($roles as $id=>$name)

									<?php $i = 0; //contador de roles del usuario 
										  $cont = 0; //contador para ver si es que un rol está seleccionado ?>
									@while (isset($types_selected[$i]))
										@if ($types_selected[$i] == $id)
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
								{!!Form::select('role_id[]',$roles, 
							 	   null, 
							 	   ['id' => 'el3','multiple'=>'true','required'=>'true'])!!}
							@endif
							</div>
							<a href="#" id="agregar_rol">Agregar nuevo tipo</a> <br>
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Correo Electr&oacute;nico *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::email('mail',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Cargo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('position',null,['class'=>'form-control'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Organizaci&oacute;n(es) *',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="organization_id[]" multiple required id="el3">
								@foreach ($organizations as $id=>$name)

									<?php $i = 0; //contador de orgs del usuario 
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
							{!!Form::select('organization_id[]',$organizations, 
						 	   null, 
						 	   ['id' => 'el3','multiple'=>'true','required'=>'true'])!!}
						@endif
						</div>
					</div>
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id'=>'btnsubmit'])!!}
						</center>
					</div>