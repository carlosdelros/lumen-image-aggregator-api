<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageProviders\Flickr;
use App\Services\ImageAggregator;

class ImageAggregatorServiceProvider extends ServiceProvider {
    public function boot() {

    }

    public function register() {
        $providers = collect([
            new Flickr()
        ]);
        $this->app->bind('App\Services\ImageAggregator', function($app) use ($providers ){
            return new ImageAggregator($providers);
        });
    }
}
