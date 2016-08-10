@extends('master')

@section('title', 'Auditor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes B&aacute;sicos</a></li>
			<li><a href="planes_accion">Auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Auditor&iacute;as</span>
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
      <p>En esta secci&oacute;n podr&aacute; ver los planes de auditor&iacute;a de cada organización con su información correspondiente.</p>

    {!!Form::open(['route'=>'genauditreports','method'=>'GET','class'=>'form-horizontal'])!!}
				<div class="form-group">
							{!!Form::label('Seleccione organización',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('organization_id',$organizations,
								 	   null, 
								 	   ['id' => 'organization_id','placeholder'=>'- Seleccione -'])!!}
							</div>
				</div>
				<br>
				<div class="form-group">
	                <center>
	                {!!Form::submit('Seleccionar', ['class'=>'btn btn-success'])!!}
	                </center>
	            </div>

	{!!Form::close()!!}

@if (isset($audit_plans))
	<br>
	<hr>
	<div id="auditorias">
		<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Plan de auditor&iacute;a</th>
		<th>Descripci&oacute;n plan</th>
		<th>Auditor&iacute;as</th>
		<th>Objetivos</th>
		<th>Alcances</th>
		<th>Recursos</th>
		<th>Metodolog&iacute;a</th>
		<th>Normas</th>
		<th>Horas-hombre plan</th>
		<th>Fecha inicial</th>
		<th>Fecha final</th>
		</thead>

		@foreach ($audit_plans as $plan)
			<tr>
				<td>{{ $plan['Plan_de_auditoría'] }}</td>
				<td>{{ $plan['Descripción_plan'] }}</td>
				<td>
				@foreach ($plan['Auditorías'] as $audit)
					<a href="#" onclick="veraudit({{$audit['id']}})">{{ $audit['name'] }}</a><br>
				@endforeach
				</td>
				<td>{{ $plan['Objetivos'] }}</td>
				<td>{{ $plan['Alcances'] }}</td>
				<td>{{ $plan['Recursos'] }}</td>
				<td>{{ $plan['Metodología'] }}</td>
				<td>{{ $plan['Normas'] }}</td>
				<td>{{ $plan['Horas_hombre_plan'] }}</td>
				<td>{{ $plan['Fecha_inicio'] }}</td>
				<td>{{ $plan['Fecha_fin'] }}</td>
			</tr>
		@endforeach
	</div>
		
	<div id="boton_exportar">
		{!! link_to_route('genexcelaudit', $title = 'Exportar', $parameters = $org_selected, $attributes = ['class'=>'btn btn-success']) !!}
	</div>
@endif
      </div>
		</div>
	</div>
</div>

				

@stop
@section('scripts2')
<script>
function veraudit(id)
{
	//se obtienen datos de plan de auditoría anterior para la organización seleccionada
	$.get('get_audit.'+ id, function (result) {

		//alert(result);
		//parseamos datos obtenidos
		var datos = JSON.parse(result);
	
		var title = '<b>Auditoría: '+ datos.audit.name +'</b>';

		var text ='<table class="table table-striped table-datatable">';

		text += '<tr><td><b>Descripción</b></td>'
		text += '<td>'+ datos.audit.description +'</td>'
		text += '</tr><tr><td><b>Programas</b></td><td>'
		if (datos.programs == null)
		{
			text += 'No se han creado programas'
		}
		else
		{
			$(datos.programs).each( function(i, program) {
				text += program.name
			});
		}
		text += '</td></tr>'
		text += '<tr><td><b>Recursos</b></td><td>'

		if (datos.audit.resources == null)
		{
			text += 'No se han agregado recursos'
		}
		else 
		{
			text += datos.audit.resources
		}	
		
		text += '</td></tr>'
		text += '<tr><td><b>Horas-Hombre</b></td><td>'

		if (datos.audit.hh == null)
		{
			text += 'No se han agregado horas-hombre'
		}
		else 
		{
			text += datos.audit.hh
		}	
		
		text += '</td></tr>'
		text += '<tr><td><b>Fecha inicial</b></td><td>'
		text += datos.audit.initial_date
		text += '</td></tr>'

		text += '<tr><td><b>Fecha final</b></td>'
		text += '<td>'+ datos.audit.final_date +'</td>'
		text += '</tr></table>'

		//text += '<a class="btn btn-success" href="genexcelaudit.'+datos.audit.id+'">Exportar</a>'

		swal({   
			title: title,   
			text: text,
			customClass: 'swal-wide',   
			html: true 
		});
	});
}
</script>
@stop