<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    public function investment(){
        return view('pages.investment.investment');
    }
}
