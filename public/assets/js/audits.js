$("#organizations").change(function() {
			
	if ($("#organizations").val() != '') //Si es que se ha seleccionado valor válido de organización
	{
		$("#audit_plans").empty();
		//Añadimos la imagen de carga en el contenedor
			$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
		//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
			//primero obtenemos controles asociados a los riesgos de negocio

			//obtenemos auditorias relacionadas al plan seleccionado
			$.get('auditorias.get_planes.'+$("#organizations").val(), function (result) {

					$("#cargando").html('<br>');
					$("#audit_plans").empty();
					
					//parseamos datos obtenidos
					var datos = JSON.parse(result);
					
					$("#audit_plans").append('<option value="" disabled selected>- Seleccione -</option>');
					//seteamos datos en select de auditorías
					$(datos).each( function() {
						$("#audit_plans").append('<option value="' + this.id + '">' + this.name +'</option>');
					});
			});

			//obtenemos auditorias relacionadas al plan seleccionado
			$.get('get_stakeholders.'+$("#organizations").val(), function (result) {					
					//parseamos datos obtenidos
					stakeholders = JSON.parse(result);
			});
	}
	else
	{
		$("#audit_plans").empty();
	}
});

$("#audit_plans").change(function() {
			
	if ($("#audit_plans").val() != '') //Si es que se ha seleccionado valor válido de plan
	{
		$("#audit").empty();
		//Añadimos la imagen de carga en el contenedor
			$('#cargando').html('<div><center><img src="/assets/img/loading.gif" width="19" height="19"/></center></div>');
		//se obtienen controles asociados a los riesgos presentes en el plan de prueba seleccionado
			//primero obtenemos controles asociados a los riesgos de negocio

			//obtenemos auditorias relacionadas al plan seleccionado
			$.get('auditorias.auditorias.'+$("#audit_plans").val(), function (result) {

					$("#cargando").html('<br>');
					$("#audit").empty();

					//parseamos datos obtenidos
					var datos = JSON.parse(result);
					$("#audit").append('<option value="" disabled selected>- Seleccione -</option>');
					//seteamos datos en select de auditorías
					$(datos).each( function() {
						$("#audit").append('<option value="' + this.id + '">' + this.name +'</option>');
					});
			});
	}
	else
	{
		$("#audit").empty();
	}
});