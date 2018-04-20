<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Iluminate\Http\Response;
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
        $this->imageServices = func_get_args();
    }

    public function show(Request $request) {

        if ($request->has('q')) {
            $params = [
                'search_term' => $request->input('q'),
                'page' => $request->input('page', '1'),
                'per_page' => $request->input('limit', '1')
            ];

            return response()->json($this->imageServices[0]->fetchImages($params));
        }
    }
}
