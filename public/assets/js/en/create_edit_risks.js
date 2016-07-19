
function agregar_causa()
{
			$("#causa").empty();
			var causa = '<div class="form-group">';
			causa += '<label for="causa_nueva" class="col-sm-4 control-label">Cause</label>';
			causa += '<div class="col-sm-5">';
			causa += '<textarea name="causa_nueva" class="form-control" rows="3" cols="4" required placeholder="Add a new cause"></textarea>';
			causa +='<div style="cursor:hand" onclick="old_causas()"><font color="CornflowerBlue"><u>Select causes</u></font></div> <br></div>';

			$("#causa").append(causa);
}

function old_causas()
{
		$("#causa").empty();
		var causa = '<div class="form-group">';
		causa += '<label for="cause_id" class="col-sm-4 control-label">Cause(s) (to store more than one you can add pressing ctrl + click)</label>';
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
			causa += '<div style="cursor:hand" onclick="agregar_causa()"><font color="CornflowerBlue"><u>Add New Cause</u></font></div> <br>';
			causa += '</div>';
			$("#causa").append(causa);
		});
}

function agregar_efecto()
{
		$("#efecto").empty();
		var efecto = '<div class="form-group">'
		efecto += '<label for="efecto_nueva" class="col-sm-4 control-label">Effect</label>';
		efecto += '<div class="col-sm-5">';
		efecto += '<textarea name="efecto_nuevo" class="form-control" rows="3" cols="4" required placeholder="Add new effect"></textarea>';
		efecto  +='<div style="cursor:hand" onclick="old_efectos()"><font color="CornflowerBlue"><u>Select effects</u></font></div> <br></div>';

		$("#efecto").append(efecto);
}

//problema con old_efectos
function old_efectos()
{
		$("#efecto").empty();
		var efecto = '<div class="form-group">';
		efecto += '<label for="effect_id" class="col-sm-4 control-label">Effect(s) (to store more than one you can add pressing ctrl + click) </label>';
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
			efecto += '<div stype="cursor:hand" onclick="agregar_efecto()"><font color="CornflowerBlue"><u>Add new effect</u></font></div> <br>';
			efecto += '</div>';

			$("#efecto").append(efecto);
		});
}