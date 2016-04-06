	$("#uni_med").change(function() {

		if ($("#uni_med").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{
				//mostramos cotas y agregamos atributos max a cotas
				$("#cotas").fadeIn(500);
				$("#green_min").attr('max','100');
				$("#yellow_min").attr('max','100');
				$("#red_min").attr('max','100');

				$("#green_max").attr('max','100');
				$("#yellow_max").attr('max','100');
				$("#red_max").attr('max','100');
			}

			else if ($("#uni_med").val() == 1) //cantidad
			{
				//mostramos cotas y agregamos atributos max a cotas
				$("#cotas").fadeIn(500);
				$("#green_min").removeAttr('max');
				$("#yellow_min").removeAttr('max');
				$("#red_min").removeAttr('max');

				$("#green_max").removeAttr('max');
				$("#yellow_max").removeAttr('max');
				$("#red_max").removeAttr('max');
			}
		}

		else
		{
			$("#green_min").val('');
			$("#green_max").val('');
			$("#description_green").val('');
			$("#yellow_min").val('');
			$("#yellow_max").val('');
			$("#description_yellow").val('');
			$("#red_min").val('');
			$("#red_max").val('');
			$("#description_red").val('');
			$("#cotas").fadeOut(500);
		}
	});

//Varias funciones de cambio para poner mensaje
	$("#green_min").change(function() {
		if ($("#green_min").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#green_min").val() > 100)
				{
					$("#div_green_min").attr('class','form-group has-error has-feedback');
					$("#error_min_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_green_min").attr('class','form-group');
					$("#error_min_green").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#green_max").change(function() {
		if ($("#green_max").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#green_max").val() > 100)
				{
					$("#div_green_max").attr('class','form-group has-error has-feedback');
					$("#error_max_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_green_max").attr('class','form-group');
					$("#error_max_green").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#yellow_min").change(function() {
		if ($("#yellow_min").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#yellow_min").val() > 100)
				{
					$("#div_yellow_min").attr('class','form-group has-error has-feedback');
					$("#error_min_yellow").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_yellow_min").attr('class','form-group');
					$("#error_min_yellow").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#yellow_max").change(function() {
		if ($("#yellow_max").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#yellow_max").val() > 100)
				{
					$("#div_yellow_max").attr('class','form-group has-error has-feedback');
					$("#error_max_yellow").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_yellow_max").attr('class','form-group');
					$("#error_max_yellow").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#red_min").change(function() {
		if ($("#red_min").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#red_min").val() > 100)
				{
					$("#div_red_min").attr('class','form-group has-error has-feedback');
					$("#error_min_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_red_min").attr('class','form-group');
					$("#error_min_red").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});

	$("#red_max").change(function() {
		if ($("#red_max").val() != "")
		{
			if ($("#uni_med").val() == 0) //porcentaje
			{	
				if ($("#red_max").val() > 100)
				{
					$("#div_red_max").attr('class','form-group has-error has-feedback');
					$("#error_max_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
					$("#guardar").attr('disabled','true');
				}
				else
				{
					$("#div_red_max").attr('class','form-group');
					$("#error_max_red").empty();
					$("#guardar").removeAttr('disabled');
				}
			}
		}
	});


	$("#uni_med").change(function() {
		if ($("#uni_med").val() == 0) //porcentaje
		{	
			//si es que se cambia la unidad de medida, se debe volver a verificar para cada una de las cotas los valores (en caso de porcentaje)
			//verde
			if ($("#green_min").val() > 100)
			{
				$("#div_green_min").attr('class','form-group has-error has-feedback');
				$("#error_min_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_green_min").attr('class','form-group');
				$("#error_min_green").empty();
				$("#guardar").removeAttr('disabled');
			}

			if ($("#green_max").val() > 100)
			{
				$("#div_green_max").attr('class','form-group has-error has-feedback');
				$("#error_max_green").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_green_max").attr('class','form-group');
				$("#error_max_green").empty();
				$("#guardar").removeAttr('disabled');
			}

			//amarillo
			if ($("#yellow_min").val() > 100)
			{
				$("#div_yellow_min").attr('class','form-group has-error has-feedback');
				$("#error_min_yellow").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_yellow_min").attr('class','form-group');
				$("#error_min_yellow").empty();
				$("#guardar").removeAttr('disabled');
			}

			if ($("#yellow_max").val() > 100)
			{
				$("#div_yellow_max").attr('class','form-group has-error has-feedback');
				$("#error_max_yellow").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_yellow_max").attr('class','form-group');
				$("#error_max_yellow").empty();
				$("#guardar").removeAttr('disabled');
			}

			//rojo
			if ($("#red_min").val() > 100)
			{
				$("#div_red_min").attr('class','form-group has-error has-feedback');
				$("#error_min_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_red_min").attr('class','form-group');
				$("#error_min_red").empty();
				$("#guardar").removeAttr('disabled');
			}

			if ($("#red_max").val() > 100)
			{
				$("#div_red_max").attr('class','form-group has-error has-feedback');
				$("#error_max_red").html('<font color="red"><b>El valor de porcentaje debe ser menor o igual a 100</b></font>');
				$("#guardar").attr('disabled','true');
			}
			else
			{
				$("#div_red_max").attr('class','form-group');
				$("#error_max_red").empty();
				$("#guardar").removeAttr('disabled');
			}
		}
		//volvemos a normalidad todas las cotas
		else
		{
			$("#div_green_min").attr('class','form-group');
			$("#error_min_green").empty();
			$("#div_green_max").attr('class','form-group');
			$("#error_max_green").empty();

			$("#div_yellow_min").attr('class','form-group');
			$("#error_min_yellow").empty();
			$("#div_yellow_max").attr('class','form-group');
			$("#error_max_yellow").empty();

			$("#div_red_min").attr('class','form-group');
			$("#error_min_red").empty();
			$("#div_red_max").attr('class','form-group');
			$("#error_max_red").empty();
			$("#guardar").removeAttr('disabled');
		}
	})
