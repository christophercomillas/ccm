@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}

@push('styles')

@endpush

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-10">
							<h5><span class="glyphicon glyphicon-list-alt"></span> Date Cleared: {{$date}}</h5>
						</div>
						<div class="col-xs-2">
							<button type="button" class="btn btn-primary btn-sm btn-block" id="printrep">
								<span class="glyphicon glyphicon-export"></span> Print Preview
							</button>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<form class="form-horizontal">
						<div class="table-responsive">
	                        <table class="table" id="clearedchecksbydate">
	                            <thead>
	                                <tr>
                                        <th>Check Received</th>
                                        <th>Check Date</th>                                        
                                        <th>Customer Name</th>
                                        <th>Bank Account Name</th> 
                                        <th>Bank Account Number</th>
                                        <th>Check No.</th>                                
                                        <th>Amount</th>
	                                </tr>
	                            </thead>
	                            <tbody>
									@foreach($checks as $check)
										<tr>
											<td>{{ date('M d, Y', strtotime($check->check->check_received)) }}</td>
											<td>{{ date('M d, Y', strtotime($check->check->check_date)) }}</td>
											<td>{{ $check->check->customer->fullname }}</td>
											<td>{{ $check->check->account_name }}</td>
											<td>{{ $check->check->account_no }}</td>
											<td>{{ $check->check->check_no }}</td>
											<td>{{ number_format($check->check->check_amount,2) }}</td>
										</tr>
	                            	@endforeach
	                            </tbody>
	                        </table>
						</div>
			 		</form>
			 	</div>				
			</div>
		</div>
	</div>
</div>
<div style="display:none">
	<div class="printable">
		<div class="printheader">{{ Auth::user()->businessunit->bname }}</div>
		<div class="printheader">{{ strtoupper(Auth::user()->usertype->usertype_name).' DEPARTMENT' }}</div>
		<div class="printheader">{{ 'SUMMARY OF CHECKS RECEIVED' }}</div>
		<div class="printheader">{{ date('F d, Y', strtotime($date))   }}</div>
		<table class="printtable">
			<thead>
				<tr>
					<th>Check Date</th>
					<th>Payees Name/Acct. Name</th>
					<th>Bank Name</th>
					<th>Check Number</th>
					<th>Amount</th>					
				</tr>
			<thead>
			<tbody>
				<?php $total = 0; ?>
				@foreach($checks as $check)
					<?php $total += $check->check->check_amount; ?>
					<tr>
						<td>{{ date('M d, Y', strtotime($check->check->check_date)) }}</td>
						<td>{{ $check->check->customer->fullname }}</td>
						<td>{{ $check->check->bank->bankbranchname }}</td>
						<td style="text-align:right">{{ $check->check->check_no }}</td>
						<td style="text-align:right">{{ number_format($check->check->check_amount,2) }}</td>
					</tr>
				@endforeach
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td style="text-align:right">Total Amount:</td>
					<td style="text-align:right">{{ number_format($total,2) }}</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td style="text-align:right">Total Check Count:</td>
					<td style="text-align:right">{{ count($checks) }}</td>
				</tr>
			</tbody>
		</table>
		<div class="printfooter">
			<table class="tablefooter">
				<tr>
					<td><h4 class="h4print">Prepared By:</h4></td>
					<td><h4 class="h4print">Confirmed By:</h4></td>

				</tr>
				<tr>
					<td><h4 class="h4print-mleft20">{{ strtoupper(Auth::user()->name) }}</h4></td>
					<td><td>
				</tr>

			</table>
		</div>
	</div>
<div>
@push('scripts')
	<script src="{{ asset('js/jQuery.print.js') }}"></script> 
	<script>
		$('#printrep').click(function(){
			jQuery('.printable').print();
		});	
	</script>
	
@endpush

@endsection

