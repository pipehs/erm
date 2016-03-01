@extends('master')

@section('title', 'Categor&iacute;as de Riesgos')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="categorias_riesgos">Categor&iacute;as de Riesgos</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-ticket"></i>
					<span>Categor&iacute;as de Riesgos</span>
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

		{!! link_to_route('categorias_riesgos.create', $title = 'Agregar Categor&iacute;a', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

		@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
			{!! link_to_route('categorias_riesgos.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
		@else
			{!! link_to_route('categorias_riesgos.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
		@endif

		@if(Session::has('message'))
			<div class="alert alert-success alert-dismissible" role="alert">
			{{ Session::get('message') }}
			</div>
		@endif

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Expiraci&oacute;n</th>
	<th>Categor&iacute;as Dependientes</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>
	@foreach ($risk_categories as $risk_category)
		<tr>
		<td>{{$risk_category['nombre']}}</td>
		<td>{{$risk_category['descripcion']}}</td>
		<td>{{$risk_category['fecha_creacion']}}</td>
		<td>{{$risk_category['fecha_exp']}}</td>
		<td>
		<ul>
		@if ($categorias_dependientes != NULL)
			@foreach ($categorias_dependientes as $categorias)
				@if ($categorias['risk_category_id'] == $risk_category['id'])
					<li>{{ $categorias['nombre'] }}</li>
				@endif
			@endforeach
		@else
			<li>Ninguna </li>
		@endif
		</td>
		<ul>
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            {!! link_to_route('categorias_riesgos.edit', $title = 'Editar', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('categorias_riesgos.desbloquear', $title = 'Desbloquear', $parameters = $risk_category['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($risk_category['estado'] == 0)
	            <button class="btn btn-danger" onclick="bloquear({{ $risk_category['id'] }},'{{ $risk_category['nombre'] }}','categorias_riesgos','la categoría de riesgo')">Bloquear</button>
	        @else
	        	
	        @endif
	        </div><!-- /btn-group -->
		</td>
		</tr>
	@endforeach
	</table>

			</div>
		</div>
	</div>
</div>
@stop

