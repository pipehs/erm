@extends('master')

@section('title', 'Matriz de subprocesos')

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
			<li><a href="matriz_subprocesos">Matriz de subprocesos</a></li>
		</ol>
	</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Matriz de subprocesos</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver la matriz de subprocesos de las distintas organizaciones ingresadas al sistema. Seleccione si desea filtrar por subproceso, organización, o ver todos</p>

      @if (!isset($subprocesses))
      	{!!Form::open(['route'=>'matriz_subprocesos2','method'=>'GET','class'=>'form-horizontal'])!!}
      			<div class="form-group">
					<label class="col-sm-3 control-label">Filtro</label>
					<div class="col-sm-9">
						<div class="radio-inline">
							<label> 
								<input type="radio" name="os" id="all" value="all" onclick="filtro()">Ver todos
								<i class="fa fa-circle-o"></i>
							</label>
						</div>
						<div class="radio-inline">
							<label> 
								<input type="radio" name="os" id="subprocess" value="subprocess" onclick="filtro()">Seleccionar Subproceso
								<i class="fa fa-circle-o"></i>
							</label>
						</div>
						<div class="radio-inline">
							<label> 
								<input type="radio" name="os" id="org" value="org" onclick="filtro()">Seleccionar Organización
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

                <div class="form-group" id="subprocesses" style="display: none;">
                 	<div class="row">
                  		{!!Form::label('Seleccione subproceso',null,['class'=>'col-sm-4 control-label'])!!}
                  		<div class="col-sm-3">
                    		{!!Form::select('subprocess_id',$subprocesses1, 
                         		null, 
                         	['id' => 'subprocess_id','placeholder'=>'- Seleccione -'])!!}
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
				<th>Subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Organización<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Descripción subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Responsable<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Subproceso clave<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Criticidad subproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Macrosubproceso<label><input type="text" placeholder="Filtrar" /></label></th>
				<th>Proceso relacionado<label><input type="text" placeholder="Filtrar" /></label></th>
			</thead>

				@foreach ($subprocesses as $s)
					<tr>
					<td>{{ $s->name }}</td>
					<td>{{ $s->organization }}</td>
					<td>@if (strlen($s->description) > 100)
							<div id="description_{{$s->id }}" title="{{ $s->description }}">{{ $s->short_des }}...
							<div style="cursor:hand" onclick="expandir({{ $s->id }},'{{ $s->description }}','{{ $s->short_des }}')">
							<font color="CornflowerBlue">Ver completo</font>
							</div></div>
						@else
							{{ $s->description }}
						@endif
					</td>
					<td> 
					@if ($s->stakeholder_id == NULL)
						No se ha asignado responsable
					@else
						{{ $s->stakeholder }}
					@endif
					</td>
					<td>
					@if ($s->key_subprocess == 1)
						Si
					@else
						No
					@endif
					</td>
					<td>
					@if ($s->criticality == NULL)
						No se ha definido
					@else
						{{ $s->criticality }}%
					@endif
					</td>
					<td>
					@if ($s->macrosubprocess == NULL)
						Ninguno
					@else
						{{ $s->macrosubprocess }}
					@endif
					</td>
					<td>
					@if (!empty($s->process))
						{{ $s->process->name }}
					@else
						El subproceso {{ $s->name }} no tiene proceso definido
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
		$('#subprocess_id').val(null)
		$('#organization_id').change()
		$('#subprocess_id').change()
		$('#subprocesses').hide(500)
		$('#organizations').hide(500)
	}
	else if ($('#subprocess').is(':checked'))
	{
		$('#organization_id').val(null)
		$('#organization_id').change()
		$('#subprocesses').show(500)
		$('#organizations').hide(500)
	}
	else if ($('#org').is(':checked'))
	{
		$('#subprocess_id').val(null)
		$('#subprocess_id').change()
		$('#subprocesses').hide(500)
		$('#organizations').show(500)
	}
}
</script>
@stop