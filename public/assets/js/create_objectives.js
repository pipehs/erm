$("#perspective").change(function() {
	if ($("#perspective").val() != '') //Si es que se ha seleccionado valor válido de perspectiva
	{
		//ahora agregamos posibles objetivos impactados
		$.get('objetivos.objectives_impact.'+strategic_plan_id+','+$("#perspective").val(), function (result) {
			//parseamos datos obtenidos
			var datos = JSON.parse(result);

			var objetivos = '';
			$(datos).each( function() {
				objetivos += '<option value="'+this.id+'"">'+this.code_name+'</option>';
			});

			$("#objectives_id").html(objetivos);
		});	

		if ($("#perspective").val() != 3) //perspectiva de clientes no tiene subdivisiones
		{	
			$("#perspective2").attr('required','true');
			$("#perspective2").removeAttr('disabled');

			$("#perspective2").empty();
			//seteamos perspectivas
			if ($("#perspective").val() == 1) //perspectivas financieras
			{
				//en caso de estar editando, vemos si que perspectiva 2 esta siendo seleccionada
				var perspectivas = '<option value="" disabled selected>- Seleccione -</disabled>';

				if (perspective2_id == 1)
				{
					perspectivas += '<option value="1" selected>Productividad</option>';
					perspectivas += '<option value="2">Aumento</option>';
				}
				else if (perspective2_id == 2)
				{
					perspectivas += '<option value="1">Productividad</option>';
					perspectivas += '<option value="2" selected>Aumento</option>';
				}
				else
				{
					perspectivas += '<option value="1">Productividad</option>';
					perspectivas += '<option value="2">Aumento</option>';
				}
				
			}
			else if ($("#perspective").val() == 2) //procesos
			{
				//en caso de estar editando, vemos si que perspectiva 2 esta siendo seleccionada
				var perspectivas = '<option value="" disabled selected>- Seleccione -</disabled>';

				if (perspective2_id == 1)
				{
					perspectivas += '<option value="1" selected>Gestión operacional</option>';
					perspectivas += '<option value="2">Gestión de clientes</option>';
					perspectivas += '<option value="3">Gestión de innovación</option>';
					perspectivas += '<option value="4">Reguladores sociales</option>';
				}
				else if (perspective2_id == 2)
				{
					perspectivas += '<option value="1">Gestión operacional</option>';
					perspectivas += '<option value="2" selected>Gestión de clientes</option>';
					perspectivas += '<option value="3">Gestión de innovación</option>';
					perspectivas += '<option value="4">Reguladores sociales</option>';
				}
				else if (perspective2_id == 3)
				{
					perspectivas += '<option value="1">Gestión operacional</option>';
					perspectivas += '<option value="2">Gestión de clientes</option>';
					perspectivas += '<option value="3" selected>Gestión de innovación</option>';
					perspectivas += '<option value="4">Reguladores sociales</option>';
				}
				else if (perspective2_id == 4)
				{
					perspectivas += '<option value="1">Gestión operacional</option>';
					perspectivas += '<option value="2">Gestión de clientes</option>';
					perspectivas += '<option value="3">Gestión de innovación</option>';
					perspectivas += '<option value="4" selected>Reguladores sociales</option>';
				}
				else
				{
					perspectivas += '<option value="1">Gestión operacional</option>';
					perspectivas += '<option value="2">Gestión de clientes</option>';
					perspectivas += '<option value="3">Gestión de innovación</option>';
					perspectivas += '<option value="4">Reguladores sociales</option>';
				}
				
			}
			else if ($("#perspective").val() == 4) //aprendizaje
			{
				var perspectivas = '<option value="" disabled selected>- Seleccione -</disabled>';
				if (perspective2_id == 1)
				{
					perspectivas += '<option value="1" selected>Capital humano</option>';
					perspectivas += '<option value="2">Capital de información</option>';
					perspectivas += '<option value="3">Capital organizativo</option>';
				}
				else if (perspective2_id == 2)
				{
					perspectivas += '<option value="1">Capital humano</option>';
					perspectivas += '<option value="2"selected>Capital de información</option>';
					perspectivas += '<option value="3">Capital organizativo</option>';
				}
				else if (perspective2_id == 3)
				{
					perspectivas += '<option value="1">Capital humano</option>';
					perspectivas += '<option value="2">Capital de información</option>';
					perspectivas += '<option value="3" selected>Capital organizativo</option>';
				}
				else
				{
					perspectivas += '<option value="1">Capital humano</option>';
					perspectivas += '<option value="2">Capital de información</option>';
					perspectivas += '<option value="3">Capital organizativo</option>';
				}
				
			}
			
			$("#perspective2").append(perspectivas);

		}
		else
		{
			$("#perspective2").attr('disabled','true');
			$("#perspective2").attr('required','false');
			$("#perspective2").val('');
		}

	}
	else
	{
		$("#perspective2").attr('required','false');
		$("#perspective2").attr('disabled','true');
		$("#perspective2").val('');
	}
});