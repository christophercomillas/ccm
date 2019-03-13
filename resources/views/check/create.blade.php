<div class="form-container">  
<form method="POST" action="{{route('addtocart') }}" id="_addnewcheck">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog">Customer Search</label>
					<div class="input-group">
	                    <input type="text" id="ssearchcustomer" class="form-control input-sm inp-b up" autocomplete="off" required="required">
	                    <span class="input-group-btn">
	                        <button class="btn btn-info input-sm btn-find" type="button" id="_addnewCustomer">
	                            <i class="fa fa-user-plus" aria-hidden="true"></i>
	                        </button>
	                    </span>
	                </div>
					<span class="custlist">
					</span>	
				</div>
				<div class="form-group">
                    <input type="hidden" name="customerid" id="cid" value="">
                    <input type="hidden" name="customercode" id="custcode" value="">
					<label class="label-dialog"><span class="requiredf">*</span>Customer Details</label>
					<input type="text" class="form form-control input-sm inp-b up" readonly="readonly" id="customerdetails" name="customerdetails" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Check No.</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge up" id="checkno" name="checkno" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Check Class</label>
					<select class="form-control input-sm inp-b" id="cclass" name="cclass">
                        <option value="">-</option>
                        @foreach($checkclass as $cl)
                            <option value="{{ $cl }}">{{ $cl }}</option>
                        @endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Check Category</label>
					<select class="form-control input-sm inp-b" id="ccategory" name="ccategory">
                        <option value="">-</option>
                        @foreach($category as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog">Expiration Date</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge up" id="checkexpdate" name="checkexpdate" autocomplete="off" placeholder="mm/dd/yyyy">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Check Date</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge up" id="checkdate" name="checkdate" autocomplete="off" placeholder="mm/dd/yyyy">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Check Type</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge" readonly="readonly" id="checktype" name="checktype" autocomplete="off">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Account No.</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge" id="accountno" name="accountno" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Account Name</label>
					<input type="text" class="form form-control input-sm inp-b inp-flarge up" id="accountname" name="accountname" autocomplete="off">
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
                    <input type="hidden" name="bankid" id="bid" value="">
                    <input type="hidden" name="bankcode" id="bankcode" value="">
                    <input type="hidden" name="bankname" id="bankname" value="">
					<label class="label-dialog"><span class="requiredf">*</span>Bank Details</label>
					<input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="bankdetails" name="bankdetails" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Currency</label>
					<select class="form-control input-sm inp-b" id="currency" name="currency">
                        <option value="">-</option>
                        @foreach($currency as $cur)
                            <option value="{{ $cur->currency_id }}">{{ $cur->currency_name }}</option>
                        @endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Amount</label>
                    <input type="text" class="form form-control input-lg inp-b inp-xflarge" name="checkamt" id="checkamt" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'autoGroup': true, 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0','allowMinus':false" autocomplete="off" value="0.00" required>
                </div>
				<div class="form-group">
                    <textarea class="form form-control" readonly="readonly" id="checkamtwords"></textarea>
                </div>
                <div class="response-dialog">
                    
                </div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
    $('input#ssearchcustomer').focus();
    $('input#checkamt').inputmask();  
</script>