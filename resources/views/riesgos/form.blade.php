			@if (isset($objetivos))
					<div class="form-group">
						{!!Form::label('Seleccione objetivos involucrados',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="objective_id[]" multiple required id="el2">
								@foreach ($objetivos as $id=>$name)

									<?php $i = 0; //contador de roles del usuario 
									$cont = 0; //contador para ver si es que un rol está seleccionado ?>
									@while (isset($obj_selected[$i]))
										@if ($obj_selected[$i] == $id)
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
								{!!Form::select('objective_id[]',$objetivos,null, 
							 	   ['id' => 'el2','required'=>'true','multiple'=>'true'])!!}
							@endif
						</div>
					</div>

			@elseif (isset($subprocesos))

					<div class="form-group">
						{!!Form::label('Seleccione subprocesos involucrados',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="subprocess_id[]" multiple required id="el2">
								@foreach ($subprocesos as $id=>$name)

									<?php $i = 0; //contador de roles del usuario 
									$cont = 0; //contador para ver si es que un rol está seleccionado ?>
									@while (isset($sub_selected[$i]))
										@if ($sub_selected[$i] == $id)
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
								{!!Form::select('subprocess_id[]',$subprocesos,null, 
							 	   ['id' => 'el2','required'=>'true','multiple'=>'true'])!!}
							@endif
						</div>
					</div>
			@endif

					<div class="form-group">
						{!!Form::label('Puede seleccionar un riesgo tipo creado previamente (opcional)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::select('risk_id',$riesgos_tipo,
							 	   null, 
							 	   ['id' => 'risk_id','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::select('risk_category_id',$categorias,
							 	   null, 
							 	   ['id'=>'categoria','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::select('stakeholder_id',$stakeholders,
							 	   null, 
							 	   ['id'=>'stakeholder','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('P&eacute;rdida esperada',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::number('expected_loss',null,['id'=>'expected_loss','class'=>'form-control','min'=>'0'])!!}
						</div>
					</div>
					<div id="causa">
						<div class="form-group">
							{!!Form::label('Causa(s) ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-5">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="cause_id[]" multiple id="cause_id">
								@foreach ($causas as $id=>$name)

									<?php $i = 0; //contador de causas 
										  $cont = 0; //contador para ver si una causa está seleccionada ?>
									@while (isset($causes_selected[$i]))
										@if ($causes_selected[$i] == $id)
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
								{!!Form::select('cause_id[]',$causas,
								 	   null, 
								 	   ['id' => 'cause_id','multiple'=>'true'])!!}
							@endif
							</div>
							<div style="cursor:hand" onclick="agregar_causa()"><font color="CornflowerBlue"><u>Agregar Nueva Causa</u></font></div> <br>
						</div>
					</div>
					<div id="efecto">
						<div class="form-group">
							{!!Form::label('Efecto(s) ',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-5">
							@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="effect_id[]" multiple id="effect_id">
								@foreach ($efectos as $id=>$name)

									<?php $i = 0; //contador de efectos 
										  $cont = 0; //contador para ver si un efecto está seleccionado ?>
									@while (isset($effects_selected[$i]))
										@if ($effects_selected[$i] == $id)
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
								{!!Form::select('effect_id[]',$efectos,
								 	   null, 
								 	   ['id' => 'effect_id','multiple'=>'true'])!!}
							@endif
							</div>
							<div style="cursor:hand" onclick="agregar_efecto()"><font color="CornflowerBlue"><u>Agregar Nuevo Efecto</u></font></div> <br>
						</div>
					</div>

					<div class="form-group">
						<label for="organization_id" class="col-sm-4 control-label">Seleccione otras organizaciones que se encuentren expuestas al riesgo (opcionalmente)</label>
						<div class="col-sm-5">
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="organization_id[]" multiple id="organization_id">
								@foreach ($organizations as $o)
								<?php $org = explode(',',$o); ?>
										<option value="{{ $org[0] }}">{{ $org[1] }}</option>
								@endforeach
								</select>
						@else
							{!!Form::select('organization_id[]',$organizations,null, 
							 	   ['id' => 'el2','multiple'=>'true'])!!}
						@endif
						</div>
					</div>

					<div class="form-group">
						<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-4">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
					</div>
					
					{!!Form::hidden('org_id',$org_id)!!}
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>