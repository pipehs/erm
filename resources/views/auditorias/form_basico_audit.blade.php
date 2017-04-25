	<div class="form-group">
		{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			{!!Form::select('org_id',$organizations,null, 
							 	   ['id' => 'organizations','required'=>'true','placeholder'=>'- Seleccione -'])!!}
		</div>
	</div>
	<div class="form-group">
		{!!Form::label('Plan de auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			<select id="audit_plans" required>
				<!-- Aquí se agregarán los planes de auditoría pertenecientes a la organización a través de Jquery -->
			</select>
		</div>
	</div>

	<div class="form-group">
		{!!Form::label('Auditor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
		<div class="col-sm-4">
			<select name="audit_id" id="audit" required>
				<!-- Aquí se agregarán las auditorías relacionadas al plan seleccionado a través de Jquery -->
			</select>
		</div>
	</div>