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