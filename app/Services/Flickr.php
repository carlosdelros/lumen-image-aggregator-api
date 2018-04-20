<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Flickr {

    public function __construct() {
        $this->client = new Client();
    }

    public function fetchImages($params) {
        $res = $this->client->get("https://api.flickr.com/services/rest/",
        [
            'query' => [
                'method' => 'flickr.photos.search',
                'api_key' => 'f8bca32f7d68c362e3d26da6a8c5f2a6',
                'text' => $params['search_term'],
                'per_page' => $params['per_page'],
                'page' => $params['page'],
                'format' => 'json',
                'nojsoncallback' => '1'
            ]
        ]);

        $response_collection = collect(json_decode($res->getBody()->getContents()))->recursive();

        if($response_collection->get('stat') != "ok") {
            return $response_collection;
        }

        $data = $response_collection->get('photos');

        $response = [
            'number_of_results' => (int) $data->get('total'),
            'pages' => $data->get('total') / $params['per_page'],
            'images' => $this->getPhotosInfo($data->get('photo'))
        ];

        return $response;
    }

    private function getPhotosInfo(Collection $photos) {
        return $photos->map(function($photo){
            return [
                'title' => $photo->get('title'),
                // 'url' =>
            ];
        });
    }
}
