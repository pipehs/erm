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
	$uri = array('organization','categorias_objetivos','procesos','subprocesos',
				'categorias_risks','riskstype','roles','stakeholders','causas','efectos');

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

//dropdown GENERAL de gestión de riesgos
function dropDown2()
{
	//OBS 19-06-18: No está riesgos en array para no confundir con reporte_riesgos... Lo que se debería hacer es cambiar el link de reporte_riesgos por, por ejemplo, risk_report
	$uri = array('evaluacion','evaluacion_agregadas','evaluacion_manual','enlazar_riesgos','kri','riesgo_kri');

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

//dropdown de identificación de eventos de riesgo
function dropDown21()
{
	$uri = array('crear_encuesta','ver_encuestas','enviar_encuesta','encuestas');

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
function dropDown22()
{
	$uri = array('evaluacion','evaluacion_agregadas','evaluacion_manual');

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

//active de kri
function dropDown23()
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

//dropdown de reportes
function dropDown4()
{
	$uri = array('heatmap','matrices','risk_matrix','matriz_procesos','matriz_procesos2','matriz_subprocesos','matriz_subprocesos2','reporte_planes','reporte_hallazgos','graficos_controles','graficos_auditorias','graficos_planes_accion','genriskmatrix','reporte_audits','reporte_riesgos','reporte_riesgos2');

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
	$uri = array('controles','evaluar_controles','residual_manual');

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

//dropdown de gestión estratégica
function dropDown7()
{
	$uri = array('plan_estrategico','mapas','objetivos','kpi','kpi2');

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

//dropdown de administración del sistema
function dropDown8()
{
	$uri = array('usuarios','controlled_risk_criteria');

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

//dropdown de sistema de denuncia
function dropDown9()
{
	$uri = array('registro_denuncia','seguimiento_denuncia','reportes_denuncias');

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

//dropdown de planes de acción
function dropDown10()
{
	$uri = array('action_plans','alert_action_plans');

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

    if (isset($file[1])) //si es que existe file[1], el archivo tenía extensión; por el contrario no tiene extensión
    {
    	//eliminamos acentos (si es que hay)
    	$nombre = eliminaAcentos($file[0]);

    	//ACTUALIZACIÓN 05-11-2016: Cada uno de los archivos se guardará en una carpeta con su id
	    $guardado = Storage::put(
	        $dir. "/" . $id . "/" . $nombre . "." . $file[1],
	        file_get_contents($archivo->getRealPath())
	    );
	}
	else
	{
		//eliminamos acentos (si es que hay)
    	$nombre = eliminaAcentos($file[0]);
		$guardado = Storage::put(
	        $dir. "/" . $id . "/" . $nombre,file_get_contents($archivo->getRealPath())
	    );
	}

    if ($guardado)
    {
    	return 0;
    }
    else
    {
    	return 1;
    }
}

//helper para descargar archivo
function downloadFile($kind,$id,$filename,$ext)
{

	//$realPath = $dir.'/'.$id.'/'.$filename;
	$kind = str_replace("'","",$kind);
	$ext = str_replace("'","",$ext);
	//echo $kind.'<br>'.$id.'<br>'.$filename.'<br>'.$ext;

	$path = 'app\\'.$kind.'\\'.$id.'\\'.$filename.'.'.$ext; //windows

	if (!file_exists($path))
	{
		$path = 'app/'.$kind.'/'.$id.'/'.$filename.'.'.$ext; //ubuntu
	}
	return response()->download(storage_path($path));

}

//Funcion php que valida rut en Chile
function validaRut($rut)
{
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

 //función interna que obtiene los archivos subidos si es que hay
function getEvidences($kind,$id)
{
        $public_path = public_path();
        if ($kind == 0) //se están solicitando evidencias de una nota
        {
            //seleccionamos carpeta de notas
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_notas\''.$id;
        }
        else if ($kind == 1) //se están solicitando respuestas a evidencias de una nota
        {
            //seleccionamos carpeta de respuestas evidencias
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_resp_notas\''.$id;
        }
        else if ($kind == 2) //se están solicitando evidencias de un hallazgo
        {
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_hallazgos\''.$id;
        }
        else if ($kind == 3) //solicitando evidencias de una evaluación de control
        {
        	$carpeta = 'C:\virtualhost\erm\storage\app\eval_controles\''.$id;
        }
        else if ($kind == 4) //solicitando evidencias de un programa de auditoría
        {
        	$carpeta = 'C:\virtualhost\erm\storage\app\programas_auditoria\''.$id;
        }
        else if ($kind == 5) //solicitando evidencias de un programa de auditoría
        {
        	$carpeta = 'C:\virtualhost\erm\storage\app\pruebas_auditoria\''.$id;
        }

        if (file_exists($carpeta) != false) //verificamos que exista la carpeta
        {
            $archivos = scandir($carpeta);

            foreach ($archivos as $archivo)
            {    
				$evidences[$j] = [
                    'id' => $id,
                    'url' => $archivo,
                ];

                $j += 1;                                        
            }
        }
        else
        {
            $evidences = NULL;
        }

        return $evidences;
}

/*FUNCIÓN OBSOLETA 23-11-16: Se actualizó esta función en ControlesController (se dividió en 2 funciones: calcControlValue() y calcControlledRisk())
//calcula el valor de riesgo controlado pasando como parametro el id del control y la evaluación de EFECTIVIDAD OPERATIVA
//por otra parte, si es un cálculo a través de una auditoría, el valor de efectividad será el de la evaluación de la prueba
function calc_controlled_risk($control_id,$efectividad)
{
	global $id;
	$id = $control_id;
	global $eval;
	$eval = $efectividad;
	DB::transaction(function() {
		 //primero que todo guardamos evaluación y obtenemos id de la misma
		$evaluation = \Ermtool\Evaluation::create([
					'name'=>'Riesgos controlados',
					'type'=>2,
					'consolidation'=>1,
					]);
		//ahora obtenemos todos los riesgos a los que este control está afectando
		//primero probaremos si el control es de proceso
		$risks = DB::table('control_risk_subprocess')
					->join('risk_subprocess','risk_subprocess.id','=','control_risk_subprocess.risk_subprocess_id')
					->join('risks','risks.id','=','risk_subprocess.risk_id')
					->where('control_risk_subprocess.control_id','=',$GLOBALS['id'])
					->select('risk_subprocess.id')
					->get();
		$type = 0; //identifica que es un riesgo de proceso
		
		if (empty($risks)) //entonces es un control de riesgo de negocio
		{
			$risks = DB::table('control_objective_risk')
						->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
						->join('risks','risks.id','=','objective_risk.risk_id')
						->where('control_objective_risk.control_id','=',$GLOBALS['id'])
						->select('objective_risk.id')
						->get();
			$type = 1; //identifica que es un riesgo de negocio
			
			if (empty($risks)) //retornamos error ya que hubo un problema para encontrar los riesgos asociados al control
			{
				echo 1;
			}
		}
		foreach ($risks as $risk)
		{
			if ($type == 0) //en evaluation risk se se usará risk_subprocess_id
			{
				//vemos si el riesgo tiene evaluacion de riesgo inherente; si es así continuamos, de lo contrario la función termina
				//primero obtenemos la última fecha de evaluación consolidada para este riesgo
				$max_fecha = DB::table('evaluation_risk')
								->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
								->where('evaluations.type','=',1)
								->where('risk_subprocess_id','=',$risk->id)
								->max('evaluations.updated_at');
			}
			else if ($type == 1)
			{
				$max_fecha = DB::table('evaluation_risk')
								->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
								->where('evaluations.type','=',1)
								->where('objective_risk_id','=',$risk->id)
								->max('evaluations.updated_at');
			}
				
			if ($max_fecha) //si es que no hay fecha, significa que no hay evaluación inherente por lo que la función termina
			{
					if ($type == 0)
					{
						//obtenemos evaluación inherente
						$evals = DB::table('evaluation_risk')
									->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
									->where('evaluation_risk.risk_subprocess_id','=',$risk->id)
									->where('evaluations.updated_at','=',$max_fecha)
									->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
									->first();
					}
					else if ($type == 1)
					{
						$evals = DB::table('evaluation_risk')
									->join('evaluations','evaluations.id','=','evaluation_risk.evaluation_id')
									->where('evaluation_risk.objective_risk_id','=',$risk->id)
									->where('evaluations.updated_at','=',$max_fecha)
									->select('evaluation_risk.avg_probability','evaluation_risk.avg_impact')
									->first();
					}
					if (!empty($evals)) //segunda verificación de que la evaluación inherente exista, puede haberse guardado mal (como NULL en valores de avg_probability y/o avg_impact)
					{
						//debemos buscar en la tabla controlled_risk_criteria el valor del riesgo controlado según el resultado de efectividad
						//OBS: Por ahora, el impacto se mantendrá igual en caso de que la evaluación sea efectiva o negativa, por lo que solo veremos probabilidad
						$val = DB::table('controlled_risk_criteria')
									->where('eval_in_risk','=',(int)$evals->avg_probability)
									->where('control_evaluation','=',$GLOBALS['eval'])
									->where('dim_eval','=',1)
									->select('eval_ctrl_risk')
									->first();
						if ($type == 0) //risk_subprocess_id
						{
							//agregamos a evaluation_risk
							$id = DB::table('evaluation_risk')
								->insertGetId([
									'evaluation_id' => $evaluation->id,
									'risk_subprocess_id' => $risk->id,
									'avg_probability' => $val->eval_ctrl_risk,
									'avg_impact' => $evals->avg_impact,
									]);
						}
						else if ($type == 1) //objective_risk_id
						{
							$id = DB::table('evaluation_risk')
								->insertGetId([
									'evaluation_id' => $evaluation->id,
									'objective_risk_id' => $risk->id,
									'avg_probability' => $val->eval_ctrl_risk,
									'avg_impact' => $evals->avg_impact,
									]);
						}
					}
					else
					{
						echo 1;
					}
			}
			else
			{
				echo 1;
			}
		}
		echo 0; //fin correcto	
	});
}*/

//función para eliminar los tíldes en el string ingresado
function eliminaAcentos($String)
{
    $String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
    $String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
    $String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
    $String = str_replace(array('í','ì','î','ï'),"i",$String);
    $String = str_replace(array('é','è','ê','ë'),"e",$String);
    $String = str_replace(array('É','È','Ê','Ë'),"E",$String);
    $String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
    $String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
    $String = str_replace(array('ú','ù','û','ü'),"u",$String);
    $String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
    $String = str_replace(array('[','^','´','`','¨','~',']'),"",$String);
    $String = str_replace("ç","c",$String);
    $String = str_replace("Ç","C",$String);
    $String = str_replace("ñ","n",$String);
    $String = str_replace("Ñ","N",$String);
    $String = str_replace("Ý","Y",$String);
    $String = str_replace("ý","y",$String);
     
    $String = str_replace("&aacute;","a",$String);
    $String = str_replace("&Aacute;","A",$String);
    $String = str_replace("&eacute;","e",$String);
    $String = str_replace("&Eacute;","E",$String);
    $String = str_replace("&iacute;","i",$String);
    $String = str_replace("&Iacute;","I",$String);
    $String = str_replace("&oacute;","o",$String);
    $String = str_replace("&Oacute;","O",$String);
    $String = str_replace("&uacute;","u",$String);
    $String = str_replace("&Uacute;","U",$String);
    return $String;
}

function eliminarArchivo($id,$kind,$name)
{
	//Elimina evidencias de prueba (carpeta pruebas_auditoria)
	if ($kind == 0)
	{
		$dir = "../../bgrcdemo2.ixus.cl/storage/app/pruebas_auditoria/".$id;
	}
	//Elimina evidencias de programa (carpeta programas_auditoria)
	else if ($kind == 1)
	{
		$dir = "../../bgrcdemo2.ixus.cl/storage/app/programas_auditoria/".$id;
	}
	//Elimina evidencias de hallazgos (carpeta evidencias_hallazgos)
	else if ($kind == 2)
	{
		$dir = "../storage/app/evidencias_hallazgos/".$id;
	}
	else if ($kind == 3) //control
	{
		$dir = "../storage/app/controles_org/".$id;
	}
	else if ($kind == 4) //notas
	{
		$dir = "../storage/app/evidencias_notas/".$id;
	}

	else if ($kind == 5) //planes de acción
	{
		$dir = "../storage/app/planes_accion/".$id;
	}
	else if ($kind == 6) //riesgos
	{
		$dir = "../storage/app/riesgos/".$id;
	}
	else if ($kind == 7) //respuestas de notas
	{
		$dir = "../storage/app/evidencias_resp_notas/".$id;
	}
	else if ($kind == 8) //evidencias para ejecución de pruebas de auditoría
	{
		$dir = "../storage/app/ejecucion_auditoria/".$id;
	}
	
	if (file_exists($dir))
	{
		$handle = opendir($dir); 

		if ($name == NULL)
		{
			while ($file = readdir($handle))  
			{   
				if (is_file($dir.'/'.$file)) 
				{ 
					//unlink($dir.'/'.$file);
					File::delete($dir.'/'.$file);	         
				}
			}

			rmdir($dir);	
		}
		else
		{
			while ($file = readdir($handle))  
			{   
				if (is_file($dir.'/'.$file)) 
				{
					$file_name = explode('.',$file);
					$file_name = $file_name[0];
					if (strcmp($file_name,$name) == 0)
					{
						//unlink($dir.'/'.$file);
						File::delete($dir.'/'.$file);	    
					}     
				}
			}
		}
	}	
} 

function eliminarSaltos($cadenaDeTexto)
{
	$buscar=array(chr(13).chr(10), "\r\n", "\n", "\r");
	$reemplazar=array(" ", " ", " ", " ");
	$cadena=str_ireplace($buscar,$reemplazar,$cadenaDeTexto);

	return $cadena;
}

//función para enviar correo de soporte para cuando se produce algún error en el sistema
//ACT 09-04-18: Además, aquí guardaremos el error en tabla errors
function enviarMailSoporte($e)
{
    $mail = 'soporte@itappsolutions.com';

    if (isset(Auth::user()->name))
    {
    	$name = Auth::user()->name.' '.Auth::user()->surnames;
    	$user_mail = Auth::user()->email;
    	$user_id = Auth::user()->id;
    }
    else
    {
    	$name = "No identificado";
    	$user_mail = "No identificado";
    	$user_id = NULL;
    }

    //OBS: Deberia agregar también organización (o súper organización) para saber de que empresa se está enviando. Para esto se debe agregar en BBDD algún atributo que identifique la organización (Pendiente Agregado el 02-08-2017)
    //ACT 09-04-18: Por el momento lo anterior no es necesario, ya que se guardará en la bbdd (y cada bd corresponde a una organización)
    \Ermtool\Error::create([
    	'user_id' => $user_id,
    	'description' => $e,
    	'status' => 0,
    	'status2' => 0
    ]);
    
    Mail::send('mail_error',['user' => $name,'user_mail' => $user_mail,'e' => $e], function ($message) use ($mail,$name)
    {
    	
        if (Session::get('languaje') == 'en')
        {
            $message->to($mail, $name)->subject('Error from B-GRC');
        }
        else
        {
            $message->to($mail, $name)->subject('Se produjo un error en B-GRC');
        }
    });
}

//Función que divide un nombre completo en nombres y apellido paterno y materno //24-10-2017: MEJORAR!!
function getNombreSplit($nombreCompleto, $apellido_primero = false)
{
    $chunks = ($apellido_primero)
        ? explode(" ", strtoupper($nombreCompleto))
        : array_reverse(explode(" ", strtoupper($nombreCompleto)));
    $exceptions = ["DE", "LA", "DEL", "LOS", "SAN", "SANTA"];
    $existen = array_intersect($chunks, $exceptions);
    $nombre = array( "Materno" => "", "Paterno" => "", "Nombres" => "" );
    $agregar_en = ($apellido_primero)
        ? "paterno"
        : "materno";
    $primera_vez = true;
    if($apellido_primero){
        if(!empty($existen)){
            foreach ($chunks as $chunk) {
                if($primera_vez){
                    $nombre["Paterno"] = $nombre["Paterno"] . " " . $chunk;
                    $primera_vez = false;
                }else{
                    if(in_array($chunk, $exceptions)){
                        if($agregar_en == "paterno")
                            $nombre["Paterno"] = $nombre["Paterno"] . " " . $chunk;
                        elseif($agregar_en == "materno")
                            $nombre["Materno"] = $nombre["Materno"] . " " . $chunk;
                        else
                            $nombre["Nombres"] = $nombre["Nombres"] . " " . $chunk;
                    }else{
                        if($agregar_en == "paterno"){
                            $nombre["Paterno"] = $nombre["Paterno"] . " " . $chunk;
                            $agregar_en = "materno";
                        }elseif($agregar_en == "materno"){
                            $nombre["Materno"] = $nombre["Materno"] . " " . $chunk;
                            $agregar_en = "nombres";
                        }else{
                            $nombre["Nombres"] = $nombre["Nombres"] . " " . $chunk;
                        }
                    }
                }
            }
        }else{
            foreach ($chunks as $chunk) {
                if($primera_vez){
                    $nombre["Paterno"] = $nombre["Paterno"] . " " . $chunk;
                    $primera_vez = false;
                }else{
                    if(in_array($chunk, $exceptions)){
                        if($agregar_en == "paterno")
                            $nombre["Paterno"] = $nombre["Paterno"] . " " . $chunk;
                        elseif($agregar_en == "materno")
                            $nombre["Materno"] = $nombre["Materno"] . " " . $chunk;
                        else
                            $nombre["Nombres"] = $nombre["Nombres"] . " " . $chunk;
                    }else{
                        if($agregar_en == "paterno"){
                            $nombre["Materno"] = $nombre["Materno"] . " " . $chunk;
                            $agregar_en = "materno";
                        }elseif($agregar_en == "materno"){
                            $nombre["Nombres"] = $nombre["Nombres"] . " " . $chunk;
                            $agregar_en = "nombres";
                        }else{
                            $nombre["Nombres"] = $nombre["Nombres"] . " " . $chunk;
                        }
                    }
                }
            }
        }
    }else{
        foreach($chunks as $chunk){
            if($primera_vez){
                $nombre["Materno"] = $chunk . " " . $nombre["Materno"];
                $primera_vez = false;
            }else{
                if(in_array($chunk, $exceptions)){
                    if($agregar_en == "materno")
                        $nombre["Materno"] = $chunk . " " . $nombre["Materno"];
                    elseif($agregar_en == "paterno")
                        $nombre["Paterno"] = $chunk . " " . $nombre["Paterno"];
                    else
                        $nombre["Nombres"] = $chunk . " " . $nombre["Nombres"];
                }else{
                    if($agregar_en == "materno"){
                        $agregar_en = "paterno";
                        $nombre["Paterno"] = $chunk . " " . $nombre["Paterno"];
                    }elseif($agregar_en == "paterno"){
                        $agregar_en = "nombres";
                        $nombre["Nombres"] = $chunk . " " . $nombre["Nombres"];
                    }else{
                        $nombre["Nombres"] = $chunk . " " . $nombre["Nombres"];
                    }
                }
            }
        }
    }
    // LIMPIEZA DE ESPACIOS
    $nombre["Materno"] = trim($nombre["Materno"]);
    $nombre["Paterno"] = trim($nombre["Paterno"]);
    $nombre["Nombres"] = trim($nombre["Nombres"]);
    return $nombre;
}

function locked()
{
        if (Session::get('languaje') == 'en')
        {
            Session::flash('error','You don\'t have permission to access to this module');
        }
        else
        {
            Session::flash('error','Usted no tiene los permisos para acceder a este módulo');
        }

        return view('locked');
}

function sendAlertMail($message,$stakeholder_mail,$name,$user_mail,$subject)
{
	Mail::queue('envio_mail3',['mensaje' => $message], function ($msj) use ($stakeholder_mail,$name,$user_mail,$subject)
    {   
		$msj->to($stakeholder_mail, $name)->bcc($user_mail)->subject($subject);
    });
}
?>