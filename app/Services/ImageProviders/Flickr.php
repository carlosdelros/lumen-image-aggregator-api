<?php

namespace App\Services\ImageProviders;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Flickr {

    private $flickrApiUrl;
    private $client;

    public function __construct() {
        $this->client = new Client();
        $this->flickrApiUrl = "https://api.flickr.com/services/rest/";
    }

    public function fetchImages($params) {
        $res = $this->client->get($this->flickrApiUrl,
        [
            'query' => [
                'method' => 'flickr.photos.search',
                'api_key' => 'b8d129851d412d7fb41628233868f70d',
                'text' => $params['search_term'],
                'per_page' => $params['per_page'],
                'page' => $params['page'],
                'format' => 'json',
                'nojsoncallback' => '1'
            ]
        ]);

        $responseCollection = collect(json_decode($res->getBody()->getContents()))->recursive();

        if($responseCollection->get('stat') != "ok") {
            return $responseCollection;
        }

        $data = $responseCollection->get('photos');

        $response = [
            'number_of_results' => (int) $data->get('total'),
            'pages' => $data->get('pages'),
            'images' => $this->getPhotosInfo($data->get('photo'))
        ];

        return $response;
    }

    private function getPhotosInfo(Collection $photos) {
        return $photos->map(function($photo) {

            $imageSizeAndUrl = $this->getImageExtraInfo($photo->get('id'));

            $size = $imageSizeAndUrl->get('size');
            $url = $imageSizeAndUrl->get('url');
            $source = $imageSizeAndUrl->get('source');
            $type = $imageSizeAndUrl->get('type');

            return [
                'title' => $photo->get('title'),
                'size' => $size,
                'type' => $type,
                'url_to_image' => $url,
                'url_to_original_image' => $source,
                'provider' => 'Flickr'
            ];
        });
    }

    private function getImageExtraInfo($photoId) {
        $res = $this->client->get($this->flickrApiUrl,
            [
                'query' => [
                    'method' => 'flickr.photos.getSizes',
                    'api_key' => 'b8d129851d412d7fb41628233868f70d',
                    'photo_id' => $photoId,
                    'format' => 'json',
                    'nojsoncallback' => '1'
                ]
            ]);

            $imageInfo = collect(json_decode($res->getBody()))->recursive();

            if ($imageInfo->get('stat') == 'ok') {
                $imageInfo = $imageInfo->get('sizes')->get('size')->filter(function($sizeOption) {
                    return $sizeOption->get('label') == "Medium";
                })->values()->first();

                $info = [
                    'size' => [
                        'width' => $imageInfo->get('width'),
                        'height' => $imageInfo->get('height')
                    ],
                    'url' => $imageInfo->get('url'),
                    'source' => $imageInfo->get('source'),
                    'type' => collect(explode('.', $imageInfo->get('source')))->last()
                ];

                return collect($info);
            }

        return collect();
    }

    public function getProviderInfo() {
        return ['Flickr' => "https://www.flickr.com/"];
    }
}
