<div class="form-container"> 
	<form method="POST" action="{{ route('updatecustomer') }}" method="POST" id="_saveUser">
		<input type="hidden" name="customer_id" value="<?php echo $customer->customer_id; ?>" id="customer_id">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Customer Code</label>
					<input type="text" class="form form-control input-sm inp-b" id="cus_code" name="cus_code" value="{{ $customer->cus_code }}" autocomplete="off" autofocus>
				</div>
			</div>
			<div class="col-md-6">
			<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ $customer->fullname }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#cus_code').select();
</script>