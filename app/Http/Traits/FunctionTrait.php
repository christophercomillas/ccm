<?php 

namespace App\Http\Traits;
use App\Customer;
use App\Bank;
use App\AppSettings;
use Auth;

trait FunctionTrait {

    //Customer Functions

    public function customerAll() {
        // Get all the customer from the customer Table.
        $customer = Customer::all();

        return $customer;
    }

    public function generateCustomerCode()
    {
        $code = 0;
        $cman = Customer::limit(1)
            ->orderBy('cus_code', 'desc')
            ->first();
        //.dd($cman);
        if($cman!=null)
        {
            $code = $cman->cus_code;
            return ++$code;
        }
        else 
        {
            echo 'deri';
            $data = AppSettings::where('app_key','app_customercode_start')
               ->get();
            return $data[0]['app_value'];
        }
    }

    public function isCustomerNameExist($customername)
    {
        $cus = Customer::where('fullname', $customername)->first();
        if($cus!=null)
        {
            return $cus->customer_id;
        }
        return false;
    }

    public function autoCreateCustomer($customername)
    {
        $cuscode = $this->generateCustomerCode();

        //echo $cuscode;
        $customer = new Customer;

        $customer->cus_code   = $cuscode;
        $customer->fullname   = strtoupper($customername);
        $customer->id         = Auth::user()->id;         

        if($customer->save())
        {
            return $customer->customer_id;
        }
        return false;       
    }

    // Bank Functions

    public function isBankExist($bankname)
    {
        $bank = Bank::where('bankbranchname',$bankname)->first();

        if($bank!=null)
        {
            return $bank->bank_id;
        }
        return false;
    }

    public function autoCreateNewBank($bankname)
    {
        $bank = new Bank;

        $bank->bankcode   		= "";
        $bank->bankbranchname	= strtoupper($bankname);
        $bank->id          		= Auth::user()->id;         

        if($bank->save())
        {
            return $bank->bank_id;
        }
        return false;  
    }
}