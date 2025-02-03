<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(){
        // Return the view found at resources/views/community/index.blade.php
        return view('community.index');
    }
}
