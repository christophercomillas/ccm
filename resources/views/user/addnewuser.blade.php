<div class="form-container"> 
	<form method="POST" action="{{ route('addnewuser') }}" method="POST" id="_saveUser">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Username</label>
					<input type="text" class="form form-control input-sm inp-b" id="username" name="username" autocomplete="off" autofocus>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>ID Number</label>
					<input type="text" class="form form-control input-sm inp-b" id="idnumber" name="idnumber" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="fullname" name="fullname" autocomplete="off" >
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Company Name</label>
					<select class="form form-control input-sm inp-b" id="company" name="company">
						<option value="">- Select -</option>
						@foreach($comp as $c)
							<option value="{{ $c->company_id }}">{{ $c->company }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Business Unit</label>
					<select class="form form-control input-sm inp-b" id="bunit" name="bunit">
						<option value="">- Select -</option>
						@foreach($bunits as $b)
							<option value="{{ $b->businessunit_id }}">{{ $b->bname }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Department</label>
					<select class="form form-control input-sm inp-b" id="department" name="department">
						<option value="">- Select -</option>
						@foreach($dept as $d)
							<option value="{{ $d->department_id }}">{{ $d->department }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>User Type</label>
					<select class="form form-control input-sm inp-b" id="usertype" name="usertype">
						<option value="">- Select -</option>
						@foreach($utype as $t)
							<option value="{{ $t->usertype_id }}">{{ $t->usertype_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Password</label>
					<div class="input-group">
                        <input type="text" class="form form-control inpmedx input-sm inp-b" id="password" name="password" autocomplete="off">
                        <span class="input-group-btn">
                        	<button class="btn btn-default input-sm btn-find upass" type="button" id="generatePassword">
                            	<i class="fa fa-cogs" aria-hidden="true"></i>
                        	</button>
                        </span>
                    </div>
				</div>

				<div class="form-group">
					<label class="label-dialog">IP Address <small class="namesm">(Optional)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="ipaddress" name="ipaddress" autocomplete="off">
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>
<script type="text/javascript">
	$('input#username').select();
</script>