$("#org").change(function() {
	if ($("#org").val() != '')
	{
		$.get('get_objective_risk.'+$("#org").val(), function (result) {
				$("#cargando").html('<br>');
				$("#objective_risk_id").empty();
				$("#objective_risk_id").change();
				$("#cargando2").empty(); //cargando de riesgos							
				//parseamos datos obtenidos
				var datos = JSON.parse(result);

				//seteamos datos en select de riesgos / objetivos
				$(datos).each( function() {
					$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
					$("#riesgos").append('<tr><td>' + this.name + '</td><td>' +this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
				});
		});

		//se obtienen riesgos de proceso para la organización seleccionada
		$.get('get_risk_subprocess.'+$("#org").val(), function (result) {
				$("#cargando").html('<br>');
				$("#risk_subprocess_id").empty();
				$("#risk_subprocess_id").change();
				//parseamos datos obtenidos
				var datos = JSON.parse(result);

				//seteamos datos en select de riesgos / procesos
				$(datos).each( function() {
					$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
					$("#riesgos").append('<tr><td>' + this.name + '</td><td>' + this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
				});
		});

		$("#riesgos_objetivos").show(500);
		$("#riesgos_procesos").show(500);
	}
	
});