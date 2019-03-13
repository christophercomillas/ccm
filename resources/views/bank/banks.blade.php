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
							<span class="glyphicon glyphicon-list-alt"></span> Banks
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<form class="form-horizontal">
						<div class="table-responsive">
	                        <table class="table" id="_banks">
	                            <thead>
									<tr>
										<th>Bank Code</th>
                                        <th>Bank Branch Name</th>
                                        <th>Created By</th>
                                        <th>Date Created</th>
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