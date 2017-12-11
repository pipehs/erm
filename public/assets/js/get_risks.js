$("#org").change(function() {
	if ($("#org").val() != '')
	{

		$.get('get_objective_risk.'+$("#org").val(), function (result) {

				riesgos_negocio = new Array()
				$("#cargando").html('<br>');
				$("#objective_risk_id").empty();
				$("#objective_risk_id").change();
				$("#cargando2").empty(); //cargando de riesgos							
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				var i = 0;
				//seteamos datos en select de riesgos / objetivos
				$(datos).each( function() {
					riesgos_negocio[i] = {id: this.id, name: this.name,description: this.description,risk_category_id: this.risk_category_id};
					i++
					$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +' - '+ this.description +'</option>');
					$("#riesgos").append('<tr><td>' + this.name + '</td><td>' +this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
				});
		});
		
		//se obtienen riesgos de proceso para la organización seleccionada
		$.get('get_risk_subprocess.'+$("#org").val(), function (result) {
				//alert(result)				
				riesgos_proceso = new Array()
				$("#cargando").html('<br>');
				$("#risk_subprocess_id").empty();
				$("#risk_subprocess_id").change();
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				var i = 0;
				//seteamos datos en select de riesgos / procesos
				$(datos).each( function() {
					riesgos_proceso[i] = {id: this.id, name: this.name,description: this.description,risk_category_id: this.risk_category_id};
					i++
					//alert(riesgos_procesos[i]);
					$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +' - '+ this.description +'</option>');
					$("#riesgos").append('<tr><td>' + this.name + '</td><td>' + this.proba_def + ' (' + this.avg_probability + ')</td><td>' + this.impact_def + ' (' + this.avg_impact + ')</td></tr>');
				});

		});

		$("#riesgos_objetivos").show(500);
		$("#riesgos_procesos").show(500);
	}
	
});

//Función para select de Riesgos simple (no múltiple) ---> Mantenedor de Controles
$("#organization").change(function() {
	if ($("#organization").val() != '')
	{

		$.get('get_objective_risk.'+$("#organization").val(), function (result) {

				riesgos_negocio = new Array()
				$("#cargando").html('<br>');
				$("#objective_risk_id").empty();
				$("#objective_risk_id").change();
				$("#cargando2").empty(); //cargando de riesgos							
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				//seteamos datos en select de riesgos / objetivos
				riesgos = '<option value="" selected="true"></option>'
				$(datos).each( function() {
					riesgos += '<option value="' + this.id + '">' + this.name +' - '+ this.description +'</option>'
				});

				$("#objective_risk_id").append(riesgos)
		});
		
		//se obtienen riesgos de proceso para la organización seleccionada
		$.get('get_risk_subprocess.'+$("#organization").val(), function (result) {
				//alert(result)				
				riesgos_proceso = new Array()
				$("#cargando").html('<br>');
				$("#risk_subprocess_id").empty();
				$("#risk_subprocess_id").change();
				//parseamos datos obtenidos
				var datos = JSON.parse(result);
				riesgos = '<option value="" selected="true"></option>'
				$(datos).each( function() {
					riesgos += '<option value="' + this.id + '">' + this.name +' - '+ this.description +'</option>'
				});

				$("#risk_subprocess_id").append(riesgos)

		});

		$("#riesgos_objetivos").show(500);
		$("#riesgos_procesos").show(500);
	}
	
});

//ACTUALIZACIÓN 21-08-17: Filtramos por categoría
$("#risk_category_id").change(function()
{
	if ($("#org").val() != '' && $("#risk_category_id").val() != '')
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();
		$(riesgos_proceso).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})

		$(riesgos_negocio).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		});
	}
	else if ($("#org").val() != '' && $("#risk_category_id").val() == '')
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();
		$(riesgos_proceso).each(function() {
			$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
		})

		$(riesgos_negocio).each(function() {
			$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
		});
	}
	else if ($("#org").val() == '' && $("#risk_category_id").val() == '')
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();
	}
});

$("#risk_subcategory_id").change(function()
{
	if ($("#org").val() != '' && $("#risk_subcategory_id").val() != '')
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();

		$(riesgos_proceso).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_subcategory_id").val())
			{
				$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})

		$(riesgos_negocio).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			//Realizamos comparación con risk_category_id al igual que en la función de arriba, ya que un riesgo de negocio debería ser siempre de la categoría principal
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		});
	}
	else if ($("#risk_subcategory_id").val() == '' && $("#risk_category_id").val() != '')
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();
		$(riesgos_proceso).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		})

		$(riesgos_negocio).each(function() {
			//agregamos sólo los riesgos de la categoría correspondiente
			if (this.risk_category_id == $("#risk_category_id").val())
			{
				$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
			}
		});
	}
	else
	{
		$("#risk_subprocess_id").empty();
		$("#risk_subprocess_id").change();
		$("#objective_risk_id").empty();
		$("#objective_risk_id").change();
		$(riesgos_proceso).each(function() {
			$("#risk_subprocess_id").append('<option value="' + this.id + '">' + this.name +'</option>');
		})

		$(riesgos_negocio).each(function() {
			$("#objective_risk_id").append('<option value="' + this.id + '">' + this.name +'</option>');
		});
	}
});