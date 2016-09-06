<?php

namespace App\Modules\Marketo;

use Slim\Slim;

class MarketoServiceProvider
{
    /**
     * @param $app
     */
    public function register(Slim $app)
    {
        $app->get('/marketo/v1/lists.json', 'App\Modules\Marketo\Controllers\ListController:get');
        $app->get('/marketo/v1/list/:leads/leads.json', 'App\Modules\Marketo\Controllers\LeadsController:get');
    }
}
