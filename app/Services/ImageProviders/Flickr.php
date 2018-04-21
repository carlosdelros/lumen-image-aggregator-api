<?php

namespace App\Services\ImageProviders;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;

class Flickr {

    private $flickrApiUrl;
    private $API_KEY;
    private $client;
    public $name = 'Flickr';

    public function __construct() {
        $this->client = new Client();
        $this->flickrApiUrl = "https://api.flickr.com/services/rest/";
        $this->API_KEY = env('FLICKR_API_KEY'); // Api key contained in .env file
    }

    /**
     * Fetches images from the current provider
     *
     * @param array query parameters
     * @return array
     */
    public function fetchImages($params) {
        $res = $this->client->get($this->flickrApiUrl,
        [
            'query' => [
                'method' => 'flickr.photos.search',
                'api_key' => $this->API_KEY,
                'text' => $params['search_term'],
                'per_page' => $params['per_page'],
                'page' => $params['page'],
                'format' => 'json',
                'nojsoncallback' => '1'
            ]
        ]);

        $imagesCollection = collect(json_decode($res->getBody()->getContents()))->recursive();

        if($imagesCollection->get('stat') != "ok") {
            return $imagesCollection;
        }

        $data = $imagesCollection->get('photos');

        $images = [
            'number_of_results' => (int) $data->get('total'),
            'pages' => (int) $data->get('pages'),
            'images' => $this->preparePhotos($data->get('photo'))
        ];

        return $images;
    }

    /**
     * Prepares the different data needed in our image payload on each fetched image
     *
     * @param Collection $photos
     * @return Collection
     */
    private function preparePhotos(Collection $photos) {
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
            ];
        });
    }

    /**
     * Gets extra information needed in our image payload that is not included in the initial request
     *
     * @param string $photoId
     * @return Collection
     */
    private function getImageExtraInfo($photoId) {
        $res = $this->client->get($this->flickrApiUrl,
            [
                'query' => [
                    'method' => 'flickr.photos.getSizes',
                    'api_key' => $this->API_KEY,
                    'photo_id' => $photoId,
                    'format' => 'json',
                    'nojsoncallback' => '1'
                ]
            ]);

            $imageInfo = collect(json_decode($res->getBody()))->recursive();

            if ($imageInfo->get('stat') == 'ok') {
                $sizes = $imageInfo->get('sizes')->get('size');

                // Flickr image size provided is inconsisten; check available sizes and choose the best provided size.
                $availableSize = $this->getBestAvailableSize($sizes->pluck('label'));

                $imageInfo = $sizes->filter(function($sizeOption) use ($availableSize) {
                    return $sizeOption->get('label') == $availableSize;
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


    /**
     * Selects the best available size supported in an image
     *
     * @param Collection $sizes Collection of sizes provided for a current picture
     * @return string Best available size
     */
    private function getBestAvailableSize($sizes) {
        if ($sizes->contains("Original")) {
            return "Original";
        } else if ($sizes->contains("Large")) {
            return "Large";
        } else if ($sizes->contains("Medium")) {
            return "Medium";
        } else if ($sizes->contains("Small")) {
            return "Small";
        } else if ($sizes->contains("Thumbnail")) {
            return "Thumbnail";
        } else {
            return "Square";
        }
    }

    /**
     * Returns prover's name and url
     *
     * @return array
     */
    public function getProviderInfo() {
        return [$this->name => "https://www.flickr.com/"];
    }
}
