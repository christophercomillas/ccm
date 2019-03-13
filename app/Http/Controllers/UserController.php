<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\BusinessUnit;
use App\Company;
use App\Department;
use App\UserType;

class UserController extends Controller
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
        $title = 'Manage Users';

        //$users = User::all();
        
        return view('user.manageusers',compact('title'));
    }

    public function addnewuserdialog()
    {
    	$bunits = BusinessUnit::all();

    	$comp = Company::all();

    	$dept = Department::all();

    	$utype = UserType::all();

    	return view('user.addnewuser',compact('bunits','comp','dept','utype'));
    }

    public function addnewuser(Request $request)
    {

		$niceNames = array(
		    'idnumber' => 'ID Number'
		);

    	$validator = Validator::make($request->all(), [
            'username'  =>  'required|unique:users,username',
            'fullname'  =>  "required|unique:users,name",
            'idnumber'	=>  "required|unique:users,empid"
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {

            $user = new User;

            $user->username         = strtolower($request->username);
            $user->empid            = $request->idnumber;
            $user->name             = strtolower($request->fullname);
            $user->company_id       = $request->company;
            $user->businessunit_id  = $request->bunit;
            $user->department_id    = $request->department;
            $user->usertype_id      = $request->usertype;
            $user->user_status      = 'active';
            $user->user_ipaddress   = strtolower($request->user_ipaddress);
            $user->password         = bcrypt($request->password);            
            $user->save();

			return response()->json(['status'=>true]);
        }
    	return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function allUsers(Request $request)
    {
            
        $columns = array( 
            0   =>  'empid', 
            1   =>  'name',
            2   =>  'username',
            3   =>  'company',
            4   =>  'department',
            5   =>  'businessunit',
            6   =>  'usertype',
            7   =>  '',
            8   =>  'created_at',
            9   =>  ''
        );


        $totalData = User::count();
            
        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = "created_at";
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $users = User::offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();
        }
        else 
        {
            $search = $request->input('search.value'); 

            $users =  User::where('empid','LIKE',"%{$search}%")
                ->orWhere('name', 'LIKE',"%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order,$dir)
                ->get();

            $totalFiltered = User::where('empid','LIKE',"%{$search}%")
                ->orWhere('name', 'LIKE',"%{$search}%")
                ->count();
        }

        $data = array();
        if(!empty($users))
        {
            foreach ($users as $user)
            {
                $nestedData['id'] = $user->empid;
                $nestedData['fullname'] = strtoupper($user->name);
                $nestedData['username'] = $user->username;
                $nestedData['company'] = $user->company->company;
                $nestedData['department'] = $user->department->department;
                $nestedData['businessunit'] = $user->businessunit->bname;
                $nestedData['usertype'] = $user->usertype->usertype_name;
                $nestedData['status'] = strtoupper($user->user_status);
                $nestedData['date'] = date('F j, Y',strtotime($user->created_at));
                $nestedData['action'] = "<div class='action-user' data-id='{$user->id}'>
                &emsp;<a href='#' title='SHOW'><span class='glyphicon glyphicon-list' id='viewuser'></span></a>
                    &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit' id='edituser'></span></a>
                    &emsp;<a href='#' title='CHANGE PASSWORD' ><span class='glyphicon glyphicon-lock' id='changepassword'></span></a></div>";
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

    public function viewuserdialog($id)
    {
        $bunits = BusinessUnit::all();

        $comp = Company::all();

        $dept = Department::all();

        $utype = UserType::all();

        $user = User::find($id);        

        return view('user.viewuser',compact('bunits','comp','dept','utype','user'));
    }

    public function edituserdialog($id)
    {
        $bunits = BusinessUnit::all();

        $comp = Company::all();

        $dept = Department::all();

        $utype = UserType::all();

        $user = User::find($id);        

        return view('user.edituser',compact('bunits','comp','dept','utype','user'));
    }

    public function changepassdialog($id)
    {
        $user = User::find($id);   
        return view('user.changepassword',compact('user'));
    }

    public function updateuser(Request $request)
    {
        $niceNames = array(
            'idnumber' => 'ID Number'
        );

        $validator = Validator::make($request->all(), [
            'username'  =>  'required|unique:users,username,'.$request->userid,
            'fullname'  =>  'required|unique:users,name,'.$request->userid,
            'idnumber'  =>  'required|unique:users,empid,'.$request->userid
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $user = User::find($request->userid);

            $user->username         = strtolower($request->username);
            $user->empid            = $request->idnumber;
            $user->name             = strtolower($request->fullname);
            $user->company_id       = $request->company;
            $user->businessunit_id  = $request->bunit;
            $user->department_id    = $request->department;
            $user->usertype_id      = $request->usertype;
            $user->user_status      = $request->user_status;
            $user->user_ipaddress   = strtolower($request->user_ipaddress);           
            $user->save();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function changepassword(Request $request)
    {
        $niceNames = array(
            'idnumber' => 'ID Number'
        );

        $validator = Validator::make($request->all(), [
            'username'  =>  'required|unique:users,username,'.$request->userid,
            'fullname'  =>  'required|unique:users,name,'.$request->userid,
            'idnumber'  =>  'required|unique:users,empid,'.$request->userid
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $user = User::find($request->userid);

            $user->username         = $user->username;
            $user->empid            = $user->empid;
            $user->name             = $user->name;
            $user->company_id       = $user->company_id;
            $user->businessunit_id  = $user->businessunit_id;
            $user->department_id    = $user->department_id;
            $user->usertype_id      = $user->usertype_id;
            $user->user_ipaddress   = $user->user_ipaddress;
            $user->password         = bcrypt($request->password);           
            $user->save();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function show($id)
    {

    }

    public function edit($id)
    {

    }
}
