<div class="_loadchecks">
    <table class="table paleBlueRows">
        <thead>
            <tr>
                <th>Check Date</th>
                <th>Customer Name</th>
                <th>Acct. No.</th>
                <th>Account Name</th>
                <th>Check No.</th>                
                <th>Bank Branch</th>
                <th>Amount</th>
                <th>Status</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody style="position: relative; ">
            @foreach($checks as $check)
            <tr>                
                <td>{{ date('F j, Y',strtotime($check->check->check_date)) }}</td>
                <td>{{ $check->check->customer->fullname }}</td>
                <td>{{ $check->check->account_no }}</td>
                <td>{{ strtoupper($check->check->account_name) }}</td>
                <td>{{ $check->check->check_no }}</td>
                <td>{{ $check->check->bank->bankbranchname }}</td>
                <td>{{ number_format($check->check->check_amount,2) }}</td>
                <td>{{ $check->check->check_status }}</td>
                <td>
                    <div class='action-check' data-id='{{$check->checks_id}}'>
                        <span class='glyphicon glyphicon-list gly-icons' id='viewcheck'></span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>    

    <div class="row">
        <div class="col-sm-6">
            Showing {{ $checks->firstItem() }} to {{ $checks->lastItem() }} of {{ $checks->total() }} entries
        </div>
        <div class="col-sm-6">
            <span class="pull-right">{{ $checks->appends(request()->except('page'))->links() 
                }}</span>
        </div>        
    </div>
</div>
