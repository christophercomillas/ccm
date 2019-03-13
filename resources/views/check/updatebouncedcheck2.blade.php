<div class="form-container">  
    <form method="POST" action="{{route('updatebouncedchecktemp2') }}" id="_updatebouncedchecktemp2">
        <input type="hidden" name="id" id="checkid" value="{{ $id }}" />
        <input type="hidden" name="ocheckamt" id="ocheckamt" value="{{ $check->check_amount }}" />
        <input type="hidden" name="oaccountname" id="oaccountname" value="{{ $check->account_name }}" />
        <input type="hidden" name="oaccountno" id="oaccountno" value="{{ $check->account_no }}" />
        <input type="hidden" name="ocheckno" id="ocheckno" value="{{ $check->check_no }}" />
        <input type="hidden" name="ocheckdate" id="ocheckdate" value="{{ $check->check_date }}" />
        <input type="hidden" name="obankid" id="obankid" value="{{ $check->bank_id }}" />
       
        <div class="row">
            <div class="col-sm-5">
                <div class="form-group">
                    <label class="label-dialog">Check #</label>
                    <input type="text" id="checknum" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_no }}">
                </div>     
                <div class="form-group"> 
                    <div class="pretty p-default p-round">
                        <input type="radio" name="state" class="upbox updatetype" value="redeposit">
                        <div class="state p-primary">
                            <label>Redeposit</label>
                        </div>
                    </div>
                </div> 
                <div class="form-group"> 
                    <div class="pretty p-default p-round">
                        <input type="radio" name="state" class="upbox updatetype" value="replacement">
                        <div class="state p-primary">
                            <label>Replacement</label>
                        </div>
                    </div>
                </div> 
                <div class="form-group"> 
                    <div class="pretty p-default p-round">
                        <input type="radio" name="state" class="upbox updatetype" value="cash">
                        <div class="state p-primary">
                            <label>Cash</label>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="col-sm-7">
                <div class="redepositdiv divhide">
                    <div class="form-group">
                        <label class="label-dialog">Check Amount</label>
                        <input type="text" name="redepcheckamount" id="redepcheckamount" class="form form-control input-sm inp-b up"  data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" autocomplete="off" value="{{ $check->check_amount }}" required>
                    </div>   
                </div>
                <div class="replacementdiv divhide">
                    <div class="form-group">
                        <label class="label-dialog">Check No.</label>
                        <input type="text" name="repcheckno" id="repcheckno" class="form form-control input-sm inp-b up" value="{{ $check->check_no }}" required>
                    </div> 
                    <div class="form-group">
                        <label class="label-dialog">Account Name</label>
                        <input type="text" name="repaccountname" id="repaccountname" class="form form-control input-sm inp-b up" value="{{ $check->account_name }}" required>
                    </div> 
                    <div class="form-group">
                        <label class="label-dialog">Account No.</label>
                        <input type="text" name="repaccountno" id="repaccountno" class="form form-control input-sm inp-b up" value="{{ $check->account_no }}" required>
                    </div> 
                    <div class="form-group">
                        <label class="label-dialog">Check Amount</label>
                        <input type="text" name="repcheckamt" id="repcheckamt" class="form form-control input-sm inp-b up" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" autocomplete="off" value="{{ $check->check_amount }}" required>
                    </div>   
                    <div class="form-group">
                        <label class="label-dialog">Bank Search</label>
                        <div class="input-group">
                            <input type="text" id="ssearchbank" class="form-control input-sm inp-b up" autocomplete="off" required="required">
                            <span class="input-group-btn">
                                <button class="btn btn-info input-sm btn-find" type="button" id="_addnewBank">
                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
                                </button>
                            </span>
                        </div>
                        <span class="banklist">
                        </span>	
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="bankid" id="bid" value="{{ $check->bank_id }}">
                        <input type="hidden" name="bankcode" id="bankcode" value="{{ $check->bankcode }}">
                        <input type="hidden" name="bankname" id="bankname" value="{{ $check->bankbranchname }}">
                        <label class="label-dialog"><span class="requiredf">*</span>Bank Details</label>
                        <input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="bankdetails" name="bankdetails" autocomplete="off" value="{{ $check->bankcode.'-'.$check->bankbranchname }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf">*</span>Check Date</label>
                        <input type="text" name="repcheckdate" id="checkdate" class="form form-control input-sm inp-b inp-flarge up" autocomplete="off" placeholder="mm/dd/yyyy" value="{{ $fdate }}">
                    </div>
                    <div class="form-group">
                        <label class="label-dialog"><span class="requiredf">*</span>Check Type</label>
                        <input type="text" name="repchecktype" id="checktype" class="form form-control input-sm inp-b inp-flarge" readonly="readonly"  autocomplete="off" value="{{ $check->check_type }}">
                    </div>
                </div>
                <div class="cashdiv divhide">
                    <div class="form-group">
                        <label class="label-dialog">Cash</label>
                        <input type="text" name="cashamt" id="cashamt" class="form form-control input-sm inp-b up" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" autocomplete="off" value="{{ $check->check_amount }}" required>
                    </div>  
                </div>
            </div>
        </div>
        <div class="response-dialog">

        </div>
    </form>
</div>
<script type="text/javascript">
    $('input#amt, input#redepcheckamount,input#repcheckamt,input#cashamt').inputmask();   
</script>