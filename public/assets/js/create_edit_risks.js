
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