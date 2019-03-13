<div class="form-container"> 
	<form method="POST" action="" method="POST" id="_saveUser">
		<input type="hidden" name="salesman_id" value="<?php echo $sman->salesman_id; ?>" id="salesman_id">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Salesman Code</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="sman_code" name="sman_code" value="{{ $sman->sman_code }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ $sman->fullname }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Created By</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="createdby" name="createdby" value="{{ strtoupper($sman->user->name) }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Date Created <small class="namesm"></small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="datecreated" name="datecreated" value="{{ date('F j, Y @ g:i:s A', strtotime($sman->created_at)) }}" autocomplete="off" >
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>