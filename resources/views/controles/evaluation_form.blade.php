<div id="cargando"><br></div>
<div class="form-group">
	<label for="results" class="col-sm-4 control-label">Descripci&oacute;n de la prueba *</label>
	<div class="col-sm-4">
			{!!Form::textarea('description',null,['class'=>'form-control','rows'=>'5','required'=>'true'])!!}
	</div>
</div>

<div class="form-group">
	<label for="results" class="col-sm-4 control-label">Resultado</label>
	<div class="col-sm-4">
		{!!Form::select('results',['1'=>'Efectiva','2'=>'Inefectiva'],
							null,['placeholder'=>'- Seleccione -','id'=>'results'])!!}
	</div>
</div>

@if (!isset($eval))
<div id="comments" style="display:none;">
@else
	@if ($eval->results == 1)
		<div id="comments">
	@else
		<div id="comments" style="display:none;">
	@endif
@endif
	<div class="form-group">
		<label for="comments" class="col-sm-4 control-label">Comentarios</label>
		<div class="col-sm-4">
				{!!Form::textarea('comments',null,['class'=>'form-control','rows'=>'5','id'=>'comments2'])!!}
		</div>
	</div>
</div>
@if (!isset($eval))
<div id="hallazgos" style="display:none;">
@else
	@if ($eval->results == 2)
		<div id="hallazgos">
	@else
		<div id="hallazgos" style="display:none;">
	@endif
@endif
<center>
<button type="button" class="btn btn-info" onClick="gestionarHallazgos()">Gestionar Hallazgos</button>
</center><br/><br/>
</div>

{!!Form::hidden('control_id',$id)!!}
{!!Form::hidden('org_id',$org_id)!!}
<div class="form-group">
	<center>
		{!!Form::submit('Guardar', ['id'=>'btnsubmit','class'=>'btn btn-success'])!!}
	</center>
</div>

	<center>
		<p><a href="evaluar_controles2?organization_id={{$org_id}}&control_id={{$id}}" class="btn btn-danger">Volver</a></p>
	<center>