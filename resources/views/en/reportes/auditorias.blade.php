@extends('en.master')

@section('title', 'Audits')

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="#">Basic Reports</a></li>
			<li><a href="planes_accion">Audits</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-m-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-table"></i>
					<span>Audits</span>
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
	<p>In this section you will be able to see the audit plans for each organization with their correspondant information</p>

    {!!Form::open(['route'=>'genauditreports','method'=>'GET','class'=>'form-horizontal'])!!}
				<div class="form-group">
							{!!Form::label('Select organization',null,['class'=>'col-sm-4 control-label'])!!}
							<div class="col-sm-3">
								{!!Form::select('organization_id',$organizations,
								 	   null, 
								 	   ['id' => 'organization_id','placeholder'=>'- Select -'])!!}
							</div>
				</div>
				<br>
				<div class="form-group">
	                <center>
	                {!!Form::submit('Select', ['class'=>'btn btn-success'])!!}
	                </center>
	            </div>

	{!!Form::close()!!}

@if (isset($audit_plans))
	<br>
	<hr>
	<div id="auditorias">
		<table id="datatable-2" class="table table-bordered table-striped table-hover table-heading table-datatable" style="font-size:11px">
		<thead>
		<th>Audit Plan</th>
		<th>Plan Description</th>
		<th>Audits</th>
		<th>Objectives</th>
		<th>Scopes</th>
		<th>Resources</th>
		<th>Methodology</th>
		<th>Rules</th>
		<th>Hours-man plan</th>
		<th>Initial date</th>
		<th>Final date</th>
		</thead>

		@foreach ($audit_plans as $plan)
			<tr>
				<td>{{ $plan['Audit_plan'] }}</td>
				<td>{{ $plan['Description'] }}</td>
				<td>
				@foreach ($plan['Audits'] as $audit)
					<a href="#" onclick="veraudit({{$audit['id']}})">{{ $audit['name'] }}</a><br>
				@endforeach
				</td>
				<td>{{ $plan['Objectives'] }}</td>
				<td>{{ $plan['Scopes'] }}</td>
				<td>{{ $plan['Resources'] }}</td>
				<td>{{ $plan['Methodology'] }}</td>
				<td>{{ $plan['Rules'] }}</td>
				<td>{{ $plan['Hours_man_plan'] }}</td>
				<td>{{ $plan['Initial_date'] }}</td>
				<td>{{ $plan['Final_date'] }}</td>
			</tr>
		@endforeach
	</div>
		
	<div id="boton_exportar">
		{!! link_to_route('genexcelaudit', $title = 'Export', $parameters = $org_selected, $attributes = ['class'=>'btn btn-success']) !!}
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
	
		var title = '<b>Audit: '+ datos.audit.name +'</b>';

		var text ='<table class="table table-striped table-datatable">';

		text += '<tr><td><b>Description</b></td>'
		text += '<td>'+ datos.audit.description +'</td>'
		text += '</tr><tr><td><b>Programs</b></td><td>'
		if (datos.programs == null)
		{
			text += 'No programs have been created'
		}
		else
		{
			$(datos.programs).each( function(i, program) {
				text += program.name
			});
		}
		text += '</td></tr>'
		text += '<tr><td><b>Resources</b></td><td>'

		if (datos.audit.resources == null)
		{
			text += 'No resources have been added'
		}
		else 
		{
			text += datos.audit.resources
		}	
		
		text += '</td></tr>'
		text += '<tr><td><b>Hours-man</b></td><td>'

		if (datos.audit.hh == null)
		{
			text += 'No hours-man have been added'
		}
		else 
		{
			text += datos.audit.hh
		}	
		
		text += '</td></tr>'
		text += '<tr><td><b>Initial date</b></td><td>'
		text += datos.audit.initial_date
		text += '</td></tr>'

		text += '<tr><td><b>Final date</b></td>'
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