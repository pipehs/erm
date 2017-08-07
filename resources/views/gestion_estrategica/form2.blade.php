				@if (isset($org_id))
					<div class="form-group">
						{!!Form::label('Objetivo(s) involucrado(s)',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
					@if (strstr($_SERVER["REQUEST_URI"],'edit'))
						<select name="objectives_id[]" multiple required id="el3">
						@foreach ($objectives as $id=>$name)

							<?php $i = 0; //contador de objetivos seleccionados
								  $cont = 0; //contador para ver si es que un objetivo está seleccionado ?>
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
						{!!Form::select('objective_id[]',$objectives, 
					 	   null, 
					 	   ['id' => 'el2','required'=>'true','multiple'=>'true'])!!}
					@endif
						</div>
					</div>
				@endif

				<div class="form-group">
					{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('name',null,['class'=>'form-control','required'=>'true'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Descripción',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'3'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Forma de cálculo',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('calculation_method',null,['class'=>'form-control'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Responsable',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::select('stakeholder_id',$stakeholders,null,
												['placeholder'=>'- Seleccione -'])!!}
					</div>
				</div>

				<div class="form-group">
					{!!Form::label('Meta del KPI',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::number('goal',null,['class'=>'form-control'])!!}
					</div>
				</div>

				<div class="form-group" id="init_date">
					{!!Form::label('Fecha Inicio',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::date('initial_date',null,['class'=>'form-control','id'=>'initial_date', 'onblur'=>'compararFechas(this.value,form.final_date.value)'])!!}
					</div>
				</div>

				<div class="form-group" id="fin_date">
					{!!Form::label('Fecha t&eacute;rmino',null,['class'=>'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
							{!!Form::date('final_date',null,['class'=>'form-control','id'=>'final_date','onblur'=>'compararFechas(form.initial_date.value,this.value)'])!!}
					</div>
				</div>

				
				@if (isset($obj_id))
					{!!Form::hidden('obj_id',$obj_id)!!}
				@elseif (isset($org_id))
					{!!Form::hidden('org_id',$org_id)!!}
				@endif
			
				<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-success','id'=>'btnsubmit'])!!}
						</center>
				</div>