//prueba pero no me funciona (en objetivos/index.blade.php)
function mostrar_objetivos(this)
{
	if (this.value == "1")
	{
		document.getElementById("objetivos_org1").style.display = 'block'
	}
	else if (this.value == "2")
	{
		document.getElementById("objetivos_org2").style.display = 'block'
	}
	else if (this.value == "3")
	{
		document.getElementById("objetivos_org3").style.display = 'block'
	}
}

