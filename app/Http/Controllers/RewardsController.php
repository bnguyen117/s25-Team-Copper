<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RewardsController extends Controller
{
    public function index(){
        // Return the view found at resources/views/rewards/index.blade.php
        return view('rewards.index');
    }
}
