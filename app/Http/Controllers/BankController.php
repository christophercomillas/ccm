<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Bank;
use DB;

class BankController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addNewBankDialog()
    {
    	return view('bank.addnewbank');
    }

    public function addNewBank(Request $request)
    {
        $niceNames = array(
            'bankcode' => 'Bank Code',
            'bankbranchname'	=>	'Branch Name'
        );

        $validator = Validator::make($request->all(), [
            'bankcode'  		=>  'required|unique:banks',
            'bankbranchname'    =>  'required|unique:banks'
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {

            $bank = new Bank;

            $bank->bankcode   		= $request->bankcode;
            $bank->bankbranchname	= strtoupper($request->bankbranchname);
            $bank->id          		= Auth::user()->id;         

            $bank->save();

            $id = $bank->bank_id;

            $codebranchname = $request->bankcode.' - '.strtoupper($request->bankbranchname);

            return response()->json(['status'=>true,'bankid'=>$id,'codebranchname'=>$codebranchname,'bankcode'=>$request->bankcode,'bankname'=>$request->bankbranchname]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function searchBankBranch(Request $request)
    {
        $bank = Bank::where('bankbranchname', 'like', '%' . $request->bankbranch . '%')
            ->orWhere('bankcode', 'like', '%' . $request->bankbranch . '%')
            ->limit(5)
            ->orderBy('bank_id', 'desc')
            ->get();
        
        if($bank!=null)
        {
            $html = "<ul>";
            foreach ($bank as $b) 
            {
                $html.= "<li class='bankbranchlist' data-id='".$b->bank_id."' data-code='".$b->bankcode."' data-name='".ucwords($b->bankbranchname)."'>".$b->bankcode." - ".$b->bankbranchname."</li>";
            }
            $html.="</ul>";
            return response()->json(['output'=> $html]);
        }
        else 
        {
            return response()->json(['output'=> '<div class="err">No Result Found.</div>']);
        }
    }

    public function show()
    {
        $title = 'Banks';
        return view('bank.banks',compact('title'));
    }

    public function getAllBanks(Request $request)
    {
        $columns = array( 
            0   =>  'bankcode', 
            1   =>  'branchname',
            2   =>  'createdby',
            3   =>  'datecreated',
            4   =>  ''
        );

        $totalData = Bank::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = "bank_id";
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $banks = DB::table('banks')
            ->join('users', 'users.id', '=', 'banks.id')
            ->select(
                    'banks.*', 
                    'users.name'
                )
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 

            $banks = DB::table('banks')
            ->join('users', 'users.id', '=', 'banks.id')
            ->select(
                    'banks.*', 
                    'users.name'
                )
            ->where('banks.bankcode','LIKE',"%{$search}%")
            ->orWhere('banks.bankbranchname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            $totalFiltered = DB::table('banks')
            ->join('users', 'users.id', '=', 'banks.id')
            ->select(
                    'banks.*', 
                    'users.name'
                )
            ->where('banks.bankcode','LIKE',"%{$search}%")
            ->orWhere('banks.bankbranchname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->count();
        }

        $data = array();
        if(!empty($banks))
        {
            foreach ($banks as $bank)
            {
                $nestedData['bankcode'] = $bank->bankcode;
                $nestedData['branchname'] = $bank->bankbranchname;
                $nestedData['createdby'] = strtoupper($bank->name);
                $nestedData['datecreated'] = date('F j, Y',strtotime($bank->created_at));
                $nestedData['action'] = "<div class='action-user' data-id='{$bank->bank_id}'>
                &emsp;<a href='#' title='SHOW' ><span class='glyphicon glyphicon-list' id='viewbank'></span></a>
                    &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit' id='editbank'></span></a></div>";
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

    public function viewbankdialog($id)
    {

        $bank = Bank::find($id);        

        return view('bank.viewbank',compact('bank'));
    }

    public function editbankdialog($id)
    {

        $bank = Bank::find($id);        

        return view('bank.editbank',compact('bank'));
    }

    public function updatebank(Request $request)
    {
        $niceNames = array(
            'bankcode' => 'Bank Code'
        );

        $validator = Validator::make($request->all(), [
            //'bankcode'  =>  'required|unique:banks,bankcode,'.$request->bank_id
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $bank = Bank::find($request->bank_id);

            $bank->bankcode        = $request->bankcode;
            $bank->bankbranchname  = strtoupper($request->bankbranchname);           
            $bank->save();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }
}
