<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ImageAggregator {

    private $providers;

    public function __construct(Collection $providers){
        $this->providers = $providers;
    }

    public function fetchImages($params){

        $images = collect();

        $this->providers->each(function($provider) use (&$images, $params) {
            $images = $images->merge($provider->fetchImages($params));
        });

        return $images;
    }

    public function getProviders() {
        return $this->providers->map(function($provider) {
            return $provider->getProviderInfo();
        });
    }
}


