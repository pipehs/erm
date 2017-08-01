@extends('master')

@section('title', 'Planes de acci&oacute;n')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
			<li><a href="planes_accion">Planes de acci&oacute;n</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Planes de acci&oacute;n</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver los planes de acci&oacute;n de cada organización con su información correspondiente.</p>
@if (!isset($action_plans))
      	{!!Form::open(['route'=>'genplanes_accion','method'=>'GET','class'=>'form-horizontal'])!!}
				<div class="form-group">
							{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('organization',$organizations,
								 	   null, 
								 	   ['id' => 'organization','placeholder'=>'- Seleccione -'])!!}
							</div>
				</div>
				<br>
				<div class="form-group">
	                <center>
	                {!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
	                </center>
	            </div>

		{!!Form::close()!!}
		
@else
	<hr>
	<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px;">
		<thead>
			<th>Origen de hallazgo<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Hallazgo<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Plan de acción<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Responsable<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Correo responsable<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Estado<label><input type='text' placeholder='Filtrar'/></label></th>
			<th>Fecha final<label><input type='text' placeholder='Filtrar'/></label></th>
		</thead>

		@foreach ($action_plans as $plan)
			<tr>
				<td>{{$plan['origin']}}</td>
				<td>{{$plan['issue']}}</td>
				<td>{{$plan['description']}}</td>
				<td>{{$plan['stakeholder']}}</td>
				<td>{{$plan['stakeholder_mail']}}</td>
				<td>{{$plan['status']}}</td>
				<td>{{$plan['final_date']}}</td>
			</tr>
		@endforeach	
	</table>
		
	<div id="boton_exportar">
		{!! link_to_route('genexcelplan', $title = 'Exportar', $parameters = $org_id, $attributes = ['class'=>'btn btn-success']) !!}
	</div>
	<br\>
	<center>
		{!! link_to('', $title = 'Volver', $attributes = ['class'=>'btn btn-danger', 'onclick' => 'history.back()'])!!}			
	<center>

@endif
				
		</div>
	</div>
</div>	
      

				

@stop
@section('scripts2')
<script>

</script>
@stop