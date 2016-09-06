<?php namespace App\Modules\Sample\Controllers;

use App\Controllers\Controller;
use App\Modules\Sample\Repositories\LeadsRepository;

class LeadsController extends Controller {

    public function get($id)
    {
        $time = microtime(true);

        // Get list length from id
        list($id, $total) = explode(':', $id);

        // Enforce maximum leads send
        $batchSize = min(300, $this->app->request->get('batchSize', 300));

        // Get current page
        $offset = $this->app->request->get('page', 0);

        // Make leads
        $repo = new LeadsRepository();
        $arr = $repo->get($total, $batchSize, $offset);

        $arr['elapsed'] = (microtime(true) - $time);
        $this->app->response->setBody(
            json_encode($arr)
        );
    }

}