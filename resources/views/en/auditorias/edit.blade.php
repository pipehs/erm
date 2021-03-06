@extends('en.master')

@section('title', 'Edit audit Plan')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_auditoria">Auditor&iacute;as</a></li>
			<li><a href="plan_auditoria.edit.{{ $audit_plan['id'] }}">Edit Audit Plan</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Edit Audit Plan</span>
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
				<div class="no-move"></div>
			</div>
			<div class="box-content">

			@if ($errors->any())
				<div class="alert alert-danger alert-dismissible" role="alert">
					<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
					</ul>
				</div>
			@endif

			Change the data that you want to update from the audit plan <b>{{ $audit_plan['name'] }}</b>.
				<div id="cargando"><br></div>

				{!!Form::model($audit_plan,['route'=>['auditorias.update',$audit_plan->id],'method'=>'PUT','class'=>'form-horizontal','name'=>'form'])!!}
					@include('en.auditorias.form')
				{!!Form::close()!!}

				<center>
					{!! link_to_route('plan_auditoria', $title = 'Return', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-danger'])!!}
				<center>
			</div>
		</div>
	</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/en/create_edit_audit_plan.js')!!}

<script>
$("#orgs").ready(function() {

			$("#orgs").change();
			//seteamos datos
			//auditor responsable
			$("#stakeholder").val('{{ $stakeholder->stakeholder_id }}');
			$("#stakeholder").change();
			
			//seleccionamos datos cuando ya existan (esperaremos 1 segundo)
			window.setTimeout(risks, 1000);
			window.setTimeout(stakes, 1000);
			window.setTimeout(audits, 1000);
	});

function stakes()
{
	//equipo de auditores
	var auditores = JSON.parse('{{ $stakeholder_team }}');
	$(auditores).each( function() {
				$("#stakeholder_team option[value='" + this + "']").attr("selected",true);
				$("#stakeholder_team").change();
			});
}

function audits()
{
	//auditorias
	var audits = JSON.parse('{{ $audits_selected }}');
	$(audits).each( function() {
				$("#auditorias option[value='" + this + "']").attr("selected",true);
				$("#auditorias").change();
			});
}

function risks()
{
	//riesgos de proceso
	var subprocess_risk = JSON.parse('{{ $riesgos_proc }}');
	$(subprocess_risk).each( function() {
				$("#risk_subprocess_id option[value='" + this + "']").attr("selected",true);
				$("#risk_subprocess_id").change();
			});

	//riesgos de negocio
	var objective_risk = JSON.parse('{{ $riesgos_neg }}');
	$(objective_risk).each( function() {
				$("#objective_risk_id option[value='" + this + "']").attr("selected",true);
				$("#objective_risk_id").change();
			});
}

</script>
@stop
