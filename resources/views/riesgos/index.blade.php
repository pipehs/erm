@extends('master')

@section('title', 'Riesgos')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="riesgos">Identificación de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Riesgos</span>
				</div>
				<div class="box-icons">
					<a class="collapse-link">
						<i class="fa fa-chevron-up"></i>
					</a>
					<a class="expand-link">
						<i class="fa fa-expand"></i>
					</a>
					<a class="close-link">
						<i class="fa fa-times"></i>
					</a>
				</div>
				<div class="move"></div>
			</div>
			<div class="box-content box ui-draggable ui-droppable" style="top: 0px; left: 0px; opacity: 1; z-index: 1999;">

		@if(Session::has('message'))
				<div class="alert alert-success alert-dismissible" role="alert">
				{{ Session::get('message') }}
				</div>
			@endif

				@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<p>En esta secci&oacute;n podr&aacute; identificar un riesgo formal en base al an&aacute;lisis realizado sobre los eventos de riesgo. Tambi&eacute;n podr&aacute; ver los riesgos previamente identificados.</p>
			<?php break; ?>

			@else
				<p>En esta secci&oacute;n podr&aacute; ver los riesgos identificados en el sistema.</p>
			@endif
		@endforeach

		{!!Form::open(['url'=>'riesgos.index2','method'=>'GET','class'=>'form-horizontal'])!!}

		<div class="form-group">
		   <div class="row">
		     {!!Form::label('Seleccione organizaci&oacute;n',null,['class'=>'col-sm-4 control-label'])!!}
		     <div class="col-sm-3">
		       {!!Form::select('organization_id',$organizations, 
		            null, 
		           ['id' => 'org','placeholder'=>'- Seleccione -','required'=>'true'])!!}
		     </div>
		  </div>
		</div>

		<div class="form-group">
               <div class="row">
                 <label for="risk_category_id" class='col-sm-4 control-label'>Seleccione categor&iacute;a de Riesgo (opcional)</label>
                 <div class="col-sm-3">
                   {!!Form::select('risk_category_id',$categories, 
                        null, 
                       ['id' => 'risk_category_id','placeholder'=>'- Seleccione -'])!!}
                 </div>
              </div>
            </div>

            <div class="form-group">
               <div class="row">
                 <label for="risk_subcategory_id" class='col-sm-4 control-label'>Seleccione categor&iacute;a nivel 2 (opcional)</label>
                 <div class="col-sm-3">
                    <select id="risk_subcategory_id" name="risk_subcategory_id"></select>
                 </div>
              </div>
            </div>
        
            <div class="form-group">
               <div class="row">
                 <label for="risk_subcategory_id2" class='col-sm-4 control-label'>Seleccione categor&iacute;a nivel 3 (opcional)</label>
                 <div class="col-sm-3">
                    <select id="risk_subcategory_id2" name="risk_subcategory_id2"></select>
                 </div>
              </div>
            </div>


		<br>
		<div class="form-group">
			<center>
				{!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
			</center>
			{!!Form::close()!!}
		</div>

@if (isset($riesgos))
{{-- ACT 13-10-17 Siempre se mostrarán los riesgos --}}

<h4><b>
@if (isset($org_id))
	Riesgos de: {{ $org_selected }} 
@endif
</b></h4>
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				@if (isset($org_id))
					<center>
					{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Proceso', $parameters = ['P' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-warning']) !!}
					&nbsp;&nbsp;
					{!! link_to_route('riesgos.create', $title = 'Agregar Riesgo de Negocio', $parameters = ['N' => 1, 'org' => $org_id], $attributes = ['class'=>'btn btn-primary']) !!}
					</center>
				@endif
			<?php break; ?>

			@else
				
			@endif
		@endforeach
			
			<table class="table table-bordered table-striped table-hover table-heading table-datatable" id="datatable-2" style="font-size:11px">
			<thead>
			@if (!isset($org_id))
				<th>Organizaci&oacute;n(es)<label><input type="text" placeholder="Filtrar" /></label></th>
			@endif
			<th>Nombre<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Descripci&oacute;n<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Tipo<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Categor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
			@if (!isset($org_id))
				<th>Responsable(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			@else
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
			@endif
			<th>Subprocesos u Objetivos Relacionados<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Causa(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Efecto(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Probabilidad<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Impacto<label><input type="text" placeholder="Filtrar" /></label></th>
			<th>Score<label><input type="text" placeholder="Filtrar" /></label></th>
			@if (isset($org_id))
				<th>Respuesta al Riesgo<label><input type="text" placeholder="Filtrar" /></label></th>
			@endif
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				<th>Editar</th>
				<th>Bloquear</th>
			<?php break; ?>
			@endif
		@endforeach
			</thead>
			@foreach ($riesgos as $riesgo)
				<tr>
				@if (!isset($org_id))
					<td><ul>
					@foreach ($riesgo['orgs'] as $o)
						<li>{{ $o }}</li>
					@endforeach
					</ul></td>
				@endif
				<td>{{ $riesgo['nombre'] }}</td>
				<td>
				@if ($riesgo['descripcion'] == NULL || $riesgo['descripcion'] == "")
					No se ha definido descripci&oacute;n
				@else
					@if (strlen($riesgo['descripcion']) > 100)
						<div id="description_{{$riesgo['id']}}" title="{{ $riesgo['descripcion'] }}">{{ $riesgo['short_des'] }}...
						<div style="cursor:hand" onclick="expandir({{ $riesgo['id'] }},'{{ $riesgo['descripcion'] }}','{{ $riesgo['short_des'] }}')">
						<font color="CornflowerBlue">Ver completo</font>
						</div></div>
					@else
						{{ $riesgo['descripcion'] }}
					@endif
				@endif
				</td>
				@if ($riesgo['tipo'] == 0)
					<td>Riesgo de Proceso</td>
				@else
					<td>Riesgo de Negocio</td>
				@endif
				<td>
				@if ($riesgo['categoria'] == NULL)
					No se ha definido categoría
				@else
					{{ $riesgo['categoria'] }}
				@endif
				</td>
				
				@if (!isset($org_id))
					<td><ul>
					@foreach ($riesgo['responsables'] as $r)
						<li>{{ $r }}</li>
					@endforeach
					</ul></td>
				@else
					<td>{{ $riesgo['stakeholder'] }}</td>
				@endif
				<td>
				<ul>
				@if (!isset($org_id))	
					@foreach($riesgo['subobj'] as $subobj)
							<li>{{ $subobj['name'] }}</li>
					@endforeach
				@else
					@foreach($relacionados as $subonegocio)
						@if ($subonegocio['risk_id'] == $riesgo['id'])
							<li>{{ $subonegocio['nombre'] }}</li>
						@endif
					@endforeach
				@endif
				</ul>	
				</td>
				<td>
				@if ($riesgo['causas'] == NULL)
					No se han especificado causas
				@else
					@if (gettype($riesgo['causas']) == "array") 
						@foreach ($riesgo['causas'] as $causa)
							<li>{{ $causa }}</li>
						@endforeach
					@else
						{{ $riesgo['causas'] }}
					@endif
				@endif
				</td>
				<td>
				@if ($riesgo['efectos'] == NULL)
					No se han especificado efectos
				@else
					@if (gettype($riesgo['efectos']) == "array") 
						@foreach ($riesgo['efectos'] as $efecto)
							<li>{{ $efecto }}</li>
						@endforeach
					@else
						{{ $riesgo['efectos'] }}
					@endif
				@endif
				</td>
				<td>{{$riesgo['probabilidad']}}</td>
				<td>{{$riesgo['impacto']}}</td>
				<td>{{$riesgo['score']}}</td>
				@if (isset($org_id))
					<td>@if (!empty($riesgo['risk_response']))
							{{$riesgo['risk_response']->name }}
						@else
							No se ha definido
						@endif
					</td>
				@endif
		@foreach (Session::get('roles') as $role)
			@if ($role != 6)
				@if (!isset($org_id))
					<td>{!! link_to_route('riesgos.edit', $title = 'Editar', $parameters = ['id' => $riesgo['id'], 'org' => NULL], $attributes = ['class'=>'btn btn-success']) !!}</td>
					<td><button class="btn btn-danger" onclick="eliminar2({{ $riesgo['id'] }}.0,'{{ $riesgo['nombre'] }}','riesgos','El riesgo')">Eliminar</button></td>
				@else
					<td>{!! link_to_route('riesgos.edit', $title = 'Editar', $parameters = ['id' => $riesgo['id'], 'org' => $org_id], $attributes = ['class'=>'btn btn-success']) !!}</td>
					<td><button class="btn btn-danger" onclick="bloquear('{{ $riesgo['id'] }}.{{ $org_id }}','{{ $riesgo['nombre'] }}','riesgos','El riesgo')">Bloquear</button></td>
				@endif
			<?php break; ?>
			@endif
		@endforeach
				</tr>
			@endforeach
			</table>
@endif
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts2')
@stop


