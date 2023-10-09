<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('jsonApi', function ($data = null, $httpStatus = 500) {
            return ($data instanceof JsonResource || $data instanceof ResourceCollection)
                ? $data->response()->setStatusCode($httpStatus)
                : response()->json($data, $httpStatus);

            return response()->json($data ?? 'Oops! Something went wrong.', $httpStatus);
        });
    }
}
