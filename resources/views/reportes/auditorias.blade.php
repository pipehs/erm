@extends('master')

@section('title', 'Auditor&iacute;as')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Reportes</a></li>
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
		<th>Plan de auditor&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Descripci&oacute;n plan<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Auditor&iacute;as<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Objetivos<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Alcances<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Recursos<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Metodolog&iacute;a<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Normas<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Horas-hombre planificadas<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Horas-hombre utilizadas<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Fecha inicial<label><input type="text" placeholder="Filtrar" /></label></th>
		<th>Fecha final<label><input type="text" placeholder="Filtrar" /></label></th>
		</thead>

		@foreach ($audit_plans as $plan)
			<tr>
				<td>{{ $plan['Plan_de_auditoría'] }}</td>
				<td>{{ $plan['Descripción_plan'] }}</td>
				<td>
				@if ($plan['verificador'] == 1)
					<a href="#" onclick="veraudits({{$plan['id']}})">Ver</a><br>
				@else
					@foreach ($plan['Auditorías'] as $audit)
						<a href="#" onclick="veraudit({{$audit['id']}})">{{ $audit['name'] }}</a><br>
					@endforeach
				@endif
				</td>
				<td>{{ $plan['Objetivos'] }}</td>
				<td>{{ $plan['Alcances'] }}</td>
				<td>{{ $plan['Recursos'] }}</td>
				<td>{{ $plan['Metodología'] }}</td>
				<td>{{ $plan['Normas'] }}</td>
				@if ($plan['Horas_hombre_plan'] < $plan['Horas_hombre_real'])
					<td style="background: #F4A7A1;">{{ $plan['Horas_hombre_plan'] }}</td>
					<td style="background: #F4A7A1;">{{ $plan['Horas_hombre_real'] }}</td>
				@else
					<td style="background: #CAF4A1;">{{ $plan['Horas_hombre_plan'] }}</td>
					<td style="background: #CAF4A1;">{{ $plan['Horas_hombre_real'] }}</td>
				@endif
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

		text += '<tr><td><b>Organización(es)</b></td><td>'

		if (datos.audit.orgs.length > 0)
		{
			$(datos.audit.orgs).each( function(i, orgs) {
				text += '<li>'+orgs.name+'</li>'
			});
		}
		else 
		{
			text += 'No se han agregado organizaciones'
		}	
		text += '</td></tr>'

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

		text += '<tr><td><b>Auditor(es)</b></td><td>'

		if (datos.audit.auditors.length > 0)
		{
			$(datos.audit.auditors).each( function(i, auditor) {
				text += '<li>'+auditor.name+' '+auditor.surnames+'</li>'
			});
		}
		else 
		{
			text += 'No se han agregado auditores'
		}	
		
		text += '</td></tr>'

		text += '<tr><td><b>Auditado(s)</b></td><td>'

		if (datos.audit.audited.length > 0)
		{
			$(datos.audit.audited).each( function(i, audited) {
				text += '<li>'+audited.name+' '+audited.surnames+'</li>'
			});
		}
		else 
		{
			text += 'No se han agregado auditados'
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

function veraudits(id)
{

	$.get('get_audits2.'+ id, function (result) {

		//parseamos datos obtenidos
		var datos = JSON.parse(result);
	
		var title = '<b>Auditorías</b>';

		var text ='<table class="table table-striped table-datatable">';
		text += '<thead><th>Auditoría</th><th>Descripción</th></thead>';
		$(datos).each( function() {
			text += '<tr><td><a href="#" onclick="veraudit('+this.id+')">'+ this.name +'</td>'
			text += '<td>'+ this.description +'</td></tr>'
		});

		text += '</table>'

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