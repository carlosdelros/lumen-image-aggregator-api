<?php

namespace App\Services;

use Illuminate\Support\Collection;

class ImageAggregator {

    private $providers;

    public function __construct(Collection $providers){
        $this->providers = $providers;
    }

    /**
     * Fetches images utilizing different providers
     *
     * @param array $params query parameters
     * @return void
     */
    public function fetchImages($params){

        $images = collect();

        $this->providers->each(function($provider) use (&$images, $params) {
            $images->put($provider->name, $provider->fetchImages($params));
        });

        return $images;
    }

    /**
     * Returns a list of currently supported providers
     *
     * @return Collection
     */
    public function getProviders() {
        return $this->providers->map(function($provider) {
            return $provider->getProviderInfo();
        });
    }
}


