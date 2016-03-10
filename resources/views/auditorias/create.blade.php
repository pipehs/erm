@extends('master')

@section('title', 'Agregar Plan de auditor&iacute;a')

@stop

@section('content')

<!-- header menu de arbol -->
<div class="row">
	<div id="breadcrumb" class="col-md-12">
		<ol class="breadcrumb">
			<li>{!!Html::link('#','Auditor&iacute;as')!!}</li>
			<li>{!!Html::link('nuevo_plan','Agregar Plan auditor&iacute;a')!!}</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Agregar Plan de Auditor&iacute;as</span>
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

			Ingrese la informaci&oacute;n asociada al nuevo plan de auditor&iacute;a.
				<div id="cargando"><br></div>
				{!!Form::open(['route'=>'agregar_plan','method'=>'POST','class'=>'form-horizontal'])!!}
					@include('auditorias.form')
				{!!Form::close()!!}

				<center>
					{!! link_to_route('plan_auditoria', $title = 'Volver', $parameters = NULL,
                 		$attributes = ['class'=>'btn btn-success'])!!}
				<center>
			</div>
		</div>
	</div>
	<div class="col-sm-12 col-sm-6">
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Informaci&oacute;n de riesgos asociados a una organizaci&oacute;n</span>
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
				<div id="cargando2"></div>
				<table id="riesgos" class="table table-bordered table-striped table-hover table-heading table-datatable" style="display: none;">
				</table>
			</div>
		</div>
		<div class="box">
			<div class="box-header">
				<div class="box-name">
					<i class="fa fa-user"></i>
					<span>Informaci&oacute;n hist&oacute;rica de &uacute;ltimo plan de auditor&iacute;a</span>
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

				<div id="informacion">Sin informaci&oacute;n</div>
			</div>
		</div>
	</div>
	</div>
</div>
@stop

@section('scripts2')
<script>
//script para agregar select de riesgos de organización
	$("#orgs").change(function() {

			if ($("#orgs").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

					//Añadimos la imagen de carga en el contenedor de plan anterior
					$('#informacion').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

					//Añadimos la imagen de carga en el contenedor de riesgos
					$("#riesgos").removeAttr("style").show();	
					$('#cargando2').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19" /></center></div>');

					//se obtienen datos de plan de auditoría anterior para la organización seleccionada
					$.get('auditorias.get_audit_plan.'+$("#orgs").val(), function (result) {
							$("#informacion").empty();
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

								$("#informacion").append('<h3><b> ' + datos.name + '</h3><hr>');
								$("#informacion").append('<b>Descripci&oacute;n:</b> ' + datos.description + '</br>');
								$("#informacion").append('<b>Objetivos:</b> ' + datos.objectives + '</br>');
								$("#informacion").append('<b>Alcances:</b> ' + datos.scopes + '</br>');
								$("#informacion").append('<b>Recursos:</b> ' + datos.resources + '</br>');
								$("#informacion").append('<b>Metodolog&iacute;a:</b> ' + datos.methodology + '</br>');
								$("#informacion").append('<b>Normas:</b> ' + datos.rules + '</br>');
								$("#informacion").append('<b>Responsable: </b>' + datos.responsable + '</br>');
								$("#informacion").append('<b>Auditores: </b>');

								$(datos.users).each( function(i, users) {
									$("#informacion").append(users + ', ');
								})
								$("#informacion").append('</br><b>Fecha inicial:</b> ' + datos.initial_date + '</br>');
								$("#informacion").append('<b>Fecha final:</b> ' + datos.final_date + '</br>');
								$("#informacion").append('<b>Estado:</b> ' + datos.status + '</br></br>');

								$("#informacion").append('<h4><b>Auditor&iacute;as realizadas</b></h4><hr>');

							//seteamos datos de cada auditoría
							$(datos.audits).each( function(i, audit) {
								$("#informacion").append('<ul><li><h4><b> ' + audit.name + '</b></h4>');
								$("#informacion").append('<small>Descripci&oacute;n: ' + audit.description +'</br>');
								$("#informacion").append('<small>Fecha inicial: ' + audit.initial_date +'</br>');
								$("#informacion").append('<small>Fecha final: ' + audit.final_date +'</br>');
								$("#informacion").append('<small>Recursos: ' + audit.resources +'</br>');
								
								if (audit.obj_risks.length > 0)
								{
									$("#informacion").append('<h5><b>Riesgos de negocio:<b></h5>');
									//seteamos datos de cada riesgo de negocio
									$(audit.obj_risks).each( function(i, risk) {
										$("#informacion").append('<ul><li>Riesgo: ' + risk.name + '</li><li>Objetivo: ' + risk.objective_name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Riesgos de negocio:</b>Ninguno</h5>');
								}

								if (audit.sub_risks.length > 0)
								{
									$("#informacion").append('<h5><b>Riesgos de procesos:<b></h5>');

									//seteamos datos de cada riesgo de proceso
									$(audit.sub_risks).each( function(i, risk) {
										$("#informacion").append('<ul><li>Riesgo: ' + risk.name + '</li><li>Subproceso: ' + risk.subprocess_name + '</li><li>Proceso: ' + risk.process_name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Riesgos de procesos:</b> Ninguno</h5>');
								}

								if (audit.audit_tests.length > 0)
								{
									$("#informacion").append('<h5><b>Pruebas de auditor&iacute;a: </h5>');

									//seteamos datos de cada prueba de auditoría
									$(audit.audit_tests).each( function(i, test) {
										$("#informacion").append('<ul><li>Nombre: ' + test.name + '</li><li>Resultado: ' + test.results + '</br></li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Pruebas de auditor&iacute;a:</b> Ninguno</h5>');
								}

								$("#informacion").append('<hr>');
							});
					});

					
							//Seteamos cabecera
							var table_head = "<thead>";
							table_head += "<th>Riesgo</th><th>Probabilidad</th><th>Impacto</th>";
							table_head += "</thead>";
							
							$("#riesgos").html(table_head);

					//se obtienen riesgos de negocio para la organización seleccionada
					$.get('auditorias.objective_risk.'+$("#orgs").val(), function (result) {
							$("#cargando").html('<br>');
							$("#objective_risk_id").empty();
							$("#cargando2").empty(); //cargando de riesgos							
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / objetivos
							$(datos).each( function() {
								$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
								$("#riesgos").append('<tr><td>' + this.name + '</td><td>' +this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
							});
					});

					//se obtienen riesgos de proceso para la organización seleccionada
					$.get('auditorias.risk_subprocess.'+$("#orgs").val(), function (result) {
							$("#cargando").html('<br>');
							$("#risk_subprocess_id").empty();
							
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / objetivos
							$(datos).each( function() {
								$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
								$("#riesgos").append('<tr><td>' + this.name + '</td><td>' + this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
							});
					});

			}

			else
			{
				$("#risk_subprocess_id").empty();
				$("#objective_risk_id").empty();
				$("#informacion").empty();
				$("#riesgos").empty();
			}
			
	    });
		
		//función que determina auditor responsable y equipo de auditores seleccionables
		$("#stakeholder").change(function() {

			if ($("#stakeholder").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');

				//se obtienen stakeholders (menos el auditor jefe)
					$.get('auditorias.stakeholders.'+$("#stakeholder").val(), function (result) {
							$("#cargando").html('<br>');
							$("#stakeholder_team").empty();
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#stakeholder_team").append('<option value="' + this.id + '">' + this.name +'</option>');
							});
					});

			}
			else
			{
				$("#stakeholder_team").empty();
			}
		});


		//función para agregar info de auditorías existentes
		$("#auditorias").change(function() {
				if ($('#auditorias').val() != null)
				{
					$('#info_auditorias').empty();

					//insertamos los campos necesarios para la información de cada una de las auditorías seleccionad iys
					$('#auditorias > option:selected').each( function () {
						
						$('#info_auditorias').append('<div id="titulo_'+ $(this).val() +'"><b><font color="red">Ingrese informaci&oacute;n para ' + $(this).text() + '</b></div></br>');
						
						//riesgos de negocio
						var objective_risk_options = null;
						$('#objective_risk_id option').each( function () {
							objective_risk_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
						})
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_objective_risks" class="col-sm-4 control-label">Riesgos de negocio (ctrl + click para seleccionar varios)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_objective_risks[]">' + objective_risk_options + '</select></div></div></br>');

						//riesgos de proceso
						var risk_subprocess_options = null;
						$('#risk_subprocess_id option').each( function () {
							risk_subprocess_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
						})
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="Riesgos de proceso (ctrl + click para seleccionar varios)" class="col-sm-4 control-label">Riesgos de proceso (ctrl + click para seleccionar varios)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_risk_subprocess[]">' + risk_subprocess_options + '</select></div></div>');
						$('#info_auditorias').append('</br></br>');

						//recursos
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_resources" class="col-sm-4 control-label">Recursos</label>');
						$('#info_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_' + $(this).val() + '_resources" class="form-control" required="required" ></div>');

						//fecha inicio
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_initial_date" class="col-sm-4 control-label">Fecha de inicio</label>');
						$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_initial_date" class="form-control" required="required"></div>');


						//fecha fin
						$('#info_auditorias').append('<div class="form-group">');
						$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_final_date" class="col-sm-4 control-label">Fecha final</label>');
						$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_final_date" class="form-control" required="required"></div>');
						$('#info_auditorias').append('</br></br>');

					})				
				}
				else
				{
					$('#info_auditorias').empty();
				}
		});	

	var cont = 1; //contador para nuevas auditorías
	//función para agregar una nueva auditoría
	$("#agregar_auditoria").click(function() {
		
		$('#info_new_auditorias').append('<div id="titulo_'+cont+'"><b><font color="red">Ingrese informaci&oacute;n para la nueva auditor&iacute;a '+cont+'</b></div></br>');
		
		//-- Info de nueva auditoría --//

		//nombre
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_name" class="col-sm-4 control-label">Nombre</label>');
						$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_name" class="form-control"></div>');

						//descripción
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_description" class="col-sm-4 control-label">Descripci&oacute;n</label>');
						$('#info_new_auditorias').append('<div class="col-sm-8"><textarea rows="3" cols="4" name="audit_new'+cont+'_description" class="form-control"></textarea></div>');
						//riesgos de negocio
						var objective_risk_options = null;
						$('#objective_risk_id option').each( function () {
							objective_risk_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
						})
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_objective_risks" class="col-sm-4 control-label">Riesgos de negocio (ctrl + click para seleccionar varios)</label><div class="col-sm-8"><select multiple class="form-control" required="required" name="audit_new'+cont+'_objective_risks[]">' + objective_risk_options + '</select></div></div></br>');

						//riesgos de proceso
						var risk_subprocess_options = null;
						$('#risk_subprocess_id option').each( function () {
							risk_subprocess_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
						})
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="Riesgos de proceso (ctrl + click para seleccionar varios)" class="col-sm-4 control-label">Riesgos de proceso (ctrl + click para seleccionar varios)</label><div class="col-sm-8"><select multiple class="form-control" required="required" name="audit_new'+cont+'_risk_subprocess[]">' + risk_subprocess_options + '</select></div></div>');
						$('#info_new_auditorias').append('</br></br>');

						//recursos
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_resources" class="col-sm-4 control-label">Recursos</label>');
						$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_resources" class="form-control" required="required" ></div>');

						//fecha inicio
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_initial_date" class="col-sm-4 control-label">Fecha de inicio</label>');
						$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_initial_date" class="form-control" required="required"></div>');


						//fecha fin
						$('#info_new_auditorias').append('<div class="form-group">');
						$('#info_new_auditorias').append('<label for="audit_new'+cont+'_final_date" class="col-sm-4 control-label">Fecha final</label>');
						$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_final_date" class="form-control" required="required"></div>');
						$('#info_new_auditorias').append('</br></br>');

						//movemos pantalla a nueva auditoría
						$('html,body').animate({
						    scrollTop: $("#titulo_"+cont).offset().top
						}, 900);

						cont = cont + 1;
	});

	

</script>
@stop
