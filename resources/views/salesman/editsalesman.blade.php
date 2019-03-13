<div class="form-container"> 
	<form method="POST" action="{{ route('updatesalesman') }}" method="POST" id="_saveUser">
		<input type="hidden" name="salesman_id" value="<?php echo $sman->salesman_id; ?>" id="salesman_id">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Salesman Code</label>
					<input type="text" class="form form-control input-sm inp-b" id="sman_code" name="sman_code" value="{{ $sman->sman_code }}" autocomplete="off" autofocus>
				</div>
			</div>
			<div class="col-md-6">
			<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ $sman->fullname }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#sman_code').select();
</script>