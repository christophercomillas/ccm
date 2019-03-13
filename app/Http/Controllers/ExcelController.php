<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Input;
use Validator;
use Auth;
use App\Bank;
use DB;

class ExcelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    public function ExportSample()
    {
    	Excel::create('clients',function($excel){
    		$excel->sheet('clients',function($sheet){
    			$sheet->loadView('reports.exportsample');
    		});
    	})->export('xlsx');
    }

    public function UploadBank()
    {
    	$title = 'Upload Bank';
    	return view('bank.uploadbanks',compact('title'));
    }

    public function ImportBanks(Request $request)
    {
    	//$file = Input::file('file');

        // foreach($request->only('file') as $file)
        // {
        //     //dd($files);
        //     //echo 'sulod';
        //     echo $file->getClientOriginalName();
        // }

        //echo count($request->only('files'));
 
        // if ($request->hasFile('files')) 
        // {
        //     echo 'naa';   
        // }

        $mimeTypes = [
            'application/csv', 'application/excel',
            'application/vnd.ms-excel', 'application/vnd.msexcel',
            'text/csv', 'text/anytext', 'text/plain', 'text/x-c', 
            'text/comma-separated-values',
            'inode/x-empty',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];


        foreach($request->only('files') as $files)
        {
            foreach ($files as $file) 
            {
                if(in_array($file->getClientMimeType(), $mimeTypes))
                {
                    $filname = $file->getClientOriginalName();
                }
            }
        }

        $response['st'] = true;
        echo json_encode($response);

        //dd(Input::all());

    	// $file_name = $file->getClientOriginalName();
    	// $file->move('files',$file_name);
    	// $results = Excel::load('files/'.$file_name,function($reader)
    	// {
    	// 	$reader->all();
    	// })->get();

    	//$file_name = $file->getClientOriginalName();
    	//$file->move('files',$file_name);


    	// $results = Excel::load($file,function($reader)
    	// {
    	// 	$reader->all();
    	// })->get();

     //    try
     //    {
     //        DB::beginTransaction();

     //        /*
     //         * Your DB code
     //         * */
     //        foreach ($results as $r ) 
     //        {

     //            $bank = new Bank;

     //            $bank->bankcode         = $r->fldbranchrtno;
     //            $bank->bankbranchname   = strtoupper($r->fldbranchname);
     //            $bank->created_by       = Auth::user()->id;           
     //            $bank->save();

     //            // echo $r->fldbranchrtno.'<br>';
     //            // echo $r->fldbranchname.'<br>';
     //        }

     //        DB::commit();
     //    }
     //    catch(\Exception $e)
     //    {
     //        echo $e->getMessage();            

     //        DB::rollback();
     //    }

    	//return view('banks',['banks'=> $results]);
    }

}
