<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Flickr;

class FlickrServiceProvider extends ServiceProvider {
    public function boot() {

    }

    public function register() {
        $this->app->bind('App\Services\Flickr', function($app) {
            return new Flickr();
        });
    }
}
