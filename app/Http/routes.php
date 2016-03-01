<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//Route::post('posts/store-new-post', 'PostsController@store');
//Route::post('posts/{slug}', 'PostsController@update');

Route::get('/', [
	'as' => '/', 'uses' => 'HomeController@index'
]
);

Route::get('home',[
	'as' => '/', 'uses' => 'HomeController@index'
]
);

// ----RUTAS PARA GESTIÓN DE DATOS MAESTROS---- //

	//Rutas para CRUD + bloquear Organización//

Route::resource('organization','OrganizationController');

Route::get('organization.create', [
	'as' => 'organization.create', 'uses' => 'OrganizationController@create'
]);

Route::get('organization.edit.{id}', [
    'as' => 'organization.edit', 'uses' => 'OrganizationController@edit'
]);

Route::get('organization.bloquear.{id}', [
    'as' => 'organization.bloquear', 'uses' => 'OrganizationController@bloquear'
]);

Route::get('organization.verbloqueados', [
	'as' => 'organization.verbloqueados', 'uses' => 'OrganizationController@index'
]);

Route::get('organization.desbloquear{id}', [
	'as' => 'organization.desbloquear', 'uses' => 'OrganizationController@desbloquear'
]);

Route::put('organization.update.{id}', [
    'as' => 'organization.update', 'uses' => 'OrganizationController@update'
]);

	//Rutas para CRUD + bloquear Objetivos//

Route::resource('objetivos','ObjetivosController');

Route::get('objetivos.create', [
	'as' => 'objetivos.create', 'uses' => 'ObjetivosController@create'
]);

Route::get('objetivos.edit.{id}', [
    'as' => 'objetivos.edit', 'uses' => 'ObjetivosController@edit'
]);

Route::get('objetivos.bloquear.{id}', [
    'as' => 'objetivos.bloquear', 'uses' => 'ObjetivosController@bloquear'
]);

Route::get('objetivos.verbloqueados.{id}', [
	'as' => 'objetivos.verbloqueados', 'uses' => 'ObjetivosController@verbloqueados'
]);

Route::get('objetivos.desbloquear.{id}', [
	'as' => 'objetivos.desbloquear', 'uses' => 'ObjetivosController@desbloquear'
]);

Route::put('objetivos.update.{id}', [
    'as' => 'objetivos.update', 'uses' => 'ObjetivosController@update'
]);

//Rutas para CRUD + bloquear Procesos//

Route::resource('procesos','ProcesosController');

Route::get('procesos.create', [
	'as' => 'procesos.create', 'uses' => 'ProcesosController@create'
]);

Route::get('procesos.edit.{id}', [
	'as' => 'procesos.edit', 'uses' => 'ProcesosController@edit'
]);

Route::get('procesos.bloquear.{id}', [
	'as' => 'procesos.bloquear', 'uses' => 'ProcesosController@bloquear'
]);

Route::get('procesos.verbloqueados', [
	'as' => 'procesos.verbloqueados', 'uses' => 'ProcesosController@index'
]);

Route::get('procesos.desbloquear.{id}', [
	'as' => 'procesos.desbloquear', 'uses' => 'ProcesosController@desbloquear'
]);

Route::put('procesos.update.{id}', [
	'as' => 'procesos.update', 'uses' => 'ProcesosController@update'
]);

//Rutas para CRUD + bloquear Subprocesos//

Route::resource('subprocesos','SubprocesosController');

Route::get('subprocesos.create', [
	'as' => 'subprocesos.create', 'uses' => 'SubprocesosController@create'
]);

Route::get('subprocesos.edit.{id}', [
	'as' => 'subprocesos.edit', 'uses' => 'SubprocesosController@edit'
]);

Route::get('subprocesos.bloquear.{id}', [
	'as' => 'subprocesos.bloquear', 'uses' => 'SubprocesosController@bloquear'
]);

Route::get('subprocesos.verbloqueados', [
	'as' => 'subprocesos.verbloqueados', 'uses' => 'SubprocesosController@index'
]);

Route::get('subprocesos.desbloquear.{id}', [
	'as' => 'subprocesos.desbloquear', 'uses' => 'SubprocesosController@desbloquear'
]);

Route::put('subprocesos.update.{id}', [
	'as' => 'subprocesos.update', 'uses' => 'SubprocesosController@update'
]);

//Rutas para CRUD + bloquear Categorías de Riesgo//

Route::resource('categorias_riesgos','CategoriasRiesgosController');

Route::get('categorias_riesgos.create', [
	'as' => 'categorias_riesgos.create', 'uses' => 'CategoriasRiesgosController@create'
]);

Route::get('categorias_riesgos.edit.{id}', [
	'as' => 'categorias_riesgos.edit', 'uses' => 'CategoriasRiesgosController@edit'
]);

Route::get('categorias_riesgos.bloquear.{id}', [
	'as' => 'categorias_riesgos.bloquear', 'uses' => 'CategoriasRiesgosController@bloquear'
]);

Route::get('categorias_riesgos.verbloqueados', [
	'as' => 'categorias_riesgos.verbloqueados', 'uses' => 'CategoriasRiesgosController@index'
]);

Route::get('categorias_riesgos.desbloquear.{id}', [
	'as' => 'categorias_riesgos.desbloquear', 'uses' => 'CategoriasRiesgosController@desbloquear'
]);

Route::put('categorias_riesgos.update.{id}', [
	'as' => 'categorias_riesgos.update', 'uses' => 'CategoriasRiesgosController@update'
]);

//Rutas para CRUD + bloquear Categorías de Objetivo//

Route::resource('categorias_objetivos','CategoriasObjetivosController');

Route::get('categorias_objetivos.create', [
	'as' => 'categorias_objetivos.create', 'uses' => 'CategoriasObjetivosController@create'
]);

Route::get('categorias_objetivos.edit.{id}', [
	'as' => 'categorias_objetivos.edit', 'uses' => 'CategoriasObjetivosController@edit'
]);

Route::get('categorias_objetivos.bloquear.{id}', [
	'as' => 'categorias_objetivos.bloquear', 'uses' => 'CategoriasObjetivosController@bloquear'
]);

Route::get('categorias_objetivos.verbloqueados', [
	'as' => 'categorias_objetivos.verbloqueados', 'uses' => 'CategoriasObjetivosController@index'
]);

Route::get('categorias_objetivos.desbloquear.{id}', [
	'as' => 'categorias_objetivos.desbloquear', 'uses' => 'CategoriasObjetivosController@desbloquear'
]);

Route::put('categorias_objetivos.update.{id}', [
	'as' => 'categorias_objetivos.update', 'uses' => 'CategoriasObjetivosController@update'
]);

//Rutas para CRUD + bloquear Riesgos tipo//

Route::resource('riskstype','RiesgosTipoController');

Route::get('riskstype.create', [
	'as' => 'riskstype.create', 'uses' => 'RiesgosTipoController@create'
]);

Route::get('riskstype.edit.{id}', [
	'as' => 'riskstype.edit', 'uses' => 'RiesgosTipoController@edit'
]);

Route::get('riskstype.bloquear.{id}', [
	'as' => 'riskstype.bloquear', 'uses' => 'RiesgosTipoController@bloquear'
]);

Route::get('riskstype.verbloqueados', [
	'as' => 'riskstype.verbloqueados', 'uses' => 'RiesgosTipoController@index'
]);

Route::get('riskstype.desbloquear.{id}', [
	'as' => 'riskstype.desbloquear', 'uses' => 'RiesgosTipoController@desbloquear'
]);

Route::put('riskstype.update.{id}', [
	'as' => 'riskstype.update', 'uses' => 'RiesgosTipoController@update'
]);

//Rutas para CRUD + bloquear Stakeholders//

Route::resource('roles','RolesController');

Route::get('roles.create', [
	'as' => 'roles.create', 'uses' => 'RolesController@create'
]);

Route::get('roles.edit.{id}', [
	'as' => 'roles.edit', 'uses' => 'RolesController@edit'
]);

Route::get('roles.bloquear.{id}', [
	'as' => 'roles.bloquear', 'uses' => 'RolesController@bloquear'
]);

Route::get('roles.verbloqueados', [
	'as' => 'roles.verbloqueados', 'uses' => 'RolesController@index'
]);

Route::get('roles.desbloquear.{id}', [
	'as' => 'roles.desbloquear', 'uses' => 'RolesController@desbloquear'
]);

Route::put('roles.update.{id}', [
	'as' => 'roles.update', 'uses' => 'RolesController@update'
]);

//Rutas para CRUD + bloquear Stakeholders//

Route::resource('stakeholders','StakeholdersController');

Route::get('stakeholders.create', [
	'as' => 'stakeholders.create', 'uses' => 'StakeholdersController@create'
]);

Route::get('stakeholders.edit.{id}', [
	'as' => 'stakeholders.edit', 'uses' => 'StakeholdersController@edit'
]);

Route::get('stakeholders.bloquear.{id}', [
	'as' => 'stakeholders.bloquear', 'uses' => 'StakeholdersController@bloquear'
]);

Route::get('stakeholders.verbloqueados', [
	'as' => 'stakeholders.verbloqueados', 'uses' => 'StakeholdersController@index'
]);

Route::get('stakeholders.desbloquear.{id}', [
	'as' => 'stakeholders.desbloquear', 'uses' => 'StakeholdersController@desbloquear'
]);

Route::put('stakeholders.update.{id}', [
	'as' => 'stakeholders.update', 'uses' => 'StakeholdersController@update'
]);

//Rutas para CRUD + bloquear Causas//

Route::resource('causas','CausasController');

Route::get('causas.create', [
	'as' => 'causas.create', 'uses' => 'CausasController@create'
]);

Route::get('causas.edit.{id}', [
	'as' => 'causas.edit', 'uses' => 'CausasController@edit'
]);

Route::get('causas.bloquear.{id}', [
	'as' => 'causas.bloquear', 'uses' => 'CausasController@bloquear'
]);

Route::get('causas.verbloqueados', [
	'as' => 'causas.verbloqueados', 'uses' => 'CausasController@index'
]);

Route::get('causas.desbloquear.{id}', [
	'as' => 'causas.desbloquear', 'uses' => 'CausasController@desbloquear'
]);

Route::put('causas.update.{id}', [
	'as' => 'causas.update', 'uses' => 'CausasController@update'
]);

//Rutas para CRUD + bloquear Efectos//

Route::resource('efectos','EfectosController');

Route::get('efectos.create', [
	'as' => 'efectos.create', 'uses' => 'EfectosController@create'
]);

Route::get('efectos.edit.{id}', [
	'as' => 'efectos.edit', 'uses' => 'EfectosController@edit'
]);

Route::get('efectos.bloquear.{id}', [
	'as' => 'efectos.bloquear', 'uses' => 'EfectosController@bloquear'
]);

Route::get('efectos.verbloqueados', [
	'as' => 'efectos.verbloqueados', 'uses' => 'EfectosController@index'
]);

Route::get('efectos.desbloquear.{id}', [
	'as' => 'efectos.desbloquear', 'uses' => 'EfectosController@desbloquear'
]);

Route::put('efectos.update.{id}', [
	'as' => 'efectos.update', 'uses' => 'EfectosController@update'
]);

// ---- FIN RUTAS PARA GESTIÓN DE DATOS MAESTROS---- //

// ----RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

Route::resource('crear_encuesta','EncuestasController@create');

Route::post('encuesta.store','EncuestasController@store');

Route::get('enviar_encuesta','EncuestasController@enviar');

Route::post('identificacion.enviarCorreo', [
	'as' => 'identificacion.enviarCorreo', 'uses' => 'EncuestasController@enviarCorreo'
]);

Route::get('identificacion.encuesta.{id}', [
	'as' => 'identificacion.encuesta', 'uses' => 'EncuestasController@generarEncuesta'
]);

Route::post('identificacion.guardarEvaluacion.{id}', [
	'as' => 'identificacion.guardarEvaluacion', 'uses' => 'EncuestasController@guardarEvaluacion'
]);

Route::post('identificacion.encuestaRespondida', [
	'as' => 'identificacion.encuestaRespondida', 'uses' => 'EncuestasController@encuestaRespondida'
]);

// ----FIN RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

// ---- RUTAS PARA IDENTIFICACIÓN DE RIESGO ---- //

Route::resource('riesgos','RiesgosController');

Route::get('riesgos.create', [
	'as' => 'riesgos.create', 'uses' => 'RiesgosController@create'
]);

Route::get('riesgos.edit.{id}', [
	'as' => 'riesgos.edit', 'uses' => 'RiesgosController@edit'
]);

Route::put('riesgos.update.{id}', [
	'as' => 'riesgos.update', 'uses' => 'RiesgosController@update'
]);

Route::get('riesgos.setriesgotipo.{id}', [
	'as' => 'riesgos.setriesgotipo', 'uses' => 'RiesgosController@setRiesgoTipo'
]);

// ---- FIN RUTAS PARA IDENTIFICACIÓN DE RIESGO ---- //

// ---- RUTAS PARA EVALUACIÓN DE RIESGOS ---- //

Route::resource('evaluacion','EvaluacionRiesgosController');

Route::post('evaluacion.store','EvaluacionRiesgosController@store');

Route::get('evaluacion_encuestas', [
	'as' => 'evaluacion_encuestas', 'uses' => 'EvaluacionRiesgosController@encuestas'
]);

Route::get('evaluacion.ver.{id}', [
	'as' => 'evaluacion.ver', 'uses' => 'EvaluacionRiesgosController@show'
]);

Route::get('evaluacion_encuestas.enviar.{id}', [
	'as' => 'evaluacion_encuestas.enviar', 'uses' => 'EvaluacionRiesgosController@enviar'
]);

Route::get('evaluacion_encuestas.consolidar.{id}', [
	'as' => 'evaluacion_encuestas.consolidar', 'uses' => 'EvaluacionRiesgosController@getRiesgosConsolidar'
]);

Route::post('evaluacion_encuestas.consolidar2.{id}', [
	'as' => 'evaluacion_encuestas.consolidar2', 'uses' => 'EvaluacionRiesgosController@consolidar'
]);

Route::get('evaluacion_encuestas.{id}', [
	'as' => 'evaluacion_encuestas.show', 'uses' => 'EvaluacionRiesgosController@show'
]);

Route::get('evaluacion.encuesta.{id}', [
	'as' => 'evaluacion.encuesta', 'uses' => 'EvaluacionRiesgosController@generarEncuesta'
]);

Route::post('evaluacion.guardarEvaluacion.{id}', [
	'as' => 'evaluacion.guardarEvaluacion', 'uses' => 'EvaluacionRiesgosController@guardarEvaluacion'
]);

Route::post('evaluacion.enviarCorreo', [
	'as' => 'evaluacion.enviarCorreo', 'uses' => 'EvaluacionRiesgosController@enviarCorreo'
]);

Route::get('evaluacion_manual', [
	'as' => 'evaluacion_manual', 'uses' => 'EvaluacionRiesgosController@evaluacionManual'
]);

// ----Rutas para gestión de controles---- //

Route::get('controles', [
	'as' =>'controles', 'uses' => 'ControlesController@index']);

Route::get('controles.create', [
	'as' =>'controles.create', 'uses' => 'ControlesController@create']);

Route::post('controles.store', [
	'as' =>'controles.store', 'uses' => 'ControlesController@store']);

Route::put('controles.update.{id}', [
	'as' => 'controles.update', 'uses' => 'ControlesController@update'
]);

//ruta para seleccionar a través de JSON los controles de negocio o de procesos en campo select
Route::get('controles.subneg.{value}', [
	'as' => 'controles.subneg', 'uses' => 'ControlesController@subneg'
]);

Route::get('controles.edit.{id}', [
	'as' =>'controles.edit', 'uses' => 'ControlesController@edit']);

Route::get('controles.docs.{id}', [
	'as' =>'controles.docs', 'uses' => 'ControlesController@docs']);

// ----Rutas para reportes básicos---- //

Route::get('heatmap', [
	'as' =>'heatmap', 'uses' => 'EvaluacionRiesgosController@listHeatmap']);

Route::post('heatmap.{id}', [
	'as' => 'heatmap2', 'uses' => 'EvaluacionRiesgosController@generarHeatmap'
]);

Route::get('encuestas', [
	'as' => 'encuestas', 'uses' => 'EncuestasController@verEncuestas']);

Route::get('encuestas.show.{id}', [
	'as' => 'encuestas.show', 'uses' => 'EncuestasController@show']);

Route::get('matrices', [
	'as' => 'matrices', 'uses' => 'ControlesController@matrices']);

Route::get('matriz_riesgos', [
	'as' => 'matriz_riesgos', 'uses' => 'RiesgosController@matrices']);

//ruta para generar matriz de riesgos a través de JSON
Route::get('genmatrizriesgos.{value}', [
	'as' => 'genmatrizriesgos', 'uses' => 'RiesgosController@generarMatriz']);

//ruta para generar matriz a través de JSON
Route::get('genmatriz.{value}', [
	'as' => 'genmatriz', 'uses' => 'ControlesController@generarMatriz']);


//------ Rutas para trabajar con Excel ------//

Route::get('genexcel.{value}', [
	'as' => 'genexcel', 'uses' => 'ExcelController@generarExcel']);

//------ Rutas para auditoría de riesgos ------//

Route::get('plan_auditoria', [
	'as' =>'plan_auditoria', 'uses' => 'AuditoriasController@index']);

Route::get('plan_auditoria.create', [
	'as' =>'plan_auditoria.create', 'uses' => 'AuditoriasController@create']);

Route::post('plan_auditoria.create2', [
	'as' =>'plan_auditoria.create2', 'uses' => 'AuditoriasController@datosAuditoria']);

Route::post('agregar_plan.{id}', [
	'as' => 'agregar_plan', 'uses' => 'AuditoriasController@store'
]);

Route::get('plan_auditoria.show.{id}', [
	'as' => 'plan_auditoria.show', 'uses' => 'AuditoriasController@show'
]);

Route::get('auditorias', [
	'as' =>'auditorias', 'uses' => 'AuditoriasController@indexAuditorias']);

Route::get('crear_auditoria', [
	'as' =>'crear_auditoria', 'uses' => 'AuditoriasController@createAuditoria']);

Route::post('agregar_auditoria.{id}', [
	'as' => 'agregar_auditoria', 'uses' => 'AuditoriasController@storeAuditoria'
]);

Route::get('auditorias.show.{id}', [
	'as' => 'auditorias.show', 'uses' => 'AuditoriasController@showAuditoria'
]);

Route::get('crear_pruebas', [
	'as' => 'crear_pruebas', 'uses' => 'AuditoriasController@createPruebas']);

Route::get('pruebas', [
	'as' => 'pruebas', 'uses' => 'AuditoriasController@Pruebas']);

Route::post('agregar_prueba.{id}', [
	'as' => 'agregar_prueba', 'uses' => 'AuditoriasController@storePrueba'
]);

Route::get('ejecutar_pruebas', [
	'as' => 'ejecutar_pruebas', 'uses' => 'AuditoriasController@ejecutar']);

Route::post('agregar_ejecucion', [
	'as' => 'agregar_ejecucion', 'uses' => 'AuditoriasController@storeEjecution']);

Route::get('supervisar', [
	'as' => 'supervisar', 'uses' => 'AuditoriasController@supervisar']);

Route::post('agregar_supervision', [
	'as' => 'agregar_supervision', 'uses' => 'AuditoriasController@storeSupervision']);

//ruta para obtener datos de plan de auditoría anterior 
Route::get('auditorias.get_audit_plan.{org}', [
	'as' => 'auditorias.get_audit_plan', 'uses' => 'AuditoriasController@getAuditPlan']);

//ruta para obtener objetivos al crear un plan de auditoría
Route::get('auditorias.objetivos.{org}', [
	'as' => 'auditorias.objetivos', 'uses' => 'AuditoriasController@getObjetivos']);

//ruta para obtener riesgos de negocio al crear un plan de auditoría
Route::get('auditorias.objective_risk.{org}', [
	'as' => 'auditorias.objective_risk', 'uses' => 'AuditoriasController@getRiesgosObjetivos']);

//ruta para obtener riesgos de negocio al crear un plan de auditoría
Route::get('auditorias.risk_subprocess.{org}', [
	'as' => 'auditorias.risk_subprocess', 'uses' => 'AuditoriasController@getRiesgosProcesos']);

//ruta para obtener todos los stakeholders menos el auditor resposable al crear un plan de auditoría
Route::get('auditorias.stakeholders.{id}', [
	'as' => 'auditorias.stakeholders', 'uses' => 'AuditoriasController@getStakeholders']);

//ruta para obtener datos de prueba de auditoría seleccionada
Route::get('auditorias.get_audit_test.{id}', [
	'as' => 'auditorias.get_audit_test', 'uses' => 'AuditoriasController@getAuditTest']);

//ruta para obtener datos de prueba de auditoría seleccionada
Route::get('auditorias.get_audit_tests2.{id}', [
	'as' => 'auditorias.get_audit_tests2', 'uses' => 'AuditoriasController@getAuditTest2']);

//ruta para obtener controles de negocio asociados a un plan de auditoría
//(según los objetivos corporativos que contemple este plan)
Route::get('auditorias.objective_controls.{id}', [
	'as' => 'auditorias.controls', 'uses' => 'AuditoriasController@getObjectiveControls']);

//ruta para obtener controles de proceso asociados a un plan de auditoría
//(según los objetivos corporativos que contemple este plan)
Route::get('auditorias.subprocess_controls.{id}', [
	'as' => 'auditorias.controls', 'uses' => 'AuditoriasController@getSubprocessControls']);

//ruta para obtener auditorias según el plan seleccionado (al crear prueba de auditoría)
Route::get('auditorias.auditorias.{id}', [
	'as' => 'auditorias.auditorias', 'uses' => 'AuditoriasController@getAudits']);

//ruta para obtener pruebas segun plan + auditoría
Route::get('auditorias.getpruebas.{id}', [
	'as' => 'auditorias.getpruebas', 'uses' => 'AuditoriasController@getTests']);

//ruta para obtener issues de una prueba (si es que tiene)
Route::get('auditorias.get_issue.{id}', [
	'as' => 'auditorias.get_issue', 'uses' => 'AuditoriasController@getIssue']);

//ruta para obtener notas de una prueba de auditoría
Route::get('auditorias.get_notes.{id}', [
	'as' => 'auditorias.get_notes', 'uses' => 'AuditoriasController@getNotes']);

//ruta para obtener notas de una prueba de auditoría
Route::post('auditorias.guardar_nota', [
	'as' => 'auditorias.guardar_nota', 'uses' => 'AuditoriasController@storeNote']);


// ---- Rutas adicionales del framework ----//

/*
Route::get('breweries', ['middleware' => 'cors', function()
{
    return \Response::json(\App\Brewery::with('beers', 'geocode')->paginate(10), 200);
}]);

*/

