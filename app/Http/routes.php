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

//----- AUTENTICACIÓN -----//
Route::get('/',[
	'as' => '/', 'uses' => 'HomeController@home'
]);

//Ruta para redirigir a vista de errores (de esta manera no se envía correo 2 veces)
Route::get('errors.query', function(){
	return View::make('errors.query');
});

//Route::get('auth/login', 'Auth\AuthController@getLogin');
//Route::post('auth/login', ['as' =>'auth/login', 'uses' => 'Auth\AuthController@postLogin']);
//Route::get('auth/logout', ['as' => 'auth/logout', 'uses' => 'Auth\AuthController@getLogout']);

//Route::post('auth/login', [ 'as' => 'login', 'uses' => 'Auth\AuthController@postLogin']);

Route::resource('log','LogController');
Route::get('logout','LogController@logout');

Route::get('home',[
	'as' => 'home', 'uses' => 'HomeController@index'
]
);

// ---- RUTAS PARA GESTIONAR CONTROLLED RISK CRITERIA ---- //
Route::get('controlled_risk_criteria', [
	'as' => 'controlled_risk_criteria', 'uses' => 'ControlesController@controlledRiskCriteria'
	]);

Route::post('criteria.update', [
    'as' => 'criteria.update', 'uses' => 'ControlesController@updateControlledRiskCriteria'
]);

// ---- RUTAS DE CONFIGURACIÓN ---- //
Route::get('configuration.create', [
	'as' => 'configuration.create', 'uses' => 'ConfigurationController@create'
	]);

Route::post('configuration.store', [
	'as' => 'configuration.store', 'uses' => 'ConfigurationController@store'
	]);

Route::get('configuration.edit', [
	'as' => 'configuration.edit', 'uses' => 'ConfigurationController@edit'
	]);

Route::post('configuration.update', [
	'as' => 'configuration.update', 'uses' => 'ConfigurationController@update'
	]);

// ---- FIN RUTAS DE CONFIGURACIÓN ---- //

// ---- RUTA PARA CREAR USUARIO ---- //
Route::get('usuario.create', [
	'as' => 'usuario.create', 'uses' => 'LogController@createUser'
	]);

Route::post('usuario.store', [
	'as' => 'usuario.store', 'uses' => 'LogController@storeUser'
	]);

Route::get('usuarios', [
	'as' => 'usuarios', 'uses' => 'LogController@index'
]);

Route::get('usuarios.edit.{id}', [
	'as' => 'usuarios.edit', 'uses' => 'LogController@edit'
]);

Route::get('usuarios.destroy.{id}', [
	'as' => 'usuarios.destroy', 'uses' => 'LogController@destroy'
]);

Route::put('usuarios.update.{id}', [
    'as' => 'usuarios.update', 'uses' => 'LogController@update'
]);

Route::get('cambiopass', [
	'as' => 'cambiopass', 'uses' => 'LogController@changePass'
	]);

Route::post('updatepass', [
	'as' => 'updatepass', 'uses' => 'LogController@storeNewPass'
	]);

Route::get('help', [
	'as' => 'help', 'uses' => 'HomeController@help'
	]);

Route::get('pdf_manual', [
	'as' => 'pdf_manual', 'uses' => 'HomeController@pdfHelp'
	]);

Route::get('support', [
	'as' => 'support', 'uses' => 'HomeController@support'
	]);

Route::post('support.store', [
	'as' => 'support.store', 'uses' => 'HomeController@supportStore'
	]);

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

Route::get('organization.desbloquear.{id}', [
	'as' => 'organization.desbloquear', 'uses' => 'OrganizationController@desbloquear'
]);

Route::put('organization.update.{id}', [
    'as' => 'organization.update', 'uses' => 'OrganizationController@update'
]);

Route::get('organization.destroy.{id}', [
	'as' => 'organization.destroy', 'uses' => 'OrganizationController@destroy'
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

Route::get('objetivos_plan.verbloqueados.{strategic_plan_id}', [
	'as' => 'objetivos_plan.verbloqueados', 'uses' => 'ObjetivosController@objetivosPlan'
]);

Route::get('objetivos.desbloquear.{id}', [
	'as' => 'objetivos.desbloquear', 'uses' => 'ObjetivosController@desbloquear'
]);

Route::put('objetivos.update.{id}', [
    'as' => 'objetivos.update', 'uses' => 'ObjetivosController@update'
]);

Route::get('objetivos.destroy.{id}', [
    'as' => 'objetivos.destroy', 'uses' => 'ObjetivosController@destroy'
]);

//Index de objetivos seleccionando a través de un plan
Route::get('objetivos_plan.{strategic_id}', [
	'as' => 'objetivos_plan', 'uses' => 'ObjetivosController@objetivosPlan']);

//Selecciona a través de JSON los objetivos a los que un objetivo puede impactar (según perspectiva)
Route::get('objetivos.objectives_impact.{strategic_plan_id},{perspective}', [
    'as' => 'objetivos.objectives_impact', 'uses' => 'ObjetivosController@getObjectivesImpact'
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

Route::get('procesos.destroy.{id}', [
	'as' => 'procesos.destroy', 'uses' => 'ProcesosController@destroy'
]);

//ACT 08-06-18: Ruta para asignar responsables (ACT 13-06-18: y también otros datos) de proceso
Route::get('procesos.attributes.{id}', [
	'as' => 'procesos.attributes', 'uses' => 'ProcesosController@attributes'
]);

Route::post('procesos.assign_attributes', [
	'as' => 'procesos.assign_attributes', 'uses' => 'ProcesosController@assignAttributes'
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

Route::get('subprocesos.destroy.{id}', [
	'as' => 'subprocesos.destroy', 'uses' => 'SubprocesosController@destroy'
]);

//ACT 08-06-18: Ruta para asignar responsables (ACT 13-06-18: y también otros datos) de proceso
Route::get('subprocesos.attributes.{id}', [
	'as' => 'subprocesos.attributes', 'uses' => 'SubprocesosController@attributes'
]);

Route::post('subprocesos.assign_attributes', [
	'as' => 'subprocesos.assign_attributes', 'uses' => 'SubprocesosController@assignAttributes'
]);

//Rutas para CRUD + bloquear Categorías de Riesgo//

Route::resource('categorias_risks','CategoriasRiesgosController');

Route::get('categorias_risks.create', [
	'as' => 'categorias_risks.create', 'uses' => 'CategoriasRiesgosController@create'
]);

Route::get('categorias_risks.edit.{id}', [
	'as' => 'categorias_risks.edit', 'uses' => 'CategoriasRiesgosController@edit'
]);

Route::get('categorias_risks.bloquear.{id}', [
	'as' => 'categorias_risks.bloquear', 'uses' => 'CategoriasRiesgosController@bloquear'
]);

Route::get('categorias_risks.verbloqueados', [
	'as' => 'categorias_risks.verbloqueados', 'uses' => 'CategoriasRiesgosController@index'
]);

Route::get('categorias_risks.desbloquear.{id}', [
	'as' => 'categorias_risks.desbloquear', 'uses' => 'CategoriasRiesgosController@desbloquear'
]);

Route::put('categorias_risks.update.{id}', [
	'as' => 'categorias_risks.update', 'uses' => 'CategoriasRiesgosController@update'
]);

Route::get('categorias_risks.destroy.{id}', [
	'as' => 'categorias_risks.destroy', 'uses' => 'CategoriasRiesgosController@destroy'
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

Route::get('categorias_objetivos.destroy.{id}', [
	'as' => 'categorias_objetivos.destroy', 'uses' => 'CategoriasObjetivosController@destroy'
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

Route::get('riskstype.destroy.{id}', [
	'as' => 'riskstype.destroy', 'uses' => 'RiesgosTipoController@destroy'
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

Route::get('roles.destroy.{id}', [
	'as' => 'roles.destroy', 'uses' => 'RolesController@destroy'
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

Route::get('get_stakeholders.{org}', [
	'as' => 'get_stakeholders', 'uses' => 'StakeholdersController@getStakeholders'
]);

Route::get('stakeholders.destroy.{id}', [
	'as' => 'stakeholders.destroy', 'uses' => 'StakeholdersController@destroy'
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

Route::get('causas.destroy.{id}', [
	'as' => 'causas.destroy', 'uses' => 'CausasController@destroy'
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

Route::get('efectos.destroy.{id}', [
	'as' => 'efectos.destroy', 'uses' => 'EfectosController@destroy'
]);

// ---- FIN RUTAS PARA GESTIÓN DE DATOS MAESTROS---- //

// ----RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

Route::resource('crear_encuesta','EncuestasController@create');

Route::post('encuesta.store', [
	'as' => 'encuesta.store', 'uses' => 'EncuestasController@store'
]);

Route::get('enviar_encuesta','EncuestasController@enviar');

Route::post('identificacion.enviarCorreo', [
	'as' => 'identificacion.enviarCorreo', 'uses' => 'EncuestasController@enviarCorreo'
]);

Route::get('identificacion.encuesta.{id}', [
	'as' => 'identificacion.encuesta', 'uses' => 'EncuestasController@verificadorUserEncuesta'
]);

Route::get('identificacion.resp_encuesta.{id}', [
	'as' => 'identificacion.resp_encuesta', 'uses' => 'EncuestasController@generarEncuesta'
]);

Route::post('identificacion.guardarEvaluacion.{id}', [
	'as' => 'identificacion.guardarEvaluacion', 'uses' => 'EncuestasController@guardarEvaluacion'
]);

Route::put('identificacion.updateEvaluacion.{id}', [
	'as' => 'identificacion.updateEvaluacion', 'uses' => 'EncuestasController@updateEvaluacion'
]);

Route::post('identificacion.encuestaRespondida', [
	'as' => 'identificacion.encuestaRespondida', 'uses' => 'EncuestasController@encuestaRespondida'
]);

//ruta para usuarios invitados para saber que se guardo o actualizó encuesta
Route::get('encuestaresp', function(){
   return View::make('evaluacion.encuestaresp');
});


Route::get('encuestas', [
	'as' => 'encuestas', 'uses' => 'EncuestasController@verEncuestas']);

Route::get('encuestas2.{id}', [
	'as' => 'encuestas2', 'uses' => 'EncuestasController@encuestas2']);


Route::get('encuestas.destroy.{id}', [
	'as' => 'encuestas.destroy', 'uses' => 'EncuestasController@destroy']);

//Lista de encuestas
Route::get('ver_encuestas', [
	'as' => 'ver_encuestas', 'uses' => 'EncuestasController@showEncuesta']);

//encuesta seleccionada a ver
Route::get('ver_encuesta.{id}', [
	'as' => 'ver_encuesta', 'uses' => 'EncuestasController@showEncuesta2']);

//Muestra encuesta y respuestas enviadas por un usuario
Route::get('encuestas.show.{id}', [
	'as' => 'encuestas.show', 'uses' => 'EncuestasController@show']);

// ----FIN RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

// ---- RUTAS PARA IDENTIFICACIÓN DE RIESGO ---- //

Route::resource('riesgos','RiesgosController');

Route::get('riesgos.index2', [
	'as' => 'riesgos.index2', 'uses' => 'RiesgosController@index2'
]);

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

//ACT 06-06-18: Bloquear para riesgos
Route::get('riesgos.bloquear.{id}.{org}', [
	'as' => 'riesgos.bloquear', 'uses' => 'RiesgosController@bloquear']);

Route::get('riesgos.destroy.{id}.{org}', [
	'as' => 'riesgos.destroy', 'uses' => 'RiesgosController@destroy']);

Route::get('riesgos.destroy.{id}', [
	'as' => 'riesgos.destroy', 'uses' => 'RiesgosController@destroy2']);
// ---- FIN RUTAS PARA IDENTIFICACIÓN DE RIESGO ---- //

// ---- RUTAS PARA EVALUACIÓN DE RIESGOS ---- //

Route::resource('evaluacion','EvaluacionRiesgosController');

Route::post('evaluacion.store','EvaluacionRiesgosController@store');

Route::get('evaluacion_agregadas', [
	'as' => 'evaluacion_agregadas', 'uses' => 'EvaluacionRiesgosController@encuestas'
]);

Route::get('evaluacion.ver.{id}', [
	'as' => 'evaluacion.ver', 'uses' => 'EvaluacionRiesgosController@show'
]);

Route::get('evaluacion.enviar.{id}', [
	'as' => 'evaluacion.enviar', 'uses' => 'EvaluacionRiesgosController@enviar'
]);

Route::get('evaluacion.consolidar.{id}', [
	'as' => 'evaluacion.consolidar', 'uses' => 'EvaluacionRiesgosController@getRiesgosConsolidar'
]);

Route::post('evaluacion.consolidar2.{id}', [
	'as' => 'evaluacion.consolidar2', 'uses' => 'EvaluacionRiesgosController@consolidar'
]);

Route::get('evaluacion.{id}', [
	'as' => 'evaluacion.show', 'uses' => 'EvaluacionRiesgosController@show'
]);

Route::get('evaluacion_delete.{id}', [
	'as' => 'evaluacion_delete', 'uses' => 'EvaluacionRiesgosController@delete'
]);

Route::get('evaluacion_encuesta.{id}', [
	'as' => 'evaluacion_encuesta', 'uses' => 'EvaluacionRiesgosController@verificadorUserEncuesta'
]);

Route::post('evaluacion.encuesta2.{id}', [
	'as' => 'evaluacion.encuesta2', 'uses' => 'EvaluacionRiesgosController@generarEncuesta'
]);

Route::get('ver_respuestas.{eval_id},{rut}', [
	'as' => 'ver_respuestas', 'uses' => 'EvaluacionRiesgosController@verRespuestas'
]);

Route::post('evaluacion.guardarEvaluacion.{id}', [
	'as' => 'evaluacion.guardarEvaluacion', 'uses' => 'EvaluacionRiesgosController@guardarEvaluacion'
]);

Route::post('evaluacion.updateEvaluacion.{id}', [
	'as' => 'evaluacion.updateEvaluacion', 'uses' => 'EvaluacionRiesgosController@updateEvaluacion'
]);

Route::post('evaluacion.enviarCorreo', [
	'as' => 'evaluacion.enviarCorreo', 'uses' => 'EvaluacionRiesgosController@enviarCorreo'
]);

Route::get('evaluacion_manual', [
	'as' => 'evaluacion_manual', 'uses' => 'EvaluacionRiesgosController@evaluacionManual'
]);

// ----RUTAS PARA GESTIÓN DE CONTROLES ---- //

Route::get('controles', [
	'as' =>'controles', 'uses' => 'ControlesController@index']);

Route::get('controles.index2', [
	'as' => 'controles.index2', 'uses' => 'ControlesController@index2']);

Route::get('controles.create.{org}', [
	'as' =>'controles.create', 'uses' => 'ControlesController@create']);

Route::post('controles.store', [
	'as' =>'controles.store', 'uses' => 'ControlesController@store']);

Route::get('controles.edit.{id}.{org}', [
	'as' =>'controles.edit', 'uses' => 'ControlesController@edit']);

Route::put('controles.update.{id}', [
	'as' => 'controles.update', 'uses' => 'ControlesController@update'
]);

Route::get('evaluar_controles', [
	'as' => 'evaluar_controles', 'uses' => 'ControlesController@indexEvaluacion'
]);

Route::get('evaluar_controles2', [
	'as' => 'evaluar_controles2', 'uses' => 'ControlesController@indexEvaluacion2'
]);

Route::get('volver_evaluacion', [
	'as' => 'volver_evaluacion', 'uses' => 'ControlesController@indexEvaluacion2'
]);

Route::get('cerrar_evaluacion.{id}', [
	'as' => 'cerrar_evaluacion', 'uses' => 'ControlesController@closeEvaluation'
]);

//ruta para ir a formulario de evaluación de cada prueba
Route::get('editar_evaluacion.{id}', [
	'as' => 'editar_evaluacion', 'uses' => 'ControlesController@editEvaluacion'
]);

Route::get('agregar_evaluacion.{id}.{kind}.{org}', [
	'as' => 'agregar_evaluacion', 'uses' => 'ControlesController@createEvaluacion'
]);

Route::put('control_evaluation.update.{id}', [
	'as' => 'control_evaluation.update', 'uses' => 'ControlesController@updateEvaluation'
]);

Route::post('control_evaluation.store', [
	'as' => 'control_evaluation.store', 'uses' => 'ControlesController@storeEvaluation'
]);

Route::get('control_evaluation.hallazgos.{id}', [
	'as' => 'control_evaluation.hallazgos', 'uses' => 'ControlesController@hallazgos'
]);

Route::get('controles.get_evaluation.{id_control}', [
	'as' => 'controles.get_evaluation', 'uses' => 'ControlesController@getEvaluacion'
]);

Route::get('controles.get_issue.{id}', [
	'as' => 'controles.get_issue', 'uses' => 'ControlesController@getIssue'
]);

Route::get('controles.get_description.{id}', [
	'as' => 'controles.get_description', 'uses' => 'ControlesController@getDescription'
]);

Route::get('controles.get_evaluation2.{id_control}', [
	'as' => 'controles.get_evaluation2', 'uses' => 'ControlesController@getEvaluacion2'
]);

Route::get('controles.destroy.{id}.{org_id}', [
	'as' => 'controles.destroy', 'uses' => 'ControlesController@destroy']);

Route::get('controles.get_objective_controls.{org_id}', [
	'as' => 'controles.get_objective_controls', 'uses' => 'ControlesController@getObjectiveControls']);

Route::get('controles.get_subprocess_controls.{org_id}.{subprocess}', [
	'as' => 'controles.get_subprocess_controls', 'uses' => 'ControlesController@getSubprocessControls']);

Route::get('residual_manual', [
	'as' => 'residual_manual', 'uses' => 'ControlesController@residualManual'
]);

Route::get('residual_manual2', [
	'as' => 'residual_manual2', 'uses' => 'ControlesController@residualManual2'
]);

Route::post('residual_manual.store', [
	'as' => 'residual_manual.store', 'uses' => 'ControlesController@storeResidualManual'
]);

// ---- Rutas para reportes ---- //

Route::get('heatmap', [
	'as' =>'heatmap', 'uses' => 'EvaluacionRiesgosController@listHeatmap']);

Route::get('heatmap.{id}', [
	'as' => 'heatmap2', 'uses' => 'EvaluacionRiesgosController@generarHeatmap'
]);

Route::get('matriz_procesos', [
	'as' => 'matriz_procesos', 'uses' => 'ProcesosController@matrix']);

Route::get('matriz_procesos2', [
	'as' => 'matriz_procesos2', 'uses' => 'ProcesosController@generateMatrix']);

Route::get('matriz_subprocesos', [
	'as' => 'matriz_subprocesos', 'uses' => 'SubprocesosController@matrix']);

Route::get('matriz_subprocesos2', [
	'as' => 'matriz_subprocesos2', 'uses' => 'SubprocesosController@generateMatrix']);

Route::get('matrices', [
	'as' => 'matrices', 'uses' => 'ControlesController@matrices']);

Route::get('risk_matrix', [
	'as' => 'risk_matrix', 'uses' => 'RiesgosController@matrices']);

// Nuevos enlaces para matrices de riesgos divididas: matriz de riesgos de proceso y corporativos

Route::get('genriskmatrix.{value},{org},{cat}', [
	'as' => 'genriskmatrix', 'uses' => 'RiesgosController@generarMatriz']);

//ruta para generar matriz de control a través de JSON
Route::get('genmatriz.{value},{org}', [
	'as' => 'genmatriz', 'uses' => 'ControlesController@generarMatriz']);

Route::get('reporte_planes', [
	'as' => 'reporte_planes', 'uses' => 'PlanesAccionController@actionPlansReport']);

Route::get('reporte_hallazgos', [
	'as' => 'reporte_hallazgos', 'uses' => 'IssuesController@issuesReport']);

Route::get('graficos_controles', [
	'as' => 'graficos_controles', 'uses' => 'ControlesController@indexGraficos']);

Route::get('graficos_controles2.{id}.{org}', [
	'as' => 'graficos_controles2', 'uses' => 'ControlesController@indexGraficos2']);

Route::get('graficos_auditorias', [
	'as' => 'graficos_auditorias', 'uses' => 'AuditoriasController@indexGraficos']);

Route::get('graficos_auditorias2.{id}.{org}', [
	'as' => 'graficos_auditorias2', 'uses' => 'AuditoriasController@indexGraficos2']);

Route::get('graficos_planes_accion', [
	'as' => 'graficos_planes_accion', 'uses' => 'PlanesAccionController@indexGraficos']);

Route::get('graficos_planes_accion2.{id}.{org}', [
	'as' => 'graficos_planes_accion2', 'uses' => 'PlanesAccionController@indexGraficos2']);

Route::get('reporte_audits', [
	'as' => 'reporte_audits', 'uses' => 'AuditoriasController@AuditsReport']);

Route::get('genauditreports.{org}', [
	'as' => 'genauditreports', 'uses' => 'AuditoriasController@generarReporteAuditorias']);

Route::get('reporte_riesgos', [
	'as' =>'reporte_riesgos', 'uses' => 'EvaluacionRiesgosController@indexReporteRiesgos']);

Route::get('reporte_riesgos2', [
	'as' => 'reporte_riesgos2', 'uses' => 'EvaluacionRiesgosController@reporteRiesgos2'
]);

//ACT 29-01-18: Lo dejamos en el home ya que utilizará todas las tablas
Route::get('reporte_consolidado', [
	'as' => 'reporte_consolidado', 'uses' => 'HomeController@reporteConsolidado'
]);

//--- Fin Reportes ---//

//obtiene datos de auditoría por JSON (a mostrar en reporte de planes de auditoría)
Route::get('get_audit.{id}', [
	'as' => 'get_audit', 'uses' => 'AuditoriasController@getAudit']);

//obtiene auditorías de una organización
Route::get('get_audits.{id}', [
	'as' => 'get_audits', 'uses' => 'AuditoriasController@getAudits2']);

//obtiene auditorías de un plan
Route::get('get_audits2.{id}', [
	'as' => 'get_audits2', 'uses' => 'AuditoriasController@getAudits3']);

//obtiene pruebas de auditoría relacionadas a una organización
Route::get('get_audit_tests.{id}', [
	'as' => 'get_audit_tests', 'uses' => 'AuditoriasController@getAuditTests']);

//------ Rutas para auditoría de riesgos ------//

Route::get('plan_auditoria', [
	'as' =>'plan_auditoria', 'uses' => 'AuditoriasController@index']);

Route::get('plan_auditoria.create', [
	'as' =>'plan_auditoria.create', 'uses' => 'AuditoriasController@create']);

Route::post('plan_auditoria.create2', [
	'as' =>'plan_auditoria.create2', 'uses' => 'AuditoriasController@datosAuditoria']);

Route::get('plan_auditoria.edit.{id}', [
	'as' =>'plan_auditoria.edit', 'uses' => 'AuditoriasController@edit']);

Route::put('auditorias.update.{id}', [
	'as' => 'auditorias.update', 'uses' => 'AuditoriasController@update'
]);

Route::post('agregar_plan.{id}', [
	'as' => 'agregar_plan', 'uses' => 'AuditoriasController@store'
]);

Route::get('plan_auditoria.show.{id}', [
	'as' => 'plan_auditoria.show', 'uses' => 'AuditoriasController@show'
]);

Route::get('audit_plan.close.{id}', [
	'as' => 'audit_plan.close', 'uses' => 'AuditoriasController@close'
]);

Route::get('audit_plan.open.{id}', [
	'as' => 'audit_plan.open', 'uses' => 'AuditoriasController@open'
]);

Route::get('audit_plan.destroy.{id}', [
	'as' => 'audit_plan.destroy', 'uses' => 'AuditoriasController@destroy']);

//ruta que elimina auditoría enlazada a un plan de auditoría (audit_audit_plan_id)
Route::get('audit.destroy.{id}', [
	'as' => 'audit.destroy', 'uses' => 'AuditoriasController@destroyAudit']);

//ruta para obtener dato de auditorías agregadas a un plan (audit_audit_plan se activa al editar plan de auditoría)
Route::get('get_audit_info.{audit_plan}.{audit}', [
	'as' => 'get_audit_info', 'uses' => 'AuditoriasController@getAuditInfo'
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

Route::get('crear_pruebas.{id}', [
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

Route::get('supervisar', [
	'as' => 'supervisar', 'uses' => 'AuditoriasController@supervisar']);

Route::get('notas', [
	'as' => 'notas', 'uses' => 'AuditoriasController@notas']);

Route::post('responder_nota', [	
	'as' => 'responder_nota', 'uses' => 'AuditoriasController@responderNota']);

Route::post('agregar_supervision', [
	'as' => 'agregar_supervision', 'uses' => 'AuditoriasController@storeSupervision']);

Route::get('planes_accion', [
	'as' => 'planes_accion', 'uses' => 'AuditoriasController@actionPlans']);


Route::post('agregar_plan2', [
	'as' => 'agregar_plan2', 'uses' => 'AuditoriasController@storePlan']);

//para seleccionar primero organización
Route::get('programas_auditoria', [
	'as' => 'programas_auditoria', 'uses' => 'AuditoriasController@auditPrograms1']);

Route::get('programas_auditoria2', [
	'as' => 'programas_auditoria2', 'uses' => 'AuditoriasController@auditPrograms']);

Route::get('programas_auditoria.show.{id}', [
	'as' => 'programas_auditoria.show', 'uses' => 'AuditoriasController@showProgram']);

Route::get('programas_auditoria.edit.{id}', [
	'as' => 'programas_auditoria.edit', 'uses' => 'AuditoriasController@editProgram']);

Route::get('programas_auditoria.edit_test.{id}', [
	'as' => 'programas_auditoria.edit_test', 'uses' => 'AuditoriasController@editTest']);

Route::put('programas_auditoria.update_program.{id}', [
	'as' => 'programas_auditoria.update_program', 'uses' => 'AuditoriasController@updateProgram']);

Route::put('programas_auditoria.update_test.{id}', [
	'as' => 'programas_auditoria.update_test', 'uses' => 'AuditoriasController@updateTest']);

Route::get('programas_auditoria.create_test.{id}', [
	'as' => 'programas_auditoria.create_test', 'uses' => 'AuditoriasController@createTest']);

Route::post('programas_auditoria.store_test', [
	'as' => 'programas_auditoria.store_test', 'uses' => 'AuditoriasController@storeTest']);

Route::get('programas_auditoria.destroy.{id}', [
	'as' => 'programas_auditoria.destroy', 'uses' => 'AuditoriasController@destroyProgram']);

Route::get('audit_tests.destroy.{id}', [
	'as' => 'audit_tests.destroy', 'uses' => 'AuditoriasController@destroyTest']);


//------ Rutas para trabajar con Excel ------//

Route::get('genexcel.{value},{org},{cat}', [
	'as' => 'genexcel', 'uses' => 'ExcelController@generarExcel']);

Route::get('genexcelplan.{org}', [
	'as' => 'genexcelplan', 'uses' => 'ExcelController@generarExcelPlan']);

Route::get('genexcelissues.{type},{org}', [
	'as' => 'genexcelissues', 'uses' => 'ExcelController@generarExcelIssue']);

Route::get('genexcelaudit.{org}', [
	'as' => 'genexcelaudit', 'uses' => 'ExcelController@generarExcelAudit']);

Route::get('genexcelgraficos.{id}.{org}', [
	'as' => 'genexcelgraficos', 'uses' => 'ExcelController@generarExcelGraficos']);

Route::get('genexcelgraficosdinamicos.{kind},{id}.{org}', [
	'as' => 'genexcelgraficosdinamicos', 'uses' => 'ExcelController@generarExcelGraficosDinamicos']);

Route::get('genexcelconsolidado', [
	'as' => 'genexcelconsolidado', 'uses' => 'ExcelController@generarExcelConsolidado']);

Route::get('genexcelencuesta.{id}', [
	'as' => 'genexcelencuesta', 'uses' => 'ExcelController@generarExcelEncuesta']);

//------ RUTAS PARA ENLACES A TRAVÉS DE JSON --------//

//ruta para seleccionar a través de JSON los riesgos de negocio o de procesos en campo select (al crear controles)
Route::get('controles.subneg.{value}.{org}', [
	'as' => 'controles.subneg', 'uses' => 'RiesgosController@subneg'
]);

Route::get('controles.docs.{id}', [
	'as' =>'controles.docs', 'uses' => 'ControlesController@docs']);

//ruta para generar matriz de riesgos a través de JSON
//Route::get('genmatrizriesgos.{value}', [
//	'as' => 'genmatrizriesgos', 'uses' => 'RiesgosController@generarMatriz']);


//ruta para generar reporte de planes de acción
Route::get('genplanes_accion.{org}', [
	'as' => 'genplanes_accion', 'uses' => 'PlanesAccionController@generarReportePlanes']);

//ruta para generar reporte de hallazgos
Route::get('genissues_report', [
	'as' => 'genissues_report', 'uses' => 'IssuesController@generarReporteIssues']);

//ruta para obtener datos de plan de auditoría anterior 
Route::get('auditorias.get_audit_plan.{org}', [
	'as' => 'auditorias.get_audit_plan', 'uses' => 'AuditoriasController@getAuditPlan']);

//ruta para obtener todos los planes de auditoría de una organización
Route::get('auditorias.get_planes.{org}', [
	'as' => 'auditorias.get_planes', 'uses' => 'AuditoriasController@getPlanes']);

//ruta para obtener objetivos al crear un plan de auditoría
Route::get('auditorias.objetivos.{org}', [
	'as' => 'auditorias.objetivos', 'uses' => 'AuditoriasController@getObjetivos']);

//ruta para obtener riesgos de negocio de una organización (al crear plan o evaluar riesgos)
Route::get('get_objective_risk.{org}', [
	'as' => 'get_objective_risk', 'uses' => 'RiesgosController@getRiesgosObjetivos']);

//ruta para obtener riesgos de proceso de una organización (al crear plan o evaluar riesgos)
Route::get('get_risk_subprocess.{org}', [
	'as' => 'get_risk_subprocess', 'uses' => 'RiesgosController@getRiesgosProcesos']);

//ruta para obtener todos los stakeholders menos el auditor resposable al crear un plan de auditoría
Route::get('auditorias.stakeholders.{id}', [
	'as' => 'auditorias.stakeholders', 'uses' => 'AuditoriasController@getStakeholders']);

//ruta para obtener datos de programa de auditoría seleccionada
Route::get('auditorias.get_audit_program.{id}', [
	'as' => 'auditorias.get_audit_program', 'uses' => 'AuditoriasController@getAuditProgram']);

//ruta para obtener programas de auditoría relacionados a una organización
Route::get('get_audit_programs.{id}', [
	'as' => 'get_audit_programs', 'uses' => 'AuditoriasController@getAuditPrograms']);

//ruta para obtener datos de prueba de auditoría seleccionada (al supervisar un plan de auditoria)
Route::get('auditorias.get_audit_program2.{id}', [
	'as' => 'auditorias.get_audit_program2', 'uses' => 'AuditoriasController@getAuditProgram2']);

//ruta para obtener controles de negocio asociados a un plan de auditoría
//(según los objetivos corporativos que contemple este plan)
//Route::get('auditorias.objective_controls.{id}', [
//	'as' => 'auditorias.controls', 'uses' => 'AuditoriasController@getObjectiveControls']);

//ruta para obtener controles de proceso asociados a un plan de auditoría
//(según los objetivos corporativos que contemple este plan)
//Route::get('auditorias.subprocess_controls.{id}', [
//	'as' => 'auditorias.controls', 'uses' => 'AuditoriasController@getSubprocessControls']);

//ruta para obtener auditorias según el plan seleccionado (al crear prueba de auditoría)
Route::get('auditorias.auditorias.{id}', [
	'as' => 'auditorias.auditorias', 'uses' => 'AuditoriasController@getAudits']);

//ruta para obtener pruebas segun plan + auditoría
Route::get('auditorias.getpruebas.{kind},{id}', [
	'as' => 'auditorias.getpruebas', 'uses' => 'AuditoriasController@getTests']);

//ruta para obtener issues de una prueba (si es que tiene)
Route::get('auditorias.get_issue.{id}', [
	'as' => 'auditorias.get_issue', 'uses' => 'AuditoriasController@getIssue']);

//ruta para obtener notas de una prueba de auditoría
Route::get('auditorias.get_notes.{id}', [
	'as' => 'auditorias.get_notes', 'uses' => 'AuditoriasController@getNotes']);

//ruta para obtener plan de acción existente
Route::get('auditorias.get_action_plan.{id}', [
	'as' => 'auditorias.get_action_plan', 'uses' => 'PlanesAccionController@getActionPlan']);

//ruta para obtener archivo de evidencias
Route::get('auditorias.get_file.{archivo}', [
	'as' => 'auditorias.get_file', 'uses' => 'AuditoriasController@getFile']);

//ruta para obtener notas de una prueba de auditoría
Route::get('auditorias.guardar_nota', [
	'as' => 'auditorias.guardar_nota', 'uses' => 'AuditoriasController@storeNote']);

//ruta para obtener notas de una prueba de auditoría
Route::get('auditorias.close_note.{id}', [
	'as' => 'auditorias.close_note', 'uses' => 'AuditoriasController@closeNote']);

//ruta para obtener procesos de una organizacion
Route::get('get_processes.{id}', [
	'as' => 'get_processes', 'uses' => 'ProcesosController@getProcesses']);

//ruta para obtener subprocesos de una organizacion
Route::get('get_subprocesses.{id}', [
	'as' => 'get_subprocesses', 'uses' => 'SubprocesosController@getSubprocesses']);

//ruta para obtener subprocesos de un proceso específico de una organización
Route::get('get_subprocesses2.{org}.{process}', [
	'as' => 'get_subprocesses2', 'uses' => 'SubprocesosController@getSubprocesses2']);

//ruta para obtener subprocesos de una organizacion y además de un proceso en específico
Route::get('get_subprocesses_from_process.{id}.{process}', [
	'as' => 'get_subprocesses_from_process', 'uses' => 'SubprocesosController@getSubprocessesFromProcess']);

//ruta para obtener objetivos de una organizacion
Route::get('get_objectives.{id}', [
	'as' => 'get_objectives', 'uses' => 'ObjetivosController@getObjectives']);

//ruta para obtener riesgos de una organizacion
Route::get('get_risks.{id}', [
	'as' => 'get_risks', 'uses' => 'RiesgosController@getRisks']);

//ruta para obtener riesgos de una organizacion y de un tipo en específico
Route::get('get_risks2.{id},{type}', [
	'as' => 'get_risks2', 'uses' => 'RiesgosController@getRisks2']);

//ruta para obtener organizacion de un plan de auditoría
Route::get('get_organization.{audit_plan_id}', [
	'as' => 'get_organization', 'uses' => 'AuditoriasController@getOrganization']);


//ruta para obtener controles de una organizacion y además de un proceso en específico
Route::get('get_controls_from_process.{id}.{process}', [
	'as' => 'get_controls_from_process', 'uses' => 'ControlesController@getControlsFromProcess']);

//ruta para obtener controles de una organizacion y además de un proceso en específico
Route::get('get_controls_from_subprocess.{id}.[{subprocesses}]', [
	'as' => 'get_controls_from_subprocess', 'uses' => 'ControlesController@getControlsFromSubprocess']);

//obtiene controles de una organización para una perspectiva en específica
Route::get('get_controls_from_perspective.{org}.{perspective}', [
	'as' => 'get_controls_from_perspective', 'uses' => 'ControlesController@getControlsFromPerspective']);

//ruta para obtener controles de una organización
Route::get('get_controls.{id}', [
	'as' => 'get_controls2', 'uses' => 'ControlesController@getControls']);

//ruta para obtener controles de una organizacion y de un determinado tipo
Route::get('get_controls2.{id},{type}', [
	'as' => 'get_controls', 'uses' => 'ControlesController@getControls2']);

Route::get('get_kri.{id}', [
	'as' => 'get_kri', 'uses' => 'KriController@getKri']);

Route::get('get_kri_evaluations.{id}', [
	'as' => 'get_kri_evaluations', 'uses' => 'KriController@getEvaluations']);


// ---- RUTAS PARA KRI ----//
Route::get('kri', [
	'as' => 'kri', 'uses' => 'KriController@index']);

Route::get('riesgo_kri', [
	'as' => 'riesgo_kri', 'uses' => 'KriController@index2']);

Route::get('enlazar_riesgos', [
	'as' => 'enlazar_riesgos', 'uses' => 'KriController@enlazar']);

Route::post('kri.guardar_enlace', [
	'as' => 'kri.guardar_enlace', 'uses' => 'KriController@guardarEnlace'
]);

Route::get('kri.create', [
	'as' => 'kri.create', 'uses' => 'KriController@create']);

Route::get('kri.create2.{id}', [
	'as' => 'kri.create2', 'uses' => 'KriController@create2']);

Route::post('kri.store', [
	'as' => 'kri.store', 'uses' => 'KriController@store']);

Route::get('kri.edit.{id}', [
	'as' => 'kri.edit', 'uses' => 'KriController@edit']);

Route::get('kri.evaluar.{id}', [
	'as' => 'kri.evaluar', 'uses' => 'KriController@evaluar']);

Route::put('kri.update.{id}', [
    'as' => 'kri.update', 'uses' => 'KriController@update']);

Route::post('kri.guardar_evaluacion', [
	'as' => 'kri.guardar_evaluacion', 'uses' => 'KriController@storeEval']);

Route::get('kri.veranteriores.{id}', [
	'as' => 'kri.veranteriores', 'uses' => 'KriController@showEvals']);

Route::get('kri.destroy.{id}', [
	'as' => 'kri.destroy', 'uses' => 'KriController@destroy']);

//ruta para obtener todas las causas de riesgo
Route::get('get_causes', [
	'as' => 'get_causes', 'uses' => 'RiesgosController@getCauses']);

//ruta para obtener todos los efectos de riesgo
Route::get('get_effects', [
	'as' => 'get_effects', 'uses' => 'RiesgosController@getEffects']);

//ACT 26-04-18: ruta para obtener respuestas al riesgo
Route::get('get_risk_responses', function() {
	return json_encode(\Ermtool\Risk_response::all(['id','name']));
});
//---- Rutas para mantenedor de Hallazgos ----//

Route::get('hallazgos', [
	'as' => 'hallazgos', 'uses' => 'IssuesController@index']);

//ruta para ver lista de hallazgos segun tipo
Route::get('hallazgos_lista', [
	'as' => 'hallazgos_lista', 'uses' => 'IssuesController@index2']);

//ruta para crear hallazgos
Route::get('create_hallazgo', [
	'as' => 'create_hallazgo', 'uses' => 'IssuesController@create']);

Route::post('store_hallazgo', [
	'as' => 'store_hallazgo', 'uses' => 'IssuesController@store']);

//ruta para editar hallazgos
Route::get('edit_hallazgo', [
	'as' => 'edit_hallazgo', 'uses' => 'IssuesController@edit']);

//ruta para eliminar  hallazgos
Route::get('hallazgo.destroy.{id}', [
	'as' => 'hallazgo.destroy', 'uses' => 'IssuesController@destroy']);

Route::put('update_hallazgo.{id}', [
    'as' => 'update_hallazgo', 'uses' => 'IssuesController@update']);

//ruta para ver hallazgo a través de ejecución de auditoría
Route::get('hallazgos_test.{id}', [
	'as' => 'hallazgos_test', 'uses' => 'IssuesController@index3']);

//---- Rutas para gestión estratégica ----//
Route::get('plan_estrategico', [
	'as' => 'plan_estrategico', 'uses' => 'GestionEstrategicaController@indexPlanes']);

Route::get('plan_estrategico.create', [
	'as' => 'plan_estrategico.create', 'uses' => 'GestionEstrategicaController@createPlanEstrategico']);

Route::post('plan_estrategico.store', [
	'as' => 'plan_estrategico.store', 'uses' => 'GestionEstrategicaController@storePlanEstrategico']);

Route::get('plan_estrategico.edit.{id}', [
	'as' => 'plan_estrategico.edit', 'uses' => 'GestionEstrategicaController@editPlanEstrategico']);

Route::put('plan_estrategico.update.{id}', [
	'as' => 'plan_estrategico.update', 'uses' => 'GestionEstrategicaController@updatePlanEstrategico']);


Route::get('kpi', [
	'as' => 'kpi', 'uses' => 'GestionEstrategicaController@kpi']);

Route::get('kpi2', [
	'as' => 'kpi2', 'uses' => 'GestionEstrategicaController@kpi2']);

Route::get('kpi.create.{id}', [
	'as' => 'kpi.create', 'uses' => 'GestionEstrategicaController@kpiCreate']);

Route::get('kpi.create2.{id}', [
	'as' => 'kpi.create2', 'uses' => 'GestionEstrategicaController@kpiCreateFromObjective']);

Route::post('kpi.store', [
	'as' => 'kpi.store', 'uses' => 'GestionEstrategicaController@kpiStore']);

Route::post('kpi.store2', [
	'as' => 'kpi.store2', 'uses' => 'GestionEstrategicaController@kpiStoreFromObjective']);

Route::get('kpi.edit.{id}', [
	'as' => 'kpi.edit', 'uses' => 'GestionEstrategicaController@kpiEdit']);

//Función editar accedida desde definición de objetivos
Route::get('kpi.edit2.{id}', [
	'as' => 'kpi.edit2', 'uses' => 'GestionEstrategicaController@kpiEditFromObjective']);

//Route::put('kpi.update.{id}', [
//	'as' => 'kpi.update', 'uses' => 'GestionEstrategicaController@kpiUpdate']);

Route::put('kpi.update2.{id}', [
	'as' => 'kpi.update2', 'uses' => 'GestionEstrategicaController@kpiUpdate']);

//Antes sólo era medir o evaluar, pero ahora también mostrará evaluaciones anteriores (tipo monitor de KRI)
Route::get('kpi.evaluate.{id}', [
	'as' => 'kpi.evaluate', 'uses' => 'GestionEstrategicaController@kpiEvaluate']);

Route::post('kpi.store_eval', [
	'as' => 'kpi.store_eval', 'uses' => 'GestionEstrategicaController@kpiStoreEvaluate']);

Route::get('kpi.validate.{id}', [
	'as' => 'kpi.validate', 'uses' => 'GestionEstrategicaController@kpiValidate']);

Route::get('monitor_kpi', [
	'as' => 'monitor_kpi', 'uses' => 'GestionEstrategicaController@kpiMonitor']);

Route::get('monitor_kpi_2', [
	'as' => 'monitor_kpi_2', 'uses' => 'GestionEstrategicaController@kpiMonitor2']);

Route::get('getkpi.{org_id}', [
	'as' => 'getkpi', 'uses' => 'GestionEstrategicaController@getKpi']);

Route::get('objective_kpi.{obj_id}', [
	'as' => 'objective_kpi', 'uses' => 'GestionEstrategicaController@objectiveKpi']);


Route::get('mapas', [
	'as' => 'mapas', 'uses' => 'GestionEstrategicaController@mapas']);

Route::get('mapas2', [
	'as' => 'mapas2', 'uses' => 'GestionEstrategicaController@mapas2']);

//---- Rutas para mantenedor de Planes de acción ----//

Route::get('action_plans', [
	'as' => 'action_plans', 'uses' => 'PlanesAccionController@index']);

//planes de acción para determinada organización
Route::get('action_plans2', [
	'as' => 'action_plans2', 'uses' => 'PlanesAccionController@index2']);

//crear nuevo plan de acción a través de mantenedor
Route::get('action_plan.create.{org}', [
	'as' =>'action_plan.create', 'uses' => 'PlanesAccionController@create']);

Route::get('action_plan.destroy.{id}', [
	'as' => 'action_plan.destroy', 'uses' => 'PlanesAccionController@destroy']);

Route::get('action_plan.close.{id}', [
	'as' => 'action_plan.close', 'uses' => 'PlanesAccionController@close'
]);

Route::get('action_plan.edit.{id}', [
	'as' => 'action_plan.edit', 'uses' => 'PlanesAccionController@edit'
]);

Route::post('action_plan.store', [
	'as' => 'action_plan.store', 'uses' => 'PlanesAccionController@store2']);

Route::put('action_plan.update.{id}', [
	'as' => 'action_plan.update', 'uses' => 'PlanesAccionController@update']);

Route::get('get_issues.{kind}.{org}', [
	'as' => 'get_issues', 'uses' => 'PlanesAccionController@getIssues']);

//ACT 09-04-18: Alerta para planes de acción que están vencidos o próximos a vencer
Route::get('alert_action_plans', function(){
   return View::make('planes_accion.index_alerts');
});

Route::get('alert_action_plans2', [
	'as' => 'alert_action_plans2', 'uses' => 'PlanesAccionController@indexAlerts']);

Route::post('send_alert_pa', [
	'as' => 'send_alert_pa', 'uses' => 'PlanesAccionController@sendAlerts']);

Route::get('info_alerts.{id}', [
	'as' => 'info_alerts', 'uses' => 'PlanesAccionController@getAlertsInfo']);

//--- FIN rutas de planes de acción ---//

Route::get('documentos', [
	'as' => 'documentos', 'uses' => 'DocumentosController@index']);

Route::get('documentos.show.{id}', [
	'as' => 'documentos.show', 'uses' => 'DocumentosController@show']);

Route::get('deleteFiles.{dir},{id}', [
	'as' => 'deleteFiles', 'uses' => 'DocumentosController@deleteFiles']);

//ruta para eliminar evidencias (llama a funcion helper)
Route::get('evidences.delete.{id},{kind},{name}', function($id,$kind,$name) {
	return eliminarArchivo($id,$kind,$name);
});


//prueba para cargar archivos por AJAX
Route::get('cargar', [
	'as' => 'cargar', 'uses' => 'DocumentosController@storeFile']);
// ---- Rutas adicionales del framework ----//

/*
Route::get('breweries', ['middleware' => 'cors', function()
{
    return \Response::json(\App\Brewery::with('beers', 'geocode')->paginate(10), 200);
}]);

*/

//act 17-04-17: Rutas para eliminar notas y respuestas de notas
Route::get('note.destroy.{id}', [
	'as' => 'note.destroy', 'uses' => 'AuditoriasController@destroyNote'
]);

Route::get('note_answer.destroy.{id}', [
	'as' => 'note_answer', 'uses' => 'AuditoriasController@destroyNoteAnswer'
]);

Route::get('error', function(){ 
    abort(404);
});

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

//ACT 17-07-18: Inicios de sesión a excel
Route::get('sessions', [
	'as' => 'sessions', 'uses' => 'ExcelController@exportSessions'
]);

Route::post('export_audit_graphics', [
	'as' => 'export_audit_graphics', 'uses' => 'AuditoriasController@docxGraficos'
]);

Route::post('export_control_graphics', [
	'as' => 'export_control_graphics', 'uses' => 'ControlesController@docxGraficos'
]);

Route::post('export_action_plan_graphics', [
	'as' => 'export_action_plan_graphics', 'uses' => 'PlanesAccionController@docxGraficos'
]);

Route::get('reporte_personalizado', [
	'as' => 'reporte_personalizado', 'uses' => 'WordController@index'
]);

Route::post('gentotalreport', [
	'as' => 'gentotalreport', 'uses' => 'wordController@genReport'
]);

Route::get('get_subcategories.{cat_id}', [
	'as' => 'get_subcategories', 'uses' => 'CategoriasRiesgosController@getSubCategories'
]);

//ruta para descargar evidencias (llama a funcion helper)
Route::get('downloadfile.{kind}.{id}.{filename}.{ext}', function($kind,$id,$filename,$ext) {
	return downloadFile($kind,$id,$filename,$ext);
});

//ACT 05-12-17
//ruta para descargar evidencias sin extensión(llama a funcion helper)
//Route::get('downloadfile2.{kind}.{id}.{filename}', function($kind,$id,$filename) {
//	return downloadFile2($kind,$id,$filename);
//});

Route::get('importador',[
	'as' => 'importador', 'uses' => 'ExcelController@importarIndex'
]);

Route::post('importar_excel', [
	'as' => 'importar_excel', 'uses' => 'ExcelController@importarExcel'
]);


//19-03-18: ----- Sistema (Canal) de Denuncias ----- //
Route::get('bgrc_denuncias', [
	'as' => 'bgrc_denuncias', 'uses' => 'DenunciasController@index'
	]);

Route::get('cc_questions', [
	'as' => 'cc_questions', 'uses' => 'DenunciasController@Questions'
	]);

Route::get('store_cc_questions1', [
	'as' => 'store_cc_questions1', 'uses' => 'DenunciasController@storeCcQuestions1'
	]);

Route::post('store_cc_questions2', [
	'as' => 'store_cc_questions2', 'uses' => 'DenunciasController@storeCcQuestions2'
	]);

Route::get('registro_denuncia', [
   'as' => 'registro_denuncia', 'uses' => 'DenunciasController@registerComplaint'
	]);

Route::post('registro_denuncia2', [
   'as' => 'registro_denuncia2', 'uses' => 'DenunciasController@registerComplaint2'
	]);

Route::get('seguimiento_admin', function(){
   return View::make('denuncias.seguimiento_admin');
});

Route::get('clasificacion', function(){
   return View::make('denuncias.clasificacion');
});

Route::get('cerrar_caso', function(){
   return View::make('denuncias.cerrar_caso');
});

Route::get('reportes_denuncias', function(){
   return View::make('denuncias.reportes');
});