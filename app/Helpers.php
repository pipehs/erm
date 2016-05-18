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
	$uri = array('crear_encuesta','enviar_encuesta','encuestas');

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

//dropdown de reportes básicos
function dropDown4()
{
	$uri = array('heatmap','matrices','matriz_riesgos','reporte_planes','hallazgos');

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

    if (isset($file[1])) //si es que existe file[1], el archivo tenía extensión; por el contrario no tiene extensión
    {
	    $guardado = Storage::put(
	        $dir.'/'. $file[0] . "___" . $id . "." . $file[1],
	        file_get_contents($archivo->getRealPath())
	    );
	}
	else
	{
		$guardado = Storage::put(
	        $dir.'/'. $file[0] . "___" . $id ,
	        file_get_contents($archivo->getRealPath())
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
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_notas';
        }
        else if ($kind == 1) //se están solicitando respuestas a evidencias de una nota
        {
            //seleccionamos carpeta de respuestas evidencias
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_resp_notas';
        }
        else if ($kind == 2) //se están solicitando evidencias de un hallazgo
        {
            $carpeta = 'C:\virtualhost\erm\storage\app\evidencias_hallazgos';
        }
        else if ($kind == 3) //solicitando evidencias de una evaluación de control
        {
        	$carpeta = 'C:\virtualhost\erm\storage\app\eval_controles';
        }

        if (file_exists($carpeta) != false) //verificamos que exista la carpeta
        {
            $archivos = scandir($carpeta);

            foreach ($archivos as $archivo)
            {    
                    //dividimos archivos para buscar id
                    if (strpos($archivo,'___'))
                    {   
                        $j = 0;
                        $temp = explode('___',$archivo);

                        //sacamos extensión del archivo
                        $temp2 = explode('.',$temp[1]);

                        if ($temp2[0] == $id)
                        {
                            $evidences[$j] = [
                                'id' => $id,
                                'url' => $archivo,
                            ];

                            $j += 1;
                        }
                    }
                    else
                        $evidences = NULL;                      
            }
        }
        else
        {
            $evidences = NULL;
        }

        return $evidences;
}

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

		if (!$risks) //entonces es un control de riesgo de negocio
		{
			$risks = DB::table('control_objective_risk')
						->join('objective_risk','objective_risk.id','=','control_objective_risk.objective_risk_id')
						->join('risks','risks.id','=','objective_risk.risk_id')
						->where('control_objective_risk.control_id','=',$GLOBALS['id'])
						->select('objective_risk.id')
						->get();
			$type = 1; //identifica que es un riesgo de negocio

			if (!$risks) //retornamos error ya que hubo un problema para encontrar los riesgos asociados al control
			{
				return 1;
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
								->where('risk_subprocess_id','=',$risk->id)
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

					if ($evals) //segunda verificación de que la evaluación inherente exista, puede haberse guardado mal (como NULL en valores de avg_probability y/o avg_impact)
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
							DB::table('evaluation_risk')
								->insertGetId([
									'evaluation_id' => $evaluation->id,
									'risk_subprocess_id' => $risk->id,
									'avg_probability' => $val->eval_ctrl_risk,
									'avg_impact' => $evals->avg_impact,
									]);
						}
						else if ($type == 1) //objective_risk_id
						{
							DB::table('evaluation_risk')
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
						return 1;
					}

			}
			else
			{
				return 1;
			}

			return 0; //fin correcto	
		}
	});
}

?>