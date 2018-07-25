<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Ermtool\Http\Controllers\ControlesController as Controles; //Para poder generar matriz de control y exportarla
use Ermtool\Http\Controllers\RiesgosController as Riesgos; //Para poder generar matriz de riesgo y exportarla
use Ermtool\Http\Controllers\AuditoriasController as Audits;
use Ermtool\Http\Controllers\PlanesAccionController as PlanesAccion;
use Ermtool\Http\Controllers\IssuesController as Issues;
use Ermtool\Http\Controllers\HomeController as Home;
use Auth;
use DB;
use Session;
use Redirect;

class ExcelController extends Controller
{
    public function generarExcel($value,$org,$cat)
    {
        try
        {
            global $id_org;
            if ($org == 0)
            {
                $id_org = NULL;
            }
            else
            {
                $id_org = $org;
            }
            global $id_cat;
            $id_cat = $cat;
            if ($value == 0) //se genera excel para controles de proceso
            {
                Excel::create('Matriz controles de procesos '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Matriz de controles de procesos');

                    //creador y compañia
                    $excel->setCreator('Administrador ERM')
                          ->setCompany('ERM - IXUS Consulting');

                    //descripción
                    $excel->setDescription('Matriz de controles para riesgos de procesos');

                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->generarMatriz(0,$GLOBALS['id_org']);

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:K1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

                })->export('xls');
            }
            else if ($value == 1) //se genera excel para controles de negocio
            {
                Excel::create('Matriz controles de negocio '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Matriz de controles de negocio');

                    //creador y compañia
                    $excel->setCreator('Administrador ERM')
                          ->setCompany('ERM - IXUS Consulting');

                    //descripción
                    $excel->setDescription('Matriz de controles para riesgos de negocio');

                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->generarMatriz(1,$GLOBALS['id_org']);

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);
                        $sheet->setAutoFilter();

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:K1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

                })->export('xls');
            }
            //ACT 03-01-18: Matriz de Riesgos genéricos
            else if ($value == 2) 
            {
                Excel::create('Matriz de riesgos '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Matriz de riesgos');

                    //creador y compañia
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('IT Apps');

                    //descripción
                    $excel->setDescription('Matriz de riesgos');

                    $excel->sheet('Riesgos', function($sheet) {
                        $riesgo = new Riesgos;
                        $datos = $riesgo->generarMatriz(0,$GLOBALS['id_org'],$GLOBALS['id_cat']);

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:O1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

                })->export('xls');
            }

            else if ($value == 3) //se genera excel para riesgos de proceso
            {
                Excel::create('Matriz de riesgos de proceso '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Matriz de riesgos de proceso');

                    //creador y compañia
                    $excel->setCreator('Administrador ERM')
                          ->setCompany('ERM - IXUS Consulting');

                    //descripción
                    $excel->setDescription('Matriz de riesgos de proceso');

                    $excel->sheet('Riesgos', function($sheet) {
                        $riesgo = new Riesgos;
                        $datos = $riesgo->generarMatriz(0,$GLOBALS['id_org'],$GLOBALS['id_cat']);

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:N1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

                })->export('xls');
            }
            else if ($value == 4) //se genera excel para riesgos de negocio
            {
                Excel::create('Matriz de riesgos de negocio '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Matriz de riesgos de negocio');

                    //creador y compañia
                    $excel->setCreator('Administrador ERM')
                          ->setCompany('ERM - IXUS Consulting');

                    //descripción
                    $excel->setDescription('Matriz de riesgos de negocio');

                    $excel->sheet('Riesgos', function($sheet) {
                        $riesgo = new Riesgos;
                        $datos = $riesgo->generarMatriz(1,$GLOBALS['id_org'],$GLOBALS['id_cat']);

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:N1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

                })->export('xls');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarExcelPlan($org)
    {
        try
        {
            //generamos variable global para usarla en la función excel
            global $id;

            $id = $org;

            Excel::create('Reporte de planes de acción '.date("d-m-Y"), function($excel) {

                // título excel
                $excel->setTitle('Planes de acción');

                //creador y compañia
                $excel->setCreator('Administrador B-GRC')
                      ->setCompany('B-GRC - IXUS Consulting');

                //descripción
                $excel->setDescription('Reporte con planes de acción para la organización seleccionada');

                $excel->sheet('Planes', function($sheet) {
                    $planes = new PlanesAccion;
                    $datos = $planes->generarReportePlanes($GLOBALS['id']);

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);

                    //editamos formato de salida de celdas
                    $sheet->cells('A1:G1', function($cells) {
                            $cells->setBackground('#013ADF');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(16);
                    });

                    $sheet->freezeFirstRow();

                });

            })->export('xls');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarExcelIssue($type,$org)
    {
        try
        {
            //generamos variable global para usarla en la función excel
            global $id;
            global $org2;
            $id = $type;
            $org2 = $org;

            Excel::create('Reporte de hallazgos '.date("d-m-Y"), function($excel) {

                // título excel
                $excel->setTitle('Hallazgos');

                //creador y compañia
                $excel->setCreator('Administrador B-GRC')
                      ->setCompany('B-GRC - IXUS Consulting');

                //descripción
                $excel->setDescription('Reporte de hallazgos');

                $excel->sheet('Auditorías', function($sheet) {
                    $issue = new Issues;
                    $datos = $issue->generarReporteIssuesExcel($GLOBALS['id'],$GLOBALS['org2']);

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);

                    //editamos formato de salida de celdas
                    $sheet->cells('A1:J1', function($cells) {
                            $cells->setBackground('#013ADF');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(16);
                    });

                    $sheet->freezeFirstRow();

                });

            })->export('xls');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarExcelAudit($org)
    {
        try
        {
            //generamos variable global para usarla en la función excel
            global $org2;
            $org2 = $org;

            Excel::create('Reporte de planes de auditoría '.date("d-m-Y"), function($excel) {

                // título excel
                $excel->setTitle('Planes de auditoría');

                //creador y compañia
                $excel->setCreator('Administrador B-GRC')
                      ->setCompany('B-GRC - IXUS Consulting');

                //descripción
                $excel->setDescription('Reporte de Planes de auditoría');

                $excel->sheet('Auditorías', function($sheet) {
                    $audit = new Audits;
                    $datos = $audit->generarReporteAuditorias($GLOBALS['org2']);

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);

                    //editamos formato de salida de celdas
                    $sheet->cells('A1:L1', function($cells) {
                            $cells->setBackground('#013ADF');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(16);
                    });

                    $sheet->freezeFirstRow();

                });

            })->export('xls');
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function generarExcelGraficos($id,$org)
    {
        try
        {
            //generamos variable global para usarla en la función excel
            /*id identifica el tipo de control gráfico que es
            1 = Controles ejecutados
            2 = Controles pendientes
            3 = Controles efectivos
            4 = Controles inefectivos
            5 = Planes de auditoría abiertos
            6 = Planes de auditoría en ejecución
            7 = Planes de auditoría cerrados
            8 = Planes de acción por evaluación de controles
            9 = Planes de acción por ejecución de auditoría
            10 = Planes - op. de mejora
            11 = Planes - Deficiencia
            12 = Planes - Deb. significativa
            13 = Planes estado - Abierto
            14 = Planes estado - próximos a cerrar
            15 = Planes estado - Fecha final terminada y aun abierto
            16 = Planes estado - Cerrado
            17 = Pruebas de auditoría abiertas
            18 = Pruebas de auditoría en ejecución
            19 = Pruebas de auditoría cerradas
            */
            global $id2;
            $id2 = $id;

            global $org2;
            $org2 = $org;

            if ($GLOBALS['id2'] == 1)
            {
                Excel::create('Reporte Controles ejecutados '.date("d-m-Y"), function($excel) {
                    // título excel
                    $excel->setTitle('Controles ejecutados');

                    //creador y compañia
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    //descripción
                    $excel->setDescription('Reporte de controles ejecutados');
                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:C1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 2)
            {   
                Excel::create('Reporte Controles pendientes '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Controles pendientes');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de controles pendientes');
                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:C1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 3)
            {
                Excel::create('Reporte Controles efectivos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Controles efectivos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de controles efectivos');
                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:C1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 4)
            {
                Excel::create('Reporte Controles inefectivos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Controles inefectivos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de controles inefectivos');
                    $excel->sheet('Controles', function($sheet) {
                        $control = new Controles;
                        $datos = $control->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:C1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 5)
            {
                Excel::create('Reporte Planes de auditoría abiertos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de auditoría abiertos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de auditoría abiertos');
                    $excel->sheet('Planes de auditoría', function($sheet) {
                        $plan = new Audits;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:E1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 6)
            {
                Excel::create('Reporte Planes de auditoría en ejecución '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de auditoría en ejecución');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de auditoría en ejecución');
                    $excel->sheet('Planes de auditoría', function($sheet) {
                        $plan = new Audits;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:E1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 7)
            {
                Excel::create('Reporte Planes de auditoría cerrados '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de auditoría cerrados');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de auditoría cerrados');
                    $excel->sheet('Planes de auditoría', function($sheet) {
                        $plan = new Audits;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:E1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 8)
            {
                Excel::create('Planes de acción en evaluación de controles '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados a través de la evaluación de controles');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 9)
            {
                Excel::create('Planes de acción en auditorías '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados a través de auditorías');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:J1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 10)
            {
                Excel::create('Planes de acción para oportunidades de mejora '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados a través de hallazgos clasificados como oportunidad de mejora');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:I1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 11)
            {
                Excel::create('Planes de acción para deficiencias '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados a través de hallazgos clasificados como deficiencias');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:I1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 12)
            {
                Excel::create('Planes de acción para debilidades significativas '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados a través de hallazgos clasificados como debilidades significativas');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:I1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 13)
            {
                Excel::create('Planes de acción abiertos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción abiertos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción que se encuentran abiertos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 14)
            {
                Excel::create('Planes de acción próximos a cerrar '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción próximos a cerrar');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción en que su fecha límite se encuentra próxima a cumplirse');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 15)
            {
                Excel::create('Planes de acción abiertos con fecha pasada '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción abiertos fecha límite terminada');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción que se encuentran abiertos siendo que la fecha final del mismo se encuentra pasada');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 16)
            {
                Excel::create('Planes de acción cerrados '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción cerrados');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción cerrados');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            //ACTUALIZACIÓN 08-02: NUEVOS TIPOS DE PLANES DE ACCIÓN
            else if ($GLOBALS['id2'] == 17)
            {
                Excel::create('Planes de acción para auditorías '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción para auditorías');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción creados directamente para auditorías');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:H1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 18)
            {
                Excel::create('Planes de acción para programas de auditoría '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción programas de auditoría');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción para programas de auditoría');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:I1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 19)
            {
                Excel::create('Planes de acción para organización '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción para organización');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción para organización');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 20)
            {
                Excel::create('Planes de acción para subprocesos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción para subprocesos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción para subprocesos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:H1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 21)
            {
                Excel::create('Planes de acción para procesos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción para procesos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción para procesos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 22)
            {
                Excel::create('Planes de acción controles de procesos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción controles de procesos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes controles de procesos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
            else if ($GLOBALS['id2'] == 23)
            {
                Excel::create('Planes de acción controles de negocio '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción controles de negocio');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción controles de negocio');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            //ACT 12-03-18: Gráficos para planes asociados a riesgo, compliance o canal de denuncia
            else if ($GLOBALS['id2'] == 24)
            {
                Excel::create('Planes de acción riesgos '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción Riesgos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción asociados a Riesgos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            else if ($GLOBALS['id2'] == 25)
            {
                Excel::create('Planes de acción compliance '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción Compliance');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción asociados a Compliance');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            else if ($GLOBALS['id2'] == 26)
            {
                Excel::create('Planes de acción Canal de denuncia '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Planes de acción Riesgos');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de planes de acción asociados a Riesgos');
                    $excel->sheet('Planes de acción', function($sheet) {
                        $plan = new PlanesAccion;
                        $datos = $plan->indexGraficos2($GLOBALS['id2'],$GLOBALS['org2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:G1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    //graficos de excel dinámicos (en una primera instancia, solo para el gráfico de pruebas de auditoría)
    public function generarExcelGraficosDinamicos($kind,$id)
    {
        try
        {
            global $id2;
            $id2 = $id;
            global $kind2;
            $kind2 = $kind;

            if ($GLOBALS['kind2'] == 1)
            {
                Excel::create('Pruebas de auditoría abiertas '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Pruebas de auditoría abiertas');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de pruebas de auditoría que se encuentran abiertas para el plan seleccionado');
                    $excel->sheet('Pruebas de auditoría', function($sheet) {
                        $audit = new Audits;
                        $datos = $audit->getTests($GLOBALS['kind2'],$GLOBALS['id2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:J1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            else if ($GLOBALS['kind2'] == 2)
            {
                Excel::create('Pruebas de auditoría en ejecución '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Pruebas de auditoría en ejecución');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de pruebas de auditoría que se encuentran en ejecución para el plan seleccionado');
                    $excel->sheet('Pruebas de auditoría', function($sheet) {
                        $audit = new Audits;
                        $datos = $audit->getTests($GLOBALS['kind2'],$GLOBALS['id2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:J1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }

            if ($GLOBALS['kind2'] == 3)
            {
                Excel::create('Pruebas de auditoría cerradas '.date("d-m-Y"), function($excel) {

                    $excel->setTitle('Pruebas de auditoría cerradas');
                    $excel->setCreator('Administrador B-GRC')
                          ->setCompany('B-GRC - IXUS Consulting');
                    $excel->setDescription('Reporte de pruebas de auditoría que se encuentran cerradas para el plan seleccionado');
                    $excel->sheet('Pruebas de auditoría', function($sheet) {
                        $audit = new Audits;
                        $datos = $audit->getTests($GLOBALS['kind2'],$GLOBALS['id2']);
                        $sheet->fromArray($datos);
                        $sheet->cells('A1:J1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();
                    });

                })->export('xls');
            }
        }
        catch (\Exception $e)
        {
            enviarMailSoporte($e);
            return view('errors.query',['e' => $e]);
        }
    }

    public function importarIndex()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
        {
            return Redirect::to('home');
        }
        else
        {
            return view('importador');
        }
    }



    //Función donde se realizará la importación de datos de excel a base de datos
    public function importarExcel(Request $request)
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else if (!isset(Auth::user()->superadmin) || Auth::user()->superadmin != 1)
        {
            return Redirect::to('home');
        }
        else
        {
            global $request2;
            $request2 = $request;

            DB::transaction(function(){
                if($GLOBALS['request2']->file('document') != NULL)
                {
                    Excel::load($GLOBALS['request2']->file('document'), function($reader) {
                        //$i = 1; //contador para nombres de acciones mitigantes (KOAndina)    
                        //recorremos filas
                        $reader->each(function($row,$i) {
                                //print_r($row);

                            //ACT 24-07-18: Ahora estará configurado para Excel de Progreso
                            if ($_POST['kind'] == 0) //excel de usuarios
                            {
                                //Primero que todo, vemos si existe organización asociada y correo correcto, si no no hacemos nada
                                $org = \Ermtool\Organization::where('name',$row['dependencia'])->first(['id']);

                                if (isset($org))
                                {
                                    if (strpos($row['correo'],'@')) //Correo válido (básicamente)
                                    {
                                        //Vemos si rut ya existe
                                        //Primero lo separamos
                                        $rut = explode('-',$row['rut']);

                                        $verrut = \Ermtool\Stakeholder::find($rut[0]);

                                        if (empty($verrut)) //significa que esta vacío y se puede cargar
                                        {
                                            //$row['nombrecompleto'] = ucwords(strtolower($row['nombrecompleto'])); Problema con Ñ
                                            $row['nombre'] = mb_convert_encoding(mb_convert_case($row['nombre'], MB_CASE_TITLE), "UTF-8");  
                                            $nombrecom = explode(' ',$row['nombre']);

                                            if (isset($nombrecom[5])) //4 nombres
                                            {   
                                                //Eliminamos coma separadora de apellidos y nombres
                                                $seclastname = explode(',',$nombrecom[1]);

                                                $nombre = $nombrecom[2].' '.$nombrecom[3].' '.$nombrecom[4].' '.$nombrecom[5];
                                                $apellido = $nombrecom[0].' '.$seclastname[0];

                                            }
                                            else if (isset($nombrecom[4])) //3 nombres
                                            {   
                                                //Eliminamos coma separadora de apellidos y nombres
                                                $seclastname = explode(',',$nombrecom[1]);

                                                $nombre = $nombrecom[2].' '.$nombrecom[3].' '.$nombrecom[4];
                                                $apellido = $nombrecom[0].' '.$seclastname[0];

                                            }
                                            else if (isset($nombrecom[3])) //2 apellidos y 2 nombres
                                            {
                                                //Eliminamos coma separadora de apellidos y nombres
                                                $seclastname = explode(',',$nombrecom[1]);

                                                $nombre = $nombrecom[2].' '.$nombrecom[3];
                                                $apellido = $nombrecom[0].' '.$seclastname[0];
                                            }
                                            else if (isset($nombrecom[2])) //2 apellidos y 1 nombre
                                            {
                                                //Eliminamos coma separadora de apellidos y nombres
                                                $seclastname = explode(',',$nombrecom[1]);

                                                $nombre = $nombrecom[2];
                                                $apellido = $nombrecom[0].' '.$seclastname[0];
                                            }
                                            else if (isset($nombrecom[1])) //1 apellido y 1 nombre
                                            {
                                                //Eliminamos coma separadora de apellidos y nombres
                                                $seclastname = explode(',',$nombrecom[0]);

                                                $nombre = $nombrecom[1];
                                                $apellido = $seclastname[0];
                                            }
                                            else
                                            {
                                                print_r($nombrecom);
                                            }

                                            $usuario = \Ermtool\Stakeholder::create([
                                                'id' => $rut[0],
                                                'dv' => $rut[1],
                                                'name' => $nombre,
                                                'surnames' => $apellido,
                                                'position' => $row['cargo'],
                                                'mail' => $row['correo']
                                            ]);

                                            //agregamos enlace entre usuario y organización
                                            DB::table('organization_stakeholder')->insert([
                                                'organization_id'=>$org->id,
                                                'stakeholder_id'=>$usuario->id
                                            ]);

                                

                                            //Seleccionamos rol (si es que existe)
                                            $role = \Ermtool\Role::getRoleByName($row['tipo_usuario']);

                                            if (empty($role)) //hay que crear el rol
                                            {
                                                $role = \Ermtool\Role::create([
                                                    'name' => $row['tipo_usuario'],
                                                    'status' => 0
                                                ]);
                                            }

                                            //agregamos enlace entre usuario y rol
                                            DB::table('role_stakeholder')->insert([
                                                'stakeholder_id' => $usuario->id,
                                                'role_id' => $role->id
                                            ]);
                                        }
                                        //echo $nombre.' '.$apellido.'<br>';
                                    }
                                }
                            
                            }/*
                            else if ($_POST['kind'] == 1) //Matriz de Riesgos Parque Arauco
                            {
                                //print_r($row);
                                //Cargamos primero Gerencia + División = Organización
                                //Seleccionamos organización (si es que existe)
                                $org = \Ermtool\Organization::getOrgByName($row['gerencia'].' - '.$row['division']);
                                if (empty($org))
                                {
                                    $org = \Ermtool\Organization::create([
                                        'name' => $row['gerencia'].' - '.$row['division'],
                                        'description' => $row['gerencia'].' - '.$row['division']
                                    ]);
                                }

                                //Cargamos categorías de riesgo
                                //nivel 1
                                $risk_category1 = \Ermtool\Risk_category::getRiskCategoryByName($row['nivel_1']);

                                if (empty($risk_category1))
                                {
                                    $risk_category1 = \Ermtool\Risk_category::create([
                                        'name' => $row['nivel_1'],
                                        'description' => $row['nivel_1']
                                    ]);
                                }

                                //nivel 2
                                $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($row['nivel_2']);

                                if (empty($risk_category2))
                                {
                                    $risk_category2 = \Ermtool\Risk_category::create([
                                        'name' => $row['nivel_2'],
                                        'description' => $row['nivel_2'],
                                        'risk_category_id' => $risk_category1->id
                                    ]);
                                }

                                //nivel 3
                                if ($row['nivel_3'] != '' && $row['nivel_3'] != NULL)
                                {
                                    $risk_category3 = \Ermtool\Risk_category::getRiskCategoryByName($row['nivel_3']);

                                    if (empty($risk_category3))
                                    {
                                        $risk_category3 = \Ermtool\Risk_category::create([
                                            'name' => $row['nivel_3'],
                                            'description' => $row['nivel_3'],
                                            'risk_category_id' => $risk_category2->id
                                        ]);
                                    }
                                }
                                else
                                {
                                    $risk_category3 = NULL;
                                }

                                //Cargamos procesos y subprocesos
                                $process = \Ermtool\Process::getProcessByName($row['proceso_afectado'].' - '.$row['proceso_de_negocio']);

                                if (empty($process))
                                {
                                    $process = \Ermtool\Process::create([
                                        'name' => $row['proceso_afectado'].' - '.$row['proceso_de_negocio'],
                                        'description' => $row['proceso_afectado'].' - '.$row['proceso_de_negocio'],
                                    ]);
                                }

                                $subprocess = \Ermtool\Subprocess::create([
                                        'name' => $row['proceso_afectado'].' - '.$row['proceso_de_negocio'],
                                        'description' => $row['proceso_afectado'].' - '.$row['proceso_de_negocio'],
                                        'process_id' => $process->id
                                    ]);

                                //hacemos enlace entre subproceso y organización
                                $org_sub = DB::table('organization_subprocess')
                                    ->insertGetId([
                                        'organization_id' => $org->id,
                                        'subprocess_id' => $subprocess->id,
                                    ]);

                                //cargamos Riesgo
                                if ($row['titulo_del_riesgo'] != '' && $row['titulo_del_riesgo'] != NULL)
                                {
                                    if ($risk_category3 != NULL)
                                    {
                                        $risk = \Ermtool\Risk::create([
                                            'name'=>$row['id_del_riesgo'].' - '.$row['titulo_del_riesgo'],
                                            'description'=>$row['descripcion'],
                                            'type'=>0,
                                            'type2'=>1,
                                            'risk_category_id'=>$risk_category3->id,
                                        ]);
                                    }
                                    else
                                    {
                                        $risk = \Ermtool\Risk::create([
                                            'name'=>$row['id_del_riesgo'].' - '.$row['titulo_del_riesgo'],
                                            'description'=>$row['descripcion'],
                                            'type'=>0,
                                            'type2'=>1,
                                            'risk_category_id'=>$risk_category2->id,
                                        ]);
                                    }

                                    //cargamos causa
                                    if ($row['descripcion_de_la_causa'] != '' && $row['descripcion_de_la_causa'] != NULL)
                                    {
                                        $cause = \Ermtool\Cause::getCauseByName($row['factor_de_riesgo_causa'].' - '.$row['descripcion_de_la_causa']);

                                        if (empty($cause))
                                        {
                                            $cause = \Ermtool\Cause::create([
                                                'name' => $row['factor_de_riesgo_causa'].' - '.$row['descripcion_de_la_causa'],
                                                'description' => $row['factor_de_riesgo_causa'].' - '.$row['descripcion_de_la_causa'],
                                            ]);
                                        }
                                    }
                                    else
                                    {
                                        $cause = \Ermtool\Cause::getCauseByName($row['factor_de_riesgo_causa']);

                                        if (empty($cause))
                                        {
                                            $cause = \Ermtool\Cause::create([
                                                'name' => $row['factor_de_riesgo_causa'],
                                                'description' => $row['factor_de_riesgo_causa'],
                                            ]);
                                        }
                                    }

                                    //enlace entre riesgo y causa
                                    DB::table('cause_risk')
                                        ->insert([
                                            'risk_id' => $risk->id,
                                            'cause_id' => $cause->id,
                                        ]);

                                    //enlace entre riesgo y organización
                                    \Ermtool\Risk::insertOrganizationRisk($org->id,$risk->id,NULL);

                                    $org_risk = DB::table('organization_risk')
                                                ->where('organization_id','=',$org->id)
                                                ->where('risk_id','=',$risk->id)
                                                ->select('id')
                                                ->first();

                                    //enlace entre riesgo y subproceso
                                    DB::table('risk_subprocess')
                                        ->insert([
                                            'risk_id' => $risk->id,
                                            'subprocess_id' => $subprocess->id,
                                        ]);
                                    
                                    //agregamos evaluación del riesgo
                                    $eval = DB::table('evaluations')
                                            ->where('id','=',1)
                                            ->select('id')
                                            ->first();

                                    if (empty($eval))
                                    {
                                        $eval_id = DB::table('evaluations')->insertGetId([
                                            'name' => 'Evaluación Manual',
                                            'consolidation' => 1,
                                            'description' => 'Evaluación Manual',
                                            'created_at' => date('Y-m-d H:i:s'),
                                            'updated_at' => date('Y-m-d H:i:s'),
                                        ]);
                                    }

                                    $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                        'evaluation_id' => 1,
                                        'organization_risk_id' => $org_risk->id,
                                        'avg_probability' => $row['p'],
                                        'avg_impact' => $row['i']
                                        ]);

                                    //insertamos en evaluation_risk_stakeholder
                                    DB::table('evaluation_risk_stakeholder')->insert([
                                        'evaluation_risk_id'=>$evaluation_risk,
                                        'stakeholder_id'=>NULL,
                                        'probability'=>$row['p'],
                                        'impact'=>$row['i'],
                                    ]);
                                }    
                            }*/
                            else if ($_POST['kind'] == 1) //Organizaciones Plantilla genérica
                            {
                                //print_r($row);
                                foreach ($row as $row)
                                {
                                    if (isset($row['nombre']) && $row['nombre'] != '' && $row['nombre'] != NULL)
                                    {
                                        //Seleccionamos organización (si es que existe)
                                        $org = \Ermtool\Organization::getOrgByName($row['nombre']);

                                        if (empty($org)) //hay que crear la organización
                                        {
                                            //seteamos servicios compartidos
                                            if ($row['es_una_organizacion_de_servicios_compartidos'] == 'Si')
                                            {
                                                $shared_services = 1;
                                            }
                                            else
                                            {
                                                $shared_services = 0;
                                            }

                                            //vemos si existe dependencia con otra organización
                                            if ($row['depende_de_otra_organizacion_especificar_organizacion'] != '' && $row['depende_de_otra_organizacion_especificar_organizacion'] != NULL)
                                            {
                                                $org_primary = \Ermtool\Organization::getOrgByName($row['depende_de_otra_organizacion_especificar_organizacion']);

                                                if (empty($org_primary))
                                                {
                                                    $org_primary = NULL;
                                                }
                                                else
                                                {
                                                    $org_primary = $org_primary->id;
                                                }
                                            }
                                            else
                                            {
                                                $org_primary = NULL;
                                            }

                                            $org = \Ermtool\Organization::create([
                                                'name' => $row['nombre'],
                                                'description' => $row['descripcion'],
                                                'expiration_date' => $row['fecha_expiracion'],
                                                'shared_services' => $shared_services,
                                                'organization_id' => $org_primary,
                                                'mision' => $row['mision'],
                                                'vision' => $row['vision'],
                                                'target_client' => $row['cliente_objetivo'],
                                            ]);
                                            
                                        }
                                    }
                                }
                            }

                            else if ($_POST['kind'] == 2) //Categorías de Riesgos Plantilla genérica
                            {
                                //print_r($row);
                                
                                if (isset($row['nombre']) && $row['nombre'] != '' && $row['nombre'] != NULL)
                                {
                                    //Seleccionamos categoría (si es que existe)
                                    $cat = \Ermtool\Risk_category::getRiskCategoryByName($row['nombre']);

                                    if (empty($cat)) //hay que crear la categoría
                                    {
                                        //vemos si existe dependencia con otra organización
                                        if ($row['depende_de_otra_categoria'] != '' && $row['depende_de_otra_categoria'] != NULL)
                                        {
                                            $cat_primary = \Ermtool\Risk_category::getRiskCategoryByName($row['depende_de_otra_categoria']);

                                            if (empty($cat_primary))
                                            {
                                                $cat_primary = NULL;
                                            }
                                            else
                                            {
                                                $cat_primary = $cat_primary->id;
                                            }
                                        }
                                        else
                                        {
                                            $cat_primary = NULL;
                                        }

                                        $category = \Ermtool\Risk_category::create([
                                            'name' => $row['nombre'],
                                            'description' => $row['descripcion'],
                                            'expiration_date' => $row['fecha_expiracion'],
                                            'risk_category_id' => $cat_primary,
                                        ]);
                                            
                                    }
                                }
                            }
                            else if ($_POST['kind'] == 3) //Procesos / Subprocesos (PArauco)
                            {
                                //print_r($row);
                                //Clasificación = MacroProceso
                                $process = \Ermtool\Process::getProcessByName($row['id_clasif'].' - '.$row['clasificacion']);

                                if (empty($process)) //creamos proceso
                                {
                                    $process = \Ermtool\Process::create([
                                        'name' => $row['id_clasif'].' - '.$row['clasificacion'],
                                        'description' => $row['clasificacion'],
                                    ]);
                                }

                                //MacroProceso = Proceso
                                $process2 = \Ermtool\Process::getProcessByName($row['id_p'].' - '.$row['mp']);

                                if (empty($process2)) //creamos proceso
                                {
                                    $process2 = \Ermtool\Process::create([
                                        'name' => $row['id_p'].' - '.$row['mp'],
                                        'description' => $row['mp'],
                                        'process_id' => $process->id
                                    ]);
                                }

                                //Proceso = MacroSubproceso
                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['referencia_hasta_proceso'].' - '.$row['pr']);

                                if (empty($subprocess)) //creamos macrosubproceso
                                {
                                    $subprocess = \Ermtool\Subprocess::create([
                                        'name' => $row['referencia_hasta_proceso'].' - '.$row['pr'],
                                        'description' => $row['pr'],
                                        'process_id' => $process2->id
                                    ]);
                                }

                                //Subproceso = Subproceso
                                $subprocess2 = \Ermtool\Subprocess::getSubprocessByName($row['codigo'].' - '.$row['sp']);

                                if (empty($subprocess2)) //creamos subproceso
                                {
                                    if ($row['sistemas_plataformas'] != '' && $row['sistemas_plataformas'] != NULL)
                                    {
                                        $systems = $row['sistemas_plataformas'];
                                    }
                                    else
                                    {
                                        $systems = NULL;
                                    }

                                    if ($row['habeas_data'] != '' && $row['habeas_data'] != NULL)
                                    {
                                        $habeas_data = $row['habeas_data'];
                                    }
                                    else
                                    {
                                        $habeas_data = NULL;
                                    }

                                    if ($row['marco_normativo'] != '' && $row['marco_normativo'] != NULL)
                                    {
                                        $regulatory_framework = $row['marco_normativo'];
                                    }
                                    else
                                    {
                                        $regulatory_framework = NULL;
                                    }

                                    $subprocess2 = \Ermtool\Subprocess::create([
                                        'name' => $row['codigo'].' - '.$row['sp'],
                                        'description' => $row['sp'],
                                        'process_id' => $process2->id,
                                        'subprocess_id' => $subprocess->id,
                                        'sistemas_plataformas' => $systems,
                                        'habeas_data' => $habeas_data,
                                        'regulatory_framework' => $regulatory_framework
                                    ]);
                                }

                                //Separamos organizaciones por coma, y enlazamos subproceso a organización
                                $orgs = explode(', ',$row['organizacion']);

                                foreach ($orgs as $org)
                                {
                                    //obtenemos org por nombre
                                    $o = \Ermtool\Organization::getOrgByName($org);

                                    DB::table('organization_subprocess')
                                        ->insert([
                                            'organization_id' => $o->id,
                                            'subprocess_id' => $subprocess2->id
                                        ]);
                                }
                            }
                            else if ($_POST['kind'] == 4) //Usuarios (KOAndina)
                            {
                                //print_r($row);
                                $dv = null;
                                //Configuramos Rut extranjero
                                if ($row['organizaciones'] != 'Embotelladora Andina S.A')
                                {
                                    //eliminamos posibles guiones
                                    $row['rut'] = str_replace('-','',$row['rut']);
                                    //eliminamos posibles puntos
                                    $row['rut'] = str_replace('.','',$row['rut']);

                                    if ($row['rut'] >= 2147483647)
                                    {
                                        //realizaremos división y guardamos entero
                                        $id = $row['rut'] / 100;
                                        $id = (int)$id;

                                        //ahora guardamos resto (utilizamos función substr por si resto parte con 0)
                                        $id2 = (string)$row['rut'];
                                        $id2 = substr($id2, -2);
                                        $dv = null;
                                    }
                                    else
                                    {
                                        $id = $row['rut'];
                                        $id2 = null;
                                        $dv = null;
                                    }
                                }
                                else //configuramos rut chileno
                                {
                                    //eliminamos posibles puntos
                                    $row['rut'] = str_replace('.','',$row['rut']);
                                    //separamos rut de dv
                                    $id_temp = explode('-', $row['rut']);
                                    $id = $id_temp[0];
                                    $dv = $id_temp[1];
                                }

                                //verificamos que no exista el rol
                                $role = \Ermtool\Role::getRoleByName($row['tipo_rol_en_la_empresa']);

                                if (empty($role)) //hay que crear el rol
                                {
                                    $role = \Ermtool\Role::create([
                                        'name' => $row['tipo_rol_en_la_empresa'],
                                        'status' => 0
                                    ]);
                                }

                                //verificamos si existe usuario
                                $user = \Ermtool\Stakeholder::find($id);

                                if (empty($user))
                                {
                                    //Configuramos nombre
                                    $row['nombre_e_mail'] = mb_convert_encoding(mb_convert_case($row['nombre_e_mail'], MB_CASE_TITLE), "UTF-8");  

                                    $nombrecom = explode(' ',$row['nombre_e_mail']);

                                    $nombre = $nombrecom[0];
                                    $apellido = $nombrecom[1];

                                    if (isset($id2))
                                    {
                                        $user = \Ermtool\Stakeholder::create([
                                            'id' => $id,
                                            'name' => $nombre,
                                            'surnames' => $apellido,
                                            'position' => $row['cargo'],
                                            'mail' => $row['e_mail'],
                                            'rest_id' => $id2
                                        ]);
                                    }
                                    else
                                    {
                                        $user = \Ermtool\Stakeholder::create([
                                            'id' => $id,
                                            'dv' => $dv,
                                            'name' => $nombre,
                                            'surnames' => $apellido,
                                            'position' => $row['cargo'],
                                            'mail' => $row['e_mail']
                                        ]);
                                    }

                                    //creamos enlace entre organización y usuario
                                    //Seleccionamos organización (si es que existe)
                                    $org = \Ermtool\Organization::getOrgByName($row['organizaciones']);

                                    if (empty($org)) //hay que crear la organización
                                    {
                                        $org = \Ermtool\Organization::create([
                                            'name' => $row['organizaciones'],
                                            'description' => $row['organizaciones']
                                        ]);
                                    }

                                    //agregamos enlace entre usuario y organización
                                    DB::table('organization_stakeholder')->insert([
                                        'organization_id'=>$org->id,
                                        'stakeholder_id'=>$user->id
                                    ]);
                                }
                                else
                                {
                                    //cambiamos nombre y apellido
                                    //Configuramos nombre
                                    $row['nombre_e_mail'] = mb_convert_encoding(mb_convert_case($row['nombre_e_mail'], MB_CASE_TITLE), "UTF-8");  

                                    $nombrecom = explode(' ',$row['nombre_e_mail']);

                                    $user->name = $nombrecom[0];
                                    $user->surnames = $nombrecom[1];

                                    $user->save();
                                }

                                //verificamos si existe enlace entre usuario y rol
                                $user_role = DB::table('role_stakeholder')
                                            ->where('stakeholder_id','=',$user->id)
                                            ->where('role_id','=',$role->id)
                                            ->select('id')
                                            ->get();

                                if (empty($user_role)) //si no existe el enalce, lo agregamos
                                {
                                    //agregamos enlace entre usuario y rol
                                    DB::table('role_stakeholder')->insert([
                                        'stakeholder_id' => $user->id,
                                        'role_id' => $role->id
                                    ]);
                                }
                            }
                            else if ($_POST['kind'] == 5) //Plantillas Riesgos (KOAndina)
                            {
                                //print_r($row);

                                //Primero que todo, si no existe el proceso, no se agrega nada:
                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['proceso_referido_al_riesgo']);

                                if (!empty($subprocess))
                                {
                                    //verificamos que exista org
                                    if ($row['organizacion'] != '' && $row['organizacion'] != NULL)
                                    {
                                        //primero verificamos que el riesgo no exista previamente
                                        //obtenemos org id
                                        $org = \Ermtool\Organization::getOrgByName($row['organizacion']);

                                        if (empty($org)) //hay que crear la organización
                                        {
                                                $org = \Ermtool\Organization::create([
                                                    'name' => $row['organizacion'],
                                                    'description' => $row['organizacion']
                                                ]);
                                        }

                                        //seteamos descripción de riesgo, tanto para obtener uno existente, como para guardar uno nuevo si no existe
                                        if ($row['descripcion_riesgo'] == '' || $row['descripcion_riesgo'] == NULL)
                                        {
                                            $description = $row['riesgo_especifico'];
                                        }
                                        else
                                        {
                                            $description = $row['riesgo_especifico'].' - '.$row['descripcion_riesgo'];
                                        }

                                        $description = eliminarSaltos($description);

                                        $risk = \Ermtool\Risk::getRiskByNameAndDescription($row['tema'],$description,$org->id);

                                        if (empty($risk))
                                        {
                                            //identificamos categorías de Riesgo
                                            //nivel 1
                                            $risk_category1 = \Ermtool\Risk_category::getRiskCategoryByName($row['categoria']);

                                            if (empty($risk_category1))
                                            {
                                                $risk_category1 = \Ermtool\Risk_category::create([
                                                    'name' => $row['categoria'],
                                                    'description' => $row['categoria']
                                                ]);
                                            }

                                            //nivel 2
                                            //configuramos: Contratistas Operacional, Contratistas Cumplimiento, (no se aun como se hará)
                                            /*if ($risk_category1 == 'Operacional' && $row['riesgo_ppal'] == 'Contratistas')
                                            {
                                                $rc = 'Contratistas Operacional';
                                            }
                                            $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($rc);
                                            */

                                            $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($row['riesgo_ppal']);

                                            if (empty($risk_category2))
                                            {
                                                $risk_category2 = \Ermtool\Risk_category::create([
                                                    'name' => $row['riesgo_ppal'],
                                                    'description' => $row['riesgo_ppal'],
                                                    'risk_category_id' => $risk_category1->id
                                                ]);
                                            }

                                            //identificamos usuario por correo
                                            $row['e_mail'] = strtolower($row['e_mail']);
                                            $user = \Ermtool\Stakeholder::getUserByMail($row['e_mail']);

                                            if (empty($user))
                                            {
                                                $user = NULL;
                                            }
                                            else
                                            {   
                                                $user = $user->id;
                                            }

                                            
                                            //creamos riesgo
                                            $risk = \Ermtool\Risk::create([
                                                'name'=>$row['tema'],
                                                'description'=>$description,
                                                'type'=> 0,
                                                'type2'=> 1,
                                                'risk_category_id'=>$risk_category2->id
                                            ]);

                                            \Ermtool\Risk::insertOrganizationRisk($org->id,$risk->id,$user);

                                            //cargamos enlace entre subproceso y riesgo
                                            $subprocess = \Ermtool\Subprocess::find($subprocess->id);
                                            $subprocess->risks()->attach($risk->id);
                                        }

                                        //agregamos cada una de las evaluaciones
                                        //obtenemos org_risk_id
                                        $org_risk = DB::table('organization_risk')
                                                    ->where('organization_id','=',$org->id)
                                                    ->where('risk_id','=',$risk->id)
                                                    ->select('id')
                                                    ->first();


                                        $fechas = [date('2016-06-01 00:00:00'),date('2017-03-01 00:00:00'),date('2017-06-01 00:00:00'),date('2017-09-01 00:00:00')];

                                        for ($j = 1; $j <= 4; $j++)
                                        {
                                            $fecha = explode('-',$fechas[($j-1)]);
                                            $ano = $fecha[0];
                                            $mes = $fecha[1];
                                            $dia = explode(' ',$fecha[2]);
                                            $dia = $dia[0];

                                            if ($row['probabilidad_'.$j] != '' && $row['probabilidad_'.$j] != NULL && $row['impacto_'.$j] != '' && $row['impacto_'.$j] != NULL)
                                            {
                                                //primero creamos evaluación manual
                                                //seleccionamos evaluación (si es que existe)
                                                $eval_id1 = DB::table('evaluations')
                                                            ->where('created_at','=',$fechas[($j-1)])
                                                            ->select('id')
                                                            ->first();

                                                if (empty($eval_id1))
                                                {
                                                    $eval_id1 = DB::table('evaluations')->insertGetId([
                                                        'name' => 'Evaluación Manual',
                                                        'consolidation' => 1,
                                                        'description' => 'Evaluación Manual',
                                                        'created_at' => $fechas[($j-1)],
                                                        'updated_at' => $fechas[($j-1)],
                                                    ]);
                                                }
                                                else
                                                {
                                                    $eval_id1 = $eval_id1->id;
                                                }

                                                //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                                $evaluation_risk = DB::table('evaluation_risk')
                                                                ->where('evaluation_id','=',$eval_id1)
                                                                ->where('organization_risk_id','=',$org_risk->id)
                                                                ->select('id')
                                                                ->first();

                                                if (empty($evaluation_risk))
                                                {
                                                    //insertamos riesgo evaluation_risk
                                                    $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                            'evaluation_id' => $eval_id1,
                                                            'organization_risk_id' => $org_risk->id,
                                                            'avg_probability' => $row['probabilidad_'.$j],
                                                            'avg_impact' => $row['impacto_'.$j]
                                                        ]);
                                                }
                                                else
                                                {
                                                    $evaluation_risk = $evaluation_risk->id;
                                                }
                                                
                                                //vemos si existe en evaluation_risk_stakeholder
                                                $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                                ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                                ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                                ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                                ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                                ->select('evaluation_risk_stakeholder.id')
                                                                ->first();

                                                if (empty($evaluation_risk_stakehoder))
                                                {
                                                    //insertamos en evaluation_risk_stakeholder
                                                    DB::table('evaluation_risk_stakeholder')->insert([
                                                        'evaluation_risk_id'=>$evaluation_risk,
                                                        'user_id'=>Auth::user()->id,
                                                        'probability'=>$row['probabilidad_'.$j],
                                                        'impact'=>$row['impacto_'.$j],
                                                    ]);
                                                }
                                                
                                            }

                                            //Agregamos acciones mitigantes

                                            if ($row['detallar_acciones'] != '' && $row['detallar_acciones'] != NULL)
                                            {
                                                //echo "DETALLAR ACCIONES: ".$row['detallar_acciones'].'<br>';
                                                //$control = \Ermtool\Control::getControlByName('Acción mitigante '.($i+1).' - '.$row['organizacion']);
                                                $description = eliminarSaltos($row['detallar_acciones']);
                                                $control = \Ermtool\Control::getControlByDescription($description);

                                                if (empty($control))
                                                {
                                                    //definimos preventivo, contingencia (correctivo)
                                                    if ($row['preventivo_contingencia'] == 'Preventivo')
                                                    {
                                                        $purpose = 0;
                                                    }
                                                    else if ($row['preventivo_contingencia'] == 'Contingencia')
                                                    {
                                                        $purpose = 2;
                                                    }
                                                    else
                                                    {
                                                        $purpose = NULL;
                                                    }

                                                    //ACT 07-12-17: Agregado para agregar controles que posiblemente no se hayan agregado antes, obtenemos id del último control para ponerle el id más grande
                                                    $newid = DB::table('controls')->max('id');
                                                    $newid = ($newid+1);

                                                    $nombre = 'Acción mitigante '.$newid.' - '.$row['organizacion'];
                                                    //$nombre = 'Acción mitigante '.($i+1).' - '.$row['organizacion'];

                                                    //identificamos usuario por correo
                                                    $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                    $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                    if (empty($user_control))
                                                    {
                                                        $user_control = NULL;
                                                    }
                                                    else
                                                    {   
                                                        $user_control = $user_control->id;
                                                    }
                                                    /*
                                                    if ($row['contribucion_acciones_mitigantes_4'] != '' && $row['contribucion_acciones_mitigantes_4'] != NULL)
                                                    {
                                                        $porcentaje_cont = $row['contribucion_acciones_mitigantes_4']*100;
                                                    }
                                                    else if ($row['contribucion_acciones_mitigantes_3'] != '' && $row['contribucion_acciones_mitigantes_3'] != NULL)
                                                    {
                                                        $porcentaje_cont = $row['contribucion_acciones_mitigantes_3']*100;
                                                    }
                                                    else if ($row['contribucion_acciones_mitigantes_2'] != '' && $row['contribucion_acciones_mitigantes_2'] != NULL)
                                                    {
                                                        $porcentaje_cont = $row['contribucion_acciones_mitigantes_2']*100;
                                                    }
                                                    else if ($row['contribucion_acciones_mitigantes'] != '' && $row['contribucion_acciones_mitigantes'] != NULL)
                                                    {
                                                        $porcentaje_cont = $row['contribucion_acciones_mitigantes']*100;
                                                    }
                                                    else
                                                    {
                                                        $porcentaje_cont = 0;
                                                    }*/

                                                    $porcentaje_cont = $row['contribucion_acciones_mitigantes']*100;

                                                    
                                                    $evidence = eliminarSaltos($row['documentacion_de_respaldo_evidencias_de_auditoria_no_adjuntar_solo_detallar_adecuadamente_para_ser_solicitada_por_auditoria_interna_al_momento_de_auditar']);
                                                    //insertamos control y obtenemos ID
                                                    $control = \Ermtool\Control::create([
                                                            'name'=>$nombre,
                                                            'description'=>$description,
                                                            'type2'=>0,
                                                            'evidence'=>$evidence,
                                                            'purpose'=>$purpose,
                                                            'porcentaje_cont'=> $porcentaje_cont
                                                    ]);

                                                    //insertamos control_organization_risk
                                                    DB::table('control_organization_risk')
                                                    ->insert([
                                                        'organization_risk_id' => $org_risk->id,
                                                        'control_id' => $control->id,
                                                        'stakeholder_id'=>$user_control
                                                    ]);

                                                    //Guardamos en control_eval_risk_temp valor del control (autoevaluación)
                                                    DB::table('control_eval_risk_temp')
                                                        ->insert([
                                                            'result' => $porcentaje_cont,
                                                            'control_id' => $control->id,
                                                            'organization_id' => $org->id,
                                                            'auto_evaluation' => 1,
                                                            'status' => 1,
                                                            'created_at' => $fechas[0]
                                                        ]);

                                                    //agregamos valor residual de riesgo
                                                    $controlclass = new Controles;
                                                    //ahora calculamos riesgo residual para ese riesgo
                                                    //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                                    //ahora calcularemos el valor de el o los riesgos a los que apunte este control
                                                    $controlclass->calcControlledRiskAutoEval($control->id,$org->id,$ano,$mes,$dia);
                                                }
                                                else
                                                {
                                                    $control = \Ermtool\Control::find($control->id);

                                                    //ACT 12-12-17: Vemos si es que existe en control_organization_risk, sino agregamos
                                                    $c_o_r = DB::table('control_organization_risk')
                                                            ->where('organization_risk_id','=',$org_risk->id)
                                                            ->where('control_id','=',$control->id)
                                                            ->get(['id']);

                                                    if (empty($c_o_r))
                                                    {
                                                        $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                        $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                        if (empty($user_control))
                                                        {
                                                            $user_control = NULL;
                                                        }
                                                        else
                                                        {   
                                                            $user_control = $user_control->id;
                                                        }
                                                            
                                                        //insertamos control_organization_risk
                                                        DB::table('control_organization_risk')
                                                        ->insert([
                                                            'organization_risk_id' => $org_risk->id,
                                                            'control_id' => $control->id,
                                                            'stakeholder_id'=>$user_control
                                                        ]);
                                                    }

                                                    if ($j > 1)
                                                    {
                                                        if ($row['contribucion_acciones_mitigantes_'.$j] != '' && $row['contribucion_acciones_mitigantes_'.$j] != NULL)
                                                        {
                                                            $porcentaje_cont = $row['contribucion_acciones_mitigantes_'.$j]*100;

                                                            //si es que ya existe no agregamos nada
                                                            $conteval = DB::table('control_eval_risk_temp')
                                                                    ->where('control_id','=',$control->id)
                                                                    ->where('organization_id','=',$org->id)
                                                                    ->where('created_at','=',$fechas[$j-1])
                                                                    ->select('id')
                                                                    ->get();

                                                            if (empty($conteval))
                                                            {
                                                                //Cambiamos estado de anteriores
                                                                DB::table('control_eval_risk_temp')
                                                                    ->where('control_id','=',$control->id)
                                                                    ->where('organization_id','=',$org->id)
                                                                    ->update(['status' => 0]);

                                                                DB::table('control_eval_risk_temp')
                                                                ->insert([
                                                                    'result' => $porcentaje_cont,
                                                                    'control_id' => $control->id,
                                                                    'organization_id' => $org->id,
                                                                    'auto_evaluation' => 1,
                                                                    'status' => 1,
                                                                    'created_at' => $fechas[$j-1]
                                                                ]);

                                                                //actualizamos porcentaje de cont de control
                                                                $control->porcentaje_cont = $porcentaje_cont;
                                                                $control->save();

                                                                //calculamos nuevo riesgo residual para ese riesgo
                                                                //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                                                //calculamos nuevo valor de el o los riesgos a los que apunte este control
                                                                $controlclass = new Controles;
                                                                $controlclass->calcControlledRiskAutoEval($control->id,$org->id,$ano,$mes,$dia);
                                                            }
                                                            
                                                        }
                                                    }
                                                }

                                                //echo 'CONTROL: '.$control->description.'<br>';
                                            }
                                            
                                        }

                                        //echo $row['plan_accion'].' '.$control->description.'<br><br>';
                                        //ahora vemos si es que se debe cargar plan de acción
                                        if ($row['plan_accion'] == 'si')
                                        {
                                            //vemos si es que ya existe plan de acción
                                            $action_plan = \Ermtool\Action_plan::getActionPlanByDescription($control->description);

                                            if (empty($action_plan))
                                            {
                                                //identificamos nuevamente usuario por correo
                                                $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                if (empty($user_control))
                                                {
                                                    $user_control = NULL;
                                                }
                                                else
                                                {   
                                                    $user_control = $user_control->id;
                                                }
                                                //creamos un issue de control
                                                $issue = DB::table('issues')
                                                    ->insertGetId([
                                                            'name' => 'Falta implementar '.$control->name,
                                                            'description' => 'Falta implementar '.$control->name,
                                                            'recommendations' => 'Implementar '.$control->name,
                                                            'classification' => 1,
                                                            'control_id' => $control->id,
                                                            'created_at' => date('Y-m-d H:i:s'),
                                                            'updated_at' => date('Y-m-d H:i:s'),
                                                        ]);

                                                //agregamos plan de acción
                                                $action_plan = \Ermtool\Action_plan::create([
                                                    'issue_id' => $issue,
                                                    'description' => $control->description,
                                                    'stakeholder_id' => $user_control,
                                                    'final_date' => NULL,
                                                    'status' => 0,
                                                ]);

                                                //ACT 08-12-17: Para agregar en cada issue la organización, obtenemos issue a través de action_plan y actualizamos organization_id
                                                $issue = \Ermtool\Issue::find($action_plan->issue_id);
                                                $issue->organization_id = $org->id;
                                                $issue->save();
                                            }
                                            else
                                            {
                                                $action_plan = \Ermtool\Action_plan::find($action_plan->id);

                                                //ACT 08-12-17: Para agregar en cada issue la organización, obtenemos issue a través de action_plan y actualizamos organization_id
                                                $issue = \Ermtool\Issue::find($action_plan->issue_id);
                                                $issue->organization_id = $org->id;
                                                $issue->save();
                                            }
                                            
                                            //insermos porcentajes de avance
                                            //vemos si ya están seteados los porcentajes de avance para cada avance
                                            $avance1 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2016-11-30 00:00:00'))
                                                    ->where('updated_at','=',date('2016-11-30 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance1))
                                            {
                                                DB::table('progress_percentage')
                                                    ->insert([
                                                        'percentage' => $row['estado_de_avance_0al_31_de_enero_2017']*100,
                                                        'action_plan_id' => $action_plan->id,
                                                        'created_at' => date('2016-11-30 00:00:00'),
                                                        'updated_at' => date('2016-11-30 00:00:00')
                                                    ]);
                                            }
                                            
                                            $avance2 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2017-01-31 00:00:00'))
                                                    ->where('updated_at','=',date('2017-01-31 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance2))
                                            {
                                                DB::table('progress_percentage')
                                                    ->insert([
                                                        'percentage' => $row['estado_de_avance_1al_28_de_febrero_2017']*100,
                                                        'action_plan_id' => $action_plan->id,
                                                        'created_at' => date('2017-01-31 00:00:00'),
                                                        'updated_at' => date('2017-01-31 00:00:00')
                                                    ]);
                                            }

                                            $avance3 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2017-05-31 00:00:00'))
                                                    ->where('updated_at','=',date('2017-05-31 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance3))
                                            {
                                                DB::table('progress_percentage')
                                                    ->insert([
                                                        'percentage' => $row['estado_de_avance_2al_31_de_mayo_2017']*100,
                                                        'action_plan_id' => $action_plan->id,
                                                        'created_at' => date('2017-05-31 00:00:00'),
                                                        'updated_at' => date('2017-05-31 00:00:00')
                                                    ]);
                                            }

                                            $avance4 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2017-08-31 00:00:00'))
                                                    ->where('updated_at','=',date('2017-08-31 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance3))
                                            {
                                                DB::table('progress_percentage')
                                                    ->insert([
                                                        'percentage' => $row['estado_de_avance_3al_31_de_agosto_2017']*100,
                                                        'action_plan_id' => $action_plan->id,
                                                        'created_at' => date('2017-08-31 00:00:00'),
                                                        'updated_at' => date('2017-08-31 00:00:00')
                                                    ]);
                                            }

                                            //identificamos nuevamente usuario por correo
                                            $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                            $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                            if (empty($user_control))
                                            {
                                                $user_control = NULL;
                                            }
                                            else
                                            {   
                                                $user_control = $user_control->id;
                                            }

                                            //echo 'Control: '.$control->description.'<br>';
                                            //echo 'El user que actualmente existe es: '.$action_plan->stakeholder_id.'<br>';
                                            //echo 'e_mail_control es: '.$row['e_mail_control'].'<br><br><br>';
                                            //Validaremos que plan de acción tenga responsable
                                            if (($action_plan->stakeholder_id == NULL || $action_plan->stakeholder_id == '') && $user_control != NULL)
                                            {
                                                //echo 'Vacío: '.$action_plan->stakeholder_id.' y user control: '.$user_control.'<br>';
                                                $action_plan->stakeholder_id = $user_control;
                                                $action_plan->save();
                                            }

                                            //vemos si el porcentaje de avance = 100%
                                            if ($row['estado_de_avance_3al_31_de_agosto_2017'] == 1)
                                            {
                                                $action_plan->status = 1;
                                                $action_plan->save();
                                            }
                                        }
                                        
                                    }
                                    
                                }    
                            }
                            else if ($_POST['kind'] == 6) //Planilla TI KOAndina
                            {
                                //Primero que todo, si no existe el proceso, no se agrega nada:
                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['proceso_referido_al_riesgo']);

                                if (!empty($subprocess))
                                {
                                    //verificamos que exista org
                                    if ($row['organizacion'] != '' && $row['organizacion'] != NULL)
                                    {
                                        //primero verificamos que el riesgo no exista previamente
                                        //obtenemos org id
                                        $org = \Ermtool\Organization::getOrgByName($row['organizacion']);

                                        if (empty($org)) //hay que crear la organización
                                        {
                                                $org = \Ermtool\Organization::create([
                                                    'name' => $row['organizacion'],
                                                    'description' => $row['organizacion']
                                                ]);
                                        }

                                        //seteamos descripción de riesgo, tanto para obtener uno existente, como para guardar uno nuevo si no existe
                                        if ($row['descripcion_riesgo'] == '' || $row['descripcion_riesgo'] == NULL)
                                        {
                                            $description = $row['riesgo_especifico'];
                                        }
                                        else
                                        {
                                            $description = $row['riesgo_especifico'].' - '.$row['descripcion_riesgo'];
                                        }

                                        $description = eliminarSaltos($description);
                                        
                                        $risk = \Ermtool\Risk::getRiskByNameAndDescription($row['tema'],$description,$org->id);

                                        if (empty($risk))
                                        {
                                            //identificamos categorías de Riesgo
                                            //nivel 1
                                            $risk_category1 = \Ermtool\Risk_category::getRiskCategoryByName($row['categoria']);

                                            if (empty($risk_category1))
                                            {
                                                $risk_category1 = \Ermtool\Risk_category::create([
                                                    'name' => $row['categoria'],
                                                    'description' => $row['categoria']
                                                ]);
                                            }

                                            //nivel 2
                                            //configuramos: Contratistas Operacional, Contratistas Cumplimiento, (no se aun como se hará)
                                            /*if ($risk_category1 == 'Operacional' && $row['riesgo_ppal'] == 'Contratistas')
                                            {
                                                $rc = 'Contratistas Operacional';
                                            }
                                            $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($rc);
                                            */

                                            $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($row['riesgo_ppal']);

                                            if (empty($risk_category2))
                                            {
                                                $risk_category2 = \Ermtool\Risk_category::create([
                                                    'name' => $row['riesgo_ppal'],
                                                    'description' => $row['riesgo_ppal'],
                                                    'risk_category_id' => $risk_category1->id
                                                ]);
                                            }

                                            //identificamos usuario por nombre
                                            //$row['e_mail'] = strtolower($row['e_mail']);
                                            if ($row['e_mail'] != '' && $row['e_mail'] != NULL)
                                            {
                                                $user = \Ermtool\Stakeholder::getUserByName($row['e_mail']);
                                            }
                                            else
                                            {
                                                $user = array();
                                            }

                                            if (empty($user))
                                            {
                                                $user = NULL;
                                            }
                                            else
                                            {   
                                                $user = $user->id;
                                            }

                                            //creamos riesgo
                                            $risk = \Ermtool\Risk::create([
                                                'name'=>$row['tema'],
                                                'description'=>$description,
                                                'type'=> 0,
                                                'type2'=> 1,
                                                'risk_category_id'=>$risk_category2->id
                                            ]);

                                            \Ermtool\Risk::insertOrganizationRisk($org->id,$risk->id,$user);

                                            //cargamos enlace entre subproceso y riesgo
                                            $subprocess = \Ermtool\Subprocess::find($subprocess->id);
                                            $subprocess->risks()->attach($risk->id);
                                        }

                                        //agregamos cada una de las evaluaciones
                                        //obtenemos org_risk_id
                                        $org_risk = DB::table('organization_risk')
                                                    ->where('organization_id','=',$org->id)
                                                    ->where('risk_id','=',$risk->id)
                                                    ->select('id')
                                                    ->first();

                                        //La 3ra fecha no debería usarse ya que sólo hay 3 evaluaciones, se agregó al azar para mantener ciclo
                                        $fechas = [date('2016-06-01 00:00:00'),date('2017-06-15 00:00:00'),date('2017-08-15 00:00:00'),date('2017-08-31 00:00:00')];

                                        for ($j = 1; $j <= 4; $j++)
                                        {
                                            if ($j != 3)
                                            {

                                                $fecha = explode('-',$fechas[($j-1)]);
                                                $ano = $fecha[0];
                                                $mes = $fecha[1];
                                                $dia = explode(' ',$fecha[2]);
                                                $dia = $dia[0];

                                                if ($row['probabilidad_'.$j] != '' && $row['probabilidad_'.$j] != NULL && $row['impacto_'.$j] != '' && $row['impacto_'.$j] != NULL)
                                                {
                                                    //primero creamos evaluación manual
                                                    //seleccionamos evaluación (si es que existe)
                                                    $eval_id1 = DB::table('evaluations')
                                                                ->where('created_at','=',$fechas[($j-1)])
                                                                ->select('id')
                                                                ->first();

                                                    if (empty($eval_id1))
                                                    {
                                                        $eval_id1 = DB::table('evaluations')->insertGetId([
                                                            'name' => 'Evaluación Manual',
                                                            'consolidation' => 1,
                                                            'description' => 'Evaluación Manual',
                                                            'created_at' => $fechas[($j-1)],
                                                            'updated_at' => $fechas[($j-1)],
                                                        ]);
                                                    }
                                                    else
                                                    {
                                                        $eval_id1 = $eval_id1->id;
                                                    }

                                                    //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                                    $evaluation_risk = DB::table('evaluation_risk')
                                                                    ->where('evaluation_id','=',$eval_id1)
                                                                    ->where('organization_risk_id','=',$org_risk->id)
                                                                    ->select('id')
                                                                    ->first();

                                                    if (empty($evaluation_risk))
                                                    {
                                                        //insertamos riesgo evaluation_risk
                                                        $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                                'evaluation_id' => $eval_id1,
                                                                'organization_risk_id' => $org_risk->id,
                                                                'avg_probability' => $row['probabilidad_'.$j],
                                                                'avg_impact' => $row['impacto_'.$j]
                                                            ]);
                                                    }
                                                    else
                                                    {
                                                        $evaluation_risk = $evaluation_risk->id;
                                                    }
                                                    
                                                    //vemos si existe en evaluation_risk_stakeholder
                                                    $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                                    ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                                    ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                                    ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                                    ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                                    ->select('evaluation_risk_stakeholder.id')
                                                                    ->first();

                                                    if (empty($evaluation_risk_stake))
                                                    {
                                                        //insertamos en evaluation_risk_stakeholder
                                                        DB::table('evaluation_risk_stakeholder')->insert([
                                                            'evaluation_risk_id'=>$evaluation_risk,
                                                            'user_id'=>Auth::user()->id,
                                                            'probability'=>$row['probabilidad_'.$j],
                                                            'impact'=>$row['impacto_'.$j],
                                                        ]);
                                                    }
                                                }

                                                //Agregamos acciones mitigantes

                                                if ($row['detallar_acciones'] != '' && $row['detallar_acciones'] != NULL)
                                                {
                                                    $description = eliminarSaltos($row['detallar_acciones']);
                                                    $control = \Ermtool\Control::getControlByDescription($description);

                                                    if (empty($control))
                                                    {
                                                        //definimos preventivo, contingencia (correctivo)
                                                        if ($row['preventivo_contingencia'] == 'Preventivo')
                                                        {
                                                            $purpose = 0;
                                                        }
                                                        else if ($row['preventivo_contingencia'] == 'Contingencia')
                                                        {
                                                            $purpose = 2;
                                                        }
                                                        else
                                                        {
                                                            $purpose = NULL;
                                                        }

                                                        //ACT 07-12-17: Agregado para agregar controles que posiblemente no se hayan agregado antes, obtenemos id del último control para ponerle el id más grande
                                                        $newid = DB::table('controls')->max('id');
                                                        $newid = ($newid+1);

                                                        $nombre = 'Acción mitigante '.$newid.' - '.$row['organizacion'];
                                                        //$nombre = 'Acción mitigante '.($i+1).' - '.$row['organizacion'];
                                                        //identificamos usuario por correo
                                                        $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                        $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                        if (empty($user_control))
                                                        {
                                                            $user_control = NULL;
                                                        }
                                                        else
                                                        {   
                                                            $user_control = $user_control->id;
                                                        }
                                                        /*
                                                        if ($row['contribucion_acciones_mitigantes_4'] != '' && $row['contribucion_acciones_mitigantes_4'] != NULL)
                                                        {
                                                            $porcentaje_cont = $row['contribucion_acciones_mitigantes_4']*100;
                                                        }
                                                        else if ($row['contribucion_acciones_mitigantes_3'] != '' && $row['contribucion_acciones_mitigantes_3'] != NULL)
                                                        {
                                                            $porcentaje_cont = $row['contribucion_acciones_mitigantes_3']*100;
                                                        }
                                                        else if ($row['contribucion_acciones_mitigantes_2'] != '' && $row['contribucion_acciones_mitigantes_2'] != NULL)
                                                        {
                                                            $porcentaje_cont = $row['contribucion_acciones_mitigantes_2']*100;
                                                        }
                                                        else if ($row['contribucion_acciones_mitigantes'] != '' && $row['contribucion_acciones_mitigantes'] != NULL)
                                                        {
                                                            $porcentaje_cont = $row['contribucion_acciones_mitigantes']*100;
                                                        }
                                                        else
                                                        {
                                                            $porcentaje_cont = 0;
                                                        }*/

                                                        $porcentaje_cont = $row['contribucion_acciones_mitigantes']*100;

                                                        $evidence = eliminarSaltos($row['documentacion_de_respaldo_evidencias_de_auditoria_no_adjuntar_solo_detallar_adecuadamente_para_ser_solicitada_por_auditoria_interna_al_momento_de_auditar']);
                                                        //insertamos control y obtenemos ID
                                                        $control = \Ermtool\Control::create([
                                                                'name'=>$nombre,
                                                                'description'=>$description,
                                                                'type2'=>0,
                                                                'evidence'=>$evidence,
                                                                'purpose'=>$purpose,
                                                                'porcentaje_cont'=> $porcentaje_cont
                                                        ]);

                                                        //insertamos control_organization_risk
                                                        DB::table('control_organization_risk')
                                                        ->insert([
                                                            'organization_risk_id' => $org_risk->id,
                                                            'control_id' => $control->id,
                                                            'stakeholder_id'=>$user_control
                                                        ]);

                                                        //Guardamos en control_eval_risk_temp valor del control (autoevaluación)
                                                        DB::table('control_eval_risk_temp')
                                                            ->insert([
                                                                'result' => $porcentaje_cont,
                                                                'control_id' => $control->id,
                                                                'organization_id' => $org->id,
                                                                'auto_evaluation' => 1,
                                                                'status' => 1,
                                                                'created_at' => $fechas[0]
                                                            ]);

                                                        //agregamos valor residual de riesgo
                                                        $controlclass = new Controles;
                                                        //ahora calculamos riesgo residual para ese riesgo
                                                        //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                                        //ahora calcularemos el valor de el o los riesgos a los que apunte este control
                                                        $controlclass->calcControlledRiskAutoEval($control->id,$org->id,$ano,$mes,$dia);
                                                    }
                                                    else
                                                    {
                                                        $control = \Ermtool\Control::find($control->id);

                                                        //ACT 12-12-17: Vemos si es que existe en control_organization_risk, sino agregamos
                                                        $c_o_r = DB::table('control_organization_risk')
                                                                ->where('organization_risk_id','=',$org_risk->id)
                                                                ->where('control_id','=',$control->id)
                                                                ->get(['id']);

                                                        if (empty($c_o_r))
                                                        {
                                                            $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                            $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                            if (empty($user_control))
                                                            {
                                                                $user_control = NULL;
                                                            }
                                                            else
                                                            {   
                                                                $user_control = $user_control->id;
                                                            }

                                                            //insertamos control_organization_risk
                                                            DB::table('control_organization_risk')
                                                            ->insert([
                                                                'organization_risk_id' => $org_risk->id,
                                                                'control_id' => $control->id,
                                                                'stakeholder_id'=>$user_control
                                                            ]);
                                                        }

                                                        if ($j > 1 && $j != 3)
                                                        {
                                                            if ($row['contribucion_acciones_mitigantes_'.$j] != '' && $row['contribucion_acciones_mitigantes_'.$j] != NULL)
                                                            {
                                                                $porcentaje_cont = $row['contribucion_acciones_mitigantes_'.$j]*100;

                                                                //si es que ya existe no agregamos nada
                                                                $conteval = DB::table('control_eval_risk_temp')
                                                                        ->where('control_id','=',$control->id)
                                                                        ->where('organization_id','=',$org->id)
                                                                        ->where('created_at','=',$fechas[$j-1])
                                                                        ->select('id')
                                                                        ->get();

                                                                if (empty($conteval))
                                                                {
                                                                    //Cambiamos estado de anteriores
                                                                    DB::table('control_eval_risk_temp')
                                                                        ->where('control_id','=',$control->id)
                                                                        ->where('organization_id','=',$org->id)
                                                                        ->update(['status' => 0]);

                                                                    DB::table('control_eval_risk_temp')
                                                                    ->insert([
                                                                        'result' => $porcentaje_cont,
                                                                        'control_id' => $control->id,
                                                                        'organization_id' => $org->id,
                                                                        'auto_evaluation' => 1,
                                                                        'status' => 1,
                                                                        'created_at' => $fechas[$j-1]
                                                                    ]);

                                                                    //actualizamos porcentaje de cont de control
                                                                    $control->porcentaje_cont = $porcentaje_cont;
                                                                    $control->save();

                                                                    //calculamos nuevo riesgo residual para ese riesgo
                                                                    //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                                                    //calculamos nuevo valor de el o los riesgos a los que apunte este control
                                                                    $controlclass = new Controles;
                                                                    $controlclass->calcControlledRiskAutoEval($control->id,$org->id,$ano,$mes,$dia);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                            }
                                        }
                                        

                                        

                                        //ahora vemos si es que se debe cargar plan de acción
                                        if ($row['plan_accion'] == 'si' || $row['plan_accion'] == 'SI')
                                        {
                                            //vemos si es que ya existe plan de acción
                                            $action_plan = \Ermtool\Action_plan::getActionPlanByDescription($control->description);

                                            if (empty($action_plan))
                                            {
                                                //identificamos nuevamente usuario por correo
                                                $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                                $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                                if (empty($user_control))
                                                {
                                                    $user_control = NULL;
                                                }
                                                else
                                                {   
                                                    $user_control = $user_control->id;
                                                }
                                                //creamos un issue de control
                                                $issue = DB::table('issues')
                                                    ->insertGetId([
                                                            'name' => 'Falta implementar '.$control->name,
                                                            'description' => 'Falta implementar '.$control->name,
                                                            'recommendations' => 'Implementar '.$control->name,
                                                            'classification' => 1,
                                                            'control_id' => $control->id,
                                                            'created_at' => date('Y-m-d H:i:s'),
                                                            'updated_at' => date('Y-m-d H:i:s'),
                                                            'organization_id' => $org->id
                                                        ]);

                                                //agregamos plan de acción
                                                $action_plan = \Ermtool\Action_plan::create([
                                                    'issue_id' => $issue,
                                                    'description' => $control->description,
                                                    'stakeholder_id' => $user_control,
                                                    'final_date' => NULL,
                                                    'status' => 0,
                                                ]);

                                                //ACT 08-12-17: Para agregar en cada issue la organización, obtenemos issue a través de action_plan y actualizamos organization_id
                                                $issue = \Ermtool\Issue::find($action_plan->issue_id);
                                                $issue->organization_id = $org->id;
                                                $issue->save();
                                            }
                                            else
                                            {
                                                $action_plan = \Ermtool\Action_plan::find($action_plan->id);

                                                //ACT 08-12-17: Para agregar en cada issue la organización, obtenemos issue a través de action_plan y actualizamos organization_id
                                                $issue = \Ermtool\Issue::find($action_plan->issue_id);
                                                $issue->organization_id = $org->id;
                                                $issue->save();
                                            }

                                            //insermos porcentajes de avance
                                            //vemos si ya están seteados los porcentajes de avance para cada avance
                                            $avance1 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2016-11-30 00:00:00'))
                                                    ->where('updated_at','=',date('2016-11-30 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance1))
                                            {
                                                DB::table('progress_percentage')
                                                ->insert([
                                                    'percentage' => $row['estado_de_avance_0al_30_de_noviembre_2016']*100,
                                                    'action_plan_id' => $action_plan->id,
                                                    'created_at' => date('2016-11-30 00:00:00'),
                                                    'updated_at' => date('2016-11-30 00:00:00')
                                                ]);
                                            }

                                            $avance2 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2017-06-15 00:00:00'))
                                                    ->where('updated_at','=',date('2017-06-15 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance2))
                                            {
                                                DB::table('progress_percentage')
                                                ->insert([
                                                    'percentage' => $row['estado_de_avance_1al_15_de_junio_2017']*100,
                                                    'action_plan_id' => $action_plan->id,
                                                    'created_at' => date('2017-06-15 00:00:00'),
                                                    'updated_at' => date('2017-06-15 00:00:00')
                                                ]);
                                            }

                                            $avance3 = DB::table('progress_percentage')
                                                    ->where('action_plan_id','=',$action_plan->id)
                                                    ->where('created_at','=',date('2017-08-31 00:00:00'))
                                                    ->where('updated_at','=',date('2017-08-31 00:00:00'))
                                                    ->get(['id']);

                                            if (empty($avance3))
                                            {
                                                DB::table('progress_percentage')
                                                ->insert([
                                                    'percentage' => $row['estado_de_avance_2al_31_de_agosto_2017']*100,
                                                    'action_plan_id' => $action_plan->id,
                                                    'created_at' => date('2017-08-31 00:00:00'),
                                                    'updated_at' => date('2017-08-31 00:00:00')
                                                ]);
                                            }

                                            //identificamos nuevamente usuario por correo
                                            $row['e_mail_control'] = strtolower($row['e_mail_control']);
                                            $user_control = \Ermtool\Stakeholder::getUserByMail($row['e_mail_control']);

                                            if (empty($user_control))
                                            {
                                                $user_control = NULL;
                                            }
                                            else
                                            {   
                                                $user_control = $user_control->id;
                                            }

                                            //echo 'Control: '.$control->description.'<br>';
                                            //echo 'El user que actualmente existe es: '.$action_plan->stakeholder_id.'<br>';
                                            //echo 'e_mail_control es: '.$row['e_mail_control'].'<br><br><br>';
                                            //Validaremos que plan de acción tenga responsable
                                            if (($action_plan->stakeholder_id == NULL || $action_plan->stakeholder_id == '') && $user_control != NULL)
                                            {
                                                //echo 'Vacío: '.$action_plan->stakeholder_id.' y user control: '.$user_control.'<br>';
                                                $action_plan->stakeholder_id = $user_control;
                                                $action_plan->save();
                                            }

                                            //vemos si el porcentaje de avance = 100%
                                            if ($row['estado_de_avance_2al_31_de_agosto_2017'] == 1)
                                            {
                                                $action_plan->status = 1;
                                                $action_plan->save();
                                            }
                                        }
                                    }
                                    
                                }
                            }
                            else if ($_POST['kind'] == 7) //Plantilla Planes de Acción (PArauco)
                            {
                                
                                foreach ($row as $row)
                                {
                                    //print_r($row);
                                    //Creamos plan de auditoría (si es que no existe)
                                    if (isset($row['3_ano']))
                                    {
                                        if ($row['3_ano'] != '' && $row['3_ano'] != NULL && $row['17_sociedad'] != '' && $row['17_sociedad'] != NULL)
                                        {
                                            //echo $row['17_sociedad'].'<br>';
                                            //obtenemos organización
                                            $org = \Ermtool\Organization::getOrgByName($row['17_sociedad']);

                                            if (!empty($org)) //Si la organización no existe, no se carga nada
                                            {
                                                //Vemos si existe el plan de auditoría para esta organización
                                                $audit_plan = \Ermtool\Audit_plan::getAuditPlanByNameAndOrg('Plan de auditoría '.$row['3_ano'],$org->id);

                                                if (empty($audit_plan))
                                                {
                                                    //creamos plan
                                                    $audit_plan = DB::table('audit_plans')->insertGetId([
                                                        'name'=> 'Plan de auditoría '.$row['3_ano'],
                                                        'description'=> 'Plan de auditoría '.$row['3_ano'],
                                                        'initial_date'=>date($row['3_ano'].'-01-02'),
                                                        'final_date'=>date($row['3_ano'].'-12-31'),
                                                        'created_at'=>date($row['3_ano'].'-01-02 00:00:00'),
                                                        'updated_at'=>date($row['3_ano'].'-01-02 00:00:00'),
                                                        'organization_id'=>$org->id
                                                    ]);

                                                    //agregamos responsable
                                                    if ($row['6_responsable'] != '' && $row['6_responsable'] != '')
                                                    {
                                                        $mail = $row['6_responsable'].'@parauco.com';

                                                        $user = \Ermtool\Stakeholder::getUserByMail($mail);

                                                        if (!empty($user))
                                                        {
                                                            DB::table('audit_plan_stakeholder')
                                                                ->insert([
                                                                    'audit_plan_id' => $audit_plan,
                                                                    'stakeholder_id' => $user->id,
                                                                    'role' => 0
                                                                ]);
                                                        }
                                                    }    
                                                }
                                                else
                                                {
                                                    $audit_plan = $audit_plan->id;
                                                }

                                                //creamos auditoría, programa y prueba (si es que no existen)
                                                $audit = \Ermtool\Audit::getAuditByName($row['4_informe']);

                                                if (empty($audit))
                                                {
                                                    $audit = DB::table('audits')->insertGetId([
                                                        'name' => $row['4_informe'],
                                                        'description' => $row['4_informe'],
                                                        'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                        'updated_at' => date($row['3_ano'].'-01-02 00:00:00')
                                                    ]);
                                                }
                                                else
                                                {
                                                    $audit = $audit->id;
                                                }

                                                $audit_audit_plan = \Ermtool\Audit::getAuditAuditPlan($audit_plan,$audit);

                                                if (empty($audit_audit_plan))
                                                {
                                                    //hacemos enlace entre auditoría y plan
                                                    $audit_audit_plan = DB::table('audit_audit_plan')
                                                    ->insertGetId([
                                                        'audit_plan_id' => $audit_plan,
                                                        'audit_id' => $audit,
                                                        'initial_date' => date($row['3_ano'].'-01-02'),
                                                        'final_date' => date($row['3_ano'].'-12-31')
                                                    ]);
                                                }
                                                else
                                                {
                                                    $audit_audit_plan = $audit_audit_plan->id;
                                                }

                                                //creamos programa y enlace de programa
                                                $audit_program = \Ermtool\Audit_program::getAUditProgramByName($row['4_informe']);

                                                if (empty($audit_program))
                                                {
                                                    $audit_program = DB::table('audit_programs')
                                                    ->insertGetId([
                                                        'name' => $row['4_informe'],
                                                        'description' => $row['4_informe'],
                                                        'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                        'updated_at' => date($row['3_ano'].'-01-02 00:00:00')
                                                    ]);

                                                    //enlace entre audit_program y audit_audit_plan
                                                    $audit_audit_plan_audit_program = DB::table('audit_audit_plan_audit_program')
                                                                    ->insertGetId([
                                                                        'audit_audit_plan_id' => $audit_audit_plan,
                                                                        'audit_program_id' => $audit_program,
                                                                        'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                                        'updated_at' => date($row['3_ano'].'-01-02 00:00:00')
                                                                    ]);

                                                    //agregamos prueba de auditoría
                                                    $audit_test = DB::table('audit_tests')
                                                        ->insertGetId([
                                                        'audit_audit_plan_audit_program_id' => $audit_audit_plan_audit_program,
                                                        'name' => $row['4_informe'],
                                                        'description' => $row['4_informe'],
                                                        'status' => 2,
                                                        'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                        'updated_at' => date($row['3_ano'].'-01-02 00:00:00')
                                                    ]);

                                                    //agregamos comentarios de auditado como nota
                                                    /* No se si será necesario agregarlo
                                                    $note = DB::table('notes')
                                                        ->insertGetId([
                                                            ''
                                                        ]); */
                                                }
                                                else
                                                {
                                                    $audit_program = $audit_program->id;
                                                    $audit_test = \Ermtool\Audit_test::getAuditTestByName($row['4_informe']);
                                                    $audit_test = $audit_test->id;
                                                }

                                                if ($row['13_comentarios'] != '' && $row['13_comentarios'] != NULL)
                                                {
                                                    $recommendations = $row['13_comentarios'];
                                                    $recommendations = eliminarSaltos($recommendations);
                                                }
                                                else
                                                {
                                                    $recommendations = NULL;
                                                }

                                                if ($row['clasificacion_coso'] != '' && $row['clasificacion_coso'] != NULL)
                                                {
                                                    $coso = $row['clasificacion_coso'];
                                                }
                                                else
                                                {
                                                    $coso = NULL;
                                                }

                                                if ($row['tipo_partida'] != '' && $row['tipo_partida'] != NULL)
                                                {
                                                    $accounting_item = $row['tipo_partida'];
                                                }
                                                else
                                                {
                                                    $accounting_item = NULL;
                                                }

                                                if ($row['moneda'] != '' && $row['moneda'] != NULL)
                                                {
                                                    $currency = $row['moneda'];
                                                }
                                                else
                                                {
                                                    $currency = NULL;
                                                }

                                                if ($row['valor_economico'] != '' && $row['valor_economico'] != NULL)
                                                {
                                                    $economic_value = $row['valor_economico'];
                                                }
                                                else
                                                {
                                                    $economic_value = NULL;
                                                }

                                                $name = eliminarSaltos($row['2_ref_obs']);
                                                $description = eliminarSaltos($row['5_observacion']);

                                                //creamos issue asociado a prueba
                                                $issue = DB::table('issues')
                                                    ->insertGetId([
                                                        'name' => $name,
                                                        'description' => $description,
                                                        'recommendations' => $recommendations,
                                                        'audit_test_id' => $audit_test,
                                                        'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                        'updated_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                        'coso_classification' => $coso,
                                                        'accounting_item' => $accounting_item,
                                                        'currency' => $currency,
                                                        'economic_value' => $economic_value
                                                    ]);

                                                if ($row['15_plan_de_accion_compromiso'] != '' && $row['15_plan_de_accion_compromiso'] != NULL)
                                                {
                                                    if ($row['estado_observacion'] == 'ABIERTO')
                                                    {
                                                        $status = 0;
                                                        $per = 0;
                                                    }
                                                    else if ($row['estado_observacion'] == 'CERRADO')
                                                    {
                                                        $status = 1;
                                                        $per = 100;
                                                    }
                                                    else if ($row['estado_observacion'] == 'EN PROCESO')
                                                    {
                                                        $status = 0;
                                                        $per = 50;
                                                    }
                                                    else
                                                    {
                                                        $status = 0;
                                                        $per = 0;
                                                    }

                                                    if ($row['rut_responsable'] != '' && $row['rut_responsable'] != NULL)
                                                    {
                                                        $stakeholder_id = $row['rut_responsable'];
                                                    }
                                                    else
                                                    {
                                                        $stakeholder_id = NULL;
                                                    }

                                                    $description = eliminarSaltos($row['15_plan_de_accion_compromiso']);
                                                    //Ahora agregamos plan de acción
                                                    $action_plan = DB::table('action_plans')
                                                        ->insertGetId([
                                                            'issue_id' => $issue,
                                                            'description' => $description,
                                                            'stakeholder_id' => $stakeholder_id,
                                                            'status' => $status,
                                                            'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                            'updated_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                            'final_date' => date($row['3_ano'].'-12-31'),
                                                        ]);

                                                    //insertamos porcentaje de avance
                                                     DB::table('progress_percentage')
                                                        ->insert([
                                                            'percentage' => $per,
                                                            'comments' => NULL,
                                                            'action_plan_id' => $action_plan,
                                                            'created_at' => date($row['3_ano'].'-01-02 00:00:00'),
                                                            'updated_at' => date($row['3_ano'].'-01-02 00:00:00')
                                                        ]);   
                                                } 
                                            }
                                              
                                        }
                                    }
                                }                            
                            }

                            else if ($_POST['kind'] == 8) //Plantilla Riesgos (genérica)
                            {
                                foreach ($row as $row)
                                {

                                if (isset($row['nombre_riesgo']))
                                {

                                echo $row['subproceso'].'<br>';
                                //creamos riesgo si es que no existe
                                $risk = \Ermtool\Risk::getRiskByName2($row['nombre_riesgo']);

                                if (empty($risk))
                                {                             
                                    //identificamos categorías de Riesgo
                                    //nivel 1
                                    if($row['categoria_1'] != '')
                                    {
                                        $risk_category1 = \Ermtool\Risk_category::getRiskCategoryByName($row['categoria_1']);

                                        if (empty($risk_category1))
                                        {
                                                $risk_category1 = \Ermtool\Risk_category::create([
                                                    'name' => $row['categoria_1'],
                                                    'description' => $row['categoria_1']
                                                ]);
                                        }
                                    }
                                    
                                    if ($row['categoria_2'] != '')
                                    {
                                        $risk_category2 = \Ermtool\Risk_category::getRiskCategoryByName($row['categoria_2']);

                                        if (empty($risk_category2))
                                        {
                                                $risk_category2 = \Ermtool\Risk_category::create([
                                                    'name' => $row['categoria_2'],
                                                    'description' => $row['categoria_2'],
                                                    'risk_category_id' => $risk_category1->id
                                                ]);
                                        }
                                    }
                                    
                                    if (isset ($risk_category2))
                                    {
                                        $rk = $risk_category2->id;
                                    }
                                    else if (isset($risk_category1))
                                    {
                                        $rk = $risk_category1->id;
                                    }
                                    else
                                    {
                                        $rk = NULL;
                                    }
                                    if ($row['descripcion'] == '' || $row['descripcion'] == NULL)
                                    {
                                        $description = $row['descripcion'];
                                    }
                                    else
                                    {
                                        $description = $row['descripcion'].' - '.$row['descripcion'];
                                    }

                                    //solo para DB de prueba (NorteGrande), agregamos id
                                    //$maxid = DB::table('risks')->max('id');
                                    //$id = $maxid+1;
                                    $description = eliminarSaltos($description);
                                    $name = eliminarSaltos($row['nombre_riesgo']);

                                    if ($row['fecha_expiracion'] != '')
                                    {
                                        $fecha_exp = $row['fecha_expiracion'];
                                    }
                                    else
                                    {
                                        $fecha_exp = NULL;
                                    }
                                    //creamos riesgo
                                    $risk = \Ermtool\Risk::create([
                                            //'id' => $id,
                                            'name'=>$name,
                                            'description'=>$description,
                                            'type'=> 0,
                                            'type2'=> 1,
                                            'risk_category_id'=>$rk,
                                            'status' => 0,
                                            'expiration_date' => $fecha_exp
                                    ]);
                                }
                                //separamos organizaciones por espacio        
                                $orgs = explode(', ',$row['organizacion']);
                                $users = explode(', ',$row['responsable']);
                                $j = 0;
                                foreach ($orgs as $org)
                                {
                                    //identificamos usuario por nombre + apellido
                                    $user = \Ermtool\Stakeholder::getUserByName($users[$j]);

                                    if (empty($user))
                                    {
                                        $user = NULL;
                                    }
                                    else
                                    {   
                                        $user = $user->id;
                                    }

                                    //obtenemos id de organización
                                    $org1 = \Ermtool\Organization::getOrgByName($org);

                                    \Ermtool\Risk::insertOrganizationRisk($org1->id,$risk->id,$user);

                                    $j += 1;
                                }

                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['subproceso']);
                                //cargamos enlace entre subproceso y riesgo
                                $subprocess = \Ermtool\Subprocess::find($subprocess->id);
                                $subprocess->risks()->attach($risk->id);
                                /*
                                //Para prueba NG
                                $maxid = DB::table('risk_subprocess')->max('id');
                                $id = $maxid+1;

                                DB::table('risk_subprocess')
                                    ->insert([
                                        'id' => $id,
                                        'risk_id' => $risk->id,
                                        'subprocess_id' => $subprocess->id
                                    ]);*/
                                }
                                

                                }
                            }

                            else if ($_POST['kind'] == 9) //Plantilla Controles (genérica)
                            {
                                //print_r($row);
                                foreach ($row as $row)
                                {
                                //echo $row['riesgo_de_proceso'].'<br>';
                                    if (isset($row['organizacion']) && $row['organizacion'] != '')
                                    {
                                        //echo $row['riesgo_de_proceso'].'<br>';
                                        $org = \Ermtool\Organization::getOrgByName($row['organizacion']);
                                        //obtenemos riesgo
                                        $risk = \Ermtool\Risk::getRiskByName($row['riesgo_de_proceso'],$org->id);

                                        //vemos si existe control
                                        $control = \Ermtool\Control::getControlByName($row['nombre_control']);

                                        //identificamos usuario por nombre
                                        $user_control = \Ermtool\Stakeholder::getUserByName($row['responsable']);

                                        if (empty($user_control))
                                        {
                                            $user_control = NULL;
                                        }
                                        else
                                        {   
                                            $user_control = $user_control->id;
                                        }

                                        if (empty($control))
                                        {
                                            //definimos preventivo, contingencia (correctivo)
                                            if ($row['proposito'] == 'Preventivo')
                                            {
                                                $purpose = 0;
                                            }
                                            else if ($row['proposito'] == 'Detectivo')
                                            {
                                                $purpose = 1;
                                            }
                                            else if ($row['proposito'] == 'Correctivo')
                                            {
                                                $purpose = 2;
                                            }
                                            else
                                            {
                                                $purpose = NULL;
                                            }

                                            $nombre = $row['nombre_control'];
                                            $nombre = eliminarSaltos($nombre);
                                            $porcentaje_cont = $row['de_contribucion']*100;

                                            $description = eliminarSaltos($row['descripcion']);
                                            $evidence = eliminarSaltos($row['evidencia']);

                                            if (isset($row['comentarios']) && $row['comentarios'] != '')
                                            {
                                                $comments = eliminarSaltos($row['comentarios']);
                                            }
                                            else
                                            {
                                                $comments = NULL;
                                            }
                                            //Para prueba NG
                                            //$maxid = DB::table('controls')->max('id');
                                            //$id = $maxid+1;

                                            //echo strlen($nombre).'<br>';
                                            //insertamos control y obtenemos ID
                                            $control = \Ermtool\Control::create([
                                                //'id' => $id,
                                                'name'=>$nombre,
                                                'description'=>$description,
                                                'type2'=>0,
                                                'evidence'=>$evidence,
                                                'purpose'=>$purpose,
                                                'stakeholder_id'=>$user_control,
                                                //'porcentaje_cont'=> $porcentaje_cont,
                                                //'comments' => $comments
                                            ]);

                                            //Guardamos en control_eval_risk_temp valor del control (autoevaluación)
                                            /* No para NG
                                            DB::table('control_eval_risk_temp')
                                            ->insert([
                                                'result' => $porcentaje_cont,
                                                'control_id' => $control->id,
                                                'organization_id' => $org->id,
                                                'auto_evaluation' => 1,
                                                'status' => 1,
                                                'created_at' => date('Y-m-d H:i:s')
                                            ]); */

                                            //agregamos valor residual de riesgo
                                            //$controlclass = new Controles;
                                            //ahora calculamos riesgo residual para ese riesgo
                                            //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                            //ahora calcularemos el valor de el o los riesgos a los que apunte este control
                                            //$controlclass->calcControlledRisk($control->id,$org->id,date('Y'),date('m'),date('d'));
                                        }
                                        else
                                        {
                                            $control = \Ermtool\Control::find($control->id);

                                            $porcentaje_cont = $row['de_contribucion']*100;
                                            //Cambiamos estado de anteriores
                                            /* No para NG
                                            DB::table('control_eval_risk_temp')
                                                ->where('control_id','=',$control->id)
                                                ->where('organization_id','=',$org->id)
                                                ->update(['status' => 0]);

                                            DB::table('control_eval_risk_temp')
                                            ->insert([
                                                'result' => $porcentaje_cont,
                                                'control_id' => $control->id,
                                                'organization_id' => $org->id,
                                                'auto_evaluation' => 1,
                                                'status' => 1,
                                                'created_at' => date('Y-m-d H:i:s')
                                            ]); */

                                            //actualizamos porcentaje de cont de control
                                            //$control->porcentaje_cont = $porcentaje_cont;
                                            //$control->save();

                                            //calculamos nuevo riesgo residual para ese riesgo
                                            //$controlclass->calcResidualRisk($org->id,$org_risk->id);
                                            //calculamos nuevo valor de el o los riesgos a los que apunte este control
                                            //$controlclass = new Controles;
                                            //$controlclass->calcControlledRisk($control->id,$org->id,date('Y'),date('m'),date('d'));
                                        }

                                        //echo $row['riesgo_de_proceso'].'<br>';
                                        //insertamos control_organization_risk
                                        //ACT 15-12-17: Insertamos porcentaje_cont aquí
                                        //vemos si existe el control para la org_risk
                                        $cor = DB::table('control_organization_risk')
                                                ->where('organization_risk_id','=',$risk->org_risk_id)
                                                ->where('control_id','=',$control->id)
                                                ->select('id')
                                                ->first();

                                        if (empty($cor))
                                        {
                                            if (isset($row['comentarios']) && $row['comentarios'] != '')
                                            {
                                                $comments = eliminarSaltos($row['comentarios']);
                                            }
                                            else
                                            {
                                                $comments = NULL;
                                            }

                                            //Para prueba NG
                                            //$maxid = DB::table('control_organization_risk')->max('id');
                                            //$id = $maxid+1;
                                            DB::table('control_organization_risk')
                                            ->insert([
                                                //'id' => $id,
                                                'organization_risk_id' => $risk->org_risk_id,
                                                'control_id' => $control->id,
                                                //'stakeholder_id'=>$user_control,
                                                //'cont_percentage' => $porcentaje_cont,
                                                //'comments' => $comments
                                            ]);
                                        }
                                        
                                    }
                                }
                            }

                            else if ($_POST['kind'] == 10) //Planes de acción y Hallazgos (genérico) --> Controles
                            {
                                foreach ($row as $row)
                                {
                                    if (isset($row['organizacion']) && $row['organizacion'] != '')
                                    {
                                        //print_r($row);

                                        //obtenemos control
                                        $control = \Ermtool\Control::getControlByName($row['control']);
                                        //obtenemos org
                                        $org = \Ermtool\Organization::getOrgByName($row['organizacion']);

                                        if ($row['recomendaciones'] != '' && $row['recomendaciones'] != NULL)
                                        {
                                            $recommendations = $row['recomendaciones'];
                                            $recommendations = eliminarSaltos($recommendations);
                                        }
                                        else
                                        {
                                            $recommendations = NULL;
                                        }

                                        if ($row['clasificacion_coso'] != '' && $row['clasificacion_coso'] != NULL)
                                        {
                                            $coso = $row['clasificacion_coso'];
                                        }
                                        else
                                        {
                                            $coso = NULL;
                                        }

                                        if ($row['tipo_de_partida'] != '' && $row['tipo_de_partida'] != NULL)
                                        {
                                            $accounting_item = $row['tipo_de_partida'];
                                        }
                                        else
                                        {
                                            $accounting_item = NULL;
                                        }

                                        if ($row['moneda'] != '' && $row['moneda'] != NULL)
                                        {
                                            $currency = $row['moneda'];
                                        }
                                        else
                                        {
                                            $currency = NULL;
                                        }

                                        if ($row['valor_economico_hallazgo'] != '' && $row['valor_economico_hallazgo'] != NULL)
                                        {
                                            $economic_value = $row['valor_economico_hallazgo'];
                                        }
                                        else
                                        {
                                            $economic_value = NULL;
                                        }

                                        $name = eliminarSaltos($row['nombre']);
                                        $description = eliminarSaltos($row['descripcion']);

                                        //creamos issue asociado a prueba
                                        //ACTUALIZACIÓN 07-12-17: Se agregó organización --> Por ahora será sólo como act

                                        $issue = DB::table('issues')
                                            ->insertGetId([
                                                'name' => $name,
                                                'description' => $description,
                                                'recommendations' => $recommendations,
                                                'control_id' => $control->id,
                                                //'organization_id' => $org->id,
                                                'created_at' => $row['fecha_avance_1'],
                                                'updated_at' => $row['fecha_avance_1'],
                                                'coso_classification' => $coso,
                                                'accounting_item' => $accounting_item,
                                                'currency' => $currency,
                                                'economic_value' => $economic_value
                                            ]);

                                        if ($row['plan_de_accion'] != '' && $row['plan_de_accion'] != NULL)
                                        {
                                            if ($row['de_avance_1'] == 1 || $row['de_avance_2'] == 1 || $row['de_avance_3'] == 1 || $row['de_avance_4'] == 1 || $row['de_avance_5'] == 1 || $row['de_avance_6'] == 1)
                                            {
                                                $status = 1;
                                            }

                                            else
                                            {
                                                $status = 0;
                                            }

                                            //echo $row['responsable_plan'].'<br>';

                                            if ($row['responsable_plan'] != '' && $row['responsable_plan'] != NULL)
                                            {
                                                $stakeholder = \Ermtool\Stakeholder::getUserByName($row['responsable_plan']);
                                            }
                                            else
                                            {
                                                $stakeholder = NULL;
                                            }

                                            $description = eliminarSaltos($row['plan_de_accion']);
                                            //Ahora agregamos plan de acción

                                            if ($row['fecha_limite_plan'] != '')
                                            {
                                                $fecha_limite = $row['fecha_limite_plan'];
                                            }
                                            else
                                            {
                                                $fecha_limite = NULL;
                                            }
                                            $action_plan = DB::table('action_plans')
                                                ->insertGetId([
                                                    'issue_id' => $issue,
                                                    'description' => $description,
                                                    'stakeholder_id' => $stakeholder->id,
                                                    'status' => $status,
                                                    'created_at' => $row['fecha_avance_1'],
                                                    'updated_at' => $row['fecha_avance_1'],
                                                    'final_date' => $fecha_limite
                                                ]);

                                            for ($j=1;$j<=6;$j++)
                                            {   
                                                if (isset($row['de_avance_'.$j]) && $row['de_avance_'.$j] != '')
                                                {
                                                    $per = $row['de_avance_'.$j] * 100;

                                                    if (isset($row['comentarios_avance_'.$j]) && $row['comentarios_avance_'.$j] != '')
                                                    {
                                                        $comments = $row['comentarios_avance_'.$j];
                                                        $comments = eliminarSaltos($row['comentarios_avance_'.$j]);
                                                    } 
                                                    else
                                                    {
                                                        $comments = NULL;
                                                    }
                                                    //insertamos porcentaje de avance
                                                     DB::table('progress_percentage')
                                                        ->insert([
                                                            'percentage' => $per,
                                                            'comments' => $comments,
                                                            'action_plan_id' => $action_plan,
                                                            'created_at' => $row['fecha_avance_'.$j],
                                                            'updated_at' => $row['fecha_avance_'.$j]
                                                        ]);
                                                }   
                                            }
                                        } 
                                    }
                                }
                            }

                            else if ($_POST['kind'] == 11) //Riesgos (PArauco)
                            {
                                //print_r($row);

                                $org = \Ermtool\Organization::getOrgByName($row['sociedad']);

                                if (!empty($org))
                                {
                                    //$risk = \Ermtool\Risk::getRiskByName($row['titulo_del_riesgo'],$org->id);
                                }
                                

                                //obtenemos subproceso
                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['id_proceso_de_negocios']);

                                if (!empty($subprocess) && !empty($org))
                                {
                                    //echo $i.'.- '.$subprocess->id.'<br>';
                                    //if (empty($risk))
                                    //{
                                        //identificamos categorías de Riesgo
                                        //nivel 1
                                        $risk_category = \Ermtool\Risk_category::getRiskCategoryByName($row['risk']);

                                        if (!empty($risk_category))
                                        {
                                            $risk_category = $risk_category->id;
                                        }
                                        else
                                        {
                                            $risk_category = NULL;
                                        }

                                        //identificamos usuario por correo
                                        $user_name = strtolower($row['dueno_del_proceso_afectado']);
                                        $user = \Ermtool\Stakeholder::getUserByMail($user_name);

                                        if (empty($user))
                                        {
                                            $user = NULL;
                                        }
                                        else
                                        {   
                                            $user = $user->id;
                                        }

                                        if ($row['descripcion'] == '' || $row['descripcion'] == NULL)
                                        {
                                            $description = NULL;
                                        }
                                        else
                                        {
                                            $description = $row['descripcion'];
                                        }

                                        $description = eliminarSaltos($description);

                                        if ($row['medidas_de_mitigacion'] == '' || $row['medidas_de_mitigacion'] == NULL)
                                        {
                                            $medidas = NULL;
                                        }
                                        else
                                        {
                                            $medidas = $row['medidas_de_mitigacion'];
                                        }

                                        $medidas = eliminarSaltos($medidas);
                                        $medidas = 'Medidas de mitigación: '.$medidas;
                                        //creamos riesgo
                                        /*
                                        $risk = \Ermtool\Risk::create([
                                            'name'=>$row['titulo_del_riesgo'],
                                            'description'=>$description,
                                            'type'=> 0,
                                            'type2'=> 1,
                                            'risk_category_id'=>$risk_category,
                                            'comments' => $medidas,
                                        ]); */

                                        $risk_id = DB::table('risks')
                                                ->insertGetId([
                                                    'name'=>$row['titulo_del_riesgo'],
                                                    'description'=>$description,
                                                    'type'=> 0,
                                                    'type2'=> 1,
                                                    'risk_category_id'=>$risk_category,
                                                    'created_at' => $row['fecha_identificacion'],
                                                    'updated_at' => $row['fecha_identificacion']
                                                ]);

                                        \Ermtool\Risk::insertOrganizationRisk($org->id,$risk_id,$user,$medidas);

                                        //cargamos enlace entre subproceso y riesgo
                                        $subprocess = \Ermtool\Subprocess::find($subprocess->id);
                                        $subprocess->risks()->attach($risk_id);

                                        //agregamos causa
                                        $name = $row['factor_de_riesgo_causa'];
                                        $desc = $row['descripcion_de_la_causa'];
                                        $desc = eliminarSaltos($desc);

                                        $cause = \Ermtool\Cause::getCauseByNameAndDescription($name,$desc);

                                        if (empty($cause))
                                        {
                                            $cause = \Ermtool\Cause::create([
                                                'name' => $name,
                                                'description' => $desc,
                                            ]);
                                        }

                                        //insertamos cause_risk
                                        DB::table('cause_risk')
                                            ->insert([
                                                'risk_id' => $risk_id,
                                                'cause_id' => $cause->id,
                                            ]);
                                        
                                    //}

                                    //agregamos cada una de las evaluaciones
                                    //obtenemos org_risk_id
                                    $org_risk = DB::table('organization_risk')
                                                ->where('organization_id','=',$org->id)
                                                ->where('risk_id','=',$risk_id)
                                                ->select('id')
                                                ->first();

                                    if ($row['p_encuesta'] != '' && $row['p_encuesta'] != NULL && $row['i_encuesta'] != '' && $row['i_encuesta'] != NULL)
                                    {
                                        //primero creamos evaluación manual
                                        //seleccionamos evaluación (si es que existe)
                                        $eval_id1 = DB::table('evaluations')
                                                ->where('created_at','=',$row['fecha_identificacion'])
                                                ->select('id')
                                                ->first();

                                        if (empty($eval_id1))
                                        {
                                                $eval_id1 = DB::table('evaluations')->insertGetId([
                                                    'name' => 'Evaluación Manual',
                                                    'consolidation' => 1,
                                                    'description' => 'Evaluación Manual',
                                                    'created_at' => $row['fecha_identificacion'],
                                                    'updated_at' => $row['fecha_identificacion'],
                                                ]);
                                        }
                                        else
                                        {
                                                $eval_id1 = $eval_id1->id;
                                        }

                                        //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                        $evaluation_risk = DB::table('evaluation_risk')
                                                            ->where('evaluation_id','=',$eval_id1)
                                                            ->where('organization_risk_id','=',$org_risk->id)
                                                            ->select('id')
                                                            ->first();

                                        if (empty($evaluation_risk))
                                        {
                                            //insertamos riesgo evaluation_risk
                                            $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                        'evaluation_id' => $eval_id1,
                                                        'organization_risk_id' => $org_risk->id,
                                                        'avg_probability' => $row['p_encuesta'],
                                                        'avg_impact' => $row['i_encuesta']
                                                ]);
                                        }
                                        else
                                        {
                                            $evaluation_risk = $evaluation_risk->id;
                                        }
                                                    
                                        //vemos si existe en evaluation_risk_stakeholder
                                        $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                ->select('evaluation_risk_stakeholder.id')
                                                ->first();

                                        if (empty($evaluation_risk_stakehoder))
                                        {
                                            //insertamos en evaluation_risk_stakeholder
                                            DB::table('evaluation_risk_stakeholder')->insert([
                                                'evaluation_risk_id'=>$evaluation_risk,
                                                'user_id'=>Auth::user()->id,
                                                'probability'=>$row['p_encuesta'],
                                                'impact'=>$row['i_encuesta'],
                                            ]);
                                        }
                                    }

                                    //$fecha = date('Y-m-d H:i:s');

                                    if ($row['p_riesgo'] != '' && $row['p_riesgo'] != NULL && $row['i_riesgo'] != '' && $row['i_riesgo'] != NULL)
                                    {
                                        //primero creamos evaluación manual
                                        //seleccionamos evaluación (si es que existe)
                                        $eval_id1 = DB::table('evaluations')
                                                ->where('created_at','=',$row['fecha_encuesta_2'])
                                                ->select('id')
                                                ->first();

                                        if (empty($eval_id1))
                                        {
                                                $eval_id1 = DB::table('evaluations')->insertGetId([
                                                    'name' => 'Evaluación Manual',
                                                    'consolidation' => 1,
                                                    'description' => 'Evaluación Manual',
                                                    'created_at' => $row['fecha_encuesta_2'],
                                                    'updated_at' => $row['fecha_encuesta_2'],
                                                ]);
                                        }
                                        else
                                        {
                                                $eval_id1 = $eval_id1->id;
                                        }

                                        //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                        $evaluation_risk = DB::table('evaluation_risk')
                                                    ->where('evaluation_id','=',$eval_id1)
                                                    ->where('organization_risk_id','=',$org_risk->id)
                                                    ->select('id')
                                                    ->first();

                                        if (empty($evaluation_risk))
                                        {
                                            //insertamos riesgo evaluation_risk
                                            $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                        'evaluation_id' => $eval_id1,
                                                        'organization_risk_id' => $org_risk->id,
                                                        'avg_probability' => $row['p_riesgo'],
                                                        'avg_impact' => $row['i_riesgo']
                                                ]);
                                        }
                                        else
                                        {
                                            $evaluation_risk = $evaluation_risk->id;
                                        }
                                                    
                                        //vemos si existe en evaluation_risk_stakeholder
                                        $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                ->select('evaluation_risk_stakeholder.id')
                                                ->first();

                                        if (empty($evaluation_risk_stakehoder))
                                        {
                                            //insertamos en evaluation_risk_stakeholder
                                            DB::table('evaluation_risk_stakeholder')->insert([
                                                'evaluation_risk_id'=>$evaluation_risk,
                                                'user_id'=>Auth::user()->id,
                                                'probability'=>$row['p_riesgo'],
                                                'impact'=>$row['i_riesgo'],
                                            ]);
                                        }
                                    }
                                }                              
                            }

                            else if ($_POST['kind'] == 12) //Riesgos tipo
                            {
                                foreach ($row as $row)
                                {

                                //print_r($row);
                                if (isset($row['nombre_riesgo']) && $row['nombre_riesgo'] != '')
                                {
                                    if ($row['perdida_esperada_num'] == '')
                                        $expected_loss = NULL;
                                    else
                                        $expected_loss = $row['perdida_esperada_num'];

                                    //eliminamos saltos de nombre y descripción
                                    $name = eliminarSaltos($row['nombre_riesgo']);
                                    $description = eliminarSaltos($row['descripcion']);

                                    if ($row['fecha_expiracion'] == '')
                                        $exp_date = NULL;
                                    else
                                        $exp_date = $row['fecha_expiracion'];

                                    $risk = \Ermtool\Risk::create([
                                        'name'=>$name,
                                        'description'=>$description,
                                        'type2'=>0,
                                        'expiration_date'=>$exp_date,
                                        'risk_category_id'=>NULL,
                                        'expected_loss' => $expected_loss
                                    ]);

                                    //causas y efectos (al menos en parauco no hay)
                                }

                                }
                            }
                            else if ($_POST['kind'] == 13) //Cuentas contables
                            {
                                //foreach ($row as $row)
                                //{
                                //print_r($row);

                                if (isset($row['sub_grupo_cuenta']) && $row['sub_grupo_cuenta'] != '')
                                {
                                    //vemos si ya existe
                                    $desc_cuenta_mayor = DB::table('financial_statements')
                                                    ->where('name','=',$row['desc_cuenta_mayor'])
                                                    ->select('id')
                                                    ->first();

                                    if (!empty($desc_cuenta_mayor) && $desc_cuenta_mayor != NULL)
                                    {
                                        //sólo actualizamos cta contable
                                        if ($row['denominacion_rubro'] != '')
                                        {
                                            $description = $row['denominacion_rubro'];
                                        }
                                        else
                                        {
                                            $description = NULL;
                                        }

                                        $fs = \Ermtool\Financial_statement::find($desc_cuenta_mayor->id);

                                        $fs->description = $description;
                                        $fs->save();
                                    }
                                    else
                                    {
                                        if ($row['desc_cuenta_mayor'] != '')
                                        {
                                            $name = $row['desc_cuenta_mayor'];
                                        }
                                        else
                                        {
                                            $name = NULL;
                                        }

                                        if ($row['denominacion_rubro'] != '')
                                        {
                                            $description = $row['denominacion_rubro'];
                                        }
                                        else
                                        {
                                            $description = NULL;
                                        }

                                        $fs = \Ermtool\Financial_statement::create([
                                            'name'=>$name,
                                            'description'=>$description,
                                            'status'=>0,
                                            'kind'=>$row['sub_grupo_cuenta']
                                        ]);
                                    }
                                    
                                }
                                //}
                            }

                            else if ($_POST['kind'] == 14) //Actualizar comentarios de control
                            {
                                //foreach ($row as $row)
                                //{

                                //obtenemos org
                                $org = \Ermtool\Organization::getOrgByName($row['organizacion']);
                                //obtenemos risk y org_risk_id
                                $risk = \Ermtool\Risk::getRiskByName($row['riesgo_de_proceso'],$org->id);
                                //obtenemos control
                                $control = \Ermtool\Control::getControlByName($row['nombre_control']);

                                //obtenemos control_organization_risk
                                $cor = DB::table('control_organization_risk')
                                        ->where('control_id','=',$control->id)
                                        ->where('organization_risk_id','=',$risk->org_risk_id)
                                        ->select('id')
                                        ->first();

                                //actualizamos comentarios
                                if ($row['comentarios'] != '')
                                {
                                    $comments = $row['comentarios'];
                                }
                                else
                                {
                                    $comments = NULL;
                                }

                                DB::table('control_organization_risk')
                                    ->where('id','=',$cor->id)
                                    ->update([
                                        'comments' => $comments
                                    ]);

                                //}
                            }

                            else if ($_POST['kind'] == 15) //Actualizar categorías de Riesgo (PArauco)
                            {
                                //foreach ($row as $row)
                                //{
                                /* ESTO ES EN CASO QUE YA SE ENCUENTREN CARGADAS LAS CATEGORÍAS */
                                if ($row['nivel_3'] != '' && $row['nivel_3'] != ' ')
                                {
                                    $ref_risk = str_replace('_','',$row['ref_risk']);
                                    $nivel3 = str_replace('.',' ',$row['nivel_3']);
                                    $risk_category_name = $ref_risk.'_'.$nivel3;

                                    //echo $risk_category.'<br>';
                                }
                                else
                                {
                                    $ref_risk = str_replace('_','',$row['ref_risk']);
                                    $nivel2 = str_replace('.',' ',$row['nivel_2']);
                                    $risk_category_name = $ref_risk.'_'.$nivel2;

                                    //echo $risk_category.'<br>';
                                }

                                $risk_category = \Ermtool\Risk_category::getRiskCategoryByName($risk_category_name);

                                if (!empty($risk_category))
                                {
                                    $risk_category = $risk_category->id;
                                }
                                else
                                {
                                    echo "NO ESTÁ LA CATEGORÍA: ".$risk_category_name.'<br>';
                                }

                                //echo $risk_category;
                                
                                //obtenemos org
                                //$org = \Ermtool\Organization::getOrgByName($row['sociedad']);
                                //obtenemos risk y org_risk_id
                                /*
                                if (!empty($org))
                                {
                                    $risk = \Ermtool\Risk::getRiskByName($row['titulo_del_riesgo'],$org->id);
                                }
                                else
                                {
                                    echo $row['sociedad'].'<br>';
                                }
                                */
                                $risk = \Ermtool\Risk::getRiskByName($row['titulo_del_riesgo'],NULL);

                                if (empty($risk))
                                {
                                    echo $row['titulo_del_riesgo'].'<br>';
                                }
                                else
                                {
                                    //obtenemos riesgo
                                    $risk = \Ermtool\Risk::find($risk->id);
                                    //echo $risk->id.'<br>';

                                    //actualizamos categoría del riesgo
                                    $risk->risk_category_id = $risk_category;
                                    $risk->save();
                                }
                                
                                
                                

                                //}
                            }
                            else if ($_POST['kind'] == 16) //Actualizar organization_subprocess (PArauco)
                            {
                                //print_r($row);
                                //Proceso = MacroSubproceso
                                $subprocess = \Ermtool\Subprocess::getSubprocessByName($row['referencia_hasta_proceso'].' - '.$row['pr']);

                                if (empty($subprocess)) //creamos macrosubproceso
                                {
                                    echo 'No se encontró: '.$row['referencia_hasta_proceso'].' - '.$row['pr'];
                                }

                                //Subproceso = Subproceso
                                $subprocess2 = \Ermtool\Subprocess::getSubprocessByName($row['codigo'].' - '.$row['sp']);

                                if (empty($subprocess2)) //creamos subproceso
                                {
                                     echo 'No se encontróóó: '.$row['codigo'].' - '.$row['sp'];
                                }

                                //Separamos organizaciones por coma, y enlazamos subproceso a organización
                                $orgs = explode(', ',$row['organizacion']);

                                foreach ($orgs as $org)
                                {
                                    //obtenemos org por nombre
                                    $o = \Ermtool\Organization::getOrgByName($org);

                                    //primero vemos que no exista previamente
                                    $org_sub = DB::table('organization_subprocess')
                                            ->where('organization_id','=',$o->id)
                                            ->where('subprocess_id','=',$subprocess->id)
                                            ->select('id')
                                            ->get();

                                    if (empty($org_sub))
                                    {
                                        DB::table('organization_subprocess')
                                        ->insert([
                                            'organization_id' => $o->id,
                                            'subprocess_id' => $subprocess->id
                                        ]);
                                    }

                                    //lo mismo para subprocess2
                                    //primero vemos que no exista previamente
                                    $org_sub = DB::table('organization_subprocess')
                                            ->where('organization_id','=',$o->id)
                                            ->where('subprocess_id','=',$subprocess2->id)
                                            ->select('id')
                                            ->get();

                                    if (empty($org_sub))
                                    {
                                        DB::table('organization_subprocess')
                                        ->insert([
                                            'organization_id' => $o->id,
                                            'subprocess_id' => $subprocess2->id
                                        ]);
                                    } 
                                    
                                }
                            }
                            /*
                            else if ($_POST['kind'] == 17) //Actualización subproceso enlazado a Riesgo (PArauco)
                            {
                                //print_r($row);

                                $risk = \Ermtool\Risk::getRiskByName($row['titulo_del_riesgo'],NULL);

                                if (empty($risk))
                                {
                                    echo $row['titulo_del_riesgo'].'<br>';
                                }
                                else
                                {
                                    //echo $risk->id.'<br>';
                                    //actualizamos risk subprocess
                                    //obtenemos subproceso
                                    $sub = \Ermtool\Subprocess::getSubprocessByName($row['id_subproceso'].' - '.$row['nombre_subproceso']);

                                    if (empty($sub))
                                    {
                                        echo $row['id_subproceso'].' - '.$row['nombre_subproceso'];
                                    }
                                    else
                                    {
                                        //actualizamos
                                        DB::table('risk_subprocess')
                                            ->where('risk_id','=',$risk->id)
                                            ->update([
                                                'subprocess_id' => $sub->id
                                            ]);

                                    }
                                } 
                            }*/
                            else if ($_POST['kind'] == 17) //Actualizar evaluaciones (PArauco)
                            {
                                $org = \Ermtool\Organization::getOrgByName($row['sociedad']);
                                
                                if (!empty($org))
                                {
                                    $risk = \Ermtool\Risk::getRiskByName($row['titulo_del_riesgo'],$org->id);

                                    if (empty($risk))
                                    {
                                        echo $row['titulo_del_riesgo'].'<br>';
                                    }
                                    else
                                    {
                                        //echo $row['dueno_del_proceso_afectado'].'<br>';

                                        if ($row['p_encuesta'] != '' && $row['p_encuesta'] != NULL && $row['i_encuesta'] != '' && $row['i_encuesta'] != NULL)
                                        {
                                            //primero creamos evaluación manual
                                            //OBS 23-03-18: Esta no es evaluación manual, sino que es encuesta
                                            //seleccionamos evaluación (si es que existe)
                                            $eval_id1 = DB::table('evaluations')
                                                    ->where('created_at','=',$row['fecha_identificacion'])
                                                    ->select('id')
                                                    ->first();

                                            if (empty($eval_id1))
                                            {
                                                $fecha = date_create($row['fecha_identificacion']);
                                                $fecha = date_format($fecha, 'd-m-Y');
                                                $eval_id1 = DB::table('evaluations')->insertGetId([
                                                    'name' => 'Encuesta de evaluación del '.$fecha,
                                                    'consolidation' => 1,
                                                    'description' => 'Encuesta de evaluación del '.$fecha,
                                                    'created_at' => $row['fecha_identificacion'],
                                                    'updated_at' => $row['fecha_identificacion'],
                                                ]);
                                            }
                                            else
                                            {
                                                    $eval_id1 = $eval_id1->id;
                                            }

                                            //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                            $evaluation_risk = DB::table('evaluation_risk')
                                                                ->where('evaluation_id','=',$eval_id1)
                                                                ->where('organization_risk_id','=',$risk->org_risk_id)
                                                                ->select('id')
                                                                ->first();

                                            if (empty($evaluation_risk))
                                            {
                                                //insertamos riesgo evaluation_risk
                                                $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                            'evaluation_id' => $eval_id1,
                                                            'organization_risk_id' => $risk->org_risk_id,
                                                            'avg_probability' => $row['p_encuesta'],
                                                            'avg_impact' => $row['i_encuesta']
                                                    ]);
                                            }
                                            else
                                            {
                                                $evaluation_risk = $evaluation_risk->id;
                                            }
                                                        
                                            //vemos si existe en evaluation_risk_stakeholder
                                            $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                    ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                    ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                    ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                    ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                    ->select('evaluation_risk_stakeholder.id')
                                                    ->first();

                                            if (empty($evaluation_risk_stakehoder))
                                            {
                                                //insertamos en evaluation_risk_stakeholder
                                                DB::table('evaluation_risk_stakeholder')->insert([
                                                    'evaluation_risk_id'=>$evaluation_risk,
                                                    'user_id'=>Auth::user()->id,
                                                    'probability'=>$row['p_encuesta'],
                                                    'impact'=>$row['i_encuesta'],
                                                ]);
                                            }
                                        }

                                        //$fecha = date('Y-m-d H:i:s');

                                        if ($row['p_riesgo'] != '' && $row['p_riesgo'] != NULL && $row['i_riesgo'] != '' && $row['i_riesgo'] != NULL)
                                        {
                                            //primero creamos evaluación manual
                                            //seleccionamos evaluación (si es que existe)
                                            $eval_id1 = DB::table('evaluations')
                                                    ->where('created_at','=',$row['fecha_encuesta_2'])
                                                    ->select('id')
                                                    ->first();

                                            if (empty($eval_id1))
                                            {
                                                    $eval_id1 = DB::table('evaluations')->insertGetId([
                                                        'name' => 'Evaluación Manual',
                                                        'consolidation' => 1,
                                                        'description' => 'Evaluación Manual',
                                                        'created_at' => $row['fecha_encuesta_2'],
                                                        'updated_at' => $row['fecha_encuesta_2'],
                                                    ]);
                                            }
                                            else
                                            {
                                                    $eval_id1 = $eval_id1->id;
                                            }

                                            //vemos si ya existe evaluación para este riesgo y en esta evaluación (para el caso que los riesgos se repiten)
                                            $evaluation_risk = DB::table('evaluation_risk')
                                                        ->where('evaluation_id','=',$eval_id1)
                                                        ->where('organization_risk_id','=',$risk->org_risk_id)
                                                        ->select('id')
                                                        ->first();

                                            if (empty($evaluation_risk))
                                            {
                                                //insertamos riesgo evaluation_risk
                                                $evaluation_risk = DB::table('evaluation_risk')->insertGetId([
                                                            'evaluation_id' => $eval_id1,
                                                            'organization_risk_id' => $risk->org_risk_id,
                                                            'avg_probability' => $row['p_riesgo'],
                                                            'avg_impact' => $row['i_riesgo']
                                                    ]);
                                            }
                                            else
                                            {
                                                $evaluation_risk = $evaluation_risk->id;
                                            }
                                                        
                                            //vemos si existe en evaluation_risk_stakeholder
                                            $evaluation_risk_stake = DB::table('evaluation_risk_stakeholder')
                                                    ->join('evaluation_risk','evaluation_risk.id','=','evaluation_risk_stakeholder.evaluation_risk_id')
                                                    ->where('evaluation_risk_stakeholder.evaluation_risk_id','=',$evaluation_risk)
                                                    ->where('evaluation_risk_stakeholder.user_id','=',Auth::user()->id)
                                                    ->where('evaluation_risk.evaluation_id','=',$eval_id1)
                                                    ->select('evaluation_risk_stakeholder.id')
                                                    ->first();

                                            if (empty($evaluation_risk_stakehoder))
                                            {
                                                //insertamos en evaluation_risk_stakeholder
                                                DB::table('evaluation_risk_stakeholder')->insert([
                                                    'evaluation_risk_id'=>$evaluation_risk,
                                                    'user_id'=>Auth::user()->id,
                                                    'probability'=>$row['p_riesgo'],
                                                    'impact'=>$row['i_riesgo'],
                                                ]);
                                            }
                                        }
                                    }    
                                }
                            }
                            else if ($_POST['kind'] == 18) //Auditorías (genérico)
                            {
                                if ($row->getTitle() == 'Planes de Auditoría')
                                {
                                    foreach ($row as $row)
                                    {
                                        print_r($row);
                                    }
                                }
                                
                            }
                        });

                    });

                    if (Session::get('languaje') == 'en')
                    {
                        Session::flash('message','The document was successfully uploaded');
                    }
                    else
                    {
                        Session::flash('message','El documento fue cargado correctamente');
                    }
                }
            });

            return Redirect::to('importador');
        }
    }

    public function generarExcelConsolidado()
    {
        //try
        //{
            Excel::create('Reporte Consolidado '.date("d-m-Y"), function($excel) {

                // título excel
                $excel->setTitle('Reporte Consolidado');

                //creador y compañia
                $excel->setCreator('Sistema B-GRC')
                      ->setCompany('It Apps Bussiness Solutions');

                //descripción
                $excel->setDescription('Reporte consolidado de Riesgos, Controles, Hallazgos y Planes de acción');

                $excel->sheet('B-GRC', function($sheet) {
                    $home = new Home;
                    $datos = $home->reporteConsolidado();

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);

                    //editamos formato de salida de celdas
                    $sheet->cells('A1:AP1', function($cells) {
                            $cells->setBackground('#013ADF');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(16);
                    });

                    $sheet->freezeFirstRow();

                });

            })->export('xls');
        //}
        //catch (\Exception $e)
        //{
            //enviarMailSoporte($e);
            //return view('errors.query',['e' => $e]);
        //}
    }

    public function exportSessions()
    {
        if (!Auth::guest())
        {
           $logs = file_get_contents('../storage/logs/sessions.log');

            $rows = explode("[] []", $logs);

            $users = array();
            
            $i = 0;
            foreach ($rows as $row)
            {
                //Rut
                //echo $row.'<br>';
                $pos1 = explode('Rut: ',$row);
                if (isset($pos1[1]))
                {
                    $pos1 = explode(',',$pos1[1]);
                
                    $rut = $pos1[0];

                    //Nombre
                    $pos1 = explode('usuario ',$row);

                    if (strpos($pos1[1],' ,'))
                    {
                       $pos1 = explode(' ,',$pos1[1]); 
                    }
                    else if (strpos($pos1[1],','))
                    {
                       $pos1 = explode(',',$pos1[1]); 
                    }
                    
                    $nombre = $pos1[0];

                    //fecha y hora
                    $pos1 = explode('fecha ',$row);
                    $fecha = $pos1[1];
                    $fechatotal = explode(' a las ',$fecha);
                    $hora = $fechatotal[1];

                    //En fechatotal[0] igual está la hora, así que separamos por espacio
                    $fechatotal = explode(' ',$fechatotal[0]);

                    $fecha = $fechatotal[0  ];

                    //echo $rut.' ,,, '.$nombre.' ,,, '.$fecha.'<br>';

                    if ($rut != 16396924 && $rut != 14196805)
                    {
                        $users[$i] = [
                            'Rut' => $rut,
                            'Nombre' => $nombre,
                            'Fecha' => $fecha,
                            'Hora' => $hora 
                        ];

                    }
                    
                    $i += 1;
                }
            }

            global $users2;
            $users2 = $users;

            //print_r($users2);

            Excel::create('Reporte Inicios de Sesión '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Reporte Inicios de Sesión');

                    //creador y compañia
                    $excel->setCreator('Sistema B-GRC')
                          ->setCompany('It Apps Bussiness Solutions');

                    //descripción
                    $excel->setDescription('Reporte de inicios de sesión en sistema B-GRC');

                    $excel->sheet('B-GRC', function($sheet) {
                        $datos = $GLOBALS['users2'];

                        //$datos2 = json_decode($datos);
                        $sheet->fromArray($datos);

                        //editamos formato de salida de celdas
                        $sheet->cells('A1:D1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(16);
                        });

                        $sheet->freezeFirstRow();

                    });

            })->export('xls'); 
        }
    }

    public function generarExcelEncuesta($id)
    {
        if (!Auth::guest())
        {
            $poll = \Ermtool\Poll::find($id);

            $poll->created_at = date_create($poll->created_at);
            $poll->created_at = date_format($poll->created_at, 'd-m-Y');
            $questions = $poll->questions;
            $stakeholders = $poll->stakeholders;
            $datos = array();

            //$datos[0] = ['Encuesta: '.$poll->name];
            $i = 0;
            foreach ($stakeholders as $s)
            {
                foreach ($questions as $q)
                {
                    $a = DB::table('answers')
                            ->where('stakeholder_id','=',$s->id)
                            ->where('question_id','=',$q->id)
                            ->first(['answer']);

                    $datos[$i] = [
                        'rut' => $s->id,
                        'name' => $s->name.' '.$s->surnames,
                        'mail' => $s->mail,
                        'q' => $q->question,
                        'a' => $a->answer,
                    ];

                    $i += 1;
                }
            }
            global $datos2;
            $datos2 = $datos;
            $GLOBALS['poll2'] = $poll;

            Excel::create('Reporte Encuesta '.$GLOBALS['poll2']->name.' '.date("d-m-Y"), function($excel) {

                    // título excel
                    $excel->setTitle('Reporte Encuesta');

                    //creador y compañia
                    $excel->setCreator('Sistema B-GRC')
                          ->setCompany('It Apps Bussiness Solutions');

                    //descripción
                    $excel->setDescription('Reporte de encuesta B-GRC');

                    $excel->sheet('B-GRC', function($sheet) {
                        $sheet->mergeCells('A1:D1');
                        $datos = $GLOBALS['datos2'];
                        //$sheet->fromArray($datos);
                        $sheet->setWidth(array(
                            'A'     =>  15,
                            'B'     =>  25,
                            'C'     =>  25,
                            'D'     =>  50,
                            'E'     =>  95,
                        ));

                        $sheet->cells('A2:E100', function($cells) {
                            $cells->setValignment('center');
                            $cells->setAlignment('center');
                        });

                        $sheet->cell('A1', function($cell) {
                            $cell->setValue('Encuesta: '.$GLOBALS['poll2']->name);   
                        });

                        $sheet->cell('E1', function($cell) {
                            $cell->setValue('Fecha creación encuesta: '.$GLOBALS['poll2']->created_at);   
                        });

                        $sheet->cell('A2', function($cell) {$cell->setValue('ID Usuario');   });
                        $sheet->cell('B2', function($cell) {$cell->setValue('Nombre');   });
                        $sheet->cell('C2', function($cell) {$cell->setValue('Correo');   });
                        $sheet->cell('D2', function($cell) {$cell->setValue('Pregunta');   });
                        $sheet->cell('E2', function($cell) {$cell->setValue('Respuesta');   });
                        //editamos formato de salida de celdas
                        $sheet->cells('A1:E1', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(14);
                        });

                        $sheet->cells('A2:E2', function($cells) {
                                $cells->setBackground('#013ADF');
                                $cells->setFontColor('#ffffff');
                                $cells->setFontFamily('Calibri');
                                $cells->setFontWeight('bold');
                                $cells->setFontSize(14);
                        });

                        $sheet->setFreeze('A2');
                        $i = 3;
                        foreach ($datos as $d) 
                        {
                            $sheet->cell('A'.$i, $d['rut']); 
                            $sheet->cell('B'.$i, $d['name']); 
                            $sheet->cell('C'.$i, $d['mail']);
                            $sheet->cell('D'.$i, $d['q']); 
                            $sheet->cell('E'.$i, $d['a']); 
                            $i += 1;
                        }

                    });

            })->export('xls'); 
        }
    }
}
