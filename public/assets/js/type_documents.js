$("#kind").change(function() {
	
	if ($("#orgs").val() != '') //vemos que se haya seleccionado una organización
	{
		if ($('#kind').val() == 1) //seleccionamos controles de organización
		{
			var select = '<div class="form-group">';
			select += '<label for="control_type" class="col-sm-4 control-label">Seleccione tipo de control</label>';
			select += '<div class="col-sm-3">'
			select += '<select name="control_type" id="control_type" onchange="getControls()" class="form-control" required="true">';
			select += '<option value="" selected disabled>- Seleccione tipo -</option>';
			select += '<option value="0">Controles de proceso</option>'
			select += '<option value="1">Controles de entidad</option>'
			select += '</select>';
			select += '</div></div>';

			$('#seleccion2').html('');
			$('#seleccion').html(select);
			$('#seleccion').show(500);

		}
		else if ($('#kind').val() == 2) //Agregaremos seleccionar un tipo de hallazgo
		{
			var select = '<div class="form-group">';
			select += '<label for="kind_issue" class="col-sm-4 control-label">Seleccione categoría de hallazgo</label>';
			select += '<div class="col-sm-3">'
			select += '<select name="kind_issue" class="form-control" required="true">';
			select += '<option value="" selected disabled>- Seleccione -</option>';
			select += '<option value="0">Procesos</option>'
			select += '<option value="1">Subprocesos</option>'
			select += '<option value="2">Organización</option>'
			select += '<option value="3">Controles de proceso</option>'
			select += '<option value="4">Controles de entidad</option>'
			select += '<option value="5">Programas de auditoría</option>'
			select += '<option value="6">Auditorías</option>'
			select += '</select>';
			select += '</div></div>';

			$('#seleccion2').html('');
			$('#seleccion').html(select);
			$('#seleccion').show(500);
		}

		else if ($('#kind').val() == 3 || $('#kind').val() == 4 || $('#kind').val() == 5) //para ver documentos de notas, programas de auditoría, o pruebas de auditoría, se debe seleccionar el plan de auditoría
		{
			//Añadimos la imagen de carga en el contenedor
			$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
			//obtenemos los datos necesarios según sea el caso
			$.get('auditorias.get_planes.'+ $("#orgs").val(), function (result) {
					//alert("entro2");

					var select = '<div class="form-group">';
					select += '<label for="control_id" class="col-sm-4 control-label">Seleccione plan de auditoría</label>';
					select += '<div class="col-sm-3">'
					select += '<select name="audit_plan_id" class="form-control" required="true">';
					select += '<option value="" selected disabled>- Seleccione -</option>';
					var datos = JSON.parse(result);
					$(datos).each( function() {
							select += '<option value="'+this.id+'">'+this.name+'</option>';
					});

					select += '</select>';
					select += '</div></div>';

					$('#seleccion2').html('');
					$('#seleccion').html(select);
					$('#seleccion').show(500);

					$("#cargando").html('<br>');
			});
		}
		//else if ($('#kind').val() == 6) Por ahora no especificaremos nada para plan de acción
		else if ($('#kind').val() == '')
		{
			$('#seleccion').html('');
			$('#seleccion2').html('');
			$('#seleccion').hide(500);
		}
	}
	else
	{
		swal('Error','Seleccione una organización','error');
	}
});

//para el caso que se seleccione una organización despues de seleccionar el tipo de documento
$("#orgs").change(function() {

	if ($("#orgs").val() != '') //vemos que se haya seleccionado una organización
	{
		//actualizamos valor de #kind
		$('#kind').change();
	}
});

function getControls()
{
	if ($('#orgs').val() != '' && $('#control_type').val() != '')
	{
			//Añadimos la imagen de carga en el contenedor
			$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>');
			//obtenemos los datos necesarios según sea el caso
			$.get('get_controls2.'+ $("#orgs").val() +','+$("#control_type").val(), function (result) {
					//alert("entro2");

					var select = '<div class="form-group">';
					select += '<label for="control_id" class="col-sm-4 control-label">Seleccione control</label>';
					select += '<div class="col-sm-3">'
					select += '<select name="control_id" class="form-control" required="true">';
					select += '<option value="" selected disabled>- Seleccione -</option>';

					var datos = JSON.parse(result);
					$(datos).each( function() {
							select += '<option value="'+this.id+'">'+this.name+'</option>';
					});

					select += '</select>';
					select += '</div></div>';

					$('#seleccion2').html(select);
					$('#seleccion2').show(500);
					$("#cargando").html('<br>');
			});

	}

}
