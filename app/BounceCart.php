<?php

namespace App;

class BounceCart 
{
    public $checks = null;    
    public $totalAmt = 0;

    public function __construct($oldCart)
    {
        if($oldCart)
        {
            $this->checks = $oldCart->checks;
            //$this->checkCol = collect($this->checks);
        }
    }

    public function add($check)
    {
        $this->checks[] = $check;          
    }

    public function is_bouncedCheckID_exist($id)
    {
        $isFound = false;
        foreach($this->checks as $item)
        {
            if(trim($id) == trim($item['checkid']))
            {
                $isFound = true;
                break;
            }
        }
        return $isFound;
    }

    public function is_id_exist($id)
    {
        $isFound = false;
        foreach($this->checks as $item)
        {
            if(trim($id) == trim($item['checkid']))
            {
                $isFound = true;
                break;
            }
        }
        return $isFound;
    }

    public function update($check)
    {
        foreach($this->checks as $key => $value)
        {
            if(trim($value['checkid'])==trim($check['checkid']))
            {
                //$data[$key]['transaction_date']
                //echo $key;
                $this->checks[$key]['updatetype']   = $check['updatetype'];
                $this->checks[$key]['checkamount']  = $check['checkamount']; 
                $this->checks[$key]['check_dsnum']  = $check['check_dsnum']; 
            }

        }
    }

    public function update2($check)
    {
        foreach($this->checks as $key => $value)
        {
            if(trim($value['checkid'])==trim($check['checkid']))
            {
                //$data[$key]['transaction_date']
                //echo $key;
                $this->checks[$key]['updatetype']     = $check['updatetype'];
                $this->checks[$key]['checkno']        = $check['checkno'];
                $this->checks[$key]['accountname']    = $check['accountname'];
                $this->checks[$key]['accountnumber']  = $check['accountnumber'];
                $this->checks[$key]['bankid']         = $check['bankid'];
                $this->checks[$key]['checkdate']      = $check['checkdate'];
                $this->checks[$key]['checktype']      = $check['checktype'];
                $this->checks[$key]['checkamount']    = $check['checkamount']; 
                $this->checks[$key]['replacementype'] = $check['replacetype'];
                $this->checks[$key]['cash']           = $check['repcash'];
                $this->checks[$key]['check_dsnum']  = $check['check_dsnum']; 
            }

        }
    }

    public function checkcount()
    {
        return count($this->checks);
    }

}
