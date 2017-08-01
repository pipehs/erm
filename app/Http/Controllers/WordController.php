<?php

//Utilizado para reporte total

namespace Ermtool\Http\Controllers;
use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;
use DB;
use Session;
use Redirect;
use Storage;
use DateTime;
use Auth;
use ArrayObject;
use Ermtool\Http\Controllers\GestionEstrategicaController as Estrategia;
use Ermtool\Http\Controllers\ObjetivosController as Objetivos;
use Ermtool\Http\Controllers\ProcesosController as Procesos;
use Ermtool\Http\Controllers\SubprocesosController as Suprocesos;
use Ermtool\Http\Controllers\RiesgosController as Riesgos;
use Ermtool\Http\Controllers\KriController as Kri;
use Ermtool\Http\Controllers\ControlesController as Controles;
use Ermtool\Http\Controllers\AuditoriasController as Auditorias;
use Ermtool\Http\Controllers\IssuesController as Issues;
use Ermtool\Http\Controllers\PlanesAccionController as PlanesAccion;


//15-05-2017: MONOLOG
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Log;

class WordController extends Controller
{
    public function index()
    {
        if (Auth::guest())
        {
            return view('login');
        }
        else
        {
            $organizations = \Ermtool\Organization::where('status',0)->lists('name','id');

            if (Session::get('languaje') == 'en')
            {
                return view('en.reportes.reporte_personalizado',['organizations' => $organizations]);
            }
            else
            {
                return view('reportes.reporte_personalizado',['organizations' => $organizations]);
            }
        }
    }

    public function genReport()
    {
        //primero que todo, verificamos que el usuario haya ingresado a lo menos un tipo para reporte
        if (isset($_POST['strategic_plan']) || isset($_POST['processes']) || isset($_POST['risks']) || isset($_POST['controls']) || isset($_POST['audit_plans']) || isset($_POST['issues']))
        {
            $word = new \PhpOffice\PhpWord\PhpWord();
            $section = $word->createSection();

            $word->setDefaultFontName('Verdana');
            $word->setDefaultFontSize(10);

            //estilos
            $titleStyle = array('size' => 18, 'color' => '045FB4');
            $subTitle1 = array('size' => 16,'bold' => true);
            $subTitle2 = array('size' => 14,'bold' => true);
            $subsubTitle = array('bold' => true);

            //estilos de tablas
            $tableStyleName = 'Custom Report Horizontal';
            $tableStyleName2 = 'Custom Report Vertical';
            $tableStyle = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 40);
            $tableFirstRowStyle = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true);
            $tableFirstRowStyle2 = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF','bold' => true,'size' => 7);
            $tableCellStyle = array('valign' => 'center');
            $tableCellStyleBold = array('valign' => 'center','bold' => true);
            $tableCellBtlrStyle = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
            $tableFontStyle = array('bold' => false);
            $tableFontStyle2 = array('bold' => false, 'size' => 7);
            $word->addTableStyle($tableStyleName, $tableStyle, $tableCellStyle);
            $word->addTableStyle($tableStyleName2, $tableStyle, $tableFirstRowStyle);

            //agregamos título que envío el usuario
            $section->addText(
                $_POST['name'],$titleStyle
            );

            //ahora vemos cada uno de los elementos
            if (isset($_POST['strategic_plans']))
            {
                $section->addText(
                    'Gestión estratégica', $subTitle1
                );

                $section->addTextBreak(1);

                $section->addText(
                    'Plan estratégico Vigente', $subTitle2
                );

                //obtenemos plan estratégico vigente
                $plan = \Ermtool\Strategic_plan::getActivePlan($_POST['organization_id']);

                $table1 = $section->addTable($tableStyleName);

                $table1->addRow();
                $table1->addCell(300)->addText('Nombre',$tableCellStyleBold);
                $table1->addCell(4000)->addText($plan->name,$tableCellStyle);

                if ($plan->comments == '')
                {
                    $plan->comments = 'No se han agregado comentarios';
                }

                $table1->addRow();
                $table1->addCell(300)->addText('Comentarios',$tableCellStyleBold);
                $table1->addCell(4000)->addText($plan->comments,$tableCellStyle);

                $init = explode('-',$plan->initial_date);
                $init = $init[2].'-'.$init[1].'-'.$init[0];

                $fin = explode('-',$plan->final_date);
                $fin = $fin[2].'-'.$fin[1].'-'.$fin[0];

                $table1->addRow();
                $table1->addCell(300)->addText('Fecha inicial',$tableCellStyleBold);
                $table1->addCell(4000)->addText($init,$tableCellStyle);

                $table1->addRow();
                $table1->addCell(300)->addText('Fecha final',$tableCellStyleBold);
                $table1->addCell(4000)->addText($fin,$tableCellStyle);

                $section->addTextBreak(1);

                $section->addText(
                    'Objetivos estratégicos', $subsubTitle
                );

                //obtenemos objetivos vigentes del plan
                $objs = \Ermtool\Objective::getObjectives($plan->id);

                //creamos tabla de objetivos
                $table2 = $section->addTable($tableStyleName2);

                $table2->addRow();
                $table2->addCell(300)->addText('Código',$tableFirstRowStyle);
                $table2->addCell(300)->addText('Nombre',$tableFirstRowStyle);
                $table2->addCell(4000)->addText('Descripción',$tableFirstRowStyle);
                $table2->addCell(300)->addText('Perspectiva',$tableFirstRowStyle);
                $table2->addCell(300)->addText('Perspectiva secundaria',$tableFirstRowStyle);

                foreach ($objs as $obj)
                {
                    $table2->addRow();
                    $table2->addCell(300)->addText($obj->code,$tableFontStyle);
                    $table2->addCell(300)->addText($obj->name,$tableFontStyle);
                    $table2->addCell(4000)->addText($obj->description,$tableFontStyle);

                    //seteamos perspectivas
                    switch ($obj->perspective) {
                        case 1:
                            $p = 'Financiera';
                            //seteamos perspectiva secundaria
                            if ($obj->perspective2 == 1)
                            {
                                $p2 = 'Productividad';
                            }
                            else if ($obj->perspective2 == 2)
                            {
                                $p2 = 'Aumento';
                            }
                            break;
                        case 2:
                            $p = 'Procesos';
                            //seteamos perspectiva secundaria
                            if ($obj->perspective2 == 1)
                            {
                                $p2 = 'Gestión operacional';
                            }
                            else if ($obj->perspective2 == 2)
                            {
                                $p2 = 'Gestión de clientes';
                            }
                            else if ($obj->perspective2 == 3)
                            {
                                $p2 = 'Gestión de innovación';
                            }
                            else if ($obj->perspective2 == 4)
                            {
                                $p2 = 'Reguladores sociales';
                            }
                            break;
                        case 3:
                            $p = 'Clientes';
                            $p2 = '';
                            break;
                        case 4:
                            $p = 'Aprendizaje';
                            //seteamos perspectiva secundaria
                            if ($obj->perspective2 == 1)
                            {
                                $p2 = 'Capital humano';
                            }
                            else if ($obj->perspective2 == 2)
                            {
                                $p2 = 'Capital de información';
                            }
                            else if ($obj->perspective2 == 3)
                            {
                                $p2 = 'Capital organizativo';
                            }
                            break;
                        default:
                            $p = 'No definida';
                            break;
                    }
                    $table2->addCell(300)->addText($p,$tableFontStyle);
                    $table2->addCell(300)->addText($p2,$tableFontStyle);
                }

                //kpis asociados al plan
                $section->addText(
                    'KPI\'s', $subsubTitle
                );

                
            }

            $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
            $objWriter->save($_POST['name'].'.docx');
            
            //generamos doc para guardar
            $file_url = $_POST['name'].'.docx';
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary"); 
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
            readfile($file_url); // do the double-download-dance (dirty but worky)

            //ahora borramos archivos temporales
            unlink($_POST['name'].'.docx');
            
        }
        else
        {
            Session::flash('message','Debe seleccionar a lo menos un elemento');
            return Redirect::to('reporte_personalizado');
        }
        

    }


}
