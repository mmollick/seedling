<?php

namespace App\Modules\Marketo\Repositories;

use Faker\Generator;
use Predis\Client;
use Slim\Slim;

class BaseRepository
{
    /**
     * BaseRepository constructor.
     *
     * @param Slim      $app
     * @param Generator $faker
     * @param Client    $predis
     */
    public function __construct(Slim $app, Generator $faker, Client $predis)
    {
        $this->app = $app;
        $this->faker = $faker;
        $this->predis = $predis;
    }

    /**
     * Marketo only allows 100 requests per every 20 seconds.
     * Were using redis lists to manage the throttling; read more
     * here: http://redis.io/commands/incr#pattern-rate-limiter-2.
     *
     * @return array|null
     */
    protected function rateLimit()
    {
        $key = 'marketo-throttler:'.$this->app->request()->getIp();
        $count = $this->predis->llen($key);

        // Enact throttling
        if ($count > 100) {

            // Marketo error response
            // Read more: http://developers.marketo.com/documentation/rest/error-codes/
            return [
                'errors' => [
                    [
                        'code'    => 608,
                        'message' => 'API Temporarily Unavailable',
                    ],
                ],
                'success' => false,
            ];
        } else {

            // Bump up requests
            if ($this->predis->exists($key) == false) {
                $this->predis->transaction()->rpush($key, $key)->expire($key, 20)->execute();
            } else {
                $this->predis->rpushx($key, $key);
            }
        }
    }
}
