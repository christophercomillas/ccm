<div class="form-container"> 
	<form method="POST" action="" method="POST" id="_saveUser">
		<input type="hidden" name="bank_id" value="<?php echo $bank->bank_id; ?>" id="bank_id">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Bank Code</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="bankcode" name="bankcode" value="{{ $bank->bankcode }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Bank Branch Name <small class="namesm"></small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="bankbranchname" name="bankbranchname" value="{{ $bank->bankbranchname }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Created By</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="createdby" name="createdby" value="{{ strtoupper($bank->user->name) }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Date Created <small class="namesm"></small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="datecreated" name="datecreated" value="{{ date('F j, Y @ g:i:s A', strtotime($bank->created_at)) }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>