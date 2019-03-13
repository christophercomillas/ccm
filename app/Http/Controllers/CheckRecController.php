<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckRecController extends Controller
{
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
        $title = 'Check Receiving';

        return view('check.checkreceiving',compact('title'));
    }

    public function createCheckDialog()
    {
        return view('check.create');
    }
}
