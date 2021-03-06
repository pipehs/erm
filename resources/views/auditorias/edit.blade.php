@extends('master')

@section('title', 'Editar Plan de auditor&iacute;a')


@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li><a href="plan_auditoria">Auditor&iacute;as</a></li>
			<li><a href="plan_auditoria.edit.{{ $audit_plan['id'] }}">Plan de auditor&iacute;as</a></li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-8">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Editar Plan de Auditor&iacute;as</span>
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

			Modifique la informaci&oacute;n que desee del plan de auditor&iacute;a <b>{{ $audit_plan['name'] }}</b>.
				<div id="cargando"><br></div>

				{!!Form::model($audit_plan,['route'=>['auditorias.update',$audit_plan->id],'method'=>'PUT','class'=>'form-horizontal','name'=>'form','onsubmit'=>'return checkSubmit();'])!!}
					@include('auditorias.form')
				{!!Form::close()!!}

				<center>
					<p><a href="#" onclick="history.back()" class="btn btn-danger">Volver</a></p>
				<center>
			</div>
		</div>
	</div>
	</div>
</div>
@stop

@section('scripts2')
{!!Html::script('assets/js/create_edit_audit_plan.js')!!}

<script>
$("#orgs").ready(function() {
			$("#orgs").change();
			//alert('{{ $stakeholder->stakeholder_id }}')
			//seteamos datos
			//auditor responsable
			//$("#stakeholder_id").val('{{ $stakeholder->stakeholder_id }}');
			$("#stakeholder_id option[value='{{ $stakeholder->stakeholder_id }}']").prop("selected",true);
			$("#stakeholder_id").change();
			
			audit_plan_id = '{{ $audit_plan->id }}'
			//seleccionamos datos cuando ya existan (esperaremos 1 segundo)
			//window.setTimeout(risks, 1000);
			window.setTimeout(stakes, 1500);
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

//05-04-17: eliminada función risks

</script>
@stop
