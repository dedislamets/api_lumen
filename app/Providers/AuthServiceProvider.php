<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        Auth::viaRequest('api', function ($request) {
            if ($request->input('token')) {
                $auth = sha1("ciaov2328");
                if($request->input('token') != $auth ){
                    return NULL;
                }else{
                    return TRUE;
                }

                // dd(Register::where('remember_token', '1234')->first());
                //ubah logic auth
                // return Register::where('remember_token', $request->input('token'))->first();
            }
        });
    }
}
