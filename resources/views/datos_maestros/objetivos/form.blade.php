					<div class="form-group">
						{!!Form::label('Perspectiva',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('perspective',["1"=>"Financiera","2"=>"Procesos","3"=>"Clientes","4"=>"Aprendizaje"],
								 	   null, 
								 	   ['placeholder' => '- Seleccione una perspectiva -',
								 	   	'id' => 'perspective','required'=>'true'])
							!!}
						</div>
					</div>

					<div id="perspective2" style="display:none;">
							<div class="form-group">
								{!!Form::label('Perspectiva de procesos',null,['class'=>'col-sm-4 control-label'])!!}
								<div class="col-sm-3">
									{!!Form::select('perspective2',['1'=>'Gestión Operacional','2'=>'Gestión de Clientes',
																'3'=>'Gestión de Innovación','4'=>'Reguladores sociales'],null,
																['id'=>'perspective2','placeholder'=>'- Seleccione -'])!!}
								</div>
							</div>
					</div>

					<div class="form-group">
						{!!Form::label('Código objetivo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('code',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

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
						{!!Form::label('Objetivos a los que impacta',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
						@if (strstr($_SERVER["REQUEST_URI"],'edit'))
								<select name="objectives_id[]" multiple id="el3">
								@foreach ($objectives as $id=>$name)

									<?php $i = 0; //contador de objetivos de la org y del plan estratégico
										  $cont = 0; //contador para ver si es que un obj está seleccionado ?>
									@while (isset($objs_selected[$i]))
										@if ($objs_selected[$i] == $id)
											<option value="{{ $id }}" selected>{{ $name }}</option>
											<?php $cont += 1; ?>
										@endif
										<?php $i += 1; ?>
									@endwhile

									@if ($cont == 0) {{--no estaba seleccionada--}}
										<option value="{{ $id }}">{{ $name }}</option>
									@endif

								@endforeach
								</select>
						@else
							{!!Form::select('objectives_id[]',$objectives, 
						 	   null, 
						 	   ['id' => 'el3','multiple'=>'true'])!!}
						@endif
						</div>
					</div>
					
					<div class="form-group">
						<center>
						{!!Form::hidden('strategic_plan_id',$strategic_plan_id)!!}
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>


					