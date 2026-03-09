<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class NteController extends Controller
{
    public function index()
    {

        return view("nte_management.nte");

    }

}