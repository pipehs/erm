@extends('master')

@section('title', 'Organizaciones')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Datos Maestros</a></li>
			<li><a href="organization">Organizaciones</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-building-o"></i>
					<span>Organizaciones</span>
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


	


	{!! link_to_route('organization.create', $title = 'Agregar Organizaci&oacute;n', $parameters = NULL, $attributes = ['class'=>'btn btn-primary']) !!}

@if (strpos($_SERVER['REQUEST_URI'],"verbloqueados"))
	{!! link_to_route('organization.index', $title = 'Ver Desbloqueadas', $parameters = NULL, $attributes = ['class'=>'btn btn-success']) !!}
@else
	{!! link_to_route('organization.verbloqueados', $title = 'Ver Bloqueadas', $parameters = 'verbloqueados', $attributes = ['class'=>'btn btn-danger']) !!}
@endif
<!--
		<form class="form-horizontal" method="REQUEST" action="organization.create">
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" >
				<button type="submit" class="btn btn-primary">Agregar Nueva Organizaci&oacute;n</button>
			</form>

			{!!Form::open()!!}
				<div class="form-group">
					{!!Form::label('Nombre',null,['class' => 'col-sm-4 control-label'])!!}
					<div class="col-sm-3">
						{!!Form::text('name',null,['class' => 'form-control','placeholder' => 'Ingrese nombre de la organizaci&oacute;n'])!!}
					</div>
				</div>			
			{!!Form::close()!!}
-->	

	<table class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
	<thead>
	<th>Nombre</th>
	<th>Descripci&oacute;n</th>
	<th>Fecha Creaci&oacute;n</th>
	<th>Fecha Actualizado</th>
	<th>Fecha Expiraci&oacute;n</th>
	<th>Org. de servicios compartidos</th>
	<th>Organizaciones dependientes</th>
	<th>Acci&oacute;n</th>
	<th>Acci&oacute;n</th>
	</thead>

	@foreach ($organizations as $organization)
		<tr>
		<td>{{$organization['nombre']}}</td>
		<td>{{$organization['descripcion']}}</td>
		<td>{{$organization['fecha_creacion']}}</td>
		<td>{{$organization['fecha_act']}}
		<td>{{$organization['fecha_exp']}}</td>
		<td>{{$organization['serv_compartidos']}}</td>
		<td>
		<ul>
		@foreach ($org_dependientes as $organizaciones)
			@if ($organizaciones['organization_id'] == $organization['id'])
				<li>{{ $organizaciones['nombre'] }}</li>
			@endif
		@endforeach</td>
		<ul>
		<td>
			<div>
			@if ($organization['estado'] == 0)
	            {!! link_to_route('organization.edit', $title = 'Editar', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @else
	        	{!! link_to_route('organization.desbloquear', $title = 'Desbloquear', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-success']) !!}
	        @endif
	        </div><!-- /btn-group -->
		</td>
		<td>
			<div>
			@if ($organization['estado'] == 0)
	            {!! link_to_route('organization.bloquear', $title = 'Bloquear', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-danger']) !!}
	        @else
	        	{!! link_to_route('organization.bloquear', $title = 'Eliminar', $parameters = $organization['id'], $attributes = ['class'=>'btn btn-danger']) !!}
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
<!--
<script>
function bloquear(id)
      {
        swal({
           title: "Entrega de bank!",
          text: "Te hemos enviado una entrega de bank inicial de monto: $ " +  + ", favor confirma esta transacción y acredítala subiendo un pantallazo de la transferencia recibida en tu software",
          type: "success",   showCancelButton: true,
          confirmButtonColor: "#04B431",
          confirmButtonText: "Aceptar",
          cancelButtonText: "Rechazar",
          closeOnConfirm: false,
          closeOnCancel: false },

        function(isConfirm)
        {
            if (isConfirm) 
            {
              {{ redirect()->route('organization.bloquear',[]) }}

            }
            else 
            {
                return location.href = "";
            }
        
        });
      }
</script>
-->
@stop

