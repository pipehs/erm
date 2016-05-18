<?php

namespace Ermtool\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //no me ha funcionado.........
        Validator::extend('validateUser', function($attribute, $value, $parameters)
        {
            $user = DB::table('evaluation_stakeholder')
                            ->where('stakeholder_id','=',$value)
                            ->select('id');

            if ($user)
            {
                return true;
            }
            else
                return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

}
