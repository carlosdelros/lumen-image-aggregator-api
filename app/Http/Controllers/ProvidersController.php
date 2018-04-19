<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProvidersController extends Controller
 {
    public function __construct()
    {

    }

    public function show(Request $request) {
        return "list of providers";
    }

 }
