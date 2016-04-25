//selecciona riesgo, control o subproceso
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
					select += '<div class="col-sm-4">'
					select += '<select name="control_id_test_'+test+'" class="form-control">';
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

			//prueba de riesgo
			else if ($("#type2_test_"+test).val() == 2)
			{
				

				$.get('get_risks.'+org, function (result) {

					var select = '<div class="form-group">';
					select += '<label for="control_id_test'+test+'" class="col-sm-4 control-label">Seleccione riesgo</label>';
					select += '<div class="col-sm-4">'
					select += '<select name="risk_id_test_'+test+'" class="form-control">';
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
			else if ($("#type2_test_"+test).val() == 3)
			{
				$.get('get_subprocesses.'+org, function (result) {

					var select = '<div class="form-group">';
					select += '<label for="control_id_test'+test+'" class="col-sm-4 control-label">Seleccione subproceso</label>';
					select += '<div class="col-sm-4">'
					select += '<select name="subprocess_id_test_'+test+'" class="form-control">';
					select += '<option value="" selected disabled>- Seleccione -</option>';

					var datos = JSON.parse(result);
					$(datos).each( function() {
						//en caso que se esté editando
						if (subprocess_id == this.id)
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
}