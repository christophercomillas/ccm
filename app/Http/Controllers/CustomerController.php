<?php

namespace App\Http\Controllers;
use App\Http\Traits\FunctionTrait;

use Illuminate\Http\Request;
use Validator;
use Auth;
use App\Customer;
use App\AppSettings;

class CustomerController extends Controller
{
    //
    use FunctionTrait;
    public function index()
    {

    }

    public function addNewCustomerDialog()
    {
        $cuscode = 0;
        //$sman = Salesman::all();

        $cuscode = $this->generateCustomerCode();

    	return view('customer.addnewcustomer',compact('cuscode'));
    }

    public function addNewCustomer(Request $request)
    {
        $cuscode = $this->generateCustomerCode();

        $niceNames = array(
            'cus_code' => 'Customer Code'
        );

        $validator = Validator::make($request->all(), [
            'fullname'  =>  'required|unique:customers',
            'cus_code'     =>  'required|unique:customers'
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {

            $customer = new Customer;

            $customer->cus_code   = $cuscode;
            $customer->fullname    = strtoupper($request->fullname);
            $customer->id          = Auth::user()->id;         

            $customer->save();

            $id = $customer->customer_id;

            $codename = $cuscode.' - '.strtoupper($request->fullname);

            return response()->json(['status'=>true,'customerid'=>$id,'cusfullname'=>$codename]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function searchCustomer(Request $request)
    {
        $customer = Customer::where('fullname', 'like', '%' . $request->cusname . '%')
        ->orWhere('cus_code', 'like', '%' . $request->cusname . '%')
        ->limit(5)
        ->orderBy('cus_code', 'desc')
        ->get();
        if($customer!=null)
        {
            $html = "<ul>";
            foreach ($customer as $s) 
            {
                $html.= "<li class='customerlist' data-id='".$s->customer_id."' data-code='".$s->cus_code."' data-name='".ucwords($s->fullname)."'>".$s->cus_code." - ".$s->fullname."</li>";
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
        $title = 'Customers';
        return view('customer.customers',compact('title'));
    }

    public function getAllCustomers(Request $request)
    {
        $columns = array( 
            0   =>  'custcode', 
            1   =>  'customername',
            2   =>  'createdby',
            3   =>  'datecreated',
            4   =>  ''
        );

        $totalData = Customer::count();

        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = "customer_id";
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $customers = Customer::offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            // dd($customers);

            // exit();
        }
        else 
        {
            $search = $request->input('search.value'); 

            $customers = Customer::join('users', 'users.id', '=', 'customers.id')
            ->select(
                'customers.*', 
                'users.name'
                )
            ->where('customers.cus_code','LIKE',"%{$search}%")
            ->orWhere('customers.fullname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();


            $totalFiltered = Customer::join('users', 'users.id', '=', 'customers.id')
            ->select(
                'customers.*', 
                'users.name'
                )
            ->where('customers.cus_code','LIKE',"%{$search}%")
            ->orWhere('customers.fullname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->count();
        }

        $data = array();
        if(!empty($customers))
        {
            foreach ($customers as $customer)
            {
                $nestedData['custcode'] = $customer->cus_code;
                $nestedData['customername'] = $customer->fullname;
                $nestedData['createdby'] = strtoupper($customer->user->name);
                $nestedData['datecreated'] = date('F j, Y',strtotime($customer->created_at));
                $nestedData['action'] = "<div class='action-user' data-id='{$customer->customer_id}'>&emsp;<a href='#' title='SHOW' ><span class='glyphicon glyphicon-list' id='viewcustomer'></span></a>
                    &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit' id='editcustomer'></span></a></div>";
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

    public function viewcustomerdialog($id)
    {

        $customer = Customer::find($id);        

        return view('customer.viewcustomer',compact('customer'));
    }

    public function editcustomerdialog($id)
    {

        $customer = Customer::find($id);        

        return view('customer.editcustomer',compact('customer'));
    }

    public function updatecustomer(Request $request)
    {
        $niceNames = array(
            'cus_code' => 'Customer Code'
        );

        $validator = Validator::make($request->all(), [
            //'cus_code'  =>  'required|unique:customers,cus_code,'.$request->customer_id
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $customer = Customer::find($request->customer_id);

            $customer->cus_code        = $request->cus_code;
            $customer->fullname        = strtoupper($request->fullname);           
            $customer->save();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }



}
