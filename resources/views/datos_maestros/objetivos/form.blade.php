<div class="form-group">
						{!!Form::label('Nombre',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('nombre',null,['class'=>'form-control','required'=>'true'])!!}
						</div>
					</div>

					<div class="form-group">
						{!!Form::label('Descripci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::textarea('descripcion',null,['class'=>'form-control','rows'=>'3','cols'=>'4','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Categor&iacute;a',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
						<!-- No será required true por si no existen categorías ingresadas -->
							{!!Form::select('objective_category_id', $categorias,
								 	   null, 
								 	   ['placeholder' => '- Seleccione una categor&iacute;a -',
								 	   	'id' => 'el2'])
							!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Creaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_creacion',null,['class'=>'form-control','id'=>'input_date','required'=>'true'])!!}
						</div>
					</div>
					<div class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::text('fecha_exp',null,['class'=>'form-control','id'=>'input_date2'])!!}
						</div>
					</div>	
					
					<div class="form-group">
						<center>
						@if (isset($_GET['organizacion']))
							{!!Form::hidden('organizacion',$_GET['organizacion'])!!}
						@else
							{!!Form::hidden('organizacion',$objetivo['organization_id'])!!}
						@endif
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>