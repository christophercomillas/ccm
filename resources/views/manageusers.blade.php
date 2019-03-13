@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-2">
							<h5><span class="glyphicon glyphicon-list-alt"></span> Manage Users</h5>
						</div>
						<div class="col-xs-offset-8 col-xs-2">
							<button type="button" class="btn btn-primary btn-sm btn-block" id="newuser">
								<i class="fa fa-user-plus" aria-hidden="true"></i></span> Add New User
							</button>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<form class="form-horizontal">
						<div class="table-responsive">
	                        <table class="table" id="userlist">
	                            <thead>
	                                <tr>
	                                    <th>ID Number</th>
	                                    <th>Fullname</th>
	                                    <th>Username</th>
	                                    <th>Company</th>
	                                    <th>Department</th>
	                                    <th>Business Unit</th>
	                                    <th>User Type</th>
	                                    <th>Status</th>
	                                    <th>Action</th>
	                                    <th>Date Created</th>                                    
	                                </tr>
	                            </thead>
	                            <tbody>

	                            </tbody>
	                        </table>
						</div>
			 		</form>
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection