<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WhatIfController extends Controller
{
    public function index(){
        // Return the view found at resources/views/what-if/index.blade.php
        return view('what-if.index');
    }
}
