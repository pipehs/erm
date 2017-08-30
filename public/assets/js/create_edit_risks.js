function agregar_causa()
{
			$("#causa").empty();
			var causa = '<div class="form-group">';
			causa += '<label for="causa_nueva" class="col-sm-4 control-label">Causa</label>';
			causa += '<div class="col-sm-5">';

			causa += '<textarea name="causa_nueva" class="form-control" rows="3" cols="4" required placeholder="Agregue nueva causa"></textarea>';
			causa +='<div style="cursor:hand" onclick="old_causas()"><font color="CornflowerBlue"><u>Seleccionar causas</u></font></div> <br></div>';

			$("#causa").append(causa);
}

function old_causas()
{
		$("#causa").empty();
		var causa = '<div class="form-group">';
		causa += '<label for="cause_id" class="col-sm-4 control-label">Causa(s) (para agregar varias presione ctrl + clic)</label>';
		causa += '<div class="col-sm-5">';
		causa += '<select name="cause_id[]" multiple class="form-control" id="cause_id">';

		//obtenemos todas las causas
		$.get('get_causes', function (result) {
			//parseamos datos obtenidos
			var datos = JSON.parse(result);

			$(datos).each( function() {
				causa += '<option value='+this.id+'>'+this.name+'</option>';
			});

			causa += '</select>';
			causa += '</div>';
			causa += '<div style="cursor:hand" onclick="agregar_causa()"><font color="CornflowerBlue"><u>Agregar Nueva Causa</u></font></div> <br>';
			causa += '</div>';
			$("#causa").append(causa);
		});
}

function agregar_efecto()
{
		$("#efecto").empty();
		var efecto = '<div class="form-group">'
		efecto += '<label for="efecto_nueva" class="col-sm-4 control-label">Efecto</label>';
		efecto += '<div class="col-sm-5">';

		efecto += '<textarea name="efecto_nuevo" class="form-control" rows="3" cols="4" required placeholder="Agregue nuevo efecto"></textarea>';
		efecto  +='<div style="cursor:hand" onclick="old_efectos()"><font color="CornflowerBlue"><u>Seleccionar efectos</u></font></div> <br></div>';

		$("#efecto").append(efecto);
}

//problema con old_efectos
function old_efectos()
{
		$("#efecto").empty();
		var efecto = '<div class="form-group">';
		efecto += '<label for="effect_id" class="col-sm-4 control-label">Efecto(s) (para agregar varias presione ctrl + clic)</label>';
		efecto += '<div class="col-sm-5">';
		efecto += '<select name="effect_id[]" multiple class="form-control" id="effect_id">';

		//obtenemos todas las causas
		$.get('get_effects', function (result) {
			//parseamos datos obtenidos
			var datos = JSON.parse(result);

			$(datos).each( function() {
				efecto += '<option value='+this.id+'>'+this.name+'</option>';
			});

			efecto += '</select>';
			efecto += '</div>';
			efecto += '<div stype="cursor:hand" onclick="agregar_efecto()"><font color="CornflowerBlue"><u>Agregar Nuevo Efecto</u></font></div> <br>';
			efecto += '</div>';

			$("#efecto").append(efecto);
		});
}

//ACTUALIZACIÓN 16-08-17: según las organizaciones que agreguemos, se deberán cargar posibles subprocesos y responsables de esta organización
function change_organization() 
{
	$('#other_subprocesses').empty()

	if ($('#organization_id').val() == null)
	{
		swal('Error','Debe seleccionar al menos una organización que esté expuesta al riesgo para seleccionar subprocesos','error')
	}
	else
	{
		$('#cargando1').html('<center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center>').delay(2000).html('')
		$('#cargando2').html('<center><img src="../public/assets/img/loading.gif" width="19" height="19"/></center>').delay(2000).html('')

		$('#organization_id option:selected').each(function() {

	    	var option = '<div class="form-group">'
	    	option += '<label for="subprocesses_'+$(this).val()+'" class="col-sm-4 control-label">Seleccione subproceso(s) de '+$(this).text()+'</label>'
	    	option += '<div class="col-sm-5">'
	    	option += '<select name="subprocesses_'+$(this).val()+'[]" id="subprocesses_'+$(this).val()+'" class="form-control" required="true" multiple>'
	    	
	    	$.get('get_subprocesses.'+$(this).val(), function (result) {		
				//parseamos datos obtenidos
				var datos = JSON.parse(result)
				$(datos).each(function() {
					option += '<option value="'+this.id+'">'+this.name+'</option>'
				});

				option += '</select></div></div>'

		    	$('#other_subprocesses').append(option)
	    	});

	    	var option2 = '<div class="form-group">'
	    	option2 += '<label for="stakeholder_'+$(this).val()+'" class="col-sm-4 control-label">Seleccione responsable de '+$(this).text()+'</label>'
	    	option2 += '<div class="col-sm-5">'
	    	option2 += '<select name="stakeholder_'+$(this).val()+'" id="stakeholder_'+$(this).val()+'" class="form-control">'
	    	option2 += '<option value="">- Seleccione -</option>'
	    	$.get('get_stakeholders.'+$(this).val(), function (result) {	
				//parseamos datos obtenidos
				var datos = JSON.parse(result)
				$(datos).each(function() {
					option2 += '<option value="'+this.rut+'">'+this.fullname+'</option>'
				});

				option2 += '</select></div></div>'
		    	//alert(option)

		    	$('#other_stakeholders').append(option2)
	    	});
	    });

		$('#other_subprocesses').show(500)
		$('#other_stakeholders').show(500)


	}

}

	
