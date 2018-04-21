<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;
use App\Services\ImageAggregator;

class ImagesController extends Controller
{
    private $provider;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ImageAggregator $aggregator)
    {
        $this->aggregator = $aggregator;
    }

    public function show(Request $request) {

        if ($request->has('q')) {

            $params = [
                'search_term' => $request->input('q'),
                'page' => $request->input('page', '1'),
                'per_page' => $request->input('limit', '1')
            ];

            return response()->json($this->aggregator->fetchImages($params));
        }
    }
}
