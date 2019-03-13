<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/about', 'HomeController@about')->name('about');

Route::group(['middleware' => 'usertype'], function(){

	Route::get('/manageusers', 'UserController@index')->name('users');

	Route::get('/addnewuserdialog', 'UserController@addnewuserdialog')->name('addnewuserdialog');

	Route::post('/addnewuser', 'UserController@addnewuser')->name('addnewuser');

    Route::get('/viewuserdialog/{id}', 'UserController@viewuserdialog')->name('viewuserdialog');

	Route::get('/edituserdialog/{id}', 'UserController@edituserdialog')->name('edituserdialog');

	Route::get('/changepassdialog/{id}', 'UserController@changepassdialog')->name('changepassdialog');

	Route::post('/updateuser', 'UserController@updateuser')->name('updateuser');

	Route::post('/changepassword', 'UserController@changepassword')->name('changepassword');

});

// Begin Salesman Routes

Route::get('/addnewsalesmandialog', 'SalesmanController@addNewSalesmanDialog')->name('addnewsalesmandialog');

Route::post('/addnewsalesman', 'SalesmanController@addNewSalesman')->name('addnewsalesman');

Route::get('/searchsalesman', 'SalesmanController@searchSalesman')->name('searchSalesman');

Route::get('/salesman', 'SalesmanController@show')->name('salesman');

Route::get('/viewsalesmandialog/{id}', 'SalesmanController@viewsalesmandialog')->name('viewsalesmandialog');

Route::get('/editsalesmandialog/{id}', 'SalesmanController@editsalesmandialog')->name('editsalesmandialog');

Route::post('/getsalesman','SalesmanController@getAllSalesman')->name('getsalesman');

Route::post('/updatesalesman', 'SalesmanController@updatesalesman')->name('updatesalesman');

//Begin Customer Routes

Route::get('/addnewcustomerdialog', 'CustomerController@addNewCustomerDialog')->name('addnewcustomerdialog');

Route::post('/addnewcustomer', 'CustomerController@addNewCustomer')->name('addnewcustomer');

Route::get('/searchcustomer', 'CustomerController@searchCustomer')->name('searchCustomer');

Route::get('/customers', 'CustomerController@show')->name('customers');

Route::post('/getcustomers','CustomerController@getAllCustomers')->name('getcustomers');

Route::get('/viewcustomerdialog/{id}', 'CustomerController@viewcustomerdialog')->name('viewcustomerdialog');

Route::get('/editcustomerdialog/{id}', 'CustomerController@editcustomerdialog')->name('editcustomerdialog');

Route::post('/updatecustomer', 'CustomerController@updatecustomer')->name('updatecustomer');


// Begin Bank Routes

Route::get('/addnewbankdialog', 'BankController@addNewBankDialog')->name('addnewbankdialog');

Route::post('/addnewbank', 'BankController@addNewBank')->name('addnewbank');

Route::get('/searchbankbranch', 'BankController@searchBankBranch')->name('searchBankBranch');

Route::get('/banks', 'BankController@show')->name('banks');

Route::post('/getbanks','BankController@getAllBanks')->name('getbanks');

Route::get('/viewbankdialog/{id}', 'BankController@viewbankdialog')->name('viewbankdialog');

Route::get('/editbankdialog/{id}', 'BankController@editbankdialog')->name('editbankdialog');

Route::post('/updatebank', 'BankController@updatebank')->name('updatebank');

// Begin Check Routes 
Route::get('/viewcheckupdateRedeposit/{id}', 'CheckController@viewcheckupdateRedeposit')->name('viewcheckupdateRedeposit');

Route::get('/viewcheckupdateReplacement/{id}', 'CheckController@viewcheckupdateReplacement')->name('viewcheckupdateReplacement');

Route::get('/viewcheckupdateCash/{id}', 'CheckController@viewcheckupdateCash')->name('viewcheckupdateCash');

// ===============================================================================================

Route::get('/savebouncedcheck', 'CheckController@savebouncedcheck')->name('savebouncedcheck');

Route::get('/createcheckdialog', 'CheckController@createCheckDialog')->name('createcheckdialog');

Route::get('/checkreceiving', 'CheckController@index')->name('receiving');

Route::post('/savecheck', 'CheckController@saveCheck')->name('savecheck');

Route::get('/deletecheck/{key}', 'CheckController@deleteCheck')->name('deletecheck');

Route::post('/addtocart', 'CheckController@getAddToCart')->name('addtocart');

Route::post('/editcart', 'CheckController@editCart')->name('editcart');

Route::get('/removecheck/{key}', 'CheckController@removeCheck')->name('removecheck');

Route::get('/taggedasbounced/{id}', 'CheckController@taggedaAsBounced')->name('taggedasbounced');

Route::get('/checks', 'CheckController@checkStatus')->name('checks');

Route::post('/checkliststatus', 'CheckController@checkStatusList' )->name('checkliststatus');

Route::post('/addtocartupload', 'CheckController@addToCartUpload')->name('addtocartupload');

Route::post('/pdctagging', 'CheckController@pdcTagging')->name('pdctagging');

Route::post('/updatepdc', 'CheckController@updatePDC')->name('updatepdc');

Route::post('/pdctagging2', 'CheckController@pdcTagging2')->name('pdctagging2');

Route::post('/checktagging', 'CheckController@checkTagging')->name('checktagging');

Route::post('/checktagging2', 'CheckController@checkTagging2')->name('checktagging2');

Route::get('/editcheckdialog/{key}', 'CheckController@editCheckCart')->name('editcheckdialog');

Route::get('/showcheckdialog/{key}', 'CheckController@showcheckdialog')->name('showcheckdialog');

Route::get('/viewcheckdialog/{id}', 'CheckController@viewCheck')->name('viewcheckdialog');

//Route::get('/viewcheckdialog2/{id}', 'CheckController@viewCheck2')->name('viewcheckdialog2');

Route::get('/tagasdialog/{id}/{state}', 'CheckController@tagAs')->name('tagasdialog');

Route::get('/checklist', 'CheckController@checkList')->name('checklist');

Route::get('/checklistpdc', 'CheckController@checkListpdc')->name('checklistpdc');

Route::get('/dbupdatefromatp','CheckController@updateDBfromAtpDB')->name('dbupdatefromatp');

Route::get('/processdbupdatefromatp','CheckController@processDBupdateFromAtp')->name('processdbupdatefromatp');

Route::get('/institutionalimport','CheckController@institutionalImport')->name('institutionalimport');

Route::get('/institutional', 'CheckController@institutional' )->name('institutional');

Route::get('/receivingupload', 'CheckController@receivingUpload')->name('receivingupload');

Route::get('/duechecks', 'CheckController@dueChecks')->name('duechecks');

Route::get('/clearedchecks', 'CheckController@clearedChecks')->name('clearedchecks');

Route::get('/checksforclearing', 'CheckController@ChecksForClearing')->name('checksforclearing');

Route::get('/viewclearedbytrid/{id}', 'CheckController@viewClearedbytrid')->name('viewclearedbytrid');

Route::get('/bouncedchecks', 'CheckController@BouncedChecks')->name('bouncedchecks');

Route::get('/bouncedchecks2', 'CheckController@BouncedChecks2')->name('bouncedchecks2');

Route::get('/updatebounced/{id}/{type}', 'CheckController@UpdateBouncedChecks')->name('updatebounced');

Route::get('/updatebounced2/{id}', 'CheckController@UpdateBouncedChecks2')->name('updatebounced2');

Route::post('/updatebouncedchecktemp', 'CheckController@UpdateBouncedChecksTemp')->name('updatebouncedchecktemp');

// Route::post('/updatebouncedchecktemp2', 'CheckController@UpdateBouncedChecksTemp2')->name('updatebouncedchecktemp2');

Route::get('/checksfordeposit', 'CheckController@ChecksForDeposit')->name('checksfordeposit');

Route::get('/duepdcexport', 'CheckController@duePdcExport')->name('duepdcexport');

Route::get('/checksforClearingExport', 'CheckController@checksforClearingExport')->name('checksforClearingExport');

Route::get('/checksforDepositExport', 'CheckController@checksforDepositExport')->name('checksforDepositExport');

Route::get('/checksPDCExport', 'CheckController@checksPDCExport')->name('checksPDCExport');

Route::get('/bouncedChecksExport', 'CheckController@bouncedChecksExport')->name('bouncedChecksExport');

Route::get('/exportsample','ExcelController@ExportSample')->name('exportssample');

Route::get('/uploadbank','ExcelController@UploadBank')->name('uploadbank');

Route::post('/importbanks','ExcelController@ImportBanks')->name('importbanks');

Route::post('/allusers', 'UserController@allUsers' )->name('allusers');

Route::get('/test','SalesmanController@test')->name('test');

Route::get('/checkexist/{id}/{status}','CheckController@checkexist')->name('checkexist');

Route::post('/getcheck','CheckController@getCheck')->name('getcheck');

Route::get('/getcheckreport/{date}','CheckController@getCheckReport')->name('getcheckreport');

Route::get('/checkreports', 'CheckController@checkReports')->name('checkreports');

Route::get('/allcheck',function(){
    $oldCart = Session::has('cart') ? Session::get('cart') : null;
    dd($oldCart);
});

Route::get('/testing',function(){

    $checks = DB::connection('sqlsrv')
    ->table('chk_dtl')
    ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
    ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
    // ->where('chk_dtl.entry_no','>','0')
    ->where('chk_dtl.chkno','=','0059414966')
    // ->where('chk_dtl.chkno','6455272')
    ->orderBy('entry_no', 'asc')
    ->select(
        'chk_mst.issue_no',
        'chk_mst.atp_date',
        'chk_dtl.entry_no',        
        'chk_dtl.chkclass',
        'chk_dtl.chktype',
        'chk_dtl.chkdate',
        'chk_dtl.chkno',
        'chk_dtl.bankname',
        'chk_dtl.brstn_rtno',
        'chk_dtl.actno',
        'chk_dtl.actname',
        'chk_dtl.chkamt',
        'customer.clastname',
        'customer.cfirstname',
        'customer.cmiddname',
        'customer.extension'
    )
    //->first();
    ->get();
    dd($checks);
    exit();
    // $users = DB::table('users')->get();
    // dd($users);
    $users = DB::connection('sqlsrv')->table('chk_mst')->first();
    // $table = DB::connection('sqlsrv')->table('chk_dtl')->get(); 
    //$table = DB::connection('sqlsrv')->select('SHOW TABLES');    
    dd($users);
    // $user = Auth::user(); 
    //echo Auth::user()->businessunit->loc_code_atp;
    // exit();

    exit();

    $checks = DB::connection('sqlsrv')
    ->table('chk_dtl')
    ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
    ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
    ->where('chk_mst.loc_code', Auth::user()->businessunit->loc_code_atp)
    // ->where('chk_dtl.entry_no','>','0')
    ->where('chk_dtl.chktype','=','PD')
    // ->where('chk_dtl.chkno','6455272')
    ->orderBy('entry_no', 'asc')
    ->select(
        'chk_mst.issue_no',
        'chk_mst.atp_date',
        'chk_dtl.entry_no',        
        'chk_dtl.chkclass',
        'chk_dtl.chktype',
        'chk_dtl.chkdate',
        'chk_dtl.chkno',
        'chk_dtl.bankname',
        'chk_dtl.brstn_rtno',
        'chk_dtl.actno',
        'chk_dtl.actname',
        'chk_dtl.chkamt',
        'customer.clastname',
        'customer.cfirstname',
        'customer.cmiddname',
        'customer.extension'
    )
    //->first();
    ->get();
    foreach($checks as $ch)
    {
        $cdate = explode(" ",$ch->atp_date);
        echo $cdate[0];
    }
    //dd($checks);
    // for($x=0; $x<=10; $x++)
    // {
    //     usleep(80000);
    //     echo $checks[$x]->chkno;

    //     if($x==10)
    //     {
    //         break;
    //     }
    // }
    //$daex = explode(" ", $checks->chkdate);
    //echo $daex[0];
    // foreach($checks as $ch)
    // {
    //     //$chz = preg_replace('!\s+!', ' ', $ch->cfirstname.' '.$ch->cmiddname.' '.$ch->clastname.''.$ch->extension);
    //     //echo trim($chz).'<br />';
    //     echo $ch->chkdate.'<br>';
    // }
});

Route::get('/getdatedcheck',function(){
    $checks = DB::connection('sqlsrv')
    ->table('chk_dtl')
    ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
    ->where('chk_dtl.chkno','1332004545')
    ->orderBy('entry_no', 'desc')
    ->select(
        'chk_dtl.*',
        'chk_mst.*'
        // 'chk_mst.issue_no',
        // 'chk_mst.atp_date',
        // 'chk_dtl.entry_no',        
        // 'chk_dtl.chkclass',
        // 'chk_dtl.chktype',
        // 'chk_dtl.chkdate',
        // 'chk_dtl.chkno',
        // 'chk_dtl.bankname',
        // 'chk_dtl.brstn_rtno',
        // 'chk_dtl.actno',
        // 'chk_dtl.actname',
        // 'chk_dtl.chkamt',
        // 'customer.clastname',
        // 'customer.cfirstname',
        // 'customer.cmiddname',
        // 'customer.extension'
    )
    //->first();
    ->get();

    dd($checks);

});

Route::get('/testingcheck',function(){

    echo "The time is " . date("h:i:sa");
    exit();

    // $users = DB::table('users')->get();
    // dd($users);
    // $users = DB::connection('sqlsrv')->table('chk_dtl')->get();
    // $table = DB::connection('sqlsrv')->table('chk_dtl')->get(); 
    // dd($table);
    // $user = Auth::user(); 
    //echo Auth::user()->businessunit->loc_code_atp;
    // exit();

    // //$data = DB::connection('sqlsrv')->getSchemaBuilder()->getColumnListing('chk_dtl');

    // dd($data);

    //testing


    // exit();


    $checks = DB::connection('sqlsrv')
    ->table('chk_dtl')
    ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
    ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
    ->where('chk_mst.atp_date', '>=', '2019-02-02 00:00:00')
    //->where('chk_dtl.Chkdate', '>', '2019-01-31')
    ->where('Loc_Code', '=', 'ICM')
    ->where('chk_dtl.actno','190106000472')
    // ->where(function ($query) {
    //     $query->whereRaw("REPLACE(chk_dtl.ChkExpiry,' ', '') ='//'");
    //     //->orWhereNotNull('chk_dtl.ChkExpiry');
    //     //whereRaw('REPLACE("chk_dtl.Category", " ", "")!=""')
    //         //   ->orWhereNotNull('chk_dtl.ChkExpiry')
    //         //->orWhere('chk_dtl.ChkExpiry','!=','');
    // })
    //->where('chk_dtl.ChkExpiry','!=','')
    //->whereNull('chk_dtl.Category')
    //->whereRaw('chk_dtl.Category')
    //->where('chk_dtl.ChkExpiry','!=','  /  /    ')
    //->whereRaw("REPLACE(chk_dtl.ChkExpiry,' ', '') =''")
    // ->where('chk_dtl.entry_no','>','0')
    // ->where('chk_dtl.chktype','=','PD')
    // ->where('chk_dtl.chkno','6455272')
    ->orderBy('entry_no', 'desc')
    ->select(
        'chk_dtl.*',
        'chk_mst.*'
        // 'chk_mst.issue_no',
        // 'chk_mst.atp_date',
        // 'chk_dtl.entry_no',        
        // 'chk_dtl.chkclass',
        // 'chk_dtl.chktype',
        // 'chk_dtl.chkdate',
        // 'chk_dtl.chkno',
        // 'chk_dtl.bankname',
        // 'chk_dtl.brstn_rtno',
        // 'chk_dtl.actno',
        // 'chk_dtl.actname',
        // 'chk_dtl.chkamt',
        // 'customer.clastname',
        // 'customer.cfirstname',
        // 'customer.cmiddname',
        // 'customer.extension'
    )
    //->first();
    ->get();
    //dd($checks);
    // foreach($checks as $c) {
    //     $checkep = '2017/20/21';
    //     $checkexpiry = '';      
    //     if(str_replace(' ','',$c->ChkExpiry)=='//' || trim($c->ChkExpiry)=='')
    //     {
    //         $checkexpiry = NULL;
    //     }
    //     else 
    //     {
    //         $checkexpiry = $checkep;
    //     }
    //     echo $checkexpiry.'<br/>';
    //     echo 'Check # => '.$c->ChkNo.'Expiry =>'.$checkexpiry.' Check Category =>'.$c->Category.'<br />'.' Check Date =>'.$c->ChkDate;
    // }

    dd($checks);
    exit();

    $checkspdc = DB::connection('sqlsrv')
    ->table('chk_dtl')
    ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
    ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
    //->where('chk_mst.atp_date', '<', '2019-01-31 00:00:00')
    ->where('chk_dtl.Chkdate', '>', '2019-01-31')
    ->where('Loc_Code', '=', 'ICM')
    // ->where(function ($query) {
    //     $query->whereRaw("REPLACE(chk_dtl.ChkExpiry,' ', '') ='//'");
    //     //->orWhereNotNull('chk_dtl.ChkExpiry');
    //     //whereRaw('REPLACE("chk_dtl.Category", " ", "")!=""')
    //         //   ->orWhereNotNull('chk_dtl.ChkExpiry')
    //         //->orWhere('chk_dtl.ChkExpiry','!=','');
    // })
    //->where('chk_dtl.ChkExpiry','!=','')
    //->whereNull('chk_dtl.Category')
    //->whereRaw('chk_dtl.Category')
    //->where('chk_dtl.ChkExpiry','!=','  /  /    ')
    //->whereRaw("REPLACE(chk_dtl.ChkExpiry,' ', '') =''")
    // ->where('chk_dtl.entry_no','>','0')
    // ->where('chk_dtl.chktype','=','PD')
    // ->where('chk_dtl.chkno','6455272')
    ->orderBy('entry_no', 'desc')
    ->select(
        'chk_dtl.*',
        'chk_mst.*'
        // 'chk_mst.issue_no',
        // 'chk_mst.atp_date',
        // 'chk_dtl.entry_no',        
        // 'chk_dtl.chkclass',
        // 'chk_dtl.chktype',
        // 'chk_dtl.chkdate',
        // 'chk_dtl.chkno',
        // 'chk_dtl.bankname',
        // 'chk_dtl.brstn_rtno',
        // 'chk_dtl.actno',
        // 'chk_dtl.actname',
        // 'chk_dtl.chkamt',
        // 'customer.clastname',
        // 'customer.cfirstname',
        // 'customer.cmiddname',
        // 'customer.extension'
    )
    //->first();
    ->get();


    
    // echo count($checks).'<br />';
    // echo count($checkspdc).'<br />';


    // $merged = $checks->merge($checkspdc); 
    
    // echo count($merged);

    //dd($checks);
    // for($x=0; $x<=10; $x++)
    // {
    //     usleep(80000);
    //     echo $checks[$x]->chkno;

    //     if($x==10)
    //     {
    //         break;
    //     }
    // }
    //$daex = explode(" ", $checks->chkdate);
    //echo $daex[0];
    // foreach($checks as $ch)
    // {
    //     //$chz = preg_replace('!\s+!', ' ', $ch->cfirstname.' '.$ch->cmiddname.' '.$ch->clastname.''.$ch->extension);
    //     //echo trim($chz).'<br />';
    //     echo $ch->chkdate.'<br>';
    // }
});

Route::get('/testingcon','CheckController@testFunc')->name('testingcon');

Route::get('/test',function(){
    dd(Session::get('bouncecart'));
});

Route::get('/truncatedb','CheckController@truncatedb')->name('truncatedb');

Route::get('/checkfiles','CheckController@checkfiles')->name('checkfiles');

