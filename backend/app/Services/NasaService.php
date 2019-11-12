<?php

/**
 * Created by PhpStorm.
 * User: Julio Costa
 * Date: 08/11/2019
 * Time: 22:31
 */

namespace App\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;


class NasaService
{
    const LIMIT = 10;

    /**
     * Method
     * 
     * @param string $start_date
     * @param string $end_date
     * @param int $currentPage
     * @param string $search
     * @return array
     */
    public function getMeteor($start_date = null, $end_date = null, $currentPage = null, $search = null)
    {

        $arr = [];
        $client = app('guzzle');
        $params = [
            'api_key' => config('nasa')['key']
        ];

        if (isset($start_date) && isset($end_date)) {
            $params['start_date'] = $start_date;
            $params['end_date'] = $end_date;
        }
        try {
            $response = $client->request(
                'GET',
                config('nasa')['url'] . '/v1/feed',
                [
                    'query' => $params
                ]
            );
            $contentType = $response->getHeader('Content-Type');
            if ($contentType && strpos(head($contentType), 'application/json') !== false) {

                $result = json_decode($response->getBody())->near_earth_objects;
                foreach ($result as $meteor) {
                    foreach ($meteor as $infoMeteor) {
                        if ($search) {
                            if (Str::contains($infoMeteor->name, $search)) {

                                $first = head($infoMeteor->close_approach_data);
                                array_push(
                                    $arr,
                                    ['id' => $infoMeteor->id, 'name' => $infoMeteor->name, 'info' => $first, 'diameter' => $infoMeteor->estimated_diameter]
                                );
                            }
                        } else {

                            $first = head($infoMeteor->close_approach_data);
                            array_push(
                                $arr,
                                ['id' => $infoMeteor->id, 'name' => $infoMeteor->name, 'info' => $first, 'diameter' => $infoMeteor->estimated_diameter]
                            );
                        }
                    }
                }
            }
        } catch (GuzzleException $e) {
            return response()->json(['error' => $e->getMessage()]);
        }

        return $this->paginate(self::LIMIT, $arr, $currentPage);
    }


      /**
     * Method
     * 
     * @param int $limit
     * @param array $params
     * @param int  $page
     * @return array
     */
    private function paginate($limit, $params = [], $page)
    {
        $currentPage = (isset($page) ? intval($page) : 1);

        $pagData = collect($params)->chunk($limit);

        if (isset($pagData[$currentPage - 1])) {
            $result = $pagData[$currentPage - 1];
            return ['currentPage' => $currentPage, 'totalPage' => $pagData->count(), 'data' => $result];
        }
    }
}
