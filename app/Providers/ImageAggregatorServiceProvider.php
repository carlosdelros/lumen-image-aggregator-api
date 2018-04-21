<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ImageProviders\Flickr;
use App\Services\ImageProviders\Pixabay;
use App\Services\ImageAggregator;

class ImageAggregatorServiceProvider extends ServiceProvider {

    public function boot() {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

        // Collection of providers supported by the API
        $providers = collect([
            new Flickr(),
            new Pixabay()
        ]);

        $this->app->bind('App\Services\ImageAggregator', function($app) use ($providers ){
            return new ImageAggregator($providers);
        });
    }
}
