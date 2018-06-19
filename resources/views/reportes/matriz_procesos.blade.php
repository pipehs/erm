@extends('master')

@section('title', 'Matriz de procesos')

@section('content')

<style>
.form-horizontal .form-group.text-left{
    text-align: left !important;
}
</style>
<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="matriz_procesos">Matriz de procesos</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de procesos</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz de procesos de las distintas organizaciones ingresadas al sistema. Seleccione si desea filtrar por proceso, organización, o ver todos</p>

      @if (!isset($processes))
      	{!!Form::open(['route'=>'matriz_procesos2','method'=>'GET','class'=>'form-horizontal'])!!}
      			<div class="form-group">
					<label class="col-sm-3 control-label">Filtro</label>
					<div class="col-sm-9">
						<div class="radio-inline">
							<label> 
								<input type="radio" name="op" id="all" value="all" onclick="filtro()">Ver todos
								<i class="fa fa-circle-o"></i>
							</label>
						</div>
						<div class="radio-inline">
							<label> 
								<input type="radio" name="op" id="process" value="process" onclick="filtro()">Seleccionar Proceso
								<i class="fa fa-circle-o"></i>
							</label>
						</div>
						<div class="radio-inline">
							<label> 
								<input type="radio" name="op" id="org" value="org" onclick="filtro()">Seleccionar Organización
								<i class="fa fa-circle-o"></i>
							</label>
						</div>
					</div>
				</div>

      			<div class="form-group" id="organizations" style="display: none;">
                 	<div class="row">
                  		{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
                  		<div class="col-sm-3">
                    		{!!Form::select('organization_id',$organizations, 
                         		null, 
                         	['id' => 'organization_id','placeholder'=>'- Seleccione -'])!!}
                  		</div>
                	</div>
                </div>

                <div class="form-group" id="processes" style="display: none;">
                 	<div class="row">
                  		{!!Form::label('Seleccione proceso',null,['class'=>'col-sm-4 control-label'])!!}
                  		<div class="col-sm-3">
                    		{!!Form::select('process_id',$processes1, 
                         		null, 
                         	['id' => 'process_id','placeholder'=>'- Seleccione -'])!!}
                  		</div>
                	</div>
                </div>

           		<div class="form-group">
	                <center>
	                {!!Form::submit('Seleccionar', ['class'=>'btn btn-primary'])!!}
	                </center>
	              </div>
			<!--{!! link_to_route('genriskmatrix', $title = 'Matriz Riesgos de Proceso', $parameters = 0, $attributes = ['class'=>'btn btn-primary']) !!}
					&nbsp;&nbsp;
			{!! link_to_route('genriskmatrix', $title = 'Matriz Riesgos de Negocio', $parameters = 1, $attributes = ['class'=>'btn btn-success']) !!}
			-->

		{!!Form::close()!!}

	@else
		<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
			<thead>
				<th>Proceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Organización<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripción proceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Proceso clave<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Criticidad proceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Macroproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subproceso(s)<label><input type="text" placeholder="Filtrar" /></label></th>
			</thead>

				@foreach ($processes as $p)
					<tr>
					<td>{{ $p->name }}</td>
					<td>{{ $p->organization }}</td>
					<td>@if (strlen($p->description) > 100)
							<div id="description_{{$p->id }}" title="{{ $p->description }}">{{ $p->short_des }}...
							<div style="cursor:hand" onclick="expandir({{ $p->id }},'{{ $p->description }}','{{ $p->short_des }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $p->description }}
						@endif
					</td>
					<td> 
					@if ($p->stakeholder_id == NULL)
						No se ha asignado responsable
					@else
						{{ $p->stakeholder }}
					@endif
					</td>
					<td>
					@if ($p->key_process == 1)
						Si
					@else
						No
					@endif
					</td>
					<td>
					@if ($p->criticality == NULL)
						No se ha definido
					@else
						{{ $p->criticality }}%
					@endif
					</td>
					<td>
					@if ($p->macroprocess == NULL)
						Ninguno
					@else
						{{ $p->macroprocess }}
					@endif
					</td>
					<td>
					@if (!empty($p->subprocesses))
						<ul>
						@foreach ($p->subprocesses as $s)
							<li>{{ $s->name }}</li>
						@endforeach
						</ul>
					@else
						El proceso {{ $p->name }} no tiene subprocesos definidos en la organización {{ $p->organization }}
					@endif
					</td>
					</tr>
				@endforeach

				</table>
	@endif
				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
      		</div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>

function filtro()
{
	if($('#all').is(':checked'))
	{
		$('#organization_id').val(null)
		$('#process_id').val(null)
		$('#organization_id').change()
		$('#process_id').change()
		$('#processes').hide(500)
		$('#organizations').hide(500)
	}
	else if ($('#process').is(':checked'))
	{
		$('#organization_id').val(null)
		$('#organization_id').change()
		$('#processes').show(500)
		$('#organizations').hide(500)
	}
	else if ($('#org').is(':checked'))
	{
		$('#process_id').val(null)
		$('#process_id').change()
		$('#processes').hide(500)
		$('#organizations').show(500)
	}
}
</script>
@stop