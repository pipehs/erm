	horasPlan = 0;
	total_horas = 0;
	//script para agregar select de riesgos de organización
	$("#orgs").change(function() {

			if ($("#orgs").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					$("#guardar").prop("disabled",false);
					$("#stakeholder").val("");
					$("#stakeholder_id").empty(); //vaciamos lista de stakeholders para que no se repitan

					//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');

					//Añadimos la imagen de carga en el contenedor de plan anterior
					$('#informacion').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');

					//Añadimos la imagen de carga en el contenedor de riesgos
					$("#riesgos").removeAttr("style").show();	
					$('#cargando2').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19" /></center></div>');

					//se obtienen datos de plan de auditoría anterior para la organización seleccionada
					$.get('auditorias.get_audit_plan.'+$("#orgs").val(), function (result) {
							$("#informacion").empty();
							$("#informacion").change();
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

								$("#informacion").append('<h3><b> ' + datos.name + '</h3><hr>');
								$("#informacion").append('<b>Description:</b> ' + datos.description + '</br>');
								$("#informacion").append('<b>Objectives:</b> ' + datos.objectives + '</br>');
								$("#informacion").append('<b>Scopes:</b> ' + datos.scopes + '</br>');
								$("#informacion").append('<b>Resources:</b> ' + datos.resources + '</br>');
								$("#informacion").append('<b>Methodology:</b> ' + datos.methodology + '</br>');
								$("#informacion").append('<b>Rules:</b> ' + datos.rules + '</br>');
								$("#informacion").append('<b>Responsable: </b>' + datos.responsable + '</br>');
								$("#informacion").append('<b>Auditors: </b>');

								$(datos.users).each( function(i, users) {
									$("#informacion").append(users + ', ');
								})
								$("#informacion").append('</br><b>Initial date:</b> ' + datos.initial_date + '</br>');
								$("#informacion").append('<b>Final date:</b> ' + datos.final_date + '</br>');
								if (datos.status == 0)
									$("#informacion").append('<b>Status:</b> Open </br></br>');
								else if (datos.status == 1)
									$("#informacion").append('<b>Status:</b> Closed </br></br>');

								$("#informacion").append('<h4><b>Audits Conducted</b></h4><hr>');

							//seteamos datos de cada auditoría
							$(datos.audits).each( function(i, audit) {
								$("#informacion").append('<ul><li><h4><b> ' + audit.name + '</b></h4>');
								$("#informacion").append('<small>Description: ' + audit.description +'</br>');
							if (audit.initial_date == null)
								$("#informacion").append('<small>Initial date: Not defined </br>');
							else
								$("#informacion").append('<small>Initial date: ' + audit.initial_date +'</br>');
							if (audit.final_date == null)
								$("#informacion").append('<small>Final date: Not defined </br>');
							else
								$("#informacion").append('<small>Final date: ' + audit.final_date +'</br>');
							if (audit.resources == null)
								$("#informacion").append('<small>Resources: Not defined </br>');
							else
								$("#informacion").append('<small>Resources: ' + audit.resources +'</br>');
								
								if (audit.obj_risks.length > 0)
								{
									$("#informacion").append('<h5><b>Bussiness Risks:<b></h5>');
									//seteamos datos de cada riesgo de negocio
									$(audit.obj_risks).each( function(i, risk) {
										$("#informacion").append('<ul><li>Risk: ' + risk.name + '</li><li>Objective: ' + risk.objective_name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Bussiness Risks:</b>None</h5>');
								}

								if (audit.sub_risks.length > 0)
								{
									$("#informacion").append('<h5><b>Process Risks:<b></h5>');

									//seteamos datos de cada riesgo de proceso
									$(audit.sub_risks).each( function(i, risk) {
										$("#informacion").append('<ul><li>Risk: ' + risk.name + '</li><li>Subprocess: ' + risk.subprocess_name + '</li><li>Process: ' + risk.process_name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Process Risks:</b> None</h5>');
								}

								if (audit.audit_programs.length > 0)
								{
									$("#informacion").append('<h5><b>Audit Programs: </h5>');

									//seteamos datos de cada prueba de auditoría
									$(audit.audit_programs).each( function(i, test) {
										$("#informacion").append('<ul><li>Name: ' + test.name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Audit Programs:</b> None</h5>');
								}

								$("#informacion").append('<hr>');
							});
					});

					
							//Seteamos cabecera
							var table_head = "<thead>";
							table_head += "<th>Risk</th><th>Probability</th><th>Impact</th>";
							table_head += "</thead>";
							
							$("#riesgos").html(table_head);

					$.get('get_objective_risk.'+$("#orgs").val(), function (result) {
							$("#cargando").html('<br>');
							$("#objective_risk_id").empty();
							$("#objective_risk_id").change();
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
					$.get('get_risk_subprocess.'+$("#orgs").val(), function (result) {
							$("#cargando").html('<br>');
							$("#risk_subprocess_id").empty();
							$("#risk_subprocess_id").change();
							//parseamos datos obtenidos
							var datos = JSON.parse(result);

							//seteamos datos en select de riesgos / procesos
							$(datos).each( function() {
								$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
								$("#riesgos").append('<tr><td>' + this.name + '</td><td>' + this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
							});
					});


					//obtenemos stakeholders de la organización
					$.get('get_stakeholders.'+$("#orgs").val(), function (result) {
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							//seteamos datos en select de stakeholders
							$(datos).each( function() {
								$("#stakeholder_id").append('<option value="' + this.rut + '">' + this.fullname +'</option>');
							});
					});
			}

			else
			{
				$("#risk_subprocess_id").empty();
				$("#risk_subprocess_id").change();
				$("#objective_risk_id").empty();
				$("#objective_risk_id").change();
				$("#informacion").empty();
				$("#informacion").change();
				$("#riesgos").empty();
				$("#riesgos").change();
				$("#auditorias").empty();
				$("#auditorias").change();
				$("#guardar").prop("disabled",true);
			}
			
	    });
		
		//función que determina auditor responsable y equipo de auditores seleccionables
		$("#stakeholder_id").change(function() {

			if ($("#stakeholder_id").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
				$("#stakeholder_team").empty();
				$("#stakeholder_team").change();
				//Añadimos la imagen de carga en el contenedor
					$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');

				//se obtienen stakeholders (menos el auditor jefe)
					$.get('auditorias.stakeholders.'+$("#stakeholder").val(), function (result) {
							$("#cargando").html('<br>');
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
				$("#stakeholder_team").change();
			}
		});


//funcion para contador general de HH del plan
$('#HH_plan').change(function() {

	horasPlan = $('#HH_plan').val();

	comprobador = horasPlan - total_horas;
	//alert(comprobador)
	if (horasPlan - total_horas >= 0)
	{
		$('#contador_HH').html('<font color="red">'+ comprobador + ' hours are available to assign to audits</font>');
	}
	else
	{
		$('#contador_HH').html('<font color="red">You must to assing more hours to the plan</font>');
	}
	
});

//función que restará horas asignadas a auditorias a las horas generales
function horas()
{
	total_horas = 0; //seteamos cantidad de horas de auditoría
	//recorremos todas las auditorias antiguas por id
	cont = 1;
	while(1)
	{
		if (typeof $('#audit_'+cont).val() != "undefined")
		{
			if ($("#audit_"+cont).val() == "")
			{
				total_horas = parseInt(total_horas) + 0;
			}
			else
			{
				total_horas = parseInt(total_horas) + parseInt($("#audit_"+cont).val());
			}

			cont++;
		}
		else
		{
			break;
		}
	}

	//ahora sumamos las horas de las nuevas auditorías
	cont2 = 1;
	while(1)
	{
		if (typeof $('#newaudit_'+cont2).val() != "undefined")
		{
			if ($("#newaudit_"+cont2).val() == "")
			{
				total_horas = parseInt(total_horas) + 0;
			}
			else
			{
				total_horas = parseInt(total_horas) + parseInt($("#newaudit_"+cont2).val());
			}

			cont2++;
		}
		else
		{
			break;
		}
	}

	//alert(total_horas)
	comprobador = horasPlan - total_horas;

	if (horasPlan - total_horas >= 0)
	{
		$('#contador_HH').html('<font color="red">' + comprobador + ' hours are available to assign to audits</font>');
	}
	else
	{
		$('#contador_HH').html('<font color="red">You must to assing more hours to the plan</font>');
	}
 
}
//por el momento no se podrán editar los datos de las auditorías ya creadas
var pathname = window.location.pathname;




	if (pathname.indexOf("edit") == -1)
	{
			//función para agregar info de auditorías existentes
			$("#auditorias").change(function() 
			{

					if ($("#orgs").val() != "" && $('input[name=type]:radio').is(':checked'))//si es que hay una organización seleccionada y tipo de audit
					{
						if ($('#auditorias').val() != null)
						{
							$('#info_auditorias').empty();
							cont1 = 0;
							//insertamos los campos necesarios para la información de cada una de las auditorías seleccionadas
							$('#auditorias > option:selected').each( function () {
								cont1 += 1; //contador para ver cantidad de auditorías existentes (para agregar en id de HH)
								//alert(cont1);
								$('#info_auditorias').append('<div id="titulo_'+ $(this).val() +'"><b><font color="red">Input information for ' + $(this).text() + '</b></div></br>');
								
								if (tipo == 0) //auditoría de procesos
								{
									//riesgos de negocio
									var processes_options = null;
									$('#processes_id option:selected').each( function () {
										processes_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									});
									$('#info_auditorias').append('<div class="form-group">');
									$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_processes" class="col-sm-4 control-label">Processes (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_processes[]">' + processes_options + '</select></div></div></br>');
								}

								else if (tipo == 1) //auditoría de negocios
								{
									//riesgos de negocio
									var objectives_options = null;
									$('#objectives_id option:selected').each( function () {
										processes_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									});
									$('#info_auditorias').append('<div class="form-group">');
									$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_objectives" class="col-sm-4 control-label">Objectives (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_objectives[]">' + objectives_options + '</select></div></div></br>');
								}
								else if (tipo == 2) //auditoría de riesgos
								{
									//riesgos de negocio
									var objective_risk_options = null;
									$('#objective_risk_id option:selected').each( function () {
										objective_risk_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									})
									$('#info_auditorias').append('<div class="form-group">');
									$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_objective_risks" class="col-sm-4 control-label">Bussiness Risks (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_objective_risks[]">' + objective_risk_options + '</select></div></div></br>');

									//riesgos de proceso
									var risk_subprocess_options = null;
									$('#risk_subprocess_id option:selected').each( function () {
										risk_subprocess_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									})
									$('#info_auditorias').append('<div class="form-group">');
									$('#info_auditorias').append('<label for="Process Risks (ctrl + click to select multiple)" class="col-sm-4 control-label">Process Risks (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_' + $(this).val() + '_risk_subprocess[]">' + risk_subprocess_options + '</select></div></div>');
								}
									
								$('#info_auditorias').append('</br></br>');
								//recursos
								$('#info_auditorias').append('<div class="form-group">');
								$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_resources" class="col-sm-4 control-label">Resources</label>');
								$('#info_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_' + $(this).val() + '_resources" class="form-control" ></div>');

								//HH
								$('#info_auditorias').append('<div class="form-group">');
								$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_HH"  class="col-sm-4 control-label">Hours-Man for audit</label>');
								$('#info_auditorias').append('<div class="col-sm-8"><input type="number" min="0" onchange="horas()" id="audit_'+cont1+'" "name="audit_' + $(this).val() + '_HH" class="form-control" ></div>');

								//fecha inicio
								$('#info_auditorias').append('<div class="form-group">');
								$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_initial_date" class="col-sm-4 control-label">Initial date</label>');
								$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_initial_date" class="form-control" required="required"></div>');


								//fecha fin
								$('#info_auditorias').append('<div class="form-group">');
								$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_final_date" class="col-sm-4 control-label">Final date</label>');
								$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_final_date" class="form-control" required="required"></div>');
								$('#info_auditorias').append('</br></br>');

							})				
						}
						else
						{
							$('#info_auditorias').empty();
							cont1 = 0;
						}
					}
					else
					{
						swal("Error","First you must choose organization and kind of audit","error");
						$("#auditorias").prop("disabled",true);

					}
			});	
	}
		var cont = 1; //contador para nuevas auditorías
		//función para agregar una nueva auditoría
		$("#agregar_auditoria").click(function() {
			
			if ($("#orgs").val() != "" && $('input[name=type]:radio').is(':checked')) //si es que hay una organización seleccionada y esta chequeado tipo de audit
			{
				$('#info_new_auditorias').append('<div id="titulo_'+cont+'"><b><font color="red">Input information for the new audit number '+cont+'</b></div></br>');
				
				//-- Info de nueva auditoría --//

				//nombre
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new'+cont+'_name" class="col-sm-4 control-label">Name</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_name" class="form-control"></div>');

								//descripción
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new'+cont+'_description" class="col-sm-4 control-label">Description</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><textarea rows="3" cols="4" name="audit_new'+cont+'_description" class="form-control"></textarea></div>');
								

								if (tipo == 0) //auditoría de procesos
								{
									//riesgos de negocio
									var processes_options = null;
									$('#processes_id option:selected').each( function () {
										processes_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									});
									$('#info_new_auditorias').append('<div class="form-group">');
									$('#info_new_auditorias').append('<label for="audit_new' + cont + '_processes" class="col-sm-4 control-label">Processes (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_new' + cont + '_processes[]">' + processes_options + '</select></div></div></br>');
								}

								else if (tipo == 1) //auditoría de negocios
								{
									//riesgos de negocio
									var objectives_options = null;
									$('#objectives_id option:selected').each( function () {
										processes_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									});
									$('#info_new_auditorias').append('<div class="form-group">');
									$('#info_new_auditorias').append('<label for="audit_new' + cont + '_objectives" class="col-sm-4 control-label">Objectives (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_new' + cont + '_objectives[]">' + objectives_options + '</select></div></div></br>');
								}
								else if (tipo == 2) //auditoría de riesgos
								{
									//riesgos de negocio
									var objective_risk_options = null;
									$('#objective_risk_id option:selected').each( function () {
										objective_risk_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									})
									$('#info_new_auditorias').append('<div class="form-group">');
									$('#info_new_auditorias').append('<label for="audit_new'+cont+'_objective_risks" class="col-sm-4 control-label">Bussiness risks (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_new'+cont+'_objective_risks[]">' + objective_risk_options + '</select></div></div></br>');

									//riesgos de proceso
									var risk_subprocess_options = null;
									$('#risk_subprocess_id option:selected').each( function () {
										risk_subprocess_options += "<option value='" + $(this).val() + "'>" + $(this).text() + "</option>";
									})
									$('#info_new_auditorias').append('<div class="form-group">');
									$('#info_new_auditorias').append('<label for="Process risks (ctrl + click to select multiple)" class="col-sm-4 control-label">Process risks (ctrl + click to select multiple)</label><div class="col-sm-8"><select multiple class="form-control" name="audit_new'+cont+'_risk_subprocesses[]">' + risk_subprocess_options + '</select></div></div>');
								}
								
								$('#info_new_auditorias').append('</br></br>');

								//recursos
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new'+cont+'_resources" class="col-sm-4 control-label">Recursos</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_resources" class="form-control" required="required" ></div>');

								//HH
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new' + cont + '_HH"  class="col-sm-4 control-label">Hours-Man for audit</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><input type="number" id="newaudit_'+cont+'" min="0" onchange="horas()" name="audit_new' + cont + '_HH" class="form-control" ></div>');

								//fecha inicio
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new'+cont+'_initial_date" class="col-sm-4 control-label">Initial date</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_initial_date" class="form-control" required="required"></div>');


								//fecha fin
								$('#info_new_auditorias').append('<div class="form-group">');
								$('#info_new_auditorias').append('<label for="audit_new'+cont+'_final_date" class="col-sm-4 control-label">Final date</label>');
								$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_final_date" class="form-control" required="required"></div>');
								$('#info_new_auditorias').append('</br></br>');

								//movemos pantalla a nueva auditoría
								$('html,body').animate({
								    scrollTop: $("#titulo_"+cont).offset().top
								}, 900);

								cont = cont + 1;
			}
			else
			{
				swal("Error","First you must choose organization and kind of audit","error");
			}
		});



	//función que define que tipo de auditoría se aplicará
	function kind(value)
	{
		tipo = value; //para utilizar al agregar auditorías
		if ($("#orgs").val() != "") //Si es que organización se cambió a un valor válido (para poder obtener riesgos o procesos)
		{
			$("#auditorias").prop("disabled",false);

			if (value == 0) //auditoría de procesos
			{
				$.get('get_processes.'+$("#orgs").val(), function (result) {
					var datos = JSON.parse(result);

					//seteamos datos en select de procesos
					$(datos).each( function() {
						$("#processes_id").append('<option value="' + this.id + '">' + this.name +'</option>');
					});


					$("#riesgos_objetivos").hide(500);
					$("#riesgos_procesos").hide(500);
					$("#procesos").show(500);
					$("#objetivos").hide(500);

					$("#objectives_id").empty();
					$("#objectives_id").change();


				});
			}
			else if (value == 1) //auditoría de negocios
			{
				$.get('get_objectives.'+$("#orgs").val(), function (result) {
					var datos = JSON.parse(result);

					//seteamos datos en select de procesos
					$(datos).each( function() {
						$("#objectives_id").append('<option value="' + this.id + '">' + this.name +'</option>');
					});

					$("#riesgos_objetivos").hide(500);
					$("#riesgos_procesos").hide(500);
					$("#procesos").hide(500);
					$("#objetivos").show(500);

					$("#processes_id").empty();
					$("#processes_id").change();

				});
			}
			else if (value == 2) //auditoría de riesgos
			{
				//solo mostraremos los riesgos ya que estos ya fueron ingresados
				$("#riesgos_objetivos").show(500);
				$("#riesgos_procesos").show(500);
				$("#procesos").hide(500);
				$("#objetivos").hide(500);

				$("#objectives_id").empty();
				$("#processes_id").empty();
				$("#objectives_id").change();
				$("#processes_id").change();
			}
		}
		else
		{
			swal("Error","First you must choose organization","error");

			  $('input:radio').attr("checked",false);
		}
	}

	
					