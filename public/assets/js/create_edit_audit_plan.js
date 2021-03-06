horasPlan = 0;
	total_horas = 0;
	//script para agregar select de riesgos de organización
	$("#orgs").change(function() {

			if ($("#orgs").val() != "") //Si es que el se ha cambiado el valor a un valor válido (y no al campo "- Seleccione -")
			{
					$("#btnsubmit").prop("disabled",false);
					$("#auditorias").prop("disabled",false);
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
							$("#informacion").append('<b>Descripci&oacute;n:</b> ' + datos.description + '</br>');

							if (datos.objectives == null)
								$("#informacion").append('<b>Objetivos:</b>No se definieron objetivos</br>');
							else
								$("#informacion").append('<b>Objetivos:</b> ' + datos.objectives + '</br>');

							if (datos.scopes == null)
								$("#informacion").append('<b>Alcances:</b>No se definieron alcances</br>');
							else
								$("#informacion").append('<b>Alcances:</b> ' + datos.scopes + '</br>');

							if (datos.resources == null)
								$("#informacion").append('<b>Recursos:</b>No se asignaron recursos</br>');
							else
								$("#informacion").append('<b>Recursos:</b> ' + datos.resources + '</br>');

							if (datos.methodology == null)
								$("#informacion").append('<b>Metodolog&iacute;a:</b>No se definió metodologia</br>');
							else
								$("#informacion").append('<b>Metodolog&iacute;a:</b> ' + datos.methodology + '</br>');

							if (datos.rules == null)
								$("#informacion").append('<b>Normas:</b>No se definieron normas</br>');
							else
								$("#informacion").append('<b>Normas:</b> ' + datos.rules + '</br>');

							if (datos.responsable == null)
								$("#informacion").append('<b>Responsable: </b>No se definió responsable</br>');
							else
								$("#informacion").append('<b>Responsable: </b>' + datos.responsable + '</br>');

							$("#informacion").append('<b>Auditores: </b>');

							$(datos.users).each( function(i, users) {
								$("#informacion").append(users + ', ');
							})
							$("#informacion").append('</br><b>Fecha inicial:</b> ' + datos.initial_date + '</br>');
							$("#informacion").append('<b>Fecha final:</b> ' + datos.final_date + '</br>');
							if (datos.status == 0)
								$("#informacion").append('<b>Estado:</b> Abierto </br></br>');
							else if (datos.status == 1)
								$("#informacion").append('<b>Estado:</b> Cerrado </br></br>');

							$("#informacion").append('<h4><b>Auditor&iacute;as Realizadas</b></h4><hr>');

							//seteamos datos de cada auditoría
							$(datos.audits).each( function(i, audit) {
								$("#informacion").append('<ul><li><h4><b> ' + audit.name + '</b></h4>');
								$("#informacion").append('<small>Descripci&oacute;n: ' + audit.description +'</br>');
							if (audit.initial_date == null)
								$("#informacion").append('<small>Fecha inicial: Sin definir</br>');
							else
								$("#informacion").append('<small>Fecha inicial: ' + audit.initial_date +'</br>');
							if (audit.final_date == null)
								$("#informacion").append('<small>Fecha final: Sin definir </br>');
							else
								$("#informacion").append('<small>Fecha final: ' + audit.final_date +'</br>');
							if (audit.resources == null)
								$("#informacion").append('<small>Recursos: Sin definir </br>');
							else
								$("#informacion").append('<small>Recursos: ' + audit.resources +'</br>');
					

								if (audit.audit_programs.length > 0)
								{
									$("#informacion").append('<h5><b>Programas de auditor&iacute;a: </h5>');

									//seteamos datos de cada prueba de auditoría
									$(audit.audit_programs).each( function(i, test) {
										$("#informacion").append('<ul><li>Nombre: ' + test.name + '</li></ul>');
									});
								}
								else
								{
									$("#informacion").append('<h5><b>Programas de auditor&iacute;a:</b> Ninguno</h5>');
								}

								$("#informacion").append('<hr>');
							});
					});

					
					//Seteamos cabecera
					var table_head = "<thead>";
					table_head += "<th>Riesgo</th><th>Probabilidad</th><th>Impacto</th><th>Score</th>";
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
								$("#riesgos").append('<tr><td>' + this.name + '</td><td>' +this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td><td>'+ this.score +'</td></tr>');
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
								$("#riesgos").append('<tr><td>' + this.name + '</td><td>' + this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td><td>'+ this.score +'</td></tr>');
							});
					});

					stakeholders = [];
					//obtenemos stakeholders de la organización
					//ACT 24-01-18: Puede ser de cualquier organización. Continuamos enviando id de org para facilitar mod en caso de que algún cliente desee que se aplique el filtro 
					$.get('get_stakeholders.'+$("#orgs").val(), function (result) {

							
							//parseamos datos obtenidos
							var datos = JSON.parse(result);
							//seteamos datos en select de stakeholders
							$(datos).each( function() {
								$("#stakeholder_id").append('<option value="' + this.rut + '">' + this.fullname +'</option>');
								//Guardamos datos de usuarios en array para utilizar en auditorías
								data = {id:this.rut, name:this.fullname}
								stakeholders.push(data)
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
				$("#btnsubmit").prop("disabled",true);
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
					$.get('auditorias.stakeholders.'+$("#stakeholder_id").val(), function (result) {
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

/*
//funcion para contador general de HH del plan
$('#HH_plan').change(function() {

	horasPlan = $('#HH_plan').val();

	comprobador = horasPlan - total_horas;
	//alert(comprobador)
	if (horasPlan - total_horas >= 0)
	{
		$('#contador_HH').html('<font color="red">Quedan ' + comprobador + ' horas disponibles para asignar a auditorías</font>');
	}
	else
	{
		$('#contador_HH').html('<font color="red">Debe volver a asignar horas hombre al plan</font>');
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
		$('#contador_HH').html('<font color="red">Quedan ' + comprobador + ' horas disponibles para asignar a auditorías</font>');
	}
	else
	{
		$('#contador_HH').html('<font color="red">Debe volver a asignar horas hombre al plan</font>');
	}
 
}
*/

//por el momento no se podrán editar los datos de las auditorías ya creadas
var pathname = window.location.pathname;

if (pathname.indexOf("create") == -1) //se está editando
{	

	//esperamos algunos segundos antes de llamar a la función (en este caso 3 segundos)
	setTimeout(function() { editInstance(); }, 3000);

	function editInstance() {
		cont1 = 0;
		if ($('#auditorias_sel').val() != null)
		{
			$('#info_auditorias').empty();
			//insertamos los campos necesarios para la información de cada una de las auditorías seleccionadas
			$('#auditorias_sel option:selected').each( function () {
				cont1 += 1; //contador para ver cantidad de auditorías existentes (para agregar en id de HH)
				//alert(cont1);
				audit_id = $(this).val();
				audit_name = $(this).text();

				//ACTUALIZACIÓN 08-02-2017: OBTENEMOS POSIBLES AUDITORÍAS EXISTENTES
				if (audit_plan_id != 0) //de esta forma verificamos que estamos editando (ya que en create asignamos la variable como 0)
				{

					$.get('get_audit_info.'+audit_plan_id+'.'+audit_id, function (result) {
						//parseamos datos obtenidos
						var datos = JSON.parse(result);
						//seteamos datos en select de riesgos / procesos
						$(datos).each( function() {
							$('#info_auditorias_sel').append('<div id="titulo_'+ this.audit_id +'"><b><font color="red">Modifique la informaci&oacute;n que desee para ' + this.audit_name + '</b></div></br>');
									
							//recursos
							$('#info_auditorias_sel').append('<div class="form-group">');
							$('#info_auditorias_sel').append('<label for="audit_' + this.audit_idaudit_id + '_resources" class="col-sm-4 control-label">Recursos</label>');

							if (!this.resources)
							{
								$('#info_auditorias_sel').append('<div class="col-sm-8"><input type="text" name="audit_' + this.audit_id + '_resources" class="form-control" ></div>');
							}
							else
							{
								$('#info_auditorias_sel').append('<div class="col-sm-8"><input type="text" name="audit_' + this.audit_id + '_resources" value="'+this.resources+'" class="form-control" ></div>');
							}

							//fecha inicio
							$('#info_auditorias_sel').append('<div class="form-group">');
							$('#info_auditorias_sel').append('<label for="audit_' + this.audit_id + '_initial_date" class="col-sm-4 control-label">Fecha de inicio *</label>');
							$('#info_auditorias_sel').append('<div class="col-sm-8"><input type="date" name="audit_' + this.audit_id + '_initial_date" onblur="validarFechaMayorActual(this.value)" value="'+this.initial_date+'" class="form-control" required="required"></div>');

							//fecha fin
							$('#info_auditorias_sel').append('<div class="form-group">');
							$('#info_auditorias_sel').append('<label for="audit_' + this.audit_id + '_final_date" class="col-sm-4 control-label">Fecha final *</label>');
							$('#info_auditorias_sel').append('<div class="col-sm-8"><input type="date" name="audit_' + this.audit_id + '_final_date" onblur="validarFechaMayorActual(this.value)" value="'+this.final_date+'" class="form-control" required="required"></div>');
							$('#info_auditorias_sel').append('</br></br>');
						});
					});
				}
			});
		}
	}
}
			

//función para agregar info de auditorías existentes
$("#auditorias").change(function() 
{

	if ($("#orgs").val() != "")//si es que hay una organización seleccionada y tipo de audit
	{
		if ($('#auditorias').val() != null)
		{
			$('#info_auditorias').empty();
			cont1 = 0;
			//insertamos los campos necesarios para la información de cada una de las auditorías seleccionadas
			$('#auditorias option:selected').each( function () {
				
				$('#info_auditorias').append('<div id="titulo_'+ $(this).val() +'"><b><font color="red">Ingrese informaci&oacute;n para ' + $(this).text() + '</b></div></br>');
						
				$('#info_auditorias').append('</br></br>');
				//recursos
				$('#info_auditorias').append('<div class="form-group">');
				$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_resources" class="col-sm-4 control-label">Recursos</label>');
				$('#info_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_' + $(this).val() + '_resources" class="form-control" ></div>');
				$('#info_auditorias').append('</div>');

				//fecha inicio
				$('#info_auditorias').append('<div class="form-group">');
				$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_initial_date" class="col-sm-4 control-label">Fecha de inicio *</label>');
				$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_initial_date" onblur="validarFechaMayorActual(this.value)" class="form-control" required="required"></div>');
				$('#info_auditorias').append('</div>');

				//fecha fin
				$('#info_auditorias').append('<div class="form-group">');
				$('#info_auditorias').append('<label for="audit_' + $(this).val() + '_final_date" class="col-sm-4 control-label">Fecha final *</label>');
				$('#info_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_' + $(this).val() + '_final_date" onblur="validarFechaMayorActual(this.value)" class="form-control" required="required"></div>');
				$('#info_auditorias').append('</div></br>');

				//ACT 12-09-18: Organizaciones
				orgs = ''
				orgs += '<div class="form-group">';
				orgs += '<label for="audit_'+ $(this).val() +'_organizations" class="col-sm-4 control-label">Organización(es)</label>';
				orgs += '<div class="col-sm-8"><select name="audit_'+ $(this).val() +'_organizations[]" class="form-control" multiple="true">';

				//seteamos datos que se guardaron en array en auditorias->create.blade.php
				$(organizations).each( function() {
					orgs += '<option value="' + this.id + '">' + this.name +'</option>';
				});

				orgs += '</select></div></div>'
				$('#info_auditorias').append(orgs);

				//ACT 10-09-18: Equipo de auditores y auditados
				auditors = ''
				auditors += '<div class="form-group">';
				auditors += '<label for="audit_'+ $(this).val() +'_auditors" class="col-sm-4 control-label">Auditor(es)</label>';
				auditors += '<div class="col-sm-8"><select name="audit_'+ $(this).val() +'_auditors[]" class="form-control" multiple="true">';

				//seteamos datos que se guardaron en array en auditorias->create.blade.php
				$(stakeholders).each( function() {
					auditors += '<option value="' + this.id + '">' + this.name +'</option>';
				});

				auditors += '</select></div>'
				$('#info_auditorias').append(auditors);

				audited = ''
				audited += '<div class="form-group">';
				audited += '<label for="audit_'+ $(this).val() +'_audited" class="col-sm-4 control-label">Auditado(s)</label>';
				audited += '<div class="col-sm-8"><select name="audit_'+ $(this).val() +'_audited[]" class="form-control" multiple="true">';

				//seteamos datos que se guardaron en array en auditorias->create.blade.php
				$(stakeholders).each( function() {
					audited += '<option value="' + this.id + '">' + this.name +'</option>';
				});

				audited += '</select></div>'
				$('#info_auditorias').append(audited);
				
			})				
		}
	}
	else
	{
		swal("Error","Primero debe seleccionar la organización","error");
		$("#auditorias").prop("disabled",true);

	}
});	


var cont = 1; //contador para nuevas auditorías
//función para agregar una nueva auditoría
$("#agregar_auditoria").click(function() {
			
	if ($("#orgs").val() != "") //si es que hay una organización seleccionada y esta chequeado tipo de audit
	{
		$('#info_new_auditorias').append('<div id="titulo_'+cont+'"><b><font color="red">Ingrese informaci&oacute;n para la nueva auditor&iacute;a '+cont+'</b></div></br>');
		
		//-- Info de nueva auditoría --//

		//nombre
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new'+cont+'_name" class="col-sm-4 control-label">Nombre *</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_name" class="form-control"></div>');
		$('#info_new_auditorias').append('</div>');

		//descripción
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new'+cont+'_description" class="col-sm-4 control-label">Descripci&oacute;n</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><textarea rows="3" cols="4" name="audit_new'+cont+'_description" class="form-control"></textarea></div>');
		$('#info_new_auditorias').append('</div>');

		//recursos
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new'+cont+'_resources" class="col-sm-4 control-label">Recursos</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><input type="text" name="audit_new'+cont+'_resources" class="form-control" ></div>');
		$('#info_new_auditorias').append('</div>');

		//HH
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new' + cont + '_HH"  class="col-sm-4 control-label">Horas-Hombre de auditoría</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><input type="number" id="newaudit_'+cont+'" min="0" onchange="horas()" name="audit_new' + cont + '_HH" class="form-control" ></div>');
		$('#info_new_auditorias').append('</div>');

		//fecha inicio
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new'+cont+'_initial_date" class="col-sm-4 control-label">Fecha de inicio *</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_initial_date" onblur="validarFechaMayorActual(this.value)" class="form-control" required="required"></div>');
		$('#info_new_auditorias').append('</div>');

		//fecha fin
		$('#info_new_auditorias').append('<div class="form-group">');
		$('#info_new_auditorias').append('<label for="audit_new'+cont+'_final_date" class="col-sm-4 control-label">Fecha final *</label>');
		$('#info_new_auditorias').append('<div class="col-sm-8"><input type="date" name="audit_new'+cont+'_final_date"  onblur="validarFechaMayorActual(this.value)"class="form-control" required="required"></div>');
		$('#info_new_auditorias').append('</div></br>');

		//ACT 12-09-18: Organizaciones
		orgs = '</br>'
		orgs += '<div class="form-group">';
		orgs += '<label for="audit_new'+cont+'_organizations" class="col-sm-4 control-label">Organización(es)</label>';
		orgs += '<div class="col-sm-8"><select name="audit_new'+cont+'_organizations[]" class="form-control" multiple="true">';

		//seteamos datos que se guardaron en array en auditorias->create.blade.php
		$(organizations).each( function() {
			orgs += '<option value="' + this.id + '">' + this.name +'</option>';
		});

		orgs += '</select></div>'
		$('#info_new_auditorias').append(orgs);

		//ACT 10-09-18: Equipo de auditores y auditados
		auditors = ''
		auditors += '<div class="form-group">';
		auditors += '<label for="audit_new'+cont+'_auditors" class="col-sm-4 control-label">Auditor(es)</label>';
		auditors += '<div class="col-sm-8"><select name="audit_new'+cont+'_auditors[]" class="form-control" multiple="true">';

		//seteamos datos que se guardaron en array en auditorias->create.blade.php
		$(stakeholders).each( function() {
			auditors += '<option value="' + this.id + '">' + this.name +'</option>';
		});

		auditors += '</select></div>'
		$('#info_new_auditorias').append(auditors);

		audited = ''
		audited += '<div class="form-group">';
		audited += '<label for="audit_new'+cont+'_audited" class="col-sm-4 control-label">Auditado(s)</label>';
		audited += '<div class="col-sm-8"><select name="audit_new'+cont+'_audited[]" class="form-control" multiple="true">';

		//seteamos datos que se guardaron en array en auditorias->create.blade.php
		$(stakeholders).each( function() {
			audited += '<option value="' + this.id + '">' + this.name +'</option>';
		});

		audited += '</select></div>'
		$('#info_new_auditorias').append(audited);

		//movemos pantalla a nueva auditoría
		$('html,body').animate({
		    scrollTop: $("#titulo_"+cont).offset().top
		}, 900);

		cont = cont + 1;
	}
	else
	{
		swal("Error","Primero debe seleccionar la organización","error");
	}
});


	//función que define que tipo de auditoría se aplicará
	function kind(value)
	{
		if ($("#orgs").val() != "") //Si es que organización se cambió a un valor válido (para poder obtener riesgos o procesos)
		{
			$("#auditorias").prop("disabled",false);
		}
		else
		{
			swal("Error","Primero debe seleccionar la organización","error");

			  $('input:radio').attr("checked",false);
		}
	}

	
					