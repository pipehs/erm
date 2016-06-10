				@if (isset($processes))
					<div class="form-group">
						{!!Form::label('Seleccione proceso involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('process_id',$processes,
							null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>
				@endif
					<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::text('name',null,['id'=>'nombre','class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'descripcion','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Recomendaciones',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('recommendations',null,['id'=>'recommendations','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>
				

					<div class="form-group">
						{!!Form::label('Clasificaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('classification',['0'=>'Oportunidad de mejora','1'=>'Deficiencia','2'=>'Debilidad significativa'],null,
													['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

				@if (strstr($_SERVER["REQUEST_URI"],'create'))
					{!!Form::hidden('kind',$kind)!!}
				@endif

				@if (isset($org_id))
					{!!Form::hidden('org_id',$org_id)!!}
				@endif

					<div class="form-group">
						<label for="file_1" class="col-sm-4 control-label">Para mayor detalle del hallazgo, puede agregar un archivo (opcional)</label>
						<div class="col-sm-4">
							<input type="file" name="evidence_doc" id="evidence_doc" class="inputfile" /><label for="evidence_doc">Cargue evidencia</label>
						</div>
						
					</div>

				@if (!isset($action_plan) || $action_plan == NULL)
					<center><b><font color="blue">Opcionalmente puede agregar plan de acci&oacute;n</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description_plan',null,['id'=>'descripcion_plan','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Seleccione -'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha límite del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',
							null,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

				@else
					<center><b><font color="blue">Opcionalmente puede agregar (o editar) plan de acci&oacute;n</font></b></center><hr>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<textarea rows="3" cols="4" name="description_plan" class="form-control" id="description_plan">{{ $action_plan->description }}</textarea>
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Seleccione responsable',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('stakeholder_id',$stakeholders,
							null,['placeholder'=>'- Seleccione -','id'=>'stakeholder_id'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Fecha límite del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::date('final_date',$action_plan->final_date,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>

					<div class="form-group">
						<label for="status" class="control-label col-sm-4">Estado del plan</label>
						<div class="col-sm-4">
							<input type="checkbox" name="status" id="status" data-toggle="toggle" data-on="Cerrado" data-off="Abierto" data-width="100" data-offstyle="primary" data-onstyle="danger">
						</div>
					</div>

					{!!Form::hidden('description_plan2',null,['id'=>'description_plan2'])!!}
					{!!Form::hidden('stakeholder_id2',null,['id'=>'stakeholder_id2'])!!}
					{!!Form::hidden('final_date2',null,['id'=>'final_date2'])!!}

				@endif


					<div class="form-group">
						<center>
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>

				<center>
					{!! link_to_route('hallazgos', $title = 'Volver', $parameters = NULL, $attributes = ['class'=>'btn btn-danger'])!!}
				<center>