<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index(){
        // Return the view found at resources/views/finance/index.blade.php
        return view('finance.index');
    }
}
