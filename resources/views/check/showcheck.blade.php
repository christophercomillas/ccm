<div class="form-container">    
    <form method="POST" action="" id="_editcheck">
    <input type="hidden" name="key" value="{{ $keyval }}" /> 
        @foreach($ch as $check)
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="hidden" name="customerid" id="cid" value="{{ $check['customerid'] }}">
                        <input type="hidden" name="customercode" id="custcode" value="{{ $check['customercode'] }}">
                        <label class="label-dialog"><span class="requiredf"></span>Customer Details</label>
                        <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" id="customerdetails" name="customerdetails" autocomplete="off" value="{{ $check['customerdetails'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Check No.</label>
                    <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" id="checkno" name="checkno" autocomplete="off" value="{{ $check['checkno'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog">Check Class</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" autocomplete="off" value="{{ $check['cclass'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog">Check Category</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" autocomplete="off" value="{{ $check['ccategory'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Expiration Date</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" autocomplete="off" value="{{ $check['checkexpdate'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Check Date</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" id="checkdate" name="checkdate" autocomplete="off" placeholder="mm/dd/yyyy" value="{{ $check['checkdate'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Check Type</label>
                        <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" id="checktype" name="checktype" autocomplete="off" value="{{ $check['checktype'] }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Account No.</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" id="accountno" name="accountno" autocomplete="off" value="{{ $check['accountno'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Account Name</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" id="accountname" name="accountname" autocomplete="off" value="{{ $check['accountname'] }}">
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="bankid" id="bid" value="{{ $check['bankid'] }}">
                        <input type="hidden" name="bankcode" id="bankcode" value="{{ $check['bankcode'] }}">
                        <input type="hidden" name="bankname" id="bankname" value="{{ $check['bankname'] }}">
                        <label class="label-dialog"><span class="requiredf"></span>Bank Details</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" readonly="readonly" id="bankdetails" name="bankdetails" autocomplete="off" value="{{ $check['bankdetails'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Currency</label>
                        <input type="text" readonly="readonly" class="form form-control input-sm inp-b up" readonly="readonly" id="bankdetails" name="bankdetails" autocomplete="off" value="{{ $check['currency'] }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf"></span>Amount</label>
                        <input type="hidden" name="checkamthid" value="{{ $check['checkamt'] }}"> 
                        <input type="text" readonly="readonly" class="form form-control input-lg inp-b inp-xflarge" name="checkamt" id="checkamt" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" autocomplete="off" value="{{ $check['checkamt'] }}" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form form-control" readonly="readonly" id="checkamtwords"></textarea>
                    </div>
                    <div class="response-dialog">                        
                    </div>
                </div>
            </div>
        @endforeach
    </form>
    </div>
    <script type="text/javascript">
        $('input#ssearchcustomer').focus();
        $('input#checkamt').inputmask();   
        $('#checkamtwords').val($('#checkamt').AmountInWords());	      
    </script>