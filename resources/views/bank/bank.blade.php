@extends('layouts.main')

@section('content')
{{--{{ dd( Illuminate\Support\Facades\Auth::user()->all()) }}--}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-2">
							<h5><span class="glyphicon glyphicon-list-alt"></span> Banks</h5>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<table class="table">
			 			<thead>
			 				<tr>
			 					<th>Name</th>
			 					<th>ID</th>
			 					<th>Email</th>
			 					<th>Address</th>
			 				</tr>
			 			</thead>
			 			<tbody>
			 				@foreach($banks as $bank)
			 					<tr>
			 						<td>{{ $bank->name }}</td>
			 						<td></td>
			 						<td></td>
			 						<td></td>
			 					</tr>
			 				@endforeach;
			 			</tbody>
			 		</table>
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection