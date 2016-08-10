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

class ExcelController extends Controller
{
    public function generarExcel($value,$org)
    {
        global $id_org;
        $id_org = $org;
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
                    $datos = $riesgo->generarMatriz(0,$GLOBALS['id_org']);

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);

                    //editamos formato de salida de celdas
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
        else if ($value == 4) //se genera excel para riesgos de proceso
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
                    $datos = $riesgo->generarMatriz(1,$GLOBALS['id_org']);

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

    public function generarExcelPlan($org)
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

    public function generarExcelIssue($type,$org)
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

    public function generarExcelAudit($org)
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

    public function generarExcelGraficos($id)
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
        */
        global $id2;
        $id2 = $id;

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
                    $datos = $control->indexGraficos($GLOBALS['id2']);
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
                    $datos = $control->indexGraficos($GLOBALS['id2']);
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
                    $datos = $control->indexGraficos($GLOBALS['id2']);
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
                    $datos = $control->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
                    $datos = $plan->indexGraficos($GLOBALS['id2']);
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
}
