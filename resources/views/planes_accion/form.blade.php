		@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						{!!Form::label('Seleccione tipo de hallazgo',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('kind',['0'=>'Procesos','1'=>'Subprocesos','2'=>'Organización','3'=>'Controles de proceso','4'=>'Controles de entidad','5'=>'Programas de auditoría','6'=>'Auditorías','7'=>'Pruebas de auditoría'],null,['placeholder'=>'- Seleccione -','id' => 'kind','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Hallazgo involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							<select id="issues" name="issue_id" required="true"></select>
						</div>
					</div>

		@else
					<div class="form-group">
						{!!Form::label('Hallazgo involucrado',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('issue_id',$issues,
							null,['placeholder'=>'- Seleccione -','id'=>'issue_id','disabled'=>'true'])!!}
						</div>
					</div>
		@endif
					<div class="form-group">
						{!!Form::label('Descripcion Plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('description',null,['id'=>'description','class'=>'form-control','rows'=>'3','cols'=>'4'])!!}
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
							{!!Form::date('final_date',
							null,['id'=>'final_date','class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>
			@if (strstr($_SERVER["REQUEST_URI"],'create'))
					<div class="form-group">
						{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('percentage',['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'],null,['placeholder'=>'- Seleccione -','id' => 'percentage'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('progress_comments',null,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>
			@else

				@if (!empty($per) && $per != NULL )
					<div class="form-group">
						{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::select('percentage',['0'=>'0 %','10'=>'10 %','20'=>'20 %','30'=>'30 %','40'=>'40 %','50'=>'50 %','60'=>'60 %','70'=>'70 %', '80' => '80 %', '90' => '90 %', '100' => '100 %'],$per->percentage,['placeholder'=>'- Seleccione -','id' => 'percentage'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('progress_comments',$per->comments,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>
				@else
					<div class="form-group">
						{!!Form::label('Porcentaje de avance del plan',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::number('percentage',null,['id'=>'percentage','class'=>'form-control','min'=>'0','max'=>'100'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Comentarios de avance',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-4">
							{!!Form::textarea('progress_comments',null,['id'=>'progress_comments','class'=>'form-control','rows'=>'6','cols'=>'4'])!!}
						</div>
					</div>
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
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary','id' => 'btnsubmit'])!!}
						</center>
					</div>

				<center>
					{!! link_to_route('action_plans2', $title = 'Volver', $parameters = ['organization_id' => $org_id], $attributes = ['class'=>'btn btn-danger'])!!}
				<center>