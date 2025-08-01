<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    public static function response($code = 200, $data = [], $message = null, $error = null)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'error'=> $error
        ], $code);
    }
}
