<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class DisciplinaryController extends Controller
{
    public function index()
    {

        return view("disciplinary.disciplinary");

    }

}