<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;

class IncidentReportController extends Controller
{
    public function index()
    {

        return view("incident_report.incident_report");

    }

}