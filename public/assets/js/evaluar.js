$('#results').change(function() {
	if ($("#results").val() == 2) //inefectiva
	{	
		$("#comments").hide(500);
		$("#comments2").empty();
		$("#hallazgos").show(500);
	}
	else if ($("#results").val() == 1)
	{
		$("#hallazgos").hide(500);
		$("#comments").show(500);

	}
	else if ($("#results").val() == "")
	{
		$("#hallazgos").hide(500);
		$("#comments").hide(500);
	}
});


