<div class="form-container"> 
	<form method="POST" action="{{ route('addnewcustomer') }}" method="POST" id="_saveCustomer">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Code</label>
					<input type="text" class="form form-control inp-b" id="cus_code" readonly="readonly" name="cus_code" autocomplete="off" value="{{ $cuscode }}">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small>(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control inp-b up" id="fullname" name="fullname" autocomplete="off" autofocus>
				</div>
				<div class="response-dialog">										
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#fullname').focus();
</script>