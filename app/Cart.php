<?php

namespace App;

class Cart 
{
    //
    public $checks = null;    
    public $totalAmt = 0;

    public function __construct($oldCart)
    {
        if($oldCart)
        {
            $this->checks = $oldCart->checks;
            $this->totalAmt = $oldCart->totalAmt;
            //$this->checkCol = collect($this->checks);
        }
    }

    public function add($check)
    {
        $checkAmt = str_replace(",","",$check['checkamt']);
        $this->totalAmt += floatval($checkAmt);   
        $this->checks[] = $check;  
        
    }

    public function update($check)
    {
        // $col = collect($this->checks);

        // dd($col);
        //dd($check);
        // /echo $check['accountname'];
        $this->checks[$check['key']]['customerid']      = $check['customerid'];
        $this->checks[$check['key']]['customercode']    = $check['customercode'];
        $this->checks[$check['key']]['customerdetails'] = $check['customerdetails'];
        $this->checks[$check['key']]['checkno']         = $check['checkno'];
        $this->checks[$check['key']]['cclass']          = $check['cclass'];
        $this->checks[$check['key']]['checkdate']       = $check['checkdate'];
        $this->checks[$check['key']]['checktype']       = $check['checktype'];
        $this->checks[$check['key']]['accountno']       = $check['accountno'];
        $this->checks[$check['key']]['accountname']     = $check['accountname'];
        $this->checks[$check['key']]['bankid']          = $check['bankid'];
        $this->checks[$check['key']]['bankcode']        = $check['bankcode'];
        $this->checks[$check['key']]['bankname']        = $check['bankname'];
        $this->checks[$check['key']]['bankdetails']     = $check['bankdetails'];
        $this->checks[$check['key']]['checkamt']        = $check['checkamt'];
        $this->checks[$check['key']]['ccategory']       = $check['ccategory'];
        $this->checks[$check['key']]['currency']        = $check['currency'];
        $this->checks[$check['key']]['checkexpdate']    = $check['checkexpdate'];
        // //$this->checks[] = $check;
        // foreach($this->checks as $key => $value)
        // {
        //     if($key==intval($check['key']))
        //     {
        //         echo $key;
        //         //$data[$key]['transaction_date'] = date('d/m/Y',$value['transaction_date']);
        //         $this->checks[$key]['customerid'] = '3';
        //         break;
        //     }
        // }
        $checkamthid = str_replace(",","",$check['checkamthid']);
        $checkAmt = str_replace(",","",$check['checkamt']);
        $this->totalAmt -= floatval($checkamthid);
        $this->totalAmt += floatval($checkAmt);   
        return true;
    }

    public function is_check_exist_except_key($check)
    {
        // dd($check);
        // echo $check['is_delete'];
        $isFound = false;
        $keyval = intval($check['key']);
        foreach($this->checks as $key => $item)
        {
            // echo $item['is_delete'];
            // var_dump($item);
            // return false;
            if($key!=intval($keyval))
            {
                //if((trim($check['checkno']) == trim($item['checkno'])) && $item['is_delete'])
                if(trim($check['checkno']) == trim($item['checkno']) && !$item['is_removed'])
                {
                    $isFound = true;
                    break;
                }
            }
        }
        return $isFound;
    }

    public function is_check_exist($check)
    {
        $isFound = false;
        foreach($this->checks as $item)
        {
            if(trim($check['checkno']) == trim($item['checkno']))
            {
                $isFound = true;
                break;
            }
        }
        return $isFound;
        // $isFound = false;
        // $cart = Session::get('cart');
        // foreach($cart as $index => $items) 
        // {               
        //     if($index === 'checks')
        //     {                                    
        //         foreach($items as $i)
        //         {
        //             if(trim($request->checkno) == trim($i['checkno']))
        //             {
        //                 $isFound = true;
        //                 break;
        //             }
        //         }

        //         break;
        //     }
            // if($data['checkno'] === $class) 
            // {
                // unset($classes[$index]);
                // $newClass = array_values($classes);
                // Session::put('class', $newClass);
                // return Response::json(array(
                //         'success' => true,
                //         'code' => 1,
                //         'class' => $classes,
                //         'message' => $data['class'] . ' removed from cart'
                //     )
                // );
            // }
        // }

        // return $isFound;
    }

    public function remove($key)
    {
        $this->checks[$key]['is_removed']  = true;
        $checkAmt = $this->checks[$key]['checkamt'];
        $checkAmt = str_replace(",","",$checkAmt);
        $this->totalAmt -= floatval($checkAmt);
        return true;
    }
}
