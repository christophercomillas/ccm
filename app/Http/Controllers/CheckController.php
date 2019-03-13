<?php

namespace App\Http\Controllers;
use App\Http\Traits\FunctionTrait;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
//use App\Checks;
use App\Cart;
Use App\BusinessUnit;
use App\BounceCart;
use Validator;
use App\CheckHistory;

use Illuminate\Http\Request;
use Session;
use App\CheckReceived;
use App\Check;
use App\CheckTagging;
use App\CheckTaggingItem;
use App\AppSettings;
use App\Currency;
use App\Customer;
use DB;
use Auth;
use Storage;
use File;

class CheckController extends Controller
{

    public $checkclass;
    use FunctionTrait;

    public function __construct()
    {
        $this->middleware('auth');
        $this->checkclass = collect(["PERSONAL","GOVERNMENT","COMPANY","SUPPLIER"]);
        $this->checkcategory = collect(["LOCAL","REGIONAL","MANILA","NATIONAL"]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $oldCart = Session::has('cart') ? Session::get('cart') : null;
        // $cart = new Cart($oldCart);
        // // end($cart->checks);
        // // $ckey =  key($cart->checks);
        // // echo $ckey;
        // //dd($cart->checks);
        // //dd($cart);
        // $val = intval(2);
        // foreach($cart->checks as $key => $value)
        // {
        //     if($key==intval($val))
        //     {
        //         //$data[$key]['transaction_date'] = date('d/m/Y',$value['transaction_date']);
        //         $cart->checks[$key]['customerid'] = '11';
        //     }
        // }

        // dd($cart->checks);

        $title = 'Check Receiving';
        $ldate = date('F j, Y', strtotime(date('m/d/Y')));


        //receiving controller number
        $controlno = 0;
        $controlno = $this->getControlNumber();
        Session::forget('cart');
        return view('check.checkreceiving',compact('title','ldate','controlno'));
    }

    public function getControlNumber()
    {
        $code = 0;
        $ctrlno = CheckReceived::limit(1)
            ->where('company_id',Auth::user()->company_id)
            ->where('businessunit_id',Auth::user()->businessunit_id)
            ->orderBy('checksreceivingtransaction_ctrlno', 'desc')
            ->get();

        $cnt = count($ctrlno);

        if($cnt > 0)
        {
            $code = $ctrlno[0]['checksreceivingtransaction_ctrlno'];
            return ++$code;
        }
        else 
        {
            $data = AppSettings::where('app_key','app_check_controlno_start')
               ->get();

            return $data[0]['app_value'];
        }
    }

    public function createCheckDialog()
    {
        $currency = Currency::all();

        return view('check.create',array(
            'checkclass'    => $this->checkclass,
            'category'      => $this->checkcategory,
            'currency'      => $currency
        ));
    }

    public function saveCheck(Request $request)
    {

        $hasError = false;
        DB::beginTransaction();

            $checkRec = new CheckReceived;

            $checkRec->checksreceivingtransaction_ctrlno    = $this->getControlNumber();
            $checkRec->salesman_id                          = $request->smanid;
            $checkRec->id                                   = Auth::user()->id;         
            $checkRec->company_id                           = Auth::user()->company_id;
            $checkRec->businessunit_id                      = Auth::user()->businessunit_id;

            $checkRec->save();

            $id = $checkRec->checksreceivingtransaction_id;            

            $oldCart = Session::has('cart') ? Session::get('cart') : null;
            $cart = new Cart($oldCart);

            if(is_null($oldCart))
            {
                DB::rollback();
                $validator->getMessageBag()->add('check', 'Table is empty.');    
                return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
            }

            foreach($cart->checks as $chek)
            {
                $niceNames = array(
                    'customerid'    =>  'Customer',
                    'checkno'       =>  'Check Number',
                    'cclass'        =>  'Check Class',
                    'checkdate'     =>  'Check Date',
                    'checktype'     =>  'Check Type',
                    'accountno'     =>  'Account Number',
                    'accountname'   =>  'Account Name',
                    'bankid'        =>  'Bank Name',
                    'checkamt'      =>  'Check Amount'
                );
                $validator = Validator::make($chek, [
                    'customerid'    =>  'required|integer',
                    'checkno'       =>  'required',
                    'cclass'        =>  'required',
                    'checkdate'     =>  'required|date',
                    'checktype'     =>  'required',
                    'accountno'     =>  'required',
                    'accountname'   =>  'required',
                    'bankid'        =>  'required|integer',
                    'checkamt'      =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
                ]);
        
                $validator->setAttributeNames($niceNames); 
                if ($validator->passes()) 
                {
                    $check = new Check();

                    $check->checksreceivingtransaction_id   = $id;
                    $check->customer_id                     = $chek['customerid'];
                    $check->check_no                        = $chek['checkno'];         
                    $check->check_class                     = $chek['cclass'];
                    $check->check_date                      = date('Y-m-d', strtotime(str_replace('-', '/', $chek['checkdate'])));
                    $check->check_type                      = $chek['checktype'];
                    $check->account_no                      = $chek['accountno'];
                    $check->account_name                    = strtoupper($chek['accountname']);
                    $check->bank_id                         = $chek['bankid'];
                    $check->businessunit_id                 = Auth::user()->businessunit_id;
                    $check->check_amount                    = str_replace(',','',$chek['checkamt']);
                    $check->currency_id                     = $chek['currency'];
                    $check->check_category                  = $chek['ccategory'];
                    $check->check_expiry                    = trim($chek['checkexpdate']) == "" ? NULL : date('Y-m-d', strtotime(str_replace('-', '/', $chek['checkexpdate'])));
                    $check->check_received                  = Carbon::now()->format('Y-m-d');
                    $check->check_status                    = "PENDING";
                    $check->department_from                 = 8;

                    $check->save();                    
                }
                else 
                {
                    $hasError = true;
                    break;
                }
            }

            if($hasError)
            {
                DB::rollback();
                return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
            }
            
        DB::commit();
        return response()->json(['status'=>true,'message'=>'Check/s Successfully Saved.']);
    }

    public function getAddToCart(Request $request)
    {        
        //echo $request->checkdate;
        $isFound = false;
        $hasCart = false;
        $niceNames = array(
            'customerid'    =>  'Customer',
            'checkno'       =>  'Check Number',
            'cclass'        =>  'Check Class',
            'checkdate'     =>  'Check Date',
            'checktype'     =>  'Check Type',
            'accountno'     =>  'Account Number',
            'accountname'   =>  'Account Name',
            'bankid'        =>  'Bank Name',
            'checkamt'      =>  'Check Amount',
            'ccategory'     =>  'Check Category',
            'currency'      =>  'Currency'
        );
        $validator = Validator::make($request->all(), [
            'customerid'    =>  'required|integer',
            'checkno'       =>  'required',
            'cclass'        =>  'required',
            'checkdate'     =>  'required|date',
            'checktype'     =>  'required',
            'accountno'     =>  'required',
            'accountname'   =>  'required',
            'bankid'        =>  'required|integer',
            'checkamt'      =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'ccategory'     =>  'required',
            'currency'      =>  'required',
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $oldCart = Session::has('cart') ? Session::get('cart') : null;
            $cart = new Cart($oldCart);

            if(is_null($oldCart))
            {
                $request->request->add(['is_removed'=>false]);
                $cart->add($request->all());                                
                $request->session()->put('cart',$cart); 
                end($cart->checks);
                $ckey =  key($cart->checks);
                return response()->json(['status'=>true,'totalamount'=>$request->checkamt,'checkkey'=>$ckey]);
            }
            else 
            {
                // validate if check already exist in a session cart array
                if($cart->is_check_exist($request->all()))
                {
                    $validator->getMessageBag()->add('checkno', 'Check Number already exist.');    
                    return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
                }
                else 
                {
                    $request->request->add(['is_removed'=>false]);
                    $cart->add($request->all());                   
                    $request->session()->put('cart',$cart); 
                    end($cart->checks);
                    $ckey =  key($cart->checks);
                    // dd($cart->checks);
                    return response()->json(['status'=>true,'totalamount'=>number_format($cart->totalAmt,2),'checkkey'=>$ckey]);
                }
            }
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);

        // // $cart->add($request->all());

        // // $request->session()->put('cart',$cart); 

        // //check if cart exist
        // if(!$cart->check($request->all()))
        // {
           
        // }     

        // if (Session::has('cart.selection', $id)) 
        // {
        //     Session::pull('user.selection', $id);
        // }

    }

    public function checkIfCheckNoAndAccountNoExist($request)
    {

        return true;
    }

    public function editCart(Request $request)
    {
        $isFound = false;
        $hasCart = false;
        $niceNames = array(
            'customerid'    =>  'Customer',
            'checkno'       =>  'Check Number',
            'cclass'        =>  'Check Class',
            'checkdate'     =>  'Check Date',
            'checktype'     =>  'Check Type',
            'accountno'     =>  'Account Number',
            'accountname'   =>  'Account Name',
            'bankid'        =>  'Bank Name',
            'checkamt'      =>  'Check Amount',
            'ccategory'     =>  'Check Category',
            'currency'      =>  'Currency'
        );
        $validator = Validator::make($request->all(), [
            'customerid'    =>  'required|integer',
            'checkno'       =>  'required',
            'cclass'        =>  'required',
            'checkdate'     =>  'required|date',
            'checktype'     =>  'required',
            'accountno'     =>  'required|numeric',
            'accountname'   =>  'required',
            'bankid'        =>  'required|integer',
            'checkamt'      =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
            'ccategory'     =>  'required',
            'currency'      =>  'required',
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $oldCart = Session::has('cart') ? Session::get('cart') : null;
            $cart = new Cart($oldCart);

            if(is_null($oldCart))
            {
                $validator->getMessageBag()->add('cart', 'Cart is empty.');    
                return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
            }

            // validate if check already exist in a session cart array
            if($cart->is_check_exist_except_key($request->all()))
            {
                $validator->getMessageBag()->add('checkno', 'Check Number already exist.');    
                return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
            }
            
            //edit item      

            if($cart->update($request->all()))
            {
                $request->session()->put('cart',$cart); 
                return response()->json(['status'=>true,'totalamount'=>number_format($cart->totalAmt,2)]);
            }
        
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }  
    
    public function checkStatus()
    {
        $title = 'Check Status';
        return view('check.checkstatus',compact('title'));
    }

    public function checkListPDC(Request $request)
    {
        $title = 'PDC';
        
        if(empty($request->input('searchvalue')))
        {
            //$checks = Check::latest('checks_id')->paginate(10);            
            $qcheck = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.check_date', '>', date('Y-m-d'))
            ->where('checks.check_status','<>','CASH')  
            ->where('checks.businessunit_id','=',Auth::user()->businessunit_id)
            ->whereNull('checks.deleted_at')  
            ->orderBy('checks.check_date','asc');


            $sum = $qcheck->sum('checks.check_amount');
            $checks = $qcheck->select(
                'checks.*', 
                'banks.bankbranchname', 
                'customers.cus_code',
                'customers.fullname'
            )
            ->paginate(10);

            // echo $sum = $checks->sum('check_amount');
            // exit();
        }
        else 
        {
            $checks = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->select(
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.cus_code',
                    'customers.fullname'
                )
            ->where('checks.check_date', '>', date('Y-m-d'))
            ->where('checks.check_status','<>','CASH')  
            ->whereNull('checks.deleted_at')  
            ->where('checks.businessunit_id','=',Auth::user()->businessunit_id)
            ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            ->paginate(10);

            //dd($checks);
        }

        if ($request->ajax()) {
            return view('check.loadcheckspdc', ['checks' => $checks])->render();  
        }

        return view('check.checklistpdc',compact('title','checks','sum'));
    }

    public function checkList(Request $request)
    {
        //dd($request->all());
    
        $title = 'Received Check';

        if(empty($request->input('searchvalue')))
        {
            //$checks = Check::latest('checks_id')->paginate(10);
            $checks = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.businessunit_id','=',Auth::user()->businessunit_id)
            ->whereNull('deleted_at')
            ->select(
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.cus_code',
                    'customers.fullname'
                )
            ->paginate(10);

            //dd($checks);
        }
        else 
        {
            $checks = DB::table('checks')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->select(
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.cus_code',
                    'customers.fullname'
                )
            ->whereNull('deleted_at')
            ->where('checks.businessunit_id','=',Auth::user()->businessunit_id)
            ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            ->paginate(10);

            //dd($checks);
        }

        if ($request->ajax()) {
            return view('check.loadchecks', ['checks' => $checks])->render();  
        }

        return view('check.checklist',compact('title','checks'));
    }

    public function checkStatusList(Request $request)
    {
        $columns = array( 
            0   =>  'custcode', 
            1   =>  'acctno',
            2   =>  'acctname',
            3   =>  'checkno',
            4   =>  'checkdate',
            5   =>  'branchname',
            6   =>  'amt',
            7   =>  'status',
            8   =>  ''
        );

        $totalData = Check::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = "checks_id";
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $checks = Check::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 

            $checks =  Check::where('customers.cus_code','LIKE',"%{$search}%")
                ->orWhere('check_no', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = User::where('cus_code','LIKE',"%{$search}%")
                ->orWhere('check_no', 'LIKE',"%{$search}%")
                ->count();
        }

        $data = array();
        if(!empty($checks))
        {
            foreach ($checks as $check)
            {
                $nestedData['custcode'] = $check->customer->cus_code;
                $nestedData['acctno'] = $check->check_no;
                $nestedData['acctname'] = $check->account_no;
                $nestedData['checkno'] = $check->check_no;
                $nestedData['checkdate'] = date('Y-m-d',strtotime($check->check_date));
                $nestedData['branchname'] = $check->bank->bankbranchname;
                $nestedData['amt'] = number_format($check->check_amount,2);
                $nestedData['status'] = $check->user_status;
                $nestedData['action'] = "<div class='action-user' data-id='{$check->id}'>&emsp;<a href='#' title='SHOW' ><span class='glyphicon glyphicon-list'></span></a>
                    &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit' id='edituser'></span></a>
                    &emsp;<a href='#' title='CHANGE PASSWORD' ><span class='glyphicon glyphicon-edit' id='changepassword'></span></a></div>";
                $data[] = $nestedData;
            }
        }
          
        $json_data = array(
            "draw"            => intval($request->input('draw')),  
            "recordsTotal"    => intval($totalData),  
            "recordsFiltered" => intval($totalFiltered), 
            "data"            => $data   
        );
            
        echo json_encode($json_data);    
    }

    public function receivingUpload()
    {
        $title = 'Receiving Check';
        $ldate = date('F j, Y', strtotime(date('m/d/Y')));

        //receiving controller number
        $controlno = 0;
        $controlno = $this->getControlNumber();
        Session::forget('cart');
        return view('check.checkreceivingupload',compact('title','ldate','controlno'));
    }

    public function addtocartupload(Request $request)
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        foreach ($request->file('files') as $key => $value) 
        {
            $arr_f = [];

            //save files to storage
            $textFile = time(). $key . '.' . $value->getClientOriginalExtension();
            $path = $value->storeAs('files/'.$this->foldername(), $textFile);
            echo $path;
            // if(Storage::disk('public')->move($value->getRealPath(), 'files/'.$this->foldername().$textFile))
            // {
            //     echo 'yeah';
            // }

            //save files to public
            //$value->move(public_path('images'), $imageName);            

            // usleep(80000);
            // $r_f = fopen($value->getRealPath(),'r');
            // while(!feof($r_f)) 
            // {
            //     usleep(80000);
            //     $arr_f[] = fgets($r_f);
            // }

            // dd($arr_f);

        }

        // for($x=0; $x<=10; $x++)
        // {
        //     usleep(80000);
        //     $response = array( 
        //         'status'    => 'looping', 
        //         'message' 	=> 'yeah', 
        //         'progress' 	=> $x
        //     );

        //     echo json_encode($response);
        // }        
    }

    public function editCheckCart($key)
    {     
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        $ch = collect(); 
        // end($cart->checks);
        // $ckey =  key($cart->checks);
        // echo $ckey;
        //dd($cart->checks);
        //dd($cart);
        $keyval = intval($key);
        foreach($cart->checks as $key => $value)
        {
            if($key==intval($keyval))
            {
                $currency = Currency::get();
                //$data[$key]['transaction_date'] = date('d/m/Y',$value['transaction_date']);
                
                $ch->push(array(
                    'customerid'        =>  $cart->checks[$key]['customerid'],
                    'customercode'      =>  $cart->checks[$key]['customercode'],
                    'customerdetails'   =>  $cart->checks[$key]['customerdetails'],
                    'checkno'           =>  $cart->checks[$key]['checkno'],
                    'cclass'            =>  $cart->checks[$key]['cclass'],
                    'checkdate'         =>  $cart->checks[$key]['checkdate'],
                    'checktype'         =>  $cart->checks[$key]['checktype'],
                    'accountno'         =>  $cart->checks[$key]['accountno'],
                    'accountname'       =>  $cart->checks[$key]['accountname'],
                    'bankid'            =>  $cart->checks[$key]['bankid'],
                    'bankcode'          =>  $cart->checks[$key]['bankcode'],
                    'bankname'          =>  $cart->checks[$key]['bankname'],
                    'bankdetails'       =>  $cart->checks[$key]['bankdetails'],
                    'checkamt'          =>  $cart->checks[$key]['checkamt'],
                    'ccategory'         =>  $cart->checks[$key]['ccategory'],
                    'currency'          =>  $cart->checks[$key]['currency'],
                    'checkexpdate'      =>  $cart->checks[$key]['checkexpdate'],
                    )
                );
                break;
            }
        }
        //return view('check.editcheck',compact('ch','keyval','checkclass'));
        return view('check.editcheck',array(
                'ch'        =>$ch,
                'keyval'    =>$keyval,
                'checkclass'=>$this->checkclass,
                'currency'  =>$currency,
                'category'  =>$this->checkcategory
            )
        );
    }

    public function showcheckdialog($key)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        $ch = collect(); 
        // end($cart->checks);
        // $ckey =  key($cart->checks);
        // echo $ckey;
        //dd($cart->checks);
        //dd($cart);
        $keyval = intval($key);
        foreach($cart->checks as $key => $value)
        {
            $currency = Currency::where('currency_id',$cart->checks[$key]['currency'])->first();

            if($key==intval($keyval))
            {
                //$data[$key]['transaction_date'] = date('d/m/Y',$value['transaction_date']);
                
                $ch->push(array(
                    'customerid'        =>  $cart->checks[$key]['customerid'],
                    'customercode'      =>  $cart->checks[$key]['customercode'],
                    'customerdetails'   =>  $cart->checks[$key]['customerdetails'],
                    'checkno'           =>  $cart->checks[$key]['checkno'],
                    'cclass'            =>  $cart->checks[$key]['cclass'],
                    'checkdate'         =>  $cart->checks[$key]['checkdate'],
                    'checktype'         =>  $cart->checks[$key]['checktype'],
                    'accountno'         =>  $cart->checks[$key]['accountno'],
                    'accountname'       =>  $cart->checks[$key]['accountname'],
                    'bankid'            =>  $cart->checks[$key]['bankid'],
                    'bankcode'          =>  $cart->checks[$key]['bankcode'],
                    'bankname'          =>  $cart->checks[$key]['bankname'],
                    'bankdetails'       =>  $cart->checks[$key]['bankdetails'],
                    'checkamt'          =>  $cart->checks[$key]['checkamt'],
                    'ccategory'         =>  $cart->checks[$key]['ccategory'],
                    'currency'          =>  $currency->currency_name,
                    'checkexpdate'      =>  $cart->checks[$key]['checkexpdate'],
                    )
                );
                break;
            }
        }
        //return view('check.editcheck',compact('ch','keyval','checkclass'));
        return view('check.showcheck',array(
                'ch'=>$ch,
                'keyval'=>$keyval,
                'checkclass'=>$this->checkclass
            )
        );
    }

    public function viewCheck($id)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->join('department','checks.department_from','=','department.department_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                DB::raw("DATE_FORMAT(checks.check_received, '%M %d, %Y') as rdate"),
                'banks.bankbranchname', 
                'customers.*',
                'department.*'
            )
        ->first();
        $cstatus = 'PENDING';
        if($check->check_status!='')
        {
            $cstatus = $check->check_status;
        }

        //dri      
        $arr = collect();
        $checkTagging = CheckTaggingItem::where('checks_id', $id)->with('checktagging')->get();
        $checkTagging->map(function ($ct) {
            if($ct['checktagging_type'] =='BOUNCED UPDATE') {
                $checkhistory = CheckHistory::where('checktaggingitems_id',$ct['checktaggingitems_id'])->with('bank')->first();

                //dd($checkhistory);
                $ct['check_no']     = $checkhistory->check_no;
                $ct['check_class']  = $checkhistory->check_class;
                $ct['check_date']   = $checkhistory->check_date;
                $ct['check_type']   = $checkhistory->check_type;
                $ct['account_no']   = $checkhistory->account_no;
                $ct['account_name'] = $checkhistory->account_name;
                if($checkhistory->bank_id!= NULL)
                {
                    $ct['bank_id']      = $checkhistory->bank->bankbranchname;
                }
                $c['check_amount']  = $checkhistory->check_amount;
                $c['cash']          = $checkhistory->cash;
            }            
            return $ct;
        });

        //exit();


        return view('check.viewcheck',array(
            'id'        =>  $id,
            'check'     =>  $check,
            'status'    =>  $cstatus,
            'history'   =>  $checkTagging
        ));
    }

    public function viewCheck2($id)
    {
        //echo $id;
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.bankbranchname', 
                'customers.*'
            )
        ->first();
        return view('check.viewcheck2',array(
            'id'    =>  $id,
            'check' =>  $check
        ));
    }

    public function removeCheck($key)
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Cart($oldCart);

        if($cart->remove($key))
        {
            //$request->session()->put('cart',$cart); 
            session(['cart' => $cart]);
            return response()->json(['status'=>true,'totalamount'=>number_format($cart->totalAmt,2)]);
        }
        return response()->json(['status'=>false,'error'=>'Something went wrong.']);
    }

    public function taggedaAsBounced($id)
    {
        DB::beginTransaction();        

            $checkTag = new CheckTagging;

            $checkTag->id                                   = Auth::user()->id;         
            $checkTag->businessunit_id                      = Auth::user()->businessunit_id;

            $checkTag->save();

            $idtag = $checkTag->checktagginghdr_id;

            $checkTagi = new CheckTaggingItem;

            $checkTagi->checktagginghdr_id          = $idtag;         
            $checkTagi->checks_id	                = $id;
            $checkTagi->checktagging_type           = "BOUNCING CHECK";
            $checkTagi->checktaggingitems_tag	    = "BOUNCED";
            $checkTagi->save();

            Check::where('checks_id', $id)
            ->update(['check_status' => 'BOUNCED']);
        
        DB::commit();

        return response()->json(['status'=>true]);
    }

    public function deleteCheck($key)
    {
        $check = Check::find( $key );
        if($check->delete())
        {
            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>'Something went wrong.']);
    }

    public function updateDBfromAtpDB()
    {
        $title = 'Update DB';
        return view('check.updatedbfromatp',compact('title'));
    }

    public function foldername()
    {
        $date = date('Y-m-d H:i:s');
        $date = str_replace( ':', '', $date);
        $date = str_replace(' ','-',$date);
		return $date;
    }
    
    public function processDBupdateFromAtp()
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        //check if b_atpgetdata is not null
        $bunit = BusinessUnit::where('businessunit_id', Auth::user()->businessunit_id)   
        ->whereNotNull('b_atpgetdata');

        $bcnt = $bunit->count();

        if($bcnt == 0){
            echo json_encode([
                'status'	=> 'noupdate',
                'message'	=> "Please set start date." 
            ]);
            exit();
        }     

        //check if b_atpgetdata is not null
        $bu = BusinessUnit::where('businessunit_id', Auth::user()->businessunit_id)   
        ->whereNotNull('b_encashstart');

        $bcnt = $bu->count();
        
        if($bcnt == 0){
            echo json_encode([
                'status'	=> 'noupdate',
                'message'	=> "Please set start date for encashment." 
            ]);
            exit();
        }     

        $bu = $bunit->first();

        $datestartatp = $bu->b_atpgetdata;

        $datestartencash = $bu->b_encashstart;

        $hasError = false;
        $checksondb = Check::where('businessunit_id', Auth::user()->businessunit_id)        
        ->count();

        if($checksondb == 0)
        {
            // $checkspdc = DB::connection('sqlsrv')
            // ->table('chk_dtl')
            // ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
            // ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
            // //->where('chk_mst.atp_date', '<', '2019-01-31 00:00:00')
            // ->where('chk_dtl.Chkdate', '>', '2019-01-31')
            // ->where('chk_mst.atp_date', '>=', '2019-01-31 00:00:00')
            // ->where('Loc_Code', '=', 'ICM')
            // ->orderBy('entry_no', 'desc')

            //check if checktable is null
            try 
            {
                $checkpdc = DB::connection('sqlsrv')
                ->table('chk_dtl')
                ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
                ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
                ->where('chk_mst.loc_code', Auth::user()->businessunit->loc_code_atp)
                ->where('chk_dtl.chkdate', '>=', $datestartatp)
                ->where('chk_mst.atp_date', '<', $datestartatp)
                //->where('chk_mst.atp_date', '<', '2019-01-31 00:00:00')
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
                    'chk_dtl.chkexpiry',
                    'chk_dtl.category',
                    'chk_dtl.approvedby',
                    'customer.clastname',
                    'customer.cfirstname',
                    'customer.cmiddname',
                    'customer.extension'
                )
                //->first();
                ->get();

            } 
            catch (\Exception $e) 
            {
                echo json_encode([
                    'status'	=> 'error',
                    'message'	=> "Could not connect to the database.  Please check your configuration. error:" . $e 
                ]);
                exit();
            }            
        }

        try 
        {
            $checksdc = DB::connection('sqlsrv')
            ->table('chk_dtl')
            ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
            ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
            ->where('chk_mst.loc_code', Auth::user()->businessunit->loc_code_atp)
            ->where('chk_dtl.issue_no','>',$this->getATPLastIssueNo())
            //->where('chk_mst.atp_date', '>=', '2019-01-30 00:00:00')
            ->where('chk_mst.atp_date', '>=', $datestartatp.' 00:00:00')
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
                'chk_dtl.chkexpiry',
                'chk_dtl.category',
                'chk_dtl.approvedby',
                'customer.clastname',
                'customer.cfirstname',
                'customer.cmiddname',
                'customer.extension'
            )
            //->first();
            ->get();

        } 
        catch (\Exception $e) 
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> "Could not connect to the database.  Please check your configuration. error:" . $e 
            ]);
            exit();
        }

        if($checksondb == 0)
        {
            if(count($checkpdc) > 0 && count($checksdc)>0)
            {
                $checks = $checksdc->merge($checkpdc); 
            }
        }
        else
        {
            $checks = $checksdc;
        }

        $hasError = false;  

        try 
        {
            $checkencash = DB::connection('sqlsrv')
            ->table('vip_dtl')
            ->join('vip_mst','vip_mst.encash_id', '=', 'vip_dtl.encash_id')
            ->join('customer', 'customer.custid', '=', 'vip_mst.custid')
            ->where('loc_code', Auth::user()->businessunit->loc_code_atp)
            ->where('vip_dtl.entry_no','>',$this->getEncashLastEntryNo())
            //->where('chk_mst.atp_date', '>=', '2019-01-30 00:00:00')
            ->where('vip_mst.encash_date', '>=', $datestartencash)
            ->orderBy('entry_no', 'asc')
            ->select(
                'vip_dtl.*',
                'vip_mst.*',                    
                'customer.clastname',
                'customer.cfirstname',
                'customer.cmiddname',
                'customer.extension'
            )
            //->first();
            ->get();

        } 
        catch (\Exception $e) 
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> "Could not connect to the database.  Please check your configuration. error:" . $e 
            ]);
            exit();
        }
        
        $nochks = intval(count($checks)) + intval(count($checkencash));  

        usleep(80000);
        echo json_encode([
            'status'	=> 'counting',
            'message'	=> $nochks
        ]);

        $cnt = 1;         

        if($nochks == 0)
        {
            echo json_encode([
                'status'	=> 'noupdate',
                'message'	=> 'There is no update this time.'
            ]);
            exit();
        }

        $issueno = NULL;

        $nochecksdc = count($checksdc);

        if($nochecksdc > 0){
            $issueno = $checksdc[$nochecksdc-1]->issue_no;
        }

        $lastcheckencashno = NULL;

        $nocheckencash = count($checkencash);

        if($nocheckencash > 0){
            $lastcheckencashno = $checkencash[$nocheckencash-1]->Entry_No;
        }        

        $hasError = false;
        DB::beginTransaction();

        $checkRec = new CheckReceived;

            $checkRec->checksreceivingtransaction_ctrlno    = $this->getControlNumber();
            $checkRec->id                                   = Auth::user()->id;         
            $checkRec->company_id                           = Auth::user()->company_id;
            $checkRec->businessunit_id                      = Auth::user()->businessunit_id;
            $checkRec->atp_issueno                          = $issueno;
            $checkRec->encash_entrynum                      = $lastcheckencashno;
            $checkRec->save();
            usleep(30000);  

            $recid = $checkRec->checksreceivingtransaction_id;    

            for($x=0; $x<=count($checks) - 1; $x++)
            {
                //$checks[$x]->cfirstname;

                usleep(30000);  
                $customer = "";
                $customerid = "";
                $bankname = "";
                $bankid = "";
                $ctype = "";
                $midname = "";
                $checkrec = "";
                $checkexpiry="";
                $checkclass="";
                $checkno = "";

                if(str_replace(' ','',$checks[$x]->chkexpiry)=='//' || trim($checks[$x]->chkexpiry)=='')
                {
                    $checkexpiry = NULL;
                }
                else 
                {
                    $checkexpiry = $checks[$x]->chkexpiry;
                }
                
                if(trim($checks[$x]->cmiddname)!='.')
                {
                    $midname = trim($checks[$x]->cmiddname);
                }
                $customer = preg_replace('!\s+!', ' ', $checks[$x]->cfirstname.' '.$midname.' '.$checks[$x]->clastname.''.$checks[$x]->extension);
                $customer = trim($customer);
                if($checks[$x]->chktype == 'PD')
                {
                    $ctype = "POST DATED";
                }
                else 
                {
                    $ctype = "DATED CHECK";
                }

                $daex = explode(" ", $checks[$x]->chkdate);
                $datex =  $daex[0];

                //check if customer exist / create customer
                if($id = $this->isCustomerNameExist($customer))
                {
                    $customerid = $id;

                }
                else 
                {
                    $customerid = $this->autoCreateCustomer($customer);

                }

                //check if bank exist / create bank
                $bankname = trim($checks[$x]->bankname);
                if($bid = $this->isBankExist($bankname))
                {
                    $bankid = $bid;
                }
                else 
                {
                    $bankid = $this->autoCreateNewBank($bankname);
                }                

               $check = new Check();
                
                $checkrec = explode(" ",$checks[$x]->atp_date);
                $checkrec = $checkrec[0];

                if (strpos($checks[$x]->chkclass, 'PERSONAL') !== false) {
                    $checkclass = 'PERSONAL';
                }
                else 
                {
                    $checkclass = $checks[$x]->chkclass;
                }

                $checkno = trim(preg_replace("/\./", "", $checks[$x]->chkno));

                $checkno = ltrim($checkno,'0');

                $check->checksreceivingtransaction_id   = $recid;
                $check->customer_id                     = $customerid;
                $check->check_no                        = $checkno;              
                $check->check_class                     = trim($checkclass);   
                $check->check_date                      = trim($datex);
                $check->check_received                  = trim($checkrec);
                $check->check_type                      = trim($ctype);   
                $check->account_no                      = trim($checks[$x]->actno);  
                $check->account_name                    = trim($checks[$x]->actname);  
                $check->bank_id                         = $bankid;
                $check->businessunit_id                 = Auth::user()->businessunit_id;
                $check->check_amount                    = trim(str_replace(',','',$checks[$x]->chkamt));
                $check->check_expiry                    = $checkexpiry;
                $check->check_category                  = trim($checks[$x]->category);
                $check->check_status                    = 'PENDING';
                $check->approving_officer               = trim($checks[$x]->approvedby) == '' ? NULL : trim($checks[$x]->approvedby);
                $check->currency_id                     = 1;
                $check->department_from                 = 15;
                $check->save();      

                echo json_encode([
                    'status'	=> 'saving',
                    'message'	=> 'Importing '.$cnt.' of '.$nochks
                ]);
                usleep(80000);
                $cnt++;
            }

            for($x=0; $x<=count($checkencash) - 1; $x++)
            {
                //$checks[$x]->cfirstname;

                usleep(30000);  
                $customer = "";
                $customerid = "";
                $bankname = "";
                $bankid = "";
                $ctype = "";
                $midname = "";
                $checkrec = "";
                $checkexpiry="";
                $checkclass="";
                $checkno = "";

                if(str_replace(' ','',$checkencash[$x]->ChkExpiry)=='//' || trim($checkencash[$x]->ChkExpiry)=='')
                {
                    $checkexpiry = NULL;
                }
                else 
                {
                    $checkexpiry = $checkencash[$x]->ChkExpiry;
                }
                
                if(trim($checkencash[$x]->cmiddname)!='.')
                {
                    $midname = trim($checkencash[$x]->cmiddname);
                }
                $customer = preg_replace('!\s+!', ' ', $checkencash[$x]->cfirstname.' '.$midname.' '.$checkencash[$x]->clastname.''.$checkencash[$x]->extension);
                $customer = trim($customer);
                if($checkencash[$x]->ChkType == 'PD')
                {
                    $ctype = "POST DATED";
                }
                else 
                {
                    $ctype = "DATED CHECK";
                }

                $daex = explode(" ", $checkencash[$x]->ChkDate);
                $datex =  $daex[0];

                //check if customer exist / create customer
                if($id = $this->isCustomerNameExist($customer))
                {
                    $customerid = $id;

                }
                else 
                {
                    $customerid = $this->autoCreateCustomer($customer);

                }

                //check if bank exist / create bank
                $bankname = trim($checkencash[$x]->BankName);
                if($bid = $this->isBankExist($bankname))
                {
                    $bankid = $bid;
                }
                else 
                {
                    $bankid = $this->autoCreateNewBank($bankname);
                }          
               
                $checkrec = explode(" ",$checkencash[$x]->Encash_Date);
                $checkrec = $checkrec[0];

                if (strpos($checkencash[$x]->ChkClass, 'PERSONAL') !== false) {
                    $checkclass = 'PERSONAL';
                }
                else 
                {
                    $checkclass = $checkencash[$x]->ChkClass;
                }

                $checkno = trim(preg_replace("/\./", "", $checkencash[$x]->ChkNo));

                $checkno = ltrim($checkno,'0');

                $check = new Check();

                $check->checksreceivingtransaction_id   = $recid;
                $check->customer_id                     = $customerid;
                $check->check_no                        = $checkno;    
                $check->check_class                     = trim($checkclass);   
                $check->check_date                      = trim($datex);
                $check->check_received                  = trim($checkrec);
                $check->check_type                      = trim($ctype);   
                $check->account_no                      = trim($checkencash[$x]->ActNo);  
                $check->account_name                    = trim($checkencash[$x]->ActName);  
                $check->bank_id                         = $bankid;
                $check->businessunit_id                 = Auth::user()->businessunit_id;
                $check->check_amount                    = trim(str_replace(',','',$checkencash[$x]->ChkAmt));
                $check->check_expiry                    = $checkexpiry;
                $check->check_category                  = trim($checkencash[$x]->Category);
                $check->check_status                    = 'PENDING';
                $check->approving_officer               = trim($checkencash[$x]->ApprovedBy) == '' ? NULL : trim($checkencash[$x]->ApprovedBy);
                $check->currency_id                     = 1;
                $check->department_from                 = 13;
                
                $check->save();      

                echo json_encode([
                    'status'	=> 'saving',
                    'message'	=> 'Importing '.$cnt.' of '.$nochks
                ]);
                usleep(80000);
                $cnt++;
            }

            if($hasError)
            {
                DB::rollback();
                echo json_encode([
                    'status'	=> 'error',
                    'message'	=> 'Something Went Wrong.'
                ]);

                exit();
            }
            
        DB::commit();

        usleep(80000);

        echo json_encode([
            'status'	=> 'complete',
            'message'	=> 'Database Successfully Updated.'
        ]);
    }

    public function checkExistToday($checknum){
        $check = Check::whereDate('check_received', Carbon::today())
            ->where('check_no',$checknum)
            ->first();
        if($check!=null){
            echo 'yeah';
        }
        else{
            echo 'nah';
        }
    }

    public function pdcTagging(Request $request)
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        $ids = $request->input('ids');
        $bids = $request->input('bids');        

        if(empty($ids))
        {
            $ids = [];
        }
        else 
        {
            $ids = explode(",",$ids);
        }
        
        if(empty($bids))
        {
            $bds = [];
        }
        else
        {
            $bids = explode(",",$bids);
        }       
        
        foreach($ids as $id)
        {
            echo json_encode([
                'status'	=> 'Clearing',
                'message'	=> 'Saving '.$id
            ]);
            usleep(80000);
        }

    }

    public function pdcTagging2(Request $request)
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        $ids = $request->input('ids');
        $bids = $request->input('bids');        

        if(empty($ids))
        {
            $ids = [];
        }
        else 
        {
            $ids = explode(",",$ids);
        }
        
        if(empty($bids))
        {
            $bds = [];
        }
        else
        {
            $bids = explode(",",$bids);
        }       
        
        foreach($ids as $id)
        {
            echo json_encode([
                'status'    => 'Clearing',
                'message'   => 'Saving '.$id
            ]);
            usleep(80000);
        }

    }

    public function checkTagging(Request $request)
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        $ids = $request->input('ids');
        $bids = $request->input('bids');   
        $remarks = $request->input('remarks');    
        
        if(empty($ids))
        {
            $ids = [];
        }
        else 
        {
            $ids = explode(",",$ids);
        }
        
        if(empty($bids))
        {
            $bds = [];
        }
        else
        {
            $bids = explode(",",$bids);
        }       

        DB::beginTransaction();        

            $checkTag = new CheckTagging;

            $checkTag->id                                   = Auth::user()->id;         
            $checkTag->checktagginghdr_remarks              = $remarks;
            $checkTag->businessunit_id                      = Auth::user()->businessunit_id;


            $checkTag->save();

            $idtag = $checkTag->checktagginghdr_id;

            //$ids == null ? $idscnt = [] : $idscnt = count($idscnt);
            if($ids!=null)
            {
                foreach($ids as $id)
                {
                    $check =  Check::where('checks_id', $id)->first();

                    $checkTagi = new CheckTaggingItem;

                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id	                = $id;
                    $checkTagi->checktagging_type           = "CLEARING";
                    $checkTagi->checktaggingitems_tag	    = "CLEARED";
                    $checkTagi->save();

                    Check::where('checks_id', $id)
                    ->update(['check_status' => 'CLEARED']);

                    usleep(100000);

                    echo json_encode([
                        'status'	=> 'Clearing',
                        'message'	=> 'Tagging Cleared Check #<span class="sp-cl">'.$check->check_no.'</span>'
                    ]);
                    //usleep(900000);
                }
            }

            if($bids!=null)
            {
                foreach($bids as $bid)
                {
                    $check =  Check::where('checks_id', $bid)->first();

                    $checkTagi = new CheckTaggingItem;

                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id	                = $bid;
                    $checkTagi->checktagging_type           = "CLEARING";
                    $checkTagi->checktaggingitems_tag	    = "BOUNCED";
                    $checkTagi->save();

                    Check::where('checks_id', $bid)
                    ->update(['check_status' => 'BOUNCED']);

                    usleep(100000);

                    echo json_encode([
                        'status'	=> 'Clearing',
                        'message'	=> 'Tagging Bounced Check #<span class="sp-bo">'.$check->check_no.'</span>'
                    ]);
                }
            }

        DB::commit();

        usleep(30000);
        echo json_encode([
            'status'	=> 'complete',
            'message'	=> 'Yo!'
        ]);

    }

    // Begin Due Check Clearing

    public function checkTagging2(Request $request)
    {
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();       

        $hasError = false;

        $ids = $request->input('ids');
        $bids = $request->input('bids');     
        
        if(empty($ids))
        {
            $ids = [];
        }
        else 
        {
            $ids = explode(",",$ids);
        }
        
        if(empty($bids))
        {
            $bds = [];
        }
        else
        {
            $bids = explode(",",$bids);
        }       

        DB::beginTransaction();        

            $checkTag = new CheckTagging;

            $checkTag->id  = Auth::user()->id;         

            $checkTag->save();

            $idtag = $checkTag->checktagginghdr_id;


            if($ids!=null)
            {
                foreach($ids as $id)
                {
                    $check =  Check::where('checks_id', $id)->first();

                    $checkTagi = new CheckTaggingItem;

                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id                   = $id;
                    $checkTagi->checktaggingitems_tag       = "CLEARED";
                    $checkTagi->save();

                    Check::where('checks_id', $id)
                    ->update(['check_status' => 'CLEARED']);

                    usleep(500000);

                    echo json_encode([
                        'status'    => 'Clearing',
                        'message'   => 'Tagging Cleared Check #<span class="sp-cl">'.$check->check_no.'</span>'
                    ]);
                    //usleep(900000);
                }
            }

            if($bids!=null)
            {
                foreach($bids as $bid)
                {
                    $check =  Check::where('checks_id', $bid)->first();

                    $checkTagi = new CheckTaggingItem;

                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id                   = $bid;
                    $checkTagi->checktaggingitems_tag       = "BOUNCED";
                    $checkTagi->save();

                    Check::where('checks_id', $bid)
                    ->update(['check_status' => 'BOUNCED']);

                    usleep(500000);

                    echo json_encode([
                        'status'    => 'Clearing',
                        'message'   => 'Tagging Bounced Check #<span class="sp-bo">'.$check->check_no.'</span>'
                    ]);
                }
            }

        DB::commit();

        usleep(80000);
        echo json_encode([
            'status'    => 'complete',
            'message'   => 'Yo!'
        ]);

    }

    // End Due Check Clearing

    public function getATPLastIssueNo()
    {

        $issueno = DB::table('checksreceivingtransaction')        
        ->join('businessunit', 'businessunit.businessunit_id', '=', 'checksreceivingtransaction.businessunit_id')
        ->where('businessunit.loc_code_atp',Auth::user()->businessunit->loc_code_atp)     
        ->whereNotNull('atp_issueno')
        ->select('atp_issueno')
        ->orderBy('checksreceivingtransaction.checksreceivingtransaction_id', 'desc')
        ->first();

        //dd($issueno);
        if(is_null($issueno))
        {
            return 0;
        }
        return $issueno->atp_issueno;

    }

    public function getEncashLastEntryNo()
    {
        $encashno = DB::table('checksreceivingtransaction')        
        ->join('businessunit', 'businessunit.businessunit_id', '=', 'checksreceivingtransaction.businessunit_id')
        ->where('businessunit.loc_code_atp',Auth::user()->businessunit->loc_code_atp)     
        ->whereNotNull('encash_entrynum')
        ->select('encash_entrynum')
        ->orderBy('checksreceivingtransaction.checksreceivingtransaction_id', 'desc')
        ->first();

        //dd($issueno);
        if(is_null($encashno))
        {
            return 0;
        }
        return $encashno->encash_entrynum;

    }

    public function dueChecks(Request $request)
    {
        $title = 'Due Checks';

        if(empty($request->input('searchvalue')))
        {
            //$checks = Check::latest('checks_id')->paginate(10);
            $checks = DB::table('checks')
            ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('checks.check_type','=','POST DATED')
            ->whereNull('checks.check_status')
            ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            ->select(
                    'checksreceivingtransaction.*',
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.*'
                )
            ->orderBy('check_date', 'ASC')
            ->paginate(10);

            //dd($checks);
        }
        else 
        {
            $checks = DB::table('checks')
            ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            ->where('checks.check_type','=','POST DATED')
            ->whereNull('checks.check_status')
            ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            ->select(
                    'checksreceivingtransaction.*',
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.*'
                )
            ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            ->orderBy('check_date', 'ASC')
            ->paginate(10);

            //dd($checks);
        }

        if ($request->ajax()) {
            return view('check.loadduechecks', ['checks' => $checks])->render();  
        }

        return view('check.checklistduechecks',compact('title','checks'));

    }

    public function clearedChecks(Request $request)
    {
        $title = 'Cleared Checks';

        if(empty($request->input('searchvalue')))
        {
            //$checks = Check::latest('checks_id')->paginate(10);
            
            $bunit = Auth::user()->businessunit_id;

            $checks = CheckTaggingItem::whereHas('checktagging', function ($q) use($bunit){
                $q->where('businessunit_id', $bunit);
            })
            ->where('checktagging_type','CLEARING')
            ->groupBy('checktagginghdr_id')
            ->orderBy('checktaggingitems_id', 'DESC')
            ->paginate(10);

            // dd($checks);

            // $checks = CheckTaggingItem::with('checktagging_hdr')->where('checktagging_type','CLEARING')
            // ->where('checktagging_hdr.businessunit_id','=',Auth::user()->businessunit_id)
            // ->groupBy('checktagginghdr_id')
            // ->orderBy('checktaggingitems_id', 'DESC')
            // //->get()
            // ->paginate(10);

            //dd($checks);

            // foreach($checks as $ch){
            //     echo $ch->checktagging->user->name.'<br />';
            //     //echo $ch->id'<br >';
            // }
            // exit();
            // ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            // ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            // ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            // ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            // ->where('checks.check_type','=','POST DATED')
            // ->whereNull('checks.check_status')
            // ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            // ->select(
            //         'checksreceivingtransaction.*',
            //         'checks.*', 
            //         'banks.bankbranchname', 
            //         'customers.*'
            //     )
            // ->orderBy('check_date', 'ASC')
            // ->paginate(10);

            //dd($checks);
        }
        else 
        {
            // $checks = DB::table('checks')
            // ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            // ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            // ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            // ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            // ->where('checks.check_type','=','POST DATED')
            // ->whereNull('checks.check_status')
            // ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            // ->select(
            //         'checksreceivingtransaction.*',
            //         'checks.*', 
            //         'banks.bankbranchname', 
            //         'customers.*'
            //     )
            // ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            // ->orderBy('check_date', 'ASC')
            // ->paginate(10);

            // //dd($checks);
        }

        if ($request->ajax()) {
            return view('check.loadclearedchecks', ['checks' => $checks])->render();  
        }

        return view('check.checklistcleared',compact('title','checks'));
    }

    public function viewClearedbytrid(Request $request,$id){
        $title = 'Cleared Checks';
        $date = '';
        $checkquery = CheckTaggingItem::where('checktagging_type','CLEARING')
        ->where('checktagginghdr_id',$id)->first();        
        if($checkquery!=null){
            $date = $checkquery->checktagging->created_at;
        }
        if(empty($request->input('searchvalue')))
        {
            $checks = CheckTaggingItem::where('checktagging_type','CLEARING')
            ->where('checktagginghdr_id',$id)
            ->orderBy('checktaggingitems_id', 'DESC')
            //->get()
            
            ->paginate(10);
        }
        else 
        {
            $checks = CheckTaggingItem::where('checktagging_type','CLEARING')
            ->where('checktagginghdr_id',$id)
            ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            ->orderBy('checktaggingitems_id', 'DESC')
            //->get()
            ->paginate(10);

        }

        // foreach($checks as $ch){
        //     dd($ch->check).'<br />';
        //     //echo $ch->id'<br >';
        // }

        if ($request->ajax()) {
            return view('check.loadclearedchecksbyid', ['checks' => $checks])->render();  
        }

        return view('check.checklistclearedbyid',array(
            'title'     => $title,
            'checks'    => $checks,
            'date'      => $date
        ));
    }



    public function checksForClearing()
    {

        $chquery = $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->where('checks.check_status','PENDING')
        ->whereNull('checks.deleted_at')
        //->whereNull('checks.date_deposit')
        //->whereNotNull('checks.date_deposit')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC');

        $checks = $chquery->get();

        $checkexistcnt = $chquery->where('checks.is_exist',1)->count();

        $checkexistsum = $chquery->where('checks.is_exist',1)->sum('check_amount');

        $totaldep = $checks->sum('check_amount');
      
        $title = 'Checks for Clearing';
        return view('check.checksforclearing',array(
            'title'         => $title,
            'checks'        => $checks,
            'totaldeposit'  => $totaldep,
            'checkexistcnt' => $checkexistcnt,
            'checkexistsum' => $checkexistsum
        ));
    }

    public function ChecksForDeposit(Request $request)
    {

        $title = 'Checks for Deposit';

        if(empty($request->input('searchvalue')))
        {
            //$checks = Check::latest('checks_id')->paginate(10);
            $checks = DB::table('checks')
            ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            ->whereNull('checks.check_status')
            ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            ->select(
                    'checksreceivingtransaction.*',
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.*'
                )
            ->orderBy('check_date', 'ASC')
            ->paginate(10);

            //dd($checks);
        }
        else 
        {
            $checks = DB::table('checks')
            ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
            ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
            ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
            ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
            ->whereNull('checks.check_status')
            ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
            ->select(
                    'checksreceivingtransaction.*',
                    'checks.*', 
                    'banks.bankbranchname', 
                    'customers.*'
                )
            ->where($request->input('searchcol'),'like','%'.$request->input('searchvalue').'%')
            ->orderBy('check_date', 'ASC')
            ->paginate(10);

            //dd($checks);
        }

        if ($request->ajax()) {
            return view('check.loadchecksfordeposit', ['checks' => $checks])->render();  
        }

        return view('check.checklistchecksfordeposit',compact('title','checks'));

    }

    public function ChecksForDeposit2()
    {
        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->whereNull('checks.check_status')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $totaldep = $checks->sum('check_amount');
      
        $title = 'Checks for Deposit';
        return view('check.checksfordeposit',array(
            'title'         => $title,
            'checks'        => $checks,
            'totaldeposit'  => $totaldep
        ));
    }

    public function BouncedChecks()
    {
        Session::forget('bouncecart');
        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        //->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        //->whereNotNull('checks.date_deposit')
        //->whereNotNull('checks.date_deposit')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->where('checks.check_status','=','BOUNCED')
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $totaldep = $checks->sum('check_amount');

        //dd($checks);
      
        $title = 'Bounced Check';
        return view('check.bouncedcheck',array(
            'title'         => $title,
            'checks'        => $checks,
            'totaldeposit'  => $totaldep
        ));
    }

    public function BouncedChecks2()
    {
        Session::forget('bouncecart');
        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->whereNotNull('checks.date_deposit')
        //->whereNotNull('checks.date_deposit')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->where('checks.check_status','=','BOUNCED')
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $totaldep = $checks->sum('check_amount');
      
        $title = 'Bounced Check';
        return view('check.bouncedcheck2',array(
            'title'         => $title,
            'checks'        => $checks,
            'totaldeposit'  => $totaldep
        ));
    }

    public function UpdateBouncedChecks($id,$type)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.*', 
                'customers.*'
            )
        ->first();
        
        $fdate = explode("-",$check->check_date);
        $fdate = $fdate[1].'/'.$fdate[2].'/'.$fdate[0];

        return view('check.updatebouncedcheck',array(
            'id'    =>  $id,
            'check' =>  $check,
            'fdate' =>  $fdate,
            'type'  =>  $type,
        ));
    }

    public function UpdateBouncedChecks2($id)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.*', 
                'customers.*'
            )
        ->first();
        
        $fdate = explode("-",$check->check_date);
        $fdate = $fdate[1].'/'.$fdate[2].'/'.$fdate[0];

        return view('check.updatebouncedcheck2',array(
            'id'    =>  $id,
            'check' =>  $check,
            'fdate' =>  $fdate
        ));
    }

    public function UpdateBouncedChecksTemp(Request $request)
    {
        if(empty($request->state))
        {
            return response()->json(['status'=>false, 'error'=> 'Please select update type.']);
        }

        if(trim($request->state)=='redeposit')
        {
            //return response()->json(['status'=>false, 'error'=> 'Yeahh!!.']);
            $niceNames = array(
                'Check Amount'  => 'redepcheckamount'
            );
            $validator = Validator::make($request->all(), [
                'id'                =>  'required|integer',
                'redepcheckamount'  =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
    
            $validator->setAttributeNames($niceNames); 
            
            if ($validator->passes()) 
            {           

                $data = array(
                    'checkamount'   => $request->redepcheckamount
                );
                
                $checkamt = $request->redepcheckamount;
                $checkb = array(
                    'checkid'       => $request->id,
                    'updatetype'    => 'redeposit',
                    //'data'          => $data
                    'checkamount'   => $request->redepcheckamount,
                    'check_dsnum'   => $request->dsnum,
                );

                $this->addToBounceCart($checkb,$request->id,$request,'redeposit');

                return response()->json([
                    'status'        => true, 
                    'updatetype'    => $request->state,
                    'icon'          => 'book',
                    'tagcount'      => $this->checkcount() 
                ]);
            }


            //return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
        }

        if(trim($request->state)=='replacement')
        {
            $niceNames = array(
                'repcheckno'        =>  'Check Number',
                'repcheckdate'      =>  'Check Date',
                'repchecktype'      =>  'Check Type',
                'repaccountname'    =>  'Account Number',
                'repaccountno'      =>  'Account Name',
                'bankid'            =>  'Bank Name',
                'repcheckamt'       =>  'Check Amount',
                'replacetype'       =>  'Replacement Type',
                'repcash'           =>  'Cash'
            );
            $validator = Validator::make($request->all(), [
                'repcheckno'        =>  'required',
                'repcheckdate'      =>  'required|date_format:m/d/Y',
                'repchecktype'      =>  'required',
                'repaccountno'      =>  'required',
                'repaccountname'    =>  'required',
                'bankid'            =>  'required|integer',
                'repcheckamt'       =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
                'replacetype'       =>  'required',
                'repcash'           =>  'regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
            //echo $request->repcheckdate;
            if ($validator->passes()) 
            {
                $hasChanged = false;
                //check 

                if(trim($request->repcheckno)!=trim($request->ocheckno))
                {
                    $hasChanged = true;
                }

                if(trim($request->repaccountname)!=trim($request->oaccountname))
                {
                    $hasChanged = true;
                }

                if(trim($request->repaccountno)!=trim($request->oaccountno))
                {
                    $hasChanged = true;
                }                     

                if(str_replace(",","",trim($request->repcheckamt))!=trim($request->ocheckamt))
                {
                    $hasChanged = true;
                }

                if(trim($request->bankid)!=trim($request->obankid))
                {
                    $hasChanged = true;
                }

                $datefr = explode("/",$request->repcheckdate);
                
                $datefr = $datefr[2].'-'.$datefr[0].'-'.$datefr[1];

                if($datefr!=$request->ocheckdate)
                {
                    $hasChanged = true;
                }

                if($request->replacetype=='CHECK AND CASH') {
                    $hasChanged = true;
                }

                if(!$hasChanged)
                {
                    return response()->json(['status'=>false,'error'=>'No data values modified.']);
                }
                else 
                {
                    // $data = array(
                    //     'checkno'       =>  $request->repcheckno,
                    //     'accountname'   =>  $request->repaccountname,
                    //     'accountnumber' =>  $request->repaccountno,
                    //     'bankid'        =>  $request->bankid,
                    //     'checkdate'     =>  $datefr,
                    //     'checkamount'   =>  $request->repcheckamt,
                    // );
                    
                    $checkamt = $request->redepcheckamount;
                    $checkb = array(
                        'checkid'       => $request->id,
                        'updatetype'    => 'replacement',
                        //'data'          => $data
                        'checkno'       =>  $request->repcheckno,
                        'accountname'   =>  $request->repaccountname,
                        'accountnumber' =>  $request->repaccountno,
                        'bankid'        =>  $request->bankid,
                        'checkdate'     =>  $datefr,
                        'checktype'     =>  $request->repchecktype,
                        'checkamount'   =>  $request->repcheckamt,
                        'replacetype'   =>  $request->replacetype,
                        'repcash'       =>  $request->repcash,
                        'check_dsnum'   => $request->dsnum,
                    );
    
                    $this->addToBounceCart($checkb,$request->id,$request,'replacement');
    
                    return response()->json([
                        'status'        => true, 
                        'updatetype'    => $request->state,
                        'icon'          => 'bookmark',
                        'tagcount'      => $this->checkcount() 
                    ]);
                }               

            }
            return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
        }

        if(trim($request->state)=='cash')
        {
            $niceNames = array(
                'Cash'  => 'cashamt'
            );
            $validator = Validator::make($request->all(), [
                'id'        =>  'required|integer',
                'cashamt'   =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
    
            $validator->setAttributeNames($niceNames); 
            
            if ($validator->passes()) 
            {           

                $data = array(
                    'cashamount'   => $request->cashamt
                );
                
                $checkamt = $request->redepcheckamount;
                $checkb = array(
                    'checkid'       => $request->id,
                    'updatetype'    => 'cash',
                    //'data'          => $data
                    'checkamount'   => $request->cashamt,
                    'check_dsnum'   => $request->dsnum,
                );

                $this->addToBounceCart($checkb,$request->id,$request,'cash');

                return response()->json([
                    'status'        => true, 
                    'updatetype'    => $request->state,
                    'icon'          => 'tag',
                    'tagcount'      => $this->checkcount() 
                ]);
            }
        }

    }

    public function updatePDC(Request $request){
        //dri
        // dd($request->all());
        // exit();
        if(empty($request->state))
        {
            return response()->json(['status'=>false, 'error'=> 'Please select update type.']);
        }

        if(trim($request->state)=='redeposit')
        {
            //return response()->json(['status'=>false, 'error'=> 'Yeahh!!.']);
            $niceNames = array(
                'Check Amount'  => 'redepcheckamount'
            );
            $validator = Validator::make($request->all(), [
                'id'                =>  'required|integer',
                'redepcheckamount'  =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
    
            $validator->setAttributeNames($niceNames); 
            
            if ($validator->passes()) 
            {         
                DB::beginTransaction();  

                    $checkTag = new CheckTagging;

                    $checkTag->id                   = Auth::user()->id;        
                    $checkTag->businessunit_id      = Auth::user()->businessunit_id; 
        
                    $checkTag->save();
        
                    $idtag = $checkTag->checktagginghdr_id;  

   
                    $checkTagi = new CheckTaggingItem;
    
                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id	                = $request->id;
                    $checkTagi->checktagging_type           = "PDC UPDATE";
                    $checkTagi->checktaggingitems_tag	    = strtoupper($request->state);
                    $checkTagi->check_ds_num	            = $request->dsnum;
                    $checkTagi->save();
    
                    $idtagi = $checkTagi->checktaggingitems_id;        
                    
                    $checkhistory = new CheckHistory;

                    $check = DB::table('checks')
                    ->where('checks.checks_id', '=', $request->id)
                    ->select('checks.*')
                    ->first();

                    $checkamount = trim(str_replace(',','',$request->redepcheckamount));

                    $checkhistory->check_amount  = $check->check_amount;
                    $checkhistory->checktaggingitems_id = $idtagi;

                    $checkhistory->save();             
                    
                    Check::where('checks_id', $request->id)
                    ->update(['check_amount' => $checkamount,'cash' => NULL,
                            'check_status' => 'PENDING' ]);


                DB::commit();

                return response()->json([
                    'status'        => true, 
                    'msg'           => 'Check Successfully Updated.'
                ]);
            }           
        }

        if(trim($request->state)=='replacement')
        {
            $niceNames = array(
                'repcheckno'        =>  'Check Number',
                'repcheckdate'      =>  'Check Date',
                'repchecktype'      =>  'Check Type',
                'repaccountname'    =>  'Account Number',
                'repaccountno'      =>  'Account Name',
                'bankid'            =>  'Bank Name',
                'repcheckamt'       =>  'Check Amount',
                'replacetype'       =>  'Replacement Type',
                'repcash'           =>  'Cash'
            );
            $validator = Validator::make($request->all(), [
                'repcheckno'        =>  'required',
                'repcheckdate'      =>  'required|date_format:m/d/Y',
                'repchecktype'      =>  'required',
                'repaccountno'      =>  'required',
                'repaccountname'    =>  'required',
                'bankid'            =>  'required|integer',
                'repcheckamt'       =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/',
                'replacetype'       =>  'required',
                'repcash'           =>  'regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
            //echo $request->repcheckdate;
            if ($validator->passes()) 
            {
                $hasChanged = false;
                //check 

                if(trim($request->repcheckno)!=trim($request->ocheckno))
                {
                    $hasChanged = true;
                }

                if(trim($request->repaccountname)!=trim($request->oaccountname))
                {
                    $hasChanged = true;
                }

                if(trim($request->repaccountno)!=trim($request->oaccountno))
                {
                    $hasChanged = true;
                }                     

                if(str_replace(",","",trim($request->repcheckamt))!=trim($request->ocheckamt))
                {
                    $hasChanged = true;
                }

                if(trim($request->bankid)!=trim($request->obankid))
                {
                    $hasChanged = true;
                }

                $datefr = explode("/",$request->repcheckdate);
                
                $datefr = $datefr[2].'-'.$datefr[0].'-'.$datefr[1];

                if($datefr!=$request->ocheckdate)
                {
                    $hasChanged = true;
                }

                if($request->replacetype=='CHECK AND CASH') {
                    $hasChanged = true;
                }

                if(!$hasChanged)
                {
                    return response()->json(['status'=>false,'error'=>'No data values modified.']);
                }
                else 
                {       
                    DB::beginTransaction();  

                    $checkTag = new CheckTagging;

                    $checkTag->id = Auth::user()->id;   
                    $checkTag->businessunit_id      = Auth::user()->businessunit_id;       
        
                    $checkTag->save();
        
                    $idtag = $checkTag->checktagginghdr_id;                    
   
                    $checkTagi = new CheckTaggingItem;
    
                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id	                = $request->id;
                    $checkTagi->checktagging_type           = "PDC UPDATE";
                    $checkTagi->checktaggingitems_tag	    = strtoupper($request->state.' - '.$request->replacetype);
                    $checkTagi->save();
    
                    $idtagi = $checkTagi->checktaggingitems_id;        
                    
                    $checkhistory = new CheckHistory;

                    $check = DB::table('checks')
                    ->where('checks.checks_id', '=', $request->id)
                    ->select('checks.*')
                    ->first();

                    $checkamount = trim(str_replace(',','',$request->redepcheckamount));

                    $cash = trim(str_replace(',','',$request->repcash));

                    $checkhistory->checktaggingitems_id  = $idtagi;
                    $checkhistory->check_no              = $check->check_no;                    
                    $checkhistory->check_class           = $check->check_class;                    
                    $checkhistory->check_date            = $check->check_date;
                    $checkhistory->check_type            = $check->check_type;
                    $checkhistory->account_no            = $check->account_no;
                    $checkhistory->account_name          = $check->account_name;
                    $checkhistory->bank_id               = $check->bank_id;
                    $checkhistory->check_amount          = $check->check_amount;
                    $checkhistory->cash                  = $check->cash;     

                    $checkhistory->save();             
                    
                    // Check::where('checks_id', $request->id)
                    // ->update(['check_amount' => $checkamount,'cash' => NULL,
                    //         'check_status' => NULL ]);       
                    $datefr = explode("/",$request->repcheckdate);
                
                    $datefr = $datefr[2].'-'.$datefr[0].'-'.$datefr[1];    
                    
                    $checkamount = trim(str_replace(',','',$request->repcheckamt));

                    $cash = trim(str_replace(',','',$request->repcash));

                    Check::where('checks_id', $request->id)
                    ->update([
                        'check_no'      => $request->repcheckno,
                        'account_name'  => $request->repaccountname,
                        'account_no'    => $request->repaccountno,
                        'bank_id'       => $request->bankid,
                        'check_date'    => $datefr,
                        'check_type'    => $request->repchecktype,
                        'check_amount'  => $checkamount,
                        'cash'          => $request->replacetype == 'CHECK AND CASH' ? $cash : NULL,
                        'check_status'  => 'PENDING' 
                    ]);

                DB::commit();

                return response()->json([
                    'status'        => true, 
                    'msg'           => 'Check Successfully Updated.'
                ]);
                    // $checkamt = $request->redepcheckamount;
                    // $checkb = array(
                    //     'checkid'       => $request->id,
                    //     'updatetype'    => 'replacement',
                    //     //'data'          => $data
                    //     'checkno'       =>  $request->repcheckno,
                    //     'accountname'   =>  $request->repaccountname,
                    //     'accountnumber' =>  $request->repaccountno,
                    //     'bankid'        =>  $request->bankid,
                    //     'checkdate'     =>  $datefr,
                    //     'checktype'     =>  $request->repchecktype,
                    //     'checkamount'   =>  $request->repcheckamt,
                    //     'replacetype'   =>  $request->replacetype,
                    //     'repcash'       =>  $request->repcash
                    // );
    
                    // $this->addToBounceCart($checkb,$request->id,$request,'replacement');
    
                    // return response()->json([
                    //     'status'        => true, 
                    //     'updatetype'    => $request->state,
                    //     'icon'          => 'bookmark',
                    //     'tagcount'      => $this->checkcount() 
                    // ]);
                }               

            }
            return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
        }

        if(trim($request->state)=='cash')
        {
            $niceNames = array(
                'Cash'  => 'cashamt'
            );
            $validator = Validator::make($request->all(), [
                'id'        =>  'required|integer',
                'cashamt'   =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
            ]);
    
            $validator->setAttributeNames($niceNames); 
            
            if ($validator->passes()) 
            {           
                DB::beginTransaction();  

                    $checkTag = new CheckTagging;

                    $checkTag->id = Auth::user()->id;    
                    $checkTag->businessunit_id      = Auth::user()->businessunit_id;      
        
                    $checkTag->save();
        
                    $idtag = $checkTag->checktagginghdr_id;  

   
                    $checkTagi = new CheckTaggingItem;
    
                    $checkTagi->checktagginghdr_id          = $idtag;         
                    $checkTagi->checks_id	                = $request->id;
                    $checkTagi->checktagging_type           = "PDC UPDATE";
                    $checkTagi->checktaggingitems_tag	    = strtoupper($request->state);
                    $checkTagi->save();
    
                    $idtagi = $checkTagi->checktaggingitems_id;        
                    
                    $checkhistory = new CheckHistory;

                    $check = DB::table('checks')
                    ->where('checks.checks_id', '=', $request->id)
                    ->select('checks.*')
                    ->first();

                    $cashamt = trim(str_replace(',','',$request->cashamt));

                    $checkhistory->check_amount  = $check->check_amount;
                    $checkhistory->checktaggingitems_id = $idtagi;

                    $checkhistory->save();             
                    
                    Check::where('checks_id', $request->id)
                    ->update(['check_amount' => NULL,'cash' => $cashamt,
                            'check_status' => 'CASH']);

                DB::commit();

                return response()->json([
                    'status'        => true, 
                    'msg'           => 'Check Successfully Updated.'
                ]);
                // $data = array(
                //     'cashamount'   => $request->cashamt
                // );
                
                // $checkamt = $request->redepcheckamount;
                // $checkb = array(
                //     'checkid'       => $request->id,
                //     'updatetype'    => 'cash',
                //     //'data'          => $data
                //     'checkamount'   => $request->cashamt
                // );

                // $this->addToBounceCart($checkb,$request->id,$request,'cash');

                // return response()->json([
                //     'status'        => true, 
                //     'updatetype'    => $request->state,
                //     'icon'          => 'tag',
                //     'tagcount'      => $this->checkcount() 
                // ]);
            }
        }
    }

    // public function UpdateBouncedChecksTemp2(Request $request)
    // {
        
    //     if(empty($request->state))
    //     {
    //         return response()->json(['status'=>false, 'error'=> 'Please select update type.']);
    //     }

    //     if(trim($request->state)=='redeposit')
    //     {
    //         //return response()->json(['status'=>false, 'error'=> 'Yeahh!!.']);
    //         $niceNames = array(
    //             'Check Amount'  => 'redepcheckamount'
    //         );
    //         $validator = Validator::make($request->all(), [
    //             'id'                =>  'required|integer',
    //             'redepcheckamount'  =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
    //         ]);
    
    //         $validator->setAttributeNames($niceNames); 
            
    //         if ($validator->passes()) 
    //         {           
    //             $check = Check::find($request->id);
    //             $check->check_amount      = str_replace(",","",trim($request->redepcheckamount));
    //             $check->check_status      = 'CLEARED';
          
    //             $check->save();

    //             // $bouncedhistory = new BouncedHistory;
    //             // $bouncedhistory->check_no = $request->repcheckno;
    //             // $bouncedhistory->check_date      = $datefr;
    //             // $bouncedhistory->check_type      = $request->repchecktype;
    //             // $bouncedhistory->account_no      = $request->repaccountno;
    //             // $bouncedhistory->account_name      = $request->repaccountname;
    //             // $bouncedhistory->bank_id      = $request->bankid;
    //             // $bouncedhistory->check_amount      = str_replace(",","",trim($request->repcheckamt));
    //             // $bouncedhistory->save();

    //             return response()->json(['status'=>true]);
    //         }


    //         //return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    //     }

    //     if(trim($request->state)=='replacement')
    //     {

    //         $niceNames = array(
    //             'repcheckno'        =>  'Check Number',
    //             'repcheckdate'      =>  'Check Date',
    //             'repchecktype'      =>  'Check Type',
    //             'repaccountname'    =>  'Account Number',
    //             'repaccountno'      =>  'Account Name',
    //             'bankid'            =>  'Bank Name',
    //             'repcheckamt'       =>  'Check Amount'
    //         );
    //         $validator = Validator::make($request->all(), [
    //             'repcheckno'        =>  'required',
    //             'repcheckdate'      =>  'required|date_format:m/d/Y',
    //             'repchecktype'      =>  'required',
    //             'repaccountno'      =>  'required',
    //             'repaccountname'    =>  'required',
    //             'bankid'            =>  'required|integer',
    //             'repcheckamt'       =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
    //         ]);
    //         //echo $request->repcheckdate;
    //         if ($validator->passes()) 
    //         {
    //             $hasChanged = false;
    //             //check 

    //             if(trim($request->repcheckno)!=trim($request->ocheckno))
    //             {
    //                 $hasChanged = true;
    //             }

    //             if(trim($request->repaccountname)!=trim($request->oaccountname))
    //             {
    //                 $hasChanged = true;
    //             }

    //             if(trim($request->repaccountno)!=trim($request->oaccountno))
    //             {
    //                 $hasChanged = true;
    //             }                     

    //             if(str_replace(",","",trim($request->repcheckamt))!=trim($request->ocheckamt))
    //             {
    //                 $hasChanged = true;
    //             }

    //             if(trim($request->bankid)!=trim($request->obankid))
    //             {
    //                 $hasChanged = true;
    //             }

    //             $datefr = explode("/",$request->repcheckdate);
                
    //             $datefr = $datefr[2].'-'.$datefr[0].'-'.$datefr[1];

    //             if($datefr!=$request->ocheckdate)
    //             {
    //                 $hasChanged = true;
    //             }

    //             if(!$hasChanged)
    //             {
    //                 return response()->json(['status'=>false,'error'=>'No data values modified.']);
    //             }
    //             else 
    //             {

    //                 $check = Check::find($request->id);

    //                 $check->check_no      = $request->repcheckno;
    //                 $check->account_name      = $request->repaccountname;
    //                 $check->account_no      = $request->repaccountno;
    //                 $check->bank_id      = $request->bankid;
    //                 $check->check_date      = $datefr;
    //                 $check->check_type      = $request->repchecktype;
    //                 $check->check_amount      = str_replace(",","",trim($request->repcheckamt));
    //                 $check->check_status      = 'CLEARED';
              
    //                 $check->save();

    //                 // $bouncedhistory = new BouncedHistory;
    //                 // $bouncedhistory->check_no = $request->repcheckno;
    //                 // $bouncedhistory->check_date      = $datefr;
    //                 // $bouncedhistory->check_type      = $request->repchecktype;
    //                 // $bouncedhistory->account_no      = $request->repaccountno;
    //                 // $bouncedhistory->account_name      = $request->repaccountname;
    //                 // $bouncedhistory->bank_id      = $request->bankid;
    //                 // $bouncedhistory->check_amount      = str_replace(",","",trim($request->repcheckamt));
    //                 // $bouncedhistory->save();

    //                 return response()->json(['status'=>true]);

    //             }               

    //         }
    //         return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    //     }

    //     if(trim($request->state)=='cash')
    //     {
    //         $niceNames = array(
    //             'Cash'  => 'cashamt'
    //         );
    //         $validator = Validator::make($request->all(), [
    //             'id'        =>  'required|integer',
    //             'cashamt'   =>  'required|regex:/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]+)*$/'
    //         ]);
    
    //         $validator->setAttributeNames($niceNames); 
            
    //         if ($validator->passes()) 
    //         {           

    //             $check = Check::find($request->id);
    //             $check->check_amount      = str_replace(",","",trim($request->cashamt));
    //             $check->check_status      = 'CLEARED';
          
    //             $check->save();

    //             // $bouncedhistory = new BouncedHistory;
    //             // $bouncedhistory->check_no = $request->repcheckno;
    //             // $bouncedhistory->check_date      = $datefr;
    //             // $bouncedhistory->check_type      = $request->repchecktype;
    //             // $bouncedhistory->account_no      = $request->repaccountno;
    //             // $bouncedhistory->account_name      = $request->repaccountname;
    //             // $bouncedhistory->bank_id      = $request->bankid;
    //             // $bouncedhistory->check_amount      = str_replace(",","",trim($request->repcheckamt));
    //             // $bouncedhistory->save();

    //             return response()->json(['status'=>true]);
    //         }
    //     }

    //     return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);

    // }

    public function addToBounceCart($checkb,$id,$request,$type)
    {
        $oldCart = Session::has('bouncecart') ? Session::get('bouncecart') : null;
        $cart = new BounceCart($oldCart);    

        if(is_null($oldCart))
        {
            $cart->add($checkb);
            $request->session()->put('bouncecart',$cart); 
        }
        else 
        {
            // validate if check already exist in a session cart array
            if($cart->is_bouncedCheckID_exist($id))
            {
                if($type=='redeposit' || $type=='cash') {
                    $cart->update($checkb);
                }
                else if($type == 'replacement') {
                    $cart->update2($checkb);
                }
                
                $request->session()->put('bouncecart',$cart); 
            }
            else 
            {
                $cart->add($checkb);
                $request->session()->put('bouncecart',$cart); 
            }
        }
    }

    // public function addToBounceCart2($checkb,$id,$request)
    // {
    //     $oldCart = Session::has('bouncecart') ? Session::get('bouncecart') : null;
    //     $cart = new BounceCart($oldCart);    

    //     if(is_null($oldCart))
    //     {
    //         $cart->add($checkb);
    //         $request->session()->put('bouncecart',$cart); 
    //     }
    //     else 
    //     {
    //         // validate if check already exist in a session cart array
    //         if($cart->is_bouncedCheckID_exist($id))
    //         {
    //             $cart->update2($checkb);
    //             $request->session()->put('bouncecart',$cart); 
    //         }
    //         else 
    //         {
    //             $cart->add($checkb);
    //             $request->session()->put('bouncecart',$cart); 
    //         }
    //     }
    // }

    public function checkCount()
    {
        $oldCart = Session::has('bouncecart') ? Session::get('bouncecart') : null;
        $cart = new BounceCart($oldCart);  

        return $cart->checkcount();
    }

    public function tagAs($id,$state)
    {
        $check = DB::table('checks')
        ->where('checks.checks_id', '=', $id)        
        ->select(
            'checks.check_no'
            )
        ->first();
        if($state == 'false')
        {
            echo "Tag Check no. {$check->check_no} as Bounce?";
        }
        else 
        {
            echo "Untag Check no. {$check->check_no} as Bounce?";
        }
    }   

    public function duePdcExport()
    {

        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->where('checks.check_type','=','POST DATED')
        ->whereNull('checks.check_status')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->get();

        $file = "PDC".Carbon::now()->format('Y-m-d');

        $spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(36);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(24);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(27);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);

        $table_columns = array(
            "CUSTOMER CODE", 
            "CUSTOMER NAME", 
            "BANK ACCOUNT NO", 
            "BANK ACCOUNT NAME", 
            "CHECK NO",
            "BANK NAME",
            "CHECK DATE",
            "AMOUNT"
        );

        $column = 1;

        foreach($table_columns as $field)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $spreadsheet->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold( true );
        $excel_row = 2;

        foreach($checks as $ch)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $ch->cus_code);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $ch->fullname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $ch->account_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $ch->account_name);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $ch->check_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $ch->bankbranchname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $ch->check_date);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(8, $excel_row, $ch->check_amount);
            $spreadsheet->getActiveSheet()->getStyle('H'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
            $excel_row++;
        }
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'.xlsx"');
        $writer->save("php://output");
    }

    public function testFunc()
    {

        try 
        {
            $checks = DB::connection('sqlsrv')
            ->table('chk_dtl')
            ->join('chk_mst', 'chk_mst.issue_no', '=', 'chk_dtl.issue_no')
            ->join('customer', 'customer.custid', '=', 'chk_mst.custid')
            ->where('chk_mst.loc_code', Auth::user()->businessunit->loc_code_atp)
            ->where('chk_dtl.entry_no','>',$this->getATPLastIssueNo())
            ->where('chk_mst.atp_date', '>=', '2019-01-24 00:00:00')
            ->limit(50)
            ->orderBy('entry_no', 'asc')
            ->select(
                'chk_mst.issue_no',
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

        } 
        catch (\Exception $e) 
        {
            echo json_encode([
                'status'	=> 'error',
                'message'	=> "Could not connect to the database.  Please check your configuration. error:" . $e 
            ]);
            exit();
        }       
        dd($checks);
        // if($id = $this->isBankExist('1ST VALLEY - AGLAYAN'))
        // {
        //     echo $id;
        // }
        // else 
        // {
        //     echo 'nah';
        // }
    }

    public function testFunc2(){

    }

    public function viewcheckupdateRedeposit($id)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.bankbranchname', 
                'customers.*'
            )
        ->first();
        return view('check.viewcheckupdateRedeposit',array(
            'id'    =>  $id,
            'check' =>  $check
        ));
    }

    public function viewcheckupdateReplacement($id)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.bankbranchname', 
                'customers.*'
            )
        ->first();
        return view('check.viewcheckupdateReplacement',array(
            'id'    =>  $id,
            'check' =>  $check
        ));
    }

    public function viewcheckupdateCash($id)
    {
        $check = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.checks_id', '=', $id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                DB::raw("DATE_FORMAT(checks.check_date, '%M %d, %Y') as cdate"),
                'banks.bankbranchname', 
                'customers.*'
            )
        ->first();
        return view('check.viewcheckupdateCash',array(
            'id'    =>  $id,
            'check' =>  $check
        ));
    }

    public function checksforClearingExport()
    {
        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->whereNull('checks.check_status')
        ->whereNull('checks.date_deposit')
        //->whereNotNull('checks.date_deposit')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $file = "ChecksForClearingExport".Carbon::now()->format('Y-m-d');

        $spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(38);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $table_columns = array( 
            "CUSTOMER NAME", 
            "BANK ACCOUNT NO", 
            "BANK ACCOUNT NAME", 
            "CHECK NO",
            "CHECK DATE",
            "CHECK TYPE",
            "AMOUNT"
        );

        $column = 1;

        foreach($table_columns as $field)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $spreadsheet->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold( true );
        $excel_row = 2;

        foreach($checks as $ch)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $ch->fullname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $ch->account_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $ch->account_name);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $ch->check_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $ch->check_date);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $ch->check_type);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $ch->check_amount);
            $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
            $excel_row++;
        }
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'.xlsx"');
        $writer->save("php://output");
    }

    public function checksPDCExport()
    {
        $checks = DB::table('checks')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')            
        ->where('checks.check_date', '>', date('Y-m-d'))                   
        ->where('checks.check_status','<>','CASH')  
        ->whereNull('checks.deleted_at')  
        ->where('checks.businessunit_id','=',Auth::user()->businessunit_id)
        ->orderBy('checks.check_date','asc')
        ->select(
                'checks.*', 
                'banks.bankbranchname', 
                'customers.cus_code',
                'customers.fullname'
        )  
        ->get();
      
        $now = Carbon::now();
        $file = "PDC".$now->format('Y-m-d');

        $spreadsheet = new Spreadsheet(); 

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, Auth::user()->businessunit->bname);
        $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2, strtoupper(Auth::user()->usertype->usertype_name).' DEPARTMENT');
        $spreadsheet->getActiveSheet()->getStyle('A2:L2')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 3, 'SUMMARY OF CHECKS RECEIVED');
        $spreadsheet->getActiveSheet()->getStyle('A3:L3')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 4, 'FOR THE MONTH OF '.strtoupper($now->format('F')).' '.$now->year);
        $spreadsheet->getActiveSheet()->getStyle('A4:L4')->getFont()->setBold(true);
        //$sheet = $spreadsheet->getActiveSheet();

        $excel_row = 6;

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(16);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(16);

        $table_columns = array( 
            "Date", 
            "Payees Name/Acct. Name", 
            "Approving Officer", 
            "Bank Name",
            "Check Number",
            "Check Date",
            "Amount"
        );

        $column = 1;

        foreach($table_columns as $field)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, $excel_row, $field);
            $column++;
        }
        $styleArray = array(
            'borders' => array(
                'outline' => array(
                    'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                    'color' => array('argb' => '00000000'),
                ),
            ),
        );
        $spreadsheet->getActiveSheet()->getStyle('A6:G6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle("A6:G6")->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $spreadsheet->getActiveSheet()->getStyle("A6")->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $spreadsheet->getActiveSheet()->getStyle("G6")->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $spreadsheet->getActiveSheet()->getStyle("A6:G6")->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        // $excel_row = 2;

        $excel_row++;
        $total = 0;
        foreach($checks as $ch)
        {
            $total += $ch->check_amount;
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $ch->check_received);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $ch->account_name);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, '');
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $ch->bankbranchname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $ch->check_no);            
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $ch->check_date);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $ch->check_amount);
            $spreadsheet->getActiveSheet()->getStyle("A".$excel_row)->getBorders()->getLeft()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $spreadsheet->getActiveSheet()->getStyle('B'.$excel_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('E'.$excel_row)->getNumberFormat();
            $spreadsheet->getActiveSheet()->getStyle('E'.$excel_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('A'.$excel_row)->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
            $spreadsheet->getActiveSheet()->getStyle('F'.$excel_row)->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
            $spreadsheet->getActiveSheet()->getStyle('A'.$excel_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$excel_row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle("G".$excel_row)->getBorders()->getRight()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
            $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
            $excel_row++;
        }
        $spreadsheet->getActiveSheet()->getStyle("A{$excel_row}:G{$excel_row}")->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, 'Total Checks Received for '.strtoupper($now->format('F')).' '.$now->year);
        $spreadsheet->getActiveSheet()->getStyle("A{$excel_row}:G{$excel_row}")->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $total);
        $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
        $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getBorders()->getBottom()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE);

        $excel_row++;
        $excel_row++;

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, 'Prepared by:');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'Confirmed by:');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, 'Audited by:');

        $excel_row++;
        $excel_row++;

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, strtoupper(Auth::user()->name));
        $spreadsheet->getActiveSheet()->getStyle("A{$excel_row}")->getFont()->setBold(true);

        $excel_row++;
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, Auth::user()->usertype->usertype_name.' Staff');
        
        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, 'ICM-Acctg. Staff');

        $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, 'ICM-IAD Staff');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'.xlsx"');
        $writer->save("php://output");
    }

    public function checksforDepositExport()
    {
        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->whereNull('checks.check_status')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $file = "ChecksForDepositExport".Carbon::now()->format('Y-m-d');

        $spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(38);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $table_columns = array( 
            "CUSTOMER NAME", 
            "BANK ACCOUNT NO", 
            "BANK ACCOUNT NAME", 
            "CHECK NO",
            "CHECK DATE",
            "CHECK TYPE",
            "AMOUNT"
        );

        $column = 1;

        foreach($table_columns as $field)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $spreadsheet->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold( true );
        $excel_row = 2;

        foreach($checks as $ch)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $ch->fullname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $ch->account_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $ch->account_name);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $ch->check_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $ch->check_date);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $ch->check_type);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $ch->check_amount);
            $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
            $excel_row++;
        }
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'.xlsx"');
        $writer->save("php://output");
    }

    public function bouncedChecksExport()
    {

        $checks = DB::table('checks')
        ->join('checksreceivingtransaction','checksreceivingtransaction.checksreceivingtransaction_id','=','checks.checksreceivingtransaction_id')
        ->join('customers', 'checks.customer_id', '=', 'customers.customer_id')
        ->join('banks', 'checks.bank_id', '=', 'banks.bank_id')
        ->where('checks.check_date', '<=', Carbon::now()->format('Y-m-d'))
        ->whereNotNull('checks.date_deposit')
        //->whereNotNull('checks.date_deposit')
        ->where('checksreceivingtransaction.businessunit_id','=',Auth::user()->businessunit_id)
        ->where('checks.check_status','=','BOUNCED')
        ->select(
                'checksreceivingtransaction.*',
                'checks.*', 
                'banks.bankbranchname', 
                'customers.*'
            )
        ->orderBy('check_date', 'ASC')
        ->get();

        $file = "BouncedChecksExport".Carbon::now()->format('Y-m-d');

        $spreadsheet = new Spreadsheet();
        //$sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(35);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $table_columns = array( 
            "CUSTOMER NAME", 
            "BANK ACCOUNT NO", 
            "BANK ACCOUNT NAME", 
            "CHECK NO",
            "CHECK DATE",
            "CHECK TYPE",
            "AMOUNT"
        );

        $column = 1;

        foreach($table_columns as $field)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $field);
            $column++;
        }

        $spreadsheet->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold( true );
        $excel_row = 2;

        foreach($checks as $ch)
        {
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, $excel_row, $ch->fullname);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(2, $excel_row, $ch->account_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(3, $excel_row, $ch->account_name);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(4, $excel_row, $ch->check_no);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(5, $excel_row, $ch->check_date);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(6, $excel_row, $ch->check_type);
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(7, $excel_row, $ch->check_amount);
            $spreadsheet->getActiveSheet()->getStyle('G'.$excel_row)->getNumberFormat()->setFormatCode('#,##0.00');
            $excel_row++;
        }
        
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'.xlsx"');
        $writer->save("php://output");
    }

    public function savebouncedcheck(Request $request)
    {
        $access = Session::get('bouncecart'); 
        $x = count($access->checks);

        //dd($access);

        // $checkamount = 0;
        // $checkids = collect();
        // $updates = collect();
        
        DB::beginTransaction();  

            $checkTag = new CheckTagging;

            $checkTag->id               = Auth::user()->id;       
            $checkTag->businessunit_id  = Auth::user()->businessunit_id;  

            $checkTag->save();

            $idtag = $checkTag->checktagginghdr_id;  

            for($c = 0; $c < $x; $c++){
                $update = "";
                if($access->checks[$c]['updatetype'] == 'replacement') {
                    $update = strtoupper($access->checks[$c]['updatetype'].' - '.$access->checks[$c]['replacetype']);
                }
                else {
                    $update = strtoupper($access->checks[$c]['updatetype']);
                }

                $checkTagi = new CheckTaggingItem;

                $checkTagi->checktagginghdr_id          = $idtag;         
                $checkTagi->checks_id	                = $access->checks[$c]['checkid'];
                $checkTagi->checktagging_type           = "BOUNCED UPDATE";
                $checkTagi->checktaggingitems_tag	    = $update;
                $checkTagi->check_ds_num	            = $access->checks[$c]['check_dsnum'];
                $checkTagi->save();

                $idtagi = $checkTagi->checktaggingitems_id;

                $check = DB::table('checks')
                    ->where('checks.checks_id', '=', $access->checks[$c]['checkid'])
                    ->select('checks.*')
                    ->first();

                $checkhistory = new CheckHistory;


                $checkamount = trim(str_replace(',','',$access->checks[$c]['checkamount']));

                if($access->checks[$c]['updatetype']=='redeposit') 
                {
                    $checkhistory->check_amount  = $check->check_amount;
                    $checkhistory->checktaggingitems_id = $idtagi;
                    Check::where('checks_id', $access->checks[$c]['checkid'])
                    ->update(['check_amount' => $checkamount,
                            'check_status' => 'PENDING' ]);
                }
                else if($access->checks[$c]['updatetype']=='cash') 
                {
                    $checkhistory->check_amount  = $check->check_amount;
                    $checkhistory->checktaggingitems_id = $idtagi;
                    Check::where('checks_id', $access->checks[$c]['checkid'])
                    ->update([
                        'check_amount'  => NULL,
                        'cash'          => $checkamount,
                        'check_status'  => 'CASH' 
                    ]);
                }
                else if($access->checks[$c]['updatetype']=='replacement')
                {
                    $cash = trim(str_replace(',','',$access->checks[$c]['repcash']));

                    $checkhistory->checktaggingitems_id = $idtagi;
                    $checkhistory->check_no              = $check->check_no;                    
                    $checkhistory->check_class           = $check->check_class;                    
                    $checkhistory->check_date            = $check->check_date;
                    $checkhistory->check_type            = $check->check_type;
                    $checkhistory->account_no            = $check->account_no;
                    $checkhistory->account_name          = $check->account_name;
                    $checkhistory->bank_id               = $check->bank_id;
                    $checkhistory->check_amount          = $check->check_amount;
                    $checkhistory->cash                  = $check->cash;                    

                    Check::where('checks_id', $access->checks[$c]['checkid'])
                    ->update([
                        'check_no' => $access->checks[$c]['checkno'],
                        'account_name' => $access->checks[$c]['accountname'],
                        'account_no' => $access->checks[$c]['accountnumber'],
                        'bank_id' => $access->checks[$c]['bankid'],
                        'check_date' => $access->checks[$c]['checkdate'],
                        'check_type' => $access->checks[$c]['checktype'],
                        'check_amount' => trim(str_replace(',','',$access->checks[$c]['checkamount'])),
                        'cash' => $access->checks[$c]['replacetype'] == 'CHECK AND CASH' ? $cash : NULL,
                        'check_status'  => 'PENDING' 
                    ]);
                }

                $checkhistory->save();

            }
        DB::commit();
        return 'Check/s Successfully Saved.';
        

        // DB::beginTransaction();    
        
        //     $checkTag = new CheckTagging;

        //     $checkTag->id                        = Auth::user()->id;         
        //     $checkTag->checktagginghdr_remarks   = $remarks;

        //     $checkTag->save();

        //     $idtag = $checkTag->checktagginghdr_id;        
            
            // for($z = 0; $z < count($checkids); $z++){
            //     $id = $checkids[$z];

        //         $update = $updates[$z];
        //         $check = DB::table('checks')
        //             ->where('checks.checks_id', '=', $id)
        //             ->select('checks.*')
        //             ->first();

        //         $checkhistory = new CheckHistory;
            
        //         $checkhistory->checks_id             = $check->checks_id;
        //         $checkhistory->check_no              = $check->check_no;
        //         $checkhistory->check_class           = $check->check_class;
        //         $checkhistory->check_date            = $check->check_date;
        //         $checkhistory->check_type            = $check->check_type;
        //         $checkhistory->account_no            = $check->account_no;
        //         $checkhistory->account_name          = $check->account_name;
        //         $checkhistory->bank_id               = $check->bank_id;
        //         $checkhistory->check_amount          = $check->check_amount;
        //         $checkhistory->update_type           = $update;            
        //         $checkhistory->save();

        //         if($update == 'redeposit'){
        //             $checkamount = trim(str_replace(',','',$access->checks[$z]['checkamount']));
        //             Check::where('checks_id', $id)
        //             ->update(['check_amount' => $checkamount,
        //                     'check_status' => 'CLEARED']);
        //         }else if($update == 'replacement'){
        //             Check::where('checks_id', $id)
        //             ->update(['check_no' => $access->checks[$z]['checkno'],
        //                     'account_name' => $access->checks[$z]['accountname'],
        //                     'account_no' => $access->checks[$z]['accountnumber'],
        //                     'bank_id' => $access->checks[$z]['bankid'],
        //                     'check_date' => $access->checks[$z]['checkdate'],
        //                     'check_type' => $access->checks[$z]['checktype'],
        //                     'check_amount' => trim(str_replace(',','',$access->checks[$z]['checkamount'])),
        //                     'check_status' => 'CLEARED']);
        //         }else if($update == 'cash'){
        //             $checkamount = trim(str_replace(',','',$access->checks[$z]['checkamount']));
        //             Check::where('checks_id', $id)
        //             ->update(['check_amount' => $checkamount,
        //                     'check_status' => 'CLEARED']);
        //         }
            // }
            
        // DB::commit();
        // return 'Check/s Successfully Saved.';
    }

    public function institutional(){
        $title = 'Institutional';
        return view('check.institutional',compact('title')); 
        //updatedbfromatp
    }

    public function institutionalImport(){
        
        set_time_limit(0);
        ob_implicit_flush(true);
        ob_end_flush();  

        //get textfile ip address
        $data = AppSettings::where('app_key','app_institutionalcheck_ip')
        ->get();
        $txtfile_ip = $data[0]['app_value'];
        $txtfile_ip_new = $txtfile_ip.'NEW\\'.strtoupper(Auth::user()->businessunit->loc_code_atp).'\\';
        $txtfile_ip_uploaded = $txtfile_ip.'UPLOADED\\'.strtoupper(Auth::user()->businessunit->loc_code_atp).'\\';


        if(!file_exists($txtfile_ip_new) || !file_exists($txtfile_ip_uploaded)){
            echo json_encode([
                'status'	=> 'error',
                'message'	=> "Cant Connect to textfile folder. Contact Administrator."
            ]);
            exit();
        }

        $files = scandir($txtfile_ip_new);

        $cnt = count($files)-2;
        
        if($cnt==0){
            echo json_encode([
                'status'	=> 'noupdate',
                'message'	=> 'There is no update this time.'
            ]);
            exit();
        }

        usleep(80000);
        echo json_encode([
            'status'	=> 'counting',
            'message'	=> $cnt
        ]);
        $ch = collect(); 

        $hasError = false;
        DB::beginTransaction();

            $checkRec = new CheckReceived;

            $checkRec->checksreceivingtransaction_ctrlno    = $this->getControlNumber();
            $checkRec->id                                   = Auth::user()->id;         
            $checkRec->company_id                           = Auth::user()->company_id;
            $checkRec->businessunit_id                      = Auth::user()->businessunit_id;
            $checkRec->save();
            usleep(30000);  

            $recid = $checkRec->checksreceivingtransaction_id; 
            $cntasc = 1;
            foreach($files as $file){
                $arr_f =[];
                if (strpos($file, '.txt') !== false || strpos($file, '.TXT') !== false) {
                    $r_f = fopen($txtfile_ip_new.$file.'','r');

                    while(!feof($r_f)) 
                    {
                        //usleep(80000);
                        $arr_f[] = fgets($r_f);
                    }
                    fclose($r_f);
                    $customer = "";
                    $checkno = "";
                    $class ="";
                    $category = "";
                    $expire = "";
                    $checkdate = "";
                    $checkreceived = "";
                    $checktype = "";
                    $accountname = "";
                    $accountnumber = "";
                    $bank ="";
                    $checkamount = "";
                    $daterec = ""; 
            
                    for ($i=0; $i < count($arr_f); $i++) 
                    {                   
                        if($i==0)
                        {
                            $arr = explode(",",$arr_f[$i]);
                            $customer = $arr[1];
                        }	
                        if($i==1)
                        {
                            $arr = explode(",",$arr_f[$i]);
                            $checkno = $arr[1];
                        }	
                        if($i==2){
                            $arr = explode(",",$arr_f[$i]);
                            $class = $arr[1];
                        }
                        if($i==3){
                            $arr = explode(",",$arr_f[$i]);
                            $checkdate = $arr[1];
                        }
                        if($i==5){
                            $arr = explode(",",$arr_f[$i]);
                            $category = strtoupper($arr[1]);
                        }
                        if($i==4){
                            $arr = explode(",",$arr_f[$i]);
                            $checktype = $arr[1];
                        }
                        if($i==6){
                            $arr = explode(",",$arr_f[$i]);
                            $expire = $arr[1];
                        }
                        if($i==7){
                            $arr = explode(",",$arr_f[$i]);
                            $accountnumber = $arr[1];
                        }
                        if($i==8){
                            $arr = explode(",",$arr_f[$i]);
                            $accountname = $arr[1];
                        }
                        if($i==9){
                            $arr = explode(",",$arr_f[$i]);
                            $bank = $arr[1];
                        }
                        if($i==10){
                            $arr = explode(",",$arr_f[$i]);
                            $checkamount = $arr[1];
                        }
                        if($i==11){
                            $arr = explode(",",$arr_f[$i]);
                            $daterec = $arr[1];
                        }

                    }

                    if(trim(str_replace('/','',$expire))=='' || trim($expire)=='')
                    {
                        $expire = NULL;
                    }
                    else 
                    {
                        $cdarr = explode("/", $expire);
                        $expire = trim($cdarr[2]).'-'.trim($cdarr[0]).'-'.trim($cdarr[1]);
                    }

                    if(str_replace('/','',$daterec)=='' || trim($daterec)=='')
                    {
                        $daterec = NULL;
                    }
                    else
                    {
                        $cdarr = explode("/", $daterec);
                        $daterec = trim($cdarr[2]).'-'.trim($cdarr[0]).'-'.trim($cdarr[1]);
                    }

                    $cdarr = explode("/", $checkdate);
                    //Y-M-D
                    //03/25/2019
                    $checkdate = trim($cdarr[2]).'-'.trim($cdarr[0]).'-'.trim($cdarr[1]);
                    $customerid = "";
                  
                    //check if customer exist / create customer
                    if($id = $this->isCustomerNameExist($customer))
                    {
                        $customerid = $id;
                    }
                    else 
                    {
                        $customerid = $this->autoCreateCustomer($customer);
                    }

                    //check if bank exist / create bank
                    $bankid = "";
                    $bankname = trim($bank);
                    if($bid = $this->isBankExist($bankname))
                    {
                        $bankid = $bid;
                    }
                    else 
                    {
                        $bankid = $this->autoCreateNewBank($bankname);
                    }                
                    $ch->push([
                        'customer'      => $customer,
                        'checkno'       => $checkno,
                        'class'         => $class,
                        'category'      => $category,
                        'expire'        => $expire,
                        'checkreceived' => $daterec,
                        'checktype'     => $checktype,
                        'accountname'   => $accountname,
                        'accountnumber' => $accountnumber,
                        'bank'          => $bank,
                        'checkamount'   => $checkamount,
                        'checkdate'     => $checkdate
                    ]);                  

                    $check = new Check();

                    $check->checksreceivingtransaction_id   = $recid;
                    $check->customer_id                     = $customerid;
                    $check->check_no                        = trim($checkno);         
                    $check->check_class                     = trim($class);   
                    $check->check_date                      = trim($checkdate);
                    $check->check_received                  = $daterec;
                    $check->check_type                      = trim(strtoupper($checktype));   
                    $check->account_no                      = trim($accountnumber);  
                    $check->account_name                    = trim($accountname);  
                    $check->bank_id                         = $bankid;
                    $check->businessunit_id                 = Auth::user()->businessunit_id;
                    $check->check_amount                    = trim(str_replace(',','',$checkamount));
                    $check->check_expiry                    = $expire;
                    $check->check_category                  = trim($category);
                    $check->check_status                    = 'PENDING';
                    $check->department_from                 = 14;
                    $check->currency_id                     = 1;

                    if(!$check->save())
                    {
                        $hasError = true;
                        break;
                    }   

                    echo json_encode([
                        'status'	=> 'saving',
                        'message'	=> 'Importing '.$cntasc.' of '.$cnt
                    ]);
                    usleep(80000);
                    $cntasc++;                
                }                
            }

            if($hasError)
            {
                DB::rollback();
                echo json_encode([
                    'status'	=> 'error',
                    'message'	=> 'Something Went Wrong.'
                ]);

                exit();
            }  
            else
            {
                foreach($ch as $c){
                    // echo $txtfile_ip_new.trim($c['checkno']).'.TXT';
                    // if(file_exists($txtfile_ip_new.trim($c['checkno']).'.TXT')){
                    //     echo 'yeah';
                    // }
                    // else{
                    //     echo 'nah';
                    // }
                    // die();
                    $move = File::move($txtfile_ip_new.trim($c['checkno']).'.TXT', $txtfile_ip_uploaded.trim($c['checkno']).'.TXT');
                }
                
                DB::commit();

                usleep(80000);
        
                echo json_encode([
                    'status'	=> 'complete',
                    'message'	=> 'Database Successfully Updated.'
                ]);
            }
        
    }

    public function truncatedb(){
        // Check::truncate();
        // CheckHistory::truncate();
        // CheckReceived::truncate();
        // CheckTagging::truncate();
        // CheckTaggingItem::truncate();
        // Customer::truncate();
    }

    public function checkfiles(){

        $ch = collect(); 
        $folder = "\\\\172.16.161.41\\Institutional\\CCM_Textfile\\New\\ICM\\";
        $newfolder = '\\\\172.16.161.41\\Institutional\\CCM_Textfile\\Uploaded\\ICM\\';
        if(file_exists($folder)) 
        {
            $files = scandir($folder);
            foreach($files as $file){
                $arr_f =[];
                if (strpos($file, '.txt') !== false) {
                    $r_f = fopen($folder.$file.'','r');
                    while(!feof($r_f)) 
                    {
                        //usleep(80000);
                        $arr_f[] = fgets($r_f);
                    }
                    fclose($r_f);
                    $customer = "";
                    $checkno = "";
                    $class ="";
                    $category = "";
                    $expire = "";
                    $checkdate = "";
                    $checkreceived = "";
                    $checktype = "";
                    $accountname = "";
                    $accountnumber = "";
                    $bank ="";
                    $checkamount = "";               
                    for ($i=0; $i < count($arr_f); $i++) 
                    {          
                        echo $arr_f[$i].'<br/>';              
                        if($i==0)
                        {
                            $arr = explode(",",$arr_f[$i]);
                            $customer = $arr[1];
                        }	
                        if($i==1)
                        {
                            $arr = explode(",",$arr_f[$i]);
                            $checkno = $arr[1];
                        }	
                    }

                    $ch->push([
                        'customer'      => $customer,
                        'checkno'       => $checkno,
                        'class'         => $class,
                        'category'      => $category,
                        'expire'        => $expire,
                        'checkreceived' => $checkreceived,
                        'checktype'     => $checktype,
                        'accountname'   => $accountname,
                        'accountnumber' => $accountnumber,
                        'bank'          => $bank,
                        'checkamount'   => $checkamount
                    ]
                    );                    
                }                
            }

            foreach($ch as $c){
                echo $c['customer'].'<br />';
                echo $c['checkno'].'<br />';
                echo 'ypw';
                $move = File::move($folder+$c['checkno'].'.txt', $newfolder+$c['checkno'].'.txt');
            }


        }
        else 
        {
            echo 'nah';
        }
    }

    public function checkexist($id,$status){
        Check::where('checks_id', $id)
        ->update(['is_exist' => $status]);
    }

    public function checkReports(){
        $title = 'Check Reports';
        return view('check.checkreports',compact('title')); 
    }

    public function getCheck(Request $request){
        $bunit = Auth::user()->businessunit_id;
        $date = $request->date;

        $checks = CheckTaggingItem::whereHas('checktagging', function ($q) use($bunit,$date){
            $q->where('businessunit_id', $bunit)
            ->whereDate('created_at',$date);
        })
        ->where('checktagging_type','CLEARING')
        ->groupBy('checktagginghdr_id')
        ->orderBy('checktaggingitems_id', 'DESC')
        ->get();

        if(count($checks)>0){
            return response()->json(['st'=>true]);
        }
        return response()->json(['st'=>false]);
    }

    public function getCheckReport($date){
        $title = 'Check Report';
        $bunit = Auth::user()->businessunit_id;

        $checks = CheckTaggingItem::whereHas('checktagging', function ($q) use($bunit,$date){
            $q->where('businessunit_id', $bunit)
            ->whereDate('created_at',$date);
        })
        ->where('checktagging_type','CLEARING')
        ->orderBy('checktaggingitems_id', 'DESC')
        ->get();

        return view('check.getcheckreport',compact('checks','date','title'));
    }

}
