<?php

namespace App\Modules\Sample;

use Slim\Slim;

class SampleServiceProvider
{
    /**
     * @param $app
     */
    public function register(Slim $app)
    {
        $app->get('/sample/lists/', 'App\Modules\Sample\Controllers\ListController:get');
        $app->get('/sample/lists/:id/leads', 'App\Modules\Sample\Controllers\LeadsController:get');
    }
}
