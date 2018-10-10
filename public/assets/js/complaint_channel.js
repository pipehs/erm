//Agrega nuevos estados
cont = 1;
function add_status(kind_id)
{
	var new_status = '<div class="form-group">'
	new_status += '<label for="new_status_'+kind_id+'_'+cont+'" class="col-sm-4 control-label">Nuevo estado '+cont+'</label>'
	new_status += '<div class="col-sm-4">'
	new_status += '<input type="text" name="new_status_'+kind_id+'_'+cont+'" class="form-control"></input>'
	new_status += '</div></div>'

	$("#new_status_"+kind_id).append(new_status)
	cont = cont + 1
}

//Agrega nuevas clasificaciones
cont2 = 1;
function add_classification(kind_id)
{
	var new_class = '<div class="form-group">'
	new_class += '<label for="new_name_class_'+kind_id+'_'+cont2+'" class="col-sm-4 control-label">Nombre nueva clasificación '+cont2+'</label>'
	new_class += '<div class="col-sm-4">'
	new_class += '<input type="text" name="new_name_class_'+kind_id+'_'+cont2+'" class="form-control"></input>'
	new_class += '</div></div>'

	new_class += '<div class="form-group">'
	new_class += '<label for="new_description_class_'+kind_id+'_'+cont2+'" class="col-sm-4 control-label">Descripción nueva clasificación '+cont2+'</label>'
	new_class += '<div class="col-sm-4">'
	new_class += '<input type="text" name="new_description_class_'+kind_id+'_'+cont2+'" class="form-control"></input>'
	new_class += '</div></div>'

	new_class += '<div class="form-group">'
	new_class += '<label for="new_role_class_'+kind_id+'_'+cont2+'" class="col-sm-4 control-label">Rol responsable nueva clasificación '+cont2+'</label>'
	new_class += '<div class="col-sm-4">'

	if (roles.length > 0)
	{	
		new_class += '<select name="new_role_class_'+kind_id+'_'+cont2+'" class="form-control">'
		//seteamos datos de cada rol
		$(roles).each( function() {
			new_class += '<option name="' + this.id + '">'+this.name+'</option>';
		});

		new_class += '</select>'
	}
	else
	{
		new_class += '<input type="text" name="new_role_class_'+kind_id+'_'+cont2+'" class="form-control"></input>'
	}
	
	new_class += '</div></div><br>'

	$("#new_classification_"+kind_id).append(new_class)
	cont2 = cont2 + 1
}

//Obtiene datos del caso
function getCase(kind) //kind = 1: Seguimiento por denunciante. 2: Seguimiento admin
{
	document.getElementById("btnsubmit").value = "Enviando...";
  	document.getElementById("btnsubmit").disabled = true;
	//alert(document.getElementById("id").value);
	//alert(document.getElementById("password").value);

	$.get('get_case.'+$("#id").val()+'.'+$("#password").val()+'.'+kind, function (result) {
		var datos = JSON.parse(result);

		$(datos).each( function() {

			if (this.response == 99)
			{
				$("#error-response").html(this.response_description);
				$("#error-response").show();
				document.getElementById("btnsubmit").value = "Revisar";
  				document.getElementById("btnsubmit").disabled = false;
			}
			else if (this.response == 0)
			{
				$("#error-response").html('');
				$("#error-response").hide();
				document.getElementById("btnsubmit").value = "Revisar";
  				document.getElementById("btnsubmit").disabled = false;

  				$(datos.case).each( function() {
  					i = 1
  					data = '<h4><b>Descripción del caso</b></h4><br>';
  					data += '<p><b>Autor:</b> '+this.complainant+'</p>';
  					data += '<p><b>Fecha de registro:</b> '+this.created_at+'</p>';
  					data += '<p><b>Estado:</b> '+this.status+'</p>';
  					data += '<p><b>Clasificación:</b> '+this.classification+'</p>';
  					questions = '<h4><b>Preguntas y respuestas del caso</b></h4><br>'
  					i = 1;
  					$(this.questions).each( function() {
  						questions += '<p><b>Pregunta '+i+':</b> '+this.question+'</p>';
  						if (this.answer != null)
  							questions += '<p><b>Respuesta denunciante:</b> '+this.answer+'</p>';
  						else
  							questions += '<p><b>Respuesta denunciante:</b> Sin respuesta</p>';

  						i += 1;
  					});

  					$('#case-info').html(data);
  					$('#questions').html(questions);
  					oldmessage = '';
  					$(this.messages).each(function() {
  						if (this.user_id == null) //mensajes de denunciante de tipo info
  						{
  							oldmessage += '<div class="alert alert-info alert-dismissible" role="alert">';
  						}
  						else //mensajes admin de tipo success
  						{
  							oldmessage += '<div class="alert alert-success alert-dismissible" role="alert">';
  						}

			            oldmessage += '<p><b>Autor: '+this.sender+'</b></p>';
			            oldmessage += '<p>Enviado el: '+this.created_at+'</p>';
			            oldmessage += '<p>'+this.description+'</p>';
			            oldmessage += '<p>';
			            $(this.files).each( function(i,file) {
							archivo = file.split('.');
							filename = archivo[0].split('/');
							id = filename[1]; //id del elemento
							kind2 = filename[0]; //por ej. evidencias_hallazgos
							filename = filename[2];
							if (typeof archivo[1] !== 'undefined') //si es que tiene extensión
							{
								if (archivo[1] == 'pdf')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>'
								}
								else if (archivo[1] == 'doc' || archivo[1] == 'docx' || archivo[1] == 'DOC' || archivo[1] == 'DOCX')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/word.png" width="30" height="30" /></a><br/>'
								}
								else if (archivo[1] == 'xls' || archivo[1] == 'xlsx' || archivo[1] == 'XLS' || archivo[1] == 'XLSX')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>'
								}
								else if (archivo[1] == 'ppt' || archivo[1] == 'pptx' || archivo[1] == 'PPT' || archivo[1] == 'PPTX')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>'
								}
								else if (archivo[1] == 'jpg' || archivo[1] == 'jpeg' || archivo[1] == 'JPG' || archivo[1] == 'jpeg')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>'
								}
								else if (archivo[1] == 'rar' || archivo[1] == 'zip' || archivo[1] == 'RAR' || archivo[1] == 'ZIP')
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/rar.png" width="30" height="30" /></a><br/>'
								}
								else
								{
									oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'.'+archivo[1]+'""><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>'
								}
							}
							else
							{
								oldmessage += '<a href="downloadfile.'+kind2+'.'+id+'.'+filename+'"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>'
							}

							oldmessage += filename+'<br>';
						});

						oldmessage += '</p></div>';
  					});
  					
  				});

	            $('#messages').append(oldmessage);

  				messages = '<h4><b>Mensajes</b></h4>';

  				messages += '<div class="form-group">';
  				messages += '<label for="new_message" class="col-sm-4 control-label">Enviar comentario *</label>';
  				messages += '<div class="col-sm-8">';
  				messages += '<textarea name="new_message" class="form-control" rows="5" id="new_message"></textarea>';
  				messages += '</div></div>';

  				messages += '<div class="form-group">';
  				messages += '<label for="evidence_doc[]" class="col-sm-4 control-label">Adjuntar evidencia</label>';
  				messages += '<div class="col-sm-8">'
				messages += '<input id="file-1" type="file" class="file" name="evidence_doc[]" multiple=true data-preview-file-type="any">'
				messages += '</div></div>'

				messages += '<input type="hidden" name="case_id" value="'+$('#id').val()+'">';
				messages += '<input type="hidden" name="kind" value="'+kind+'">';
				messages += '<div class="form-group">';
				messages += '<label for="btn_guardar" class="col-sm-4 control-label"></label>'
				messages += '<div class="col-sm-8">'
				messages += '<button class="btn btn-success">Enviar mensaje</button>'
				messages += '</div></div>'

  				$('#messages2').html(messages);
			}
		});
		
	});
}

$("form#messages2").submit(function(e) {

	if ($('#new_message').val() == '')
	{
		swal('Atención','Debe ingresar un mensaje','warning');
	}
	else
	{

		var formData = new FormData(this);

	    $.ajax({
	        url: 'send_user_cc_message',
	        type: 'POST',
	        data: formData,
	        success: function (data) {
	            swal('Mensaje enviado','Su mensaje ha sido enviado correctamente','success');
	            data2 = JSON.parse(data);
	            //alert(data2.message)
	            if (data2.kind == 1)
	            {
	            	message = '<div class="alert alert-info alert-dismissible" role="alert">';
	            }
	            else if (data2.kind == 2)
	            {
	            	message = '<div class="alert alert-success alert-dismissible" role="alert">';
	            }
	            message += '<p><b>Autor: '+data2.sender+'</b></p>';
	            message += '<p>'+data2.message+'</p>';
	            message += '<p>';
	            $(data2.files).each( function() {
					archivo = this.split('.');
					filename = archivo[0].split('/');
					id = filename[1]; //id del elemento
					kind = filename[0]; //por ej. evidencias_hallazgos
					filename = filename[2];
					if (typeof archivo[1] !== 'undefined') //si es que tiene extensión
					{
						if (archivo[1] == 'pdf')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/pdf.png" width="30" height="30" /></a><br/>'
						}
						else if (archivo[1] == 'doc' || archivo[1] == 'docx' || archivo[1] == 'DOC' || archivo[1] == 'DOCX')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/word.png" width="30" height="30" /></a><br/>'
						}
						else if (archivo[1] == 'xls' || archivo[1] == 'xlsx' || archivo[1] == 'XLS' || archivo[1] == 'XLSX')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/excel.png" width="30" height="30" /></a><br/>'
						}
						else if (archivo[1] == 'ppt' || archivo[1] == 'pptx' || archivo[1] == 'PPT' || archivo[1] == 'PPTX')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/powerpoint.png" width="30" height="30" /></a><br/>'
						}
						else if (archivo[1] == 'jpg' || archivo[1] == 'jpeg' || archivo[1] == 'JPG' || archivo[1] == 'jpeg')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/jpg.png" width="30" height="30" /></a><br/>'
						}
						else if (archivo[1] == 'rar' || archivo[1] == 'zip' || archivo[1] == 'RAR' || archivo[1] == 'ZIP')
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'"><img src="assets/img/rar.png" width="30" height="30" /></a><br/>'
						}
						else
						{
							message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'.'+archivo[1]+'""><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>'
						}
					}
					else
					{
						message += '<a href="downloadfile.'+kind+'.'+id+'.'+filename+'"><img src="assets/img/desconocido.png" width="30" height="30" /></a><br/>'
					}
				});
				message += '</p>';

	            $('#messages').append(message);

	        },
	        cache: false,
	        contentType: false,
	        processData: false
	    });
	}
	event.preventDefault();
});


function cerrar()
{
	texto = '<div class="form-group">'
	texto += '<label for="close_reason" class="col-sm-4 control-label">Seleccione motivo de cierre</label>'
	texto += '<div class="col-sm-5">'
	texto += '{!!Form::select("close_reason",["1" => "Resuelto con sanción","2"=>"Resuelto sin sanción","3"=>"No resuelto por falta de antecedentes","4"=>"No resuelto por abandono de la denuncia"], null, ["id" => "close_reason","placeholder"=>"- Seleccione -","class"=>"form-control"])!!}'
	texto += '</div></div><br>'

	texto += '<div class="form-group">'
	texto += '<label for="close_description" class="col-sm-4 control-label">Describa motivo de cierre</label>'
	texto += '<div class="col-sm-5">'
	texto += '{!!Form::textarea("close_description", null, ["id"=>"description_close", "class"=>"form-control", "rows"=>"8","cols"=>"4","required" => "true"])!!}'
	texto += '</div></div>'

	texto += '<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>'

	swal({   title: "Cerrar caso con id: 12031801",
		   text: texto,  
		   showCancelButton: true,   
		   confirmButtonColor: "#31B404",   
		   confirmButtonText: "Cerrar",
		   cancelButtonText: "Cancelar",
		   html: true,
		   customClass: 'swal-wide',    
		   closeOnConfirm: false }, 
		   function(){
		   		//$.get(kind+'.bloquear.'+id, function (result) {
		   			swal(
		   			{   title: "",
		   			   text: "Caso con id: 12031801 cerrado exitosamente",
		   			   type: "success",   
		   			   showCancelButton: false,   
		   			   confirmButtonColor: "#31B404",   
		   			   confirmButtonText: "Aceptar",   
		   			   closeOnConfirm: false,
		   			   html: true 
		   			}, 
		   				function()
		   				{   
		   			   		location.reload();
		   			   	}
		   			);

		   		//});
		   		 
		});
}

$("input[name='anonymous']").change(function(){
	//alert($("input[name='anonymous[]']:checked").val())
	if ($("input[name='anonymous']:checked").val() == 1)
	{
		$('#anonymous2').hide(500);
	}
	else
	{
		$('#anonymous2').show(500);
	}
});

$("form#registercomplaint").submit(function(e) {

	var formData = new FormData(this);

	$.ajax({
	    url: 'registro_denuncia2',
	    type: 'POST',
	    data: formData,
	    success: function (data) {
	        data2 = JSON.parse(data);
	        //alert(data2.message)
	       	if (data2.response == 0)
	       	{
	       		info = '<div class="alert alert-success alert-dismissible" role="alert">';
	       	}

	       	info += data2.response_description+'</div>';
	       	
	       	info += '<div class="alert alert-info alert-dismissible" role="alert">';
	       	info += 'Puede dar seguimiento al caso con el siguiente id y con la contraseña ingresada anteriormente.<br><br>';
	       	info += '<b>Id: '+data2.id+'</b><br><br>';
	       	info += '<b>No olvide guarda este ID y su contraseña, ya que sólo con estos datos podrá consultar el estado del caso.</b>';
	       	info += '</div>';

	       	info += '<div style="text-align: center;">';
	       	info += '<p><a href="denuncias" class="btn btn-danger">Volver</a></p>';
	       	info += '</div>';

	       	$('#register-form').hide();
	       	$('#register-response').html(info);
	    },
	    cache: false,
	    contentType: false,
	    processData: false
	});

	event.preventDefault();
});