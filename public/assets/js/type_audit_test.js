//ACTUALIZACIÓN 06-12-16: Se realizarán varios filtros según sea prueba de proceso o de entidad
$('#type1').change(function() {

	if ($('#type1').val() != '')
	{
		//primero obtenemos organizacion de plan de auditoría
		$.get('get_organization.'+$("#audit_plans").val(), function (result) {
			//alert($('#type2_test_'+test).val());
			var organization = JSON.parse(result);
			org = organization.id; //variable global org
			if (org)
			{
				if ($('#type1').val() == 1) //prueba a nivel de proceso
				{
					//obtenemos procesos
					$.get('get_processes.'+org, function (result) {
						var select = '<div class="form-group">';
						select += '<label for="process_id" class="col-sm-4 control-label">Seleccione proceso</label>';
						select += '<div class="col-sm-6">'
						select += '<select name="process_id" id="process_id" onchange="getSubprocesses()" class="form-control" required="true">';
						select += '<option value="" selected disabled>- Seleccione -</option>';

						var datos = JSON.parse(result);
								
						$(datos).each( function() {
							if (type_id == this.id)
							{
								select += '<option value="'+this.id+'" selected>'+this.name+'</option>';
							}
							else
							{
								select += '<option value="'+this.id+'">'+this.name+'</option>';
							}
						});

						select += '</select>';
						select += '</div></div>';
						select += '<div id="subprocess_cat" style="display:none;"></div>';
						select += '<div id="control_cat" style="display:none;"></div>';

						$('#categoria_test_1').html(select);
						$('#categoria_test_1').show(500);
					});
				}
				else if ($('#type1').val() == 2) //prueba a nivel de entidad
				{
					//agregamos select de perspectiva
					var select = '<div class="form-group">'
					select += '<label for="perspective" class="col-sm-4 control-label">Seleccione perspectiva</label>'
					select += '<div class="col-sm-6">'
					select += '<select name="perspective" id="perspective" onchange="getObjectiveControls()" class="form-control" required="true">'
					select += '<option value="" selected disabled>- Seleccione -</option>'
					select += '<option value="1">Financiera</option>'
					select += '<option value="2">Procesos</option>'
					select += '<option value="3">Clientes</option>'
					select += '<option value="4">Aprendizaje</option>'
					select += '</select>'
					select += '</div></div>';
					select += '<div id="control_cat" style="display:none;"></div>';

					$('#categoria_test_1').html(select);
					$('#categoria_test_1').show(500);
				}
			}
		});
	}
	else
	{
		$('#categoria_test_1').html('');
	} 
});

//añadimos subprocesos y controles de proceso
function getSubprocesses()
{
	$.get('get_subprocesses_from_process.'+org+'.'+$('#process_id').val(), function (result) {

		var select = '<div class="form-group">';
		select += '<label for="subprocess_id" class="col-sm-4 control-label">Seleccione subproceso(s) de forma opcional</label>';
		select += '<div class="col-sm-6">'
		select += '<select name="subprocess_id[]" class="form-control" id="subprocess_id" onchange="getSubprocessControls()" multiple>';
		select += '<option value="" selected disabled>- Seleccione -</option>';

		var datos = JSON.parse(result);
		$(datos).each( function() {
			//en caso que se esté editando
			if (type_id == this.id)
			{
				select += '<option value="'+this.id+'" selected>'+this.name+'</option>';
			}
			else
			{
				select += '<option value="'+this.id+'">'+this.name+'</option>';
			}
							
		});

		select += '</select>';
		select += '</div></div>';

		$('#subprocess_cat').html(select);
		$('#subprocess_cat').show(500);
	});

	$.get('get_controls_from_process.'+org+'.'+$('#process_id').val(), function (result) {

		var select = '<div class="form-group">';
		select += '<label for="control_id" class="col-sm-4 control-label">Seleccione Control(es) de forma opcional</label>';
		select += '<div class="col-sm-6">'
		select += '<select name="control_id[]" class="form-control" multiple>';
		select += '<option value="" selected disabled>- Seleccione -</option>';

		var datos = JSON.parse(result);
		$(datos).each( function() {
			//en caso que se esté editando
			if (type_id == this.id)
			{
				select += '<option value="'+this.id+'" selected>'+this.name+' - '+this.description+'</option>';
			}
			else
			{
				select += '<option value="'+this.id+'">'+this.name+' - '+this.description+'</option>';
			}
							
		});

		select += '</select>';
		select += '</div></div>';

		$('#control_cat').html(select);
		$('#control_cat').show(500);
	});

}

//cada vez que se agregue o quite un subproceso se filtrarán los controles
function getSubprocessControls()
{
	var select = '<div class="form-group">';
	select += '<label for="control_id" class="col-sm-4 control-label">Seleccione Control(es) de forma opcional</label>';
	select += '<div class="col-sm-6">'
	select += '<select name="control_id[]" id="control_id" onchange="getRisks()" class="form-control" multiple>';
	select += '<option value="" selected>- Seleccione -</option>';
	select += '</select>';
	select += '</div></div>';
	$('#control_cat').html(select);
	$('#control_cat').show(500);

	var subprocesses_id = [];
	$('#subprocess_id option:selected').each(function() {
		subprocesses_id.push(this.value)
	});

	$.get('get_controls_from_subprocess.'+org+'.['+subprocesses_id+']', function (result) {
		var datos = JSON.parse(result);
		$(datos).each( function() {
			//en caso que se esté editando
			select += '<option value="'+this.id+'">'+this.name+' - '+this.description+'</option>';		
		});

		$('#control_id').append(select);	
	});
}

function getRisks()
{
	var riesgos = '<div class="col-sm-12 col-sm-6">'
		riesgos += '<div class="box">'
		riesgos += '<div class="box-header">'
		riesgos += '<div class="box-name">'
		riesgos += '<i class="fa fa-user"></i>'
		riesgos += '<span>Informaci&oacute;n de riesgos mitigados por los controles seleccionados</span>'
		riesgos += '</div>'
		riesgos += '<div class="box-icons">'
		riesgos += '<a class="collapse-link">'
		riesgos += '<i class="fa fa-chevron-up"></i>'
		riesgos += '</a><a class="expand-link"><i class="fa fa-expand"></i></a>'
		riesgos += '<a class="close-link"><i class="fa fa-times"></i></a>'
		riesgos += '</div><div class="no-move"></div></div><div class="box-content">'
		
	$('#riesgos').html(riesgos)
	$('#riesgos').show(500)
}

function getObjectiveControls()
{
	var select = '<div class="form-group">';
	select += '<label for="control_id" class="col-sm-4 control-label">Seleccione Control(es) de forma opcional</label>';
	select += '<div class="col-sm-6">'
	select += '<select name="control_id[]" id="control_id" onchange="getRisks()" class="form-control" multiple>';
	select += '<option value="" selected>- Seleccione -</option>';
	select += '</select>';
	select += '</div></div>';
	$('#control_cat').html(select);
	$('#control_cat').show(500);

	$.get('get_controls_from_perspective.'+org+'.'+$('#perspective').val(), function (result) {
		var datos = JSON.parse(result);
		$(datos).each( function() {
			//en caso que se esté editando
			select += '<option value="'+this.id+'">'+this.name+' - '+this.description+'</option>';		
		});

		$('#control_id').append(select);	
	});
}
/*
//selecciona control o subproceso
function getType(test)
{
	//primero obtenemos organizacion de plan de auditoría
	$.get('get_organization.'+$("#audit_plans").val(), function (result) {
		//alert($('#type2_test_'+test).val());
		var organization = JSON.parse(result);
		org = organization.id; //variable global org
		if (org)
		{
			//prueba de control
			if ($('#type2_test_'+test).val() == 1)
			{
				//alert("entro1");
				//obtenemos los datos necesarios según sea el caso
				$.get('get_controls.'+org, function (result) {
					//alert("entro2");
					//creamos select en div de categorias
					var select = '<div class="form-group">';
					select += '<label for="control_id_test'+test+'" class="col-sm-4 control-label">Seleccione control</label>';
					select += '<div class="col-sm-6">'
					select += '<select name="control_id_test_'+test+'" class="form-control" required="true">';
					select += '<option value="" selected disabled>- Seleccione -</option>';

					var datos = JSON.parse(result);
					
					$(datos).each( function() {
						if (type_id == this.id)
						{
							select += '<option value="'+this.id+'" selected>'+this.name+'</option>';
						}
						else
						{
							select += '<option value="'+this.id+'">'+this.name+'</option>';
						}
					});

					select += '</select>';
					select += '</div></div>';

					$('#categoria_test_'+test).html(select);
					$('#categoria_test_'+test).show(500);
				});
			}

			//prueba de subproceso
			else if ($("#type2_test_"+test).val() == 2)
			{
				$.get('get_subprocesses.'+org, function (result) {

					var select = '<div class="form-group">';
					select += '<label for="control_id_test'+test+'" class="col-sm-4 control-label">Seleccione subproceso</label>';
					select += '<div class="col-sm-6">'
					select += '<select name="subprocess_id_test_'+test+'" class="form-control" required="true">>';
					select += '<option value="" selected disabled>- Seleccione -</option>';

					var datos = JSON.parse(result);
					$(datos).each( function() {
						//en caso que se esté editando
						if (type_id == this.id)
						{
							select += '<option value="'+this.id+'" selected>'+this.name+'</option>';
						}
						else
						{
							select += '<option value="'+this.id+'">'+this.name+'</option>';
						}
							
					});

					select += '</select>';
					select += '</div></div>';

					$('#categoria_test_'+test).html(select);
					$('#categoria_test_'+test).show(500);
				});
			}
			else
			{
				$('#categoria_test_'+test).hide(500);
			}
		}
		
	});	
} */