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
						{!!Form::label('Perspectiva',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::select('perspective',["1"=>"Financiera","2"=>"Procesos","3"=>"Clientes","4"=>"Aprendizaje"],
								 	   null, 
								 	   ['placeholder' => '- Seleccione una perspectiva -',
								 	   	'id' => 'el2'])
							!!}
						</div>
					</div>
					<div id="exp_date" class="form-group">
						{!!Form::label('Fecha Expiraci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
						<div class="col-sm-3">
							{!!Form::date('expiration_date',null,['class'=>'form-control','onblur'=>'validarFechaMayorActual(this.value)'])!!}
						</div>
					</div>	
					
					<div class="form-group">
						<center>
						@if (isset($_GET['organizacion']))
							{!!Form::hidden('organization_id',$_GET['organizacion'])!!}
						@else
							{!!Form::hidden('organization_id',$objetivo['organization_id'])!!}
						@endif
						{!!Form::submit('Guardar', ['class'=>'btn btn-primary'])!!}
						</center>
					</div>