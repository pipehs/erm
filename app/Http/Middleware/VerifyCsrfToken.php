<?php

namespace Ermtool\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
	/**
	 * Determine if the session and input CSRF tokens match.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @return bool
	 */
	protected function tokensMatch($request)
	{
	    // If request is an ajax request, then check to see if token matches token provider in
	    // the header. This way, we can use CSRF protection in ajax requests also.
	    $token = $request->ajax() ? $request->header('X-CSRF-Token') : $request->input('_token');

	    return $request->session()->token() == $token;
	}
	
	
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
    	'crear_encuesta',
        'encuesta.store',
        'organization',
        'organization.update',
        'procesos',
        'subprocesos',
        'categorias_riesgos',
        'categorias_objetivos',
        'stakeholders',
        'riesgostipo',
        'causas',
        'efectos',
        'riesgos',
        'evaluacion'
    ];

}

