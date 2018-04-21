<?php

namespace App\Services\ImageProviders;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use GuzzleHttp\Exception\GuzzleException;

class Pixabay {

    private $pixabayApiUrl;
    private $client;
    public $name = 'Pixabay';


    public function __construct() {
        $this->client = new Client();
        $this->flickrApiUrl = "https://pixabay.com/api/";
        $this->API_KEY = env('PIXABAY_API_KEY'); // Api key contained in .env file
    }

    /**
     * Fetches images from the current provider
     *
     * @param array query parameters
     * @return array
     */
    public function fetchImages($params) {

        try {
            $res = $this->client->get($this->flickrApiUrl,
            [
                'query' => [
                    'key' => $this->API_KEY,
                    'q' => $params['search_term'],
                    'image_type' => 'photo',
                    'pretty' => 'true',
                    'per_page' => $params['per_page'],
                    'page' => $params['page']
                ]
            ]
            );
        } catch (GuzzleException $e) {
            $errorMessage = $e->getResponseBodySummary($e->getResponse());
            return ['error' => $errorMessage];
        }

        if($res->getStatusCode() == 200) {
            $imagesCollection = collect(json_decode($res->getBody()->getContents()))->recursive();

            $images = [
                'number_of_results' => (int) $imagesCollection->get('totalHits'),
                'pages' => ceil($imagesCollection->get('totalHits') / $params['per_page']),
                'images' => $this->preparePhotos($imagesCollection->get('hits'))
            ];

            return $images;
        }

        return $res->getReasonPhrase();
    }

    /**
     * Prepares the different data needed in our image payload on each fetched image
     *
     * @param Collection $photos
     * @return Collection
     */
    private function preparePhotos(Collection $photos) {
        return $photos->map(function($photo) {
            $path = explode('/', parse_url($photo->get('pageURL'), PHP_URL_PATH));
            $title = $path[count($path)-2];
            return [
                'title' => $title,
                'size' => [
                    "width" => $photo->get('imageWidth'),
                    "height" => $photo->get('imageHeight')
                ],
                // Pixabay photo do not contain titles. Title is being extracted from url path.
                'type' => collect(explode('.', $photo->get('webformatURL')))->last(),
                'url_to_image' => $photo->get('pageURL'),
                'url_to_original_image' => $photo->get('webformatURL'),
                'provider' => 'Pixabay'
            ];
        });
    }

    /**
     * Returns prover's name and url
     *
     * @return array
     */
    public function getProviderInfo() {
        return [$this->name => "https://pixabay.com/"];
    }
}
