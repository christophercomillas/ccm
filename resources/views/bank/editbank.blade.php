<div class="form-container"> 
	<form method="POST" action="{{ route('updatebank') }}" method="POST" id="_saveUser">
		<input type="hidden" name="bank_id" value="<?php echo $bank->bank_id; ?>" id="bank_id">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Bank Code</label>
					<input type="text" class="form form-control input-sm inp-b" id="bankcode" name="bankcode" value="{{ $bank->bankcode }}" autocomplete="off" autofocus>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Bank Branch Name <small class="namesm"></small></label>
					<input type="text" class="form form-control input-sm inp-b" id="bankbranchname" name="bankbranchname" value="{{ $bank->bankbranchname }}" autocomplete="off" >
				</div>
			<div class="response-dialog">
			</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#bankcode').select();
</script>