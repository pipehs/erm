<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Ermtool\Http\Controllers\ControlesController as Controles; //Para poder generar matriz de control y exportarla
use Ermtool\Http\Controllers\RiesgosController as Riesgos; //Para poder generar matriz de riesgo y exportarla
use Ermtool\Http\Controllers\AuditoriasController as Auditorias;
use Ermtool\Http\Controllers\PlanesAccionController as PlanesAccion;
use Ermtool\Http\Controllers\IssuesController as Issues;

class ExcelController extends Controller
{
    public function generarExcel($value)
    {

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
                    $datos = $control->generarMatriz(0);

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

                });

            })->export('xlsx');
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
                    $datos = $control->generarMatriz(1);

                    //$datos2 = json_decode($datos);
                    $sheet->fromArray($datos);
                    $sheet->setAutoFilter();

                    //editamos formato de salida de celdas
                    $sheet->cells('A1:N1', function($cells) {
                            $cells->setBackground('#013ADF');
                            $cells->setFontColor('#ffffff');
                            $cells->setFontFamily('Calibri');
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(16);
                    });

                });

            })->export('xlsx');
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
                    $datos = $riesgo->generarMatriz(0);

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

                });

            })->export('xlsx');
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
                    $datos = $riesgo->generarMatriz(1);

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

                });

            })->export('xlsx');
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

                });

            })->export('xlsx');
    }

    public function generarExcelIssue($type)
    {
        //generamos variable global para usarla en la función excel
        global $id;

        $id = $type;

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
                    $datos = $issue->generarReporteIssues($GLOBALS['id']);

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

                });

            })->export('xlsx');
    }
}
