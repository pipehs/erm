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

Route::get('/', 
function()
	{
		return view('home');
	}
);

Route::get('home', 
function()
	{
		return view('home');
	}
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

Route::resource('riesgos','RiesgosController');

Route::get('riesgos.create','RiesgosController@create');

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

// ---- FIN RUTAS PARA GESTIÓN DE DATOS MAESTROS---- //

// ----RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

Route::resource('crear_encuesta','EncuestasController@index');

Route::post('encuesta.store','EncuestasController@store');

Route::resource('enviar_encuesta','EncuestasController@enviar');

// ----FIN RUTAS PARA IDENTIFICACIÓN DE EVENTOS DE RIESGO---- //

// ----Rutas para reportes básicos---- //

Route::resource('heatmap','ControlesController@heatmap');
// ---- Rutas adicionales del framework ----//
Route::get('breweries', ['middleware' => 'cors', function()
{
    return \Response::json(\App\Brewery::with('beers', 'geocode')->paginate(10), 200);
}]);

