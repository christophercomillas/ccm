<div class="form-container"> 
	<form method="POST" action="{{ route('updateuser') }}" method="POST" id="_saveUser">
		<input type="hidden" name="userid" value="<?php echo $user->id; ?>" id="userid">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Username</label>
					<input type="text" class="form form-control input-sm inp-b" id="username" name="username" value="{{ $user->username }}" autocomplete="off" autofocus>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>ID Number</label>
					<input type="text" class="form form-control input-sm inp-b" id="idnumber" name="idnumber" value="{{ $user->empid }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ $user->name }}" autocomplete="off" >
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Company Name</label>
					<select class="form form-control input-sm inp-b" id="company" name="company">
						<option value="{{ $user->company_id }}">{{ $user->company->company }}</option>
						@foreach($comp as $c)
							<option value="{{ $c->company_id }}">{{ $c->company }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Busines Unit</label>
					<select class="form form-control input-sm inp-b" id="bunit" name="bunit">
						<option value="{{ $user->businessunit_id }}">{{ $user->businessunit->bname }}</option>
						@foreach($bunits as $b)
							<option value="{{ $b->businessunit_id }}">{{ $b->bname }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>Department</label>
					<select class="form form-control input-sm inp-b" id="department" name="department">
						<option value="{{ $user->department_id }}">{{ $user->department->department }}</option>
						@foreach($dept as $d)
							<option value="{{ $d->department_id }}">{{ $d->department }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>User Type</label>
					<select class="form form-control input-sm inp-b" id="usertype" name="usertype">
						<option value="{{ $user->usertype_id }}">{{ $user->usertype->usertype_name }}</option>
						@foreach($utype as $t)
							<option value="{{ $t->usertype_id }}">{{ $t->usertype_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf">*</span>User Status</label>
					<select class="form form-control input-sm inp-b" id="user_status" name="user_status" >
						<option value="active">Active</option>
						<option value="inactive">Inactive</option>
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog">IP Address <small class="namesm">(Optional)</small></label>
					<input type="text" class="form form-control input-sm inp-b" id="ipaddress" value="{{ $user->user_ipaddress }}" name="ipaddress" autocomplete="off">
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