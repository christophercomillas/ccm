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
							<h5><span class="glyphicon glyphicon-list-alt"></span> Check Status</h5>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<form class="form-horizontal">
						<div class="table-responsive">
	                        <table class="table" id="checkstatus">
	                            <thead>
									<tr>
										<th>Cust Code</th>
										<th>Acct. No.</th>
										<th>Account Name</th>
										<th>Check No.</th>
										<th>Check Date</th>
										<th>Branch Name</th>
                                        <th>Amount</th>
                                        <th>Status</th>
										<th>Action</th>
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