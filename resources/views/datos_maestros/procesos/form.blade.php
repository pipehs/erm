	<div class="form-group">
		{!!Form::label('Organizaci&oacute;n(es) *',null,['class'=>'col-sm-3 control-label'])!!}
		<div class="col-sm-5">
			@if (strstr($_SERVER["REQUEST_URI"],'edit'))
				<select name="organization_id[]" multiple id="organization_id">
				@foreach ($organizations as $id=>$name)
					
					@foreach ($orgs_selected as $os) 
						<?php $cont = 0; //contador para ver si una organización está seleccionada ?>
							@if ($os->organization_id == $id)
								<option value="{{ $id }}" selected>{{ $name }}</option>
								<?php $cont += 1; ?>
							@endif
					@endforeach

					@if (!isset($cont) || $cont == 0)
						<option value="{{ $id }}">{{ $name }}</option>
					@endif

				@endforeach
				</select>
			@else
				{!!Form::select('organization_id[]',$organizations,null, ['id' => 'organization_id','multiple'=>'true','required'=>'true'])!!}
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

	<div id="exp_date" class="form-group">
		{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-3 control-label'])!!}
		<div class="col-sm-5">
			{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('¿Depende de otro proceso?',null,['class'=>'col-sm-3 control-label'])!!}
		<div class="col-sm-5">
			{!!Form::select('process_id',$procesos, 
			 	   null, 
			 	   ['id' => 'el2','placeholder'=>'No'])!!}
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