<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class PerformanceEvaluationController extends Controller {

    public function index() 
    {
        return view('performance_evaluation.performance_eval');
    }
}