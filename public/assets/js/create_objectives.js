$("#perspective").change(function() {
			
	if ($("#perspective").val() != '') //Si es que se ha seleccionado valor válido de plan
	{
		if ($("#perspective").val() == 2) //perspectiva de procesos
		{	
			$("#perspective2").attr('required','true');
			$("#perspective2").show(500);
		}
		else
		{
			$("#perspective2").hide(500);
			$("#perspective2").attr('required','false');
			$("#perspective2").val('');
		}					
	}
	else
	{
		$("#perspective2").hide(500);
		$("#perspective2").attr('required','false');
		$("#perspective2").val('');
	}
});