<div class="form-container"> 

    <?php $access = Session::get('bouncecart'); 
    $x = count($access->checks);
    $checkamount = 0;
    $checkdate = null;
    $newformat = null;
    ?>

    @for($c = 0; $c < $x; $c++)
    @if($access->checks[$c]['checkid'] == $check->checks_id)
    <?php 
    $checkno = $access->checks[$c]['checkno'];
    $accountname = $access->checks[$c]['accountname'];
    $accountnumber = $access->checks[$c]['accountnumber'];
    $bankid = $access->checks[$c]['bankid'];
    $checkdate = strtotime($access->checks[$c]['checkdate']);
    $checktype = $access->checks[$c]['checktype'];
    $checkamount = $access->checks[$c]['checkamount'];
    ?>
    @endif
    @endfor

    <form method="POST" action="" method="POST" id="_saveUser">
        <input type="hidden" name="checks_id" value="<?php echo $check->checks_id; ?>" id="checks_id">
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label-dialog">Customer</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->cus_code.' - '.$check->fullname }}">
            </div>
            <div class="form-group">
                <label class="label-dialog"><u>Old Record</u></label>
            </div>
            <div class="form-group">
                <label class="label-dialog">Check No.</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_no }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Date</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->cdate }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Type (Received as)</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_type }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Account No.</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->account_no }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Account Name</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->account_name }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Bank Branch Name</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->bankbranchname }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Amount</label>
                <input type="text" class="form form-control input-sm inp-b up" id="checkamt2" readonly="readonly" value="{{ number_format($check->check_amount,2) }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label-dialog"></label>
            </div>
            <div class="form-group">
                <label class="label-dialog"></label>
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span><u>New Record</u></label>
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Check No.</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="<?php echo $checkno; ?>">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Check Date</label>
                <input type="text" class="form form-control input-sm inp-b up" name="repcheckdate2" id="checkdate2" readonly="readonly" value="<?php echo $newformat = date('F j, Y',$checkdate); ?>">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Check Type (Received as)</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="<?php echo $checktype; ?>">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Account No.</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="<?php echo $accountnumber; ?>">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Account Name</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="<?php echo $accountname; ?>">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>Bank Branch Name</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->bankbranchname }}">
            </div>
            <div class="form-group">
                <label class="label-dialog"><span class="requiredf">*</span>New Amount</label>
                <input type="text" class="form form-control input-sm inp-b up" id="checkamt" readonly="readonly" value="<?php echo $checkamount; ?>">
            </div>
            <div class="form-group">
                    <textarea rows="5" cols="30" class="form form-control" readonly="readonly" id="checkamtwords"></textarea>
            </div>
        </div>
    </div>
    </form>
</div>

<script type="text/javascript">
    $('input#ssearchcustomer').focus();
    $('input#checkamt').inputmask();   
    $('#checkamtwords').val($('#checkamt').AmountInWords());	      
</script>