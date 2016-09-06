<?php

namespace App\Controllers;

use Slim\Slim;

class Controller
{
    /**
     * @var null|Slim
     */
    protected $app;

    public function __construct()
    {
        $this->app = Slim::getInstance();
    }

    /**
     * Generic entry.
     */
    public function getHello()
    {
        $this->app->response->setBody('Hello Seedlings!');
    }

    /**
     * Generic health test.
     */
    public function getHealth()
    {
        $this->app->response->setBody(
            json_encode([
                'application' => $this->app->getName(),
                'version'     => $this->app->config('version'),
                'time'        => Carbon::now()->toDateTimeString(),
            ])
        );
    }
}
