<div class="form-container"> 
	<form method="POST" action="{{ route('changepassword') }}" method="POST" id="_saveUser">
		<input type="hidden" name="userid" value="<?php echo $user->id; ?>" id="userid">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Username</label>
					<input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="username" name="username" value="{{ $user->username }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>ID Number</label>
					<input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="idnumber" name="idnumber" value="{{ $user->empid }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="fullname" name="fullname" value="{{ $user->name }}" autocomplete="off" >
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>New Password</label>
					<div class="input-group">
                        <input type="text" class="form form-control inpmedx input-sm inp-b" id="password" name="password" autocomplete="off" autofocus>
                        <span class="input-group-btn">
                        	<button class="btn btn-default input-sm btn-find upass" type="button" id="generatePassword">
                            	<i class="fa fa-cogs" aria-hidden="true"></i>
                        	</button>
                        </span>
                    </div>
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#password').select();
</script>