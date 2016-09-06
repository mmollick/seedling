<?php

namespace App\Modules\Sample\Repositories;

use Faker\Factory;
use Predis\Client;

class ListRepository
{
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * Mocks single list entry.
     *
     * @return array
     */
    private function single()
    {
        return [
            'id'    => $this->faker->sha1.':'.$this->faker->numberBetween(100, 500000),
            'title' => $this->faker->words(mt_rand(1, 5), true),
        ];
    }

    /**
     * @param $records
     * @param $batchSize
     * @param int $page
     *
     * @return array
     */
    private function multiple($records, $batchSize, $page = 0)
    {
        $arr = [
            'requestId' => $this->faker->sha1,
            'data'      => [],
            'success'   => true,
        ];

        $offset = $page * $batchSize;
        $limit = min($records, ($batchSize + $offset));

        if ($records > 300 && $limit < $records) {
            $arr['nextPageToken'] = $page + 1;
        }

        for ($i = $offset; $i < $limit; $i++) {
            $arr['data'][] = $this->single();
        }

        return $arr;
    }

    /**
     * Mocks array of leads.
     *
     * @param $records
     * @param $batchSize
     * @param int $page
     *
     * @return array
     */
    public function get($records, $batchSize, $page = 0)
    {
        $key = "SeedlingSampleLists-{$records}-{$batchSize}-{$page}";
        $predis = new Client();

        if ($predis->exists($key)) {
            return json_decode($predis->get($key), true);
        } else {
            $arr = $this->multiple($records, $batchSize, $page);
            $predis->set($key, json_encode($arr));
            $predis->expire($key, 3600 * 24);

            return $arr;
        }
    }
}
