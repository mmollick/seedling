<?php

namespace App\Modules\Marketo\Repositories;

use Carbon\Carbon;

class LeadsRepository extends BaseRepository
{
    /**
     * Mocks single lead entry.
     *
     * @return array
     */
    private function single()
    {
        return [
            'id'        => $this->faker->sha1,
            'email'     => $this->faker->email,
            'firstName' => $this->faker->firstName,
            'lastName'  => $this->faker->lastName,
            'updatedAt' => Carbon::createFromTimestamp($this->faker->unixTime)->toIso8601String(),
            'createdAt' => Carbon::createFromTimestamp($this->faker->unixTime)->toIso8601String(),
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
            'result'    => [],
            'success'   => true,
        ];

        $offset = $page * $batchSize;
        $limit = min($records, ($batchSize + $offset));

        if ($records > 300 && $limit < $records) {
            $arr['nextPageToken'] = $page + 1;
        }

        for ($i = $offset; $i < $limit; $i++) {
            $arr['result'][] = $this->single();
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
        $key = "SeedlingMarketoLeads-{$records}-{$batchSize}-{$page}";

        if (($rate = $this->rateLimit()) !== null) {
            return $rate;
        }

        if ($this->predis->exists($key)) {
            return json_decode($this->predis->get($key), true);
        } else {
            $arr = $this->multiple($records, $batchSize, $page);
            $this->predis->set($key, json_encode($arr));
            $this->predis->expire($key, 3600 * 24);

            return $arr;
        }
    }
}
