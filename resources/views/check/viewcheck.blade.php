<div class="form-container"> 
    <form method="POST" action="" method="POST" id="_saveUser">
        <input type="hidden" name="checks_id" value="<?php echo $check->checks_id; ?>" id="checks_id">
        <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="label-dialog">Customer</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->cus_code.' - '.$check->fullname }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check No.</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_no }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Class</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_class }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Date</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->cdate }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Received</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->rdate }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Recieved (Received as)</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_type }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Category</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_category }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Date Expire</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->check_expiry }}">
            </div>
            @foreach($history as $h)
                {{    date("F j, Y, g:i a",strtotime($h->checktagging->created_at)).' '.$h->checktaggingitems_tag }}  <br />
                <!-- {{  date('M-Y', strtotime($h->checktagging->created_at))  }} -->
            @endforeach
            
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="label-dialog">Check From</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->department }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Approving Officer</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $check->approving_officer }}">
            </div>
            <div class="form-group">
                <label class="label-dialog">Check Status</label>
                <input type="text" class="form form-control input-sm inp-b up" readonly="readonly" value="{{ $status }}">
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
                <input type="text" class="form form-control input-sm inp-b up" id="checkamt" readonly="readonly" value="{{ number_format($check->check_amount,2) }}">
            </div>
            <div class="form-group">
                    <textarea rows="3" cols="30" class="form form-control" readonly="readonly" id="checkamtwords"></textarea>
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