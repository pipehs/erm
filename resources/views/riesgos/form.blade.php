@if (isset($objetivos) || isset($subprocesos))	
		<div id="cargando"><br></div>		
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
							 	   ['id'=>'risk_category_id','placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
		               <div class="row">
		                 <label for="risk_subcategory_id" class='col-sm-4 control-label'>Sub-categor&iacute;a</label>
		                 <div class="col-sm-5">
		                    <select id="risk_subcategory_id" name="risk_subcategory_id"></select>
		                 </div>
		              </div>
		            </div>
			@if (isset($stakeholders) && isset($stakeholder))	
					<div class="form-group">
						{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
						@if (isset($stakeholder))
							{!!Form::select('stakeholder_id',$stakeholders,$stakeholder->id, 
							 	   ['id'=>'stakeholder','placeholder'=>'- Seleccione -'])!!}
						@else
							{!!Form::select('stakeholder_id',$stakeholders,null, 
							 	   ['id'=>'stakeholder','placeholder'=>'- Seleccione -'])!!}
						@endif
						</div>
					</div>
			@endif
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

					<div class="form-group">
						{!!Form::label('Comentarios',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-5">
							{!!Form::textarea('comments',null,['id'=>'comments','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
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
@if (isset($objetivos) || isset($subprocesos))	
			@if (isset($subprocesos))		
					<div class="form-group">
						<label for="organization_id" class="col-sm-4 control-label">Seleccione otras organizaciones que se encuentren expuestas al riesgo (opcionalmente)</label>
						<div class="col-sm-5">
						{{-- Actualización 17-08-17: Ya no se ocupará ya que mejor sólo se editarán los datos del riesgo sin poder agregar o modificar en otras orgs --}}
						{{-- Act 16-10-17: Si se ocupará para poder agregar más organizaciones en caso de que no se hayan agregado todas al comienzo--}}
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="organization_id[]" multiple id="organization_id">
								@foreach ($organizations as $o)
										<option value="{{ $o['id'] }}">{{ $o['name'] }}</option>
								@endforeach
								</select>
						@else
							{!!Form::select('organization_id[]',$organizations,null, 
							 	   ['id' => 'organization_id','multiple'=>'true'])!!}
						@endif
						</div>
						<div style="cursor:hand" onclick="change_organization()"><font color="CornflowerBlue"><u>Seleccionar subprocesos</u></font></div> <br>
					</div>
					<div id="cargando1"></div>
					<div id="cargando2"></div>
					<div id="other_subprocesses" style="display: none;"></div>
					<div id="other_stakeholders" style="display: none;"></div>
			@endif
@endif
					<div class="form-group">
						<label for="file" class="col-sm-4 control-label">Cargar documentos (para seleccionar más de uno haga click en ctrl + botón izquierdo)</label>
						<div class="col-sm-4">
							<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">
						</div>
					</div>
			@if (isset($org_id))			
					{!!Form::hidden('org_id',$org_id)!!}
			@endif
					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success'])!!}
						</center>
					</div>