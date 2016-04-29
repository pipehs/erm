<?php

//funciones para menus activos y dropdown
function activeMenu($uri='')
 {
  $active = '';

  if (Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
  {
   $active = 'active';
  }

  //verificación para menús compuestos
  $compuesto = explode(".",Request::segment(1));

	foreach ($compuesto as $compuesto)
	{
		if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
		{
			$active = 'active';
		}
	}

  return $active;
 }

//dropdown de datos maestros
function dropDown1()
{
	$uri = array('organization','categorias_objetivos','objetivos','procesos','subprocesos',
				'categorias_riesgos','riskstype','roles','stakeholders','causas','efectos');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//dropdown de identificación de eventos de riesgo
function dropDown2()
{
	$uri = array('crear_encuesta','enviar_encuesta','ver_encuesta');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri) || stristr(Request::segment(1), $uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//dropdown de evaluación de riesgos
function dropDown3()
{
	$uri = array('evaluacion','evaluacion_encuestas','evaluacion_manual');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//dropdown de reportes básicos
function dropDown4()
{
	$uri = array('heatmap','matrices','matriz_riesgos','encuestas','reporte_planes','hallazgos');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '.' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}

}

//active de controles
function dropDown5()
{
	$uri = array('controles','evaluar_controles');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//active de auditoría de riesgos
function dropDown6()
{
	$uri = array('auditorias','plan_auditoria','nuevo_plan','plan','ver_plan','crear_pruebas','pruebas','programas_auditoria','ejecutar_pruebas',
				'supervisar','notas','planes_accion');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//active de risks
function dropDown7()
{
	$uri = array('enlazar_riesgos','kri','riesgo_kri');

	foreach ($uri as $uri)
	{
		if(Request::is(Request::segment(1) . '/' . $uri . '/*') || Request::is(Request::segment(1) . '/' . $uri) || Request::is($uri))
		{
			return 'display: block;';
		}

		//verificación para menús compuestos
		$compuesto = explode(".",Request::segment(1));

		foreach ($compuesto as $compuesto)
		{
			if(Request::is($compuesto . '/' . $uri . '/*') || Request::is($compuesto . '.' . $uri) || $compuesto == $uri)
			{
				return 'display: block;';
			}
		}
	}
}

//helper para cargar archivos
function upload_file($archivo,$dir,$id)
{
	//separamos nombre archivo extension
    $file = explode('.',$archivo->getClientOriginalName());

    $guardado = Storage::put(
        $dir.'/'. $file[0] . "___" . $id . "." . $file[1],
        file_get_contents($archivo->getRealPath())
    );

    if ($guardado)
    {
    	return 0;
    }
    else
    {
    	return 1;
    }
}

//Funcion php que valida rut en Chile
function validaRut($rut){
    $suma=0;
    if(strpos($rut,"-")==false)
    {
        $RUT[0] = substr($rut, 0, -1);
        $RUT[1] = substr($rut, -1);
    }
    else
    {
        $RUT = explode("-", trim($rut));
    }
    $elRut = str_replace(".", "", trim($RUT[0]));
    $factor = 2;
    for($i = strlen($elRut)-1; $i >= 0; $i--):
        $factor = $factor > 7 ? 2 : $factor;
        $suma += $elRut{$i}*$factor++;
    endfor;
    $resto = $suma % 11;
    $dv = 11 - $resto;
    if($dv == 11)
    {
        $dv=0;
    }
    else if($dv == 10)
    {
        $dv="k";
    }
    else
    {
        $dv=$dv;
    }
   if($dv == trim(strtolower($RUT[1]))){
       return true;
   }else{
       return false;
   }
}

?>