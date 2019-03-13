<div class="form-container"> 
	<form method="POST" action="{{ route('addnewbank') }}" method="POST" id="_saveBank">
		<div class="row">
			<div class="col-md-12">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Bank Code</label>
					<input type="text" class="form form-control inp-b up" id="bankcode" name="bankcode" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Branch Name</label>
					<input type="text" class="form form-control inp-b up" id="bankbranchname" name="bankbranchname" autocomplete="off">

				</div>
				<div class="response-bankdialog">										
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#bankcode').focus();
</script>