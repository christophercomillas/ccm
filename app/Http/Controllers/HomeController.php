<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Auth;
use App\Check;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //echo Auth::user()->businessunit_id;

        $title = 'Dashboard';
        $arr = array('x','76');
        $status = 'POST DATED';

        $checkpdc = Check::where('checks.check_date', '>', date('Y-m-d'))
                        ->where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        $checks = Check::where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        $datedChecks = Check::where('checks.check_date', '<=', date('Y-m-d'))
                        ->where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        $cleared = Check::where('check_status', '=', "CLEARED")
                        ->where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        $bounced = Check::where('check_status', '=', "BOUNCED")
                        ->where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        $cash  = Check::where('check_status', '=', "CASH")
                        ->where('checks.businessunit_id', '=', Auth::user()->businessunit_id)
                        ->count();

        return view('home',compact('title','arr','checkpdc', 'checks', 'datedChecks', 'cleared','bounced','cash'));
    }

    public function about()
    {
        $title = 'About CCMS';

        return view('about',compact('title'));
    }
}
