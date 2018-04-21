<?php

namespace App\Http\Controllers;

use App\Services\ImageAggregator;

class ProvidersController extends Controller
 {
    private $aggreagtor;

    public function __construct(ImageAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function show() {
        return $this->aggregator->getProviders();
    }

 }
