<div class="form-container"> 
	<form method="POST" action="" method="POST" id="_saveUser">
		<input type="hidden" name="userid" value="<?php echo $user->id; ?>" id="userid">
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Username</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="username" name="username" value="{{ $user->username }}" autocomplete="off" >
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>ID Number</label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="idnumber" name="idnumber" value="{{ $user->empid }}" autocomplete="off">
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Fullname <small class="namesm">(Firstname Middle Initial Lastname)</small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ $user->name }}" autocomplete="off" >
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Company Name</label>
					<select disabled class="form form-control input-sm inp-b" readonly="readonly" id="company" name="company">
						<option value="{{ $user->company_id }}">{{ $user->company->company }}</option>
						@foreach($comp as $c)
							<option value="{{ $c->company_id }}">{{ $c->company }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Date Created <small class="namesm"></small></label>
					<input type="text" readonly="readonly" class="form form-control input-sm inp-b" id="fullname" name="fullname" value="{{ date('F j, Y @ g:i:s A', strtotime($user->created_at)) }}" autocomplete="off" >
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Busines Unit</label>
					<select disabled class="form form-control input-sm inp-b" readonly="readonly" id="bunit" name="bunit">
						<option value="{{ $user->businessunit_id }}">{{ $user->businessunit->bname }}</option>
						@foreach($bunits as $b)
							<option value="{{ $b->businessunit_id }}">{{ $b->bname }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>Department</label>
					<select disabled class="form form-control input-sm inp-b" readonly="readonly" id="department" name="department">
						<option value="{{ $user->department_id }}">{{ $user->department->department }}</option>
						@foreach($dept as $d)
							<option value="{{ $d->department_id }}">{{ $d->department }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>User Type</label>
					<select disabled class="form form-control input-sm inp-b" readonly="readonly" id="usertype" name="usertype">
						<option value="{{ $user->usertype_id }}">{{ $user->usertype->usertype_name }}</option>
						@foreach($utype as $t)
							<option value="{{ $t->usertype_id }}">{{ $t->usertype_name }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog"><span class="requiredf"></span>User Status</label>
					<select disabled class="form form-control input-sm inp-b" id="user_status" name="user_status">
						<option value="{{ $user->user_status }}">{{ $user->user_status }}</option>
					</select>
				</div>
				<div class="form-group">
					<label class="label-dialog">IP Address <small class="namesm">(Optional)</small></label>
					<input type="text" class="form form-control input-sm inp-b" readonly="readonly" id="ipaddress" value="{{ $user->user_ipaddress }}" name="ipaddress" autocomplete="off">
				</div>
				<div class="response-dialog">
				</div>
			</div>
		</div>
	</form>
</div>