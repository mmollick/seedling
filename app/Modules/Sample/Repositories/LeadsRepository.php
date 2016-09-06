<?php

namespace App\Modules\Sample\Repositories;

use Faker\Factory;
use Predis\Client;

class LeadsRepository
{
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * Mocks single lead entry.
     *
     * @return array
     */
    private function single()
    {
        $address = new \Faker\Provider\en_US\Address($this->faker);

        return [
            'id'         => $this->faker->sha1,
            'email'      => $this->faker->email,
            'name'       => $this->faker->name,
            'phone'      => $this->faker->phoneNumber,
            'address_1'  => $address->streetAddress(),
            'address_2'  => mt_rand(0, 1) ? $address->secondaryAddress() : '',
            'state'      => mt_rand(0, 1) ? $address->state() : $address->stateAbbr(),
            'city'       => $address->city(),
            'postalcode' => $address->postcode(),
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
        $key = "SeedlingSampleLeads-{$records}-{$batchSize}-{$page}";
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
