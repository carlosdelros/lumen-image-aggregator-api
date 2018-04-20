<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use lluminate\Http\Response;
use App\Services\Flickr;

class ImagesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Flickr $flickr)
    {
        $this->imageService = $flickr;
    }

    public function show(Request $request) {

        if ($request->has('q')) {
            $params = [
                'search_term' => $request->input('q'),
                'page' => $request->input('page', '1'),
                'limit' => $request->input('limit', '100')
            ];

            $this->imageService->fetchImages($params);

            return response()->json();
        }
    }
}
