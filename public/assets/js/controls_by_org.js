function selectControls()
{

	if ($("#organization_id").val() != '' && $("#control_kind").val() != '') //Si es que se ha seleccionado valor válido de control
	{
		if ($('#control_kind').val() == 1) //controles de entidad
		{
			$('#control').hide(500)
			$('#procesos').hide(500)
			$('#subprocesos').hide(500)
			$('#procesos').empty()
			$('#subprocesos').empty()
			$('#create_edit').hide(500)
			$('#button').hide(500)

			$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>')

			$.get('controles.get_objective_controls.'+$("#organization_id").val(), function (result) {
				var datos = JSON.parse(result);
				var controles = '<div class="form-group">'
				controles += '<label for="control_id" class="col-sm-4 control-label">Seleccione control</label>'
				controles += '<div class="col-sm-3">'
				controles += '<select name="control_id" id="control_id" onChange="selectControls2()" class="form-control" required>'
				controles += '<option value="" disabled selected>- Seleccione -</option>'
				$(datos).each( function() {
					controles += '<option value="'+this.id+'">'+this.name+'</option>'
				});

				controles += '</select>'
				$('#control').html(controles)
				$('#control').show(500)
				$('#cargando').html('<br>')
			});
		}
		else if ($('#control_kind').val() == 2) //controles de proceso
		{
			$('#control').hide(500)
			$('#control').html('')
			$('#create_edit').hide(500)
			$('#button').hide(500)

			$('#cargando').html('<div><center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center></div>')
			$.get('get_processes.'+$("#organization_id").val(), function (result) {
				var datos = JSON.parse(result);
				var procesos = '<div class="form-group">'
				procesos += '<label for="process_id" class="col-sm-4 control-label">Seleccione proceso</label>'
				procesos += '<div class="col-sm-3">'
				procesos += '<select name="process_id" id="process_id" onChange="subprocesos()" class="form-control" required>'
				procesos += '<option value="" disabled selected>- Seleccione -</option>'
				$(datos).each( function() {
					procesos += '<option value="'+this.id+'">'+this.name+'</option>'
				});

				procesos += '</select>'
				$('#procesos').html(procesos)
				$('#procesos').show(500)
				$('#cargando').html('<br>')
			});
		}
	}
	else
	{
		$('#control_id').empty();
		$('#procesos').empty();
		$('#subprocesos').empty();
	}
}


function subprocesos()
{
	if ($('#process_id').val() != '' && $("#organization_id").val() != '' && $("#control_kind").val() == 2)
	{
		$.get('get_subprocesses.'+$("#organization_id").val(), function (result) {
				var datos = JSON.parse(result);
				var subprocesos = '<div class="form-group">'
				subprocesos += '<label for="subprocess_id" class="col-sm-4 control-label">Seleccione subproceso</label>'
				subprocesos += '<div class="col-sm-3">'
				subprocesos += '<select name="subprocess_id" id="subprocess_id" onChange="subprocesscontrol()" class="form-control" required>'
				subprocesos += '<option value="" disabled selected>- Seleccione -</option>'
				$(datos).each( function() {
					subprocesos += '<option value="'+this.id+'">'+this.name+'</option>'
				});

				subprocesos += '</select>'
				$('#control').hide(500);
				$('#control').empty();
				$('#subprocesos').html(subprocesos)
				$('#subprocesos').show(500)
				$('#cargando').html('<br>')
		});
	}
	else
	{
		swal('Cuidado','Verifique que las opciones seleccionadas sean correctas','warning')
	}
}

function subprocesscontrol()
{
	if ($('#process_id').val() != '' && $("#organization_id").val() != '' && $("#control_kind").val() == 2 && $('#subprocess_id').val() != '')
	{
		$.get('controles.get_subprocess_controls.'+$("#organization_id").val()+'.'+$("#subprocess_id").val(), function (result) {
				var datos = JSON.parse(result);
				var controles = '<div class="form-group">'
				controles += '<label for="control_id" class="col-sm-4 control-label">Seleccione control</label>'
				controles += '<div class="col-sm-3">'
				controles += '<select name="control_id" id="control_id" onChange="selectControls2()" class="form-control" required>'
				controles += '<option value="" disabled selected>- Seleccione -</option>'
				$(datos).each( function() {
					controles += '<option value="'+this.id+'">'+this.name+'</option>'
				});

				controles += '</select>'
				$('#control').html(controles)
				$('#control').show(500)
				$('#cargando').html('<br>')
			});
	}
	else
	{
		swal('Cuidado','Verifique que las opciones seleccionadas sean correctas','warning')
	}
}

//agregamos si quiere agregar una nueva eval o editar última evaluación ==> ACT 16-11-2016: No será necesario ver si es nueva o editar, ya que estarán las 2 opciones en la vista
function selectControls2()
{
	if ($('#control_id').val() != '' || $('#subprocess_id').val() != '')
	{
		$('#button').show(500)
	}
}