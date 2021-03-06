<?php

namespace App\Modules\Marketo\Controllers;

use App\Controllers\Controller;
use App\Modules\Marketo\Repositories\ListRepository;
use Faker\Factory;
use Predis\Client;

class ListController extends Controller
{
    public function get()
    {
        $time = microtime(true);

        // Arbitrarily get a number of lists to return
        $total = mt_rand(1, 300);

        // Enforce a maximum batch size
        $batchSize = min(300, $this->app->request->get('batchSize', 300));

        // Get the current offset, default to 0
        $offset = $this->app->request->get('page', 0);

        // Make lists
        $repo = new ListRepository($this->app, Factory::create(), new Client());
        $arr = $repo->get($total, $batchSize, $offset);

        $arr['elapsed'] = (microtime(true) - $time);
        $this->app->response->setBody(
            json_encode($arr)
        );
    }
}
