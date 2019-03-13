<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;
use Auth;
use App\Salesman;
use App\AppSettings;

use App\User;
//use Collections;

class SalesmanController extends Controller
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
        $sm = Salesman::all();
        foreach ($sm as $s) 
        {
            echo $s->fullname.' -- '.$s->users->username.'<br />';
        }
    }

    public function addNewSalesman(Request $request)
    {
        $smancode = $this->generateSalesmanCode();

        $niceNames = array(
            'sman_code' => 'Salesman Code'
        );

        $validator = Validator::make($request->all(), [
            'fullname'  =>  'required|unique:salesman',
            'sman_code'     =>  'required|unique:salesman'
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {

            $salesman = new Salesman;

            $salesman->sman_code    = $smancode;
            $salesman->fullname     = strtoupper($request->fullname);
            $salesman->id           = Auth::user()->id;         

            $salesman->save();

            $id = $salesman->salesman_id;

            $codename = $smancode.' - '.strtoupper($request->fullname);

            return response()->json(['status'=>true,'sid','salesmanid'=>$id,'smanfullname'=>$codename]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

    public function addNewSalesmanDialog()
    {
        $smancode = 0;
        //$sman = Salesman::all();

        $smancode = $this->generateSalesmanCode();

    	return view('salesman.addnewsalesman',compact('smancode'));
    }

    public function generateSalesmanCode()
    {
        $code = 0;
        $sman = Salesman::limit(1)
            ->orderBy('sman_code', 'desc')
            ->get();

        if(count($sman)>0)
        {
            $code = $sman[0]['sman_code'];
            return ++$code;
        }
        else 
        {
            $data = AppSettings::where('app_key','app_salesmancode_start')
               ->get();

            return $data[0]['app_value'];
        }
    }

    public function searchSalesman(Request $request)
    {
        $salesman = Salesman::where('fullname', 'like', '%' . $request->search . '%')
            ->orWhere('sman_code', 'like', '%' . $request->search . '%')
            ->limit(5)
            ->orderBy('sman_code', 'desc')
            ->get();
        
        if($salesman!=null)
        {
            $html = "<ul>";
            foreach ($salesman as $s) 
            {
                $html.= "<li class='salesmanlist' data-id='".$s->salesman_id."' data-code='".$s->sman_code."' data-name='".ucwords($s->fullname)."'>".$s->sman_code." - ".$s->fullname."</li>";
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
        $title = 'Salesman';
        return view('salesman.salesman',compact('title'));
    }

    public function getAllSalesman(Request $request)
    {
        $columns = array( 
            0   =>  'smancode', 
            1   =>  'smanname',
            2   =>  'createdby',
            3   =>  'datecreated',
            4   =>  ''
        );

        $totalData = Salesman::count();

        $totalFiltered = $totalData; 

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = "salesman_id";
        $dir = $request->input('order.0.dir');
            
        if(empty($request->input('search.value')))
        {            
            $salesman = Salesman::offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

            // dd($customers);

            // exit();
        }
        else 
        {
            $search = $request->input('search.value'); 

            $salesman = Salesman::join('users', 'users.id', '=', 'salesman.id')
            ->select(
                'salesman.*', 
                'users.name'
                )
            ->where('salesman.sman_code','LIKE',"%{$search}%")
            ->orWhere('salesman.fullname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();


            $totalFiltered = Salesman::join('users', 'users.id', '=', 'salesman.id')
            ->select(
                'salesman.*', 
                'users.name'
                )
            ->where('salesman.sman_code','LIKE',"%{$search}%")
            ->orWhere('salesman.fullname', 'LIKE',"%{$search}%")
            ->orWhere('users.name', 'LIKE',"%{$search}%")
            ->count();
        }

        $data = array();
        if(!empty($salesman))
        {
            foreach ($salesman as $sman)
            {
                $nestedData['smancode'] = $sman->sman_code;
                $nestedData['smanname'] = $sman->fullname;
                $nestedData['createdby'] = strtoupper($sman->user->name);
                $nestedData['datecreated'] = date('F j, Y', strtotime($sman->created_at));
                $nestedData['action'] = "<div class='action-user' data-id='{$sman->salesman_id}'>&emsp;<a href='#' title='SHOW' ><span class='glyphicon glyphicon-list' id='viewsalesman'></span></a>
                    &emsp;<a href='#' title='EDIT' ><span class='glyphicon glyphicon-edit' id='editsalesman'></span></a></div>";
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

    public function test()
    {

        $items = new Collection();        

        $items->push(array(
            'name'      =>  'Plan B P2',
            'category'  =>  'Decks',
            'price'     =>  '55.50'
            )
        );

        $items->push(array(
            'name'      =>  'Element Pro',
            'category'  =>  'Decks',
            'price'     =>  '51.25'
            )
        );
        
        $items->push(array(
            'name'      =>  'Spitpire 701',
            'category'  =>  'Wheels',
            'price'     =>  '34.01'
            )
        );        
        //$items->push('humans',array(['person'=>'sampl2','age'=>'30']));
        // foreach ($items['humans'] as $key => $human) {
        //     // $products->push('pro','products1');
        //    //echo $human['person'];
        //    //echo $human;
        // }
        //dd($items);

        echo $items->sum('price');

    }

    public function viewsalesmandialog($id)
    {

        $sman = Salesman::find($id);        

        return view('salesman.viewsalesman',compact('sman'));
    }

    public function editsalesmandialog($id)
    {

        $sman = Salesman::find($id);        

        return view('salesman.editsalesman',compact('sman'));
    }

    public function updatesalesman(Request $request)
    {
        $niceNames = array(
            'sman_code' => 'Salesman Code'
        );

        $validator = Validator::make($request->all(), [
            //'sman_code'  =>  'required|unique:salesman,sman_code,'.$request->salesman_id
        ]);

        $validator->setAttributeNames($niceNames); 

        if ($validator->passes()) 
        {
            $sman = Salesman::find($request->salesman_id);

            $sman->sman_code        = $request->sman_code;
            $sman->fullname         = strtoupper($request->fullname);           
            $sman->save();

            return response()->json(['status'=>true]);
        }
        return response()->json(['status'=>false,'error'=>$validator->errors()->all()]);
    }

}
