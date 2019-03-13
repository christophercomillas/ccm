
<div class="_loadchecks">
    <table class="table table-responsive" id="checklist">
        <thead>
            <tr>
                <th>Check Date</th>
                <th>Customer</th>
                <th>Acct. No.</th>
                <th>Account Name</th>
                <th>Check No.</th>                
                <th>Bank Branch</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody style="position: relative; ">
            @foreach($checks as $check)
                <tr>
                    <td style="text-align: right;">{{ date('F j, Y',strtotime($check->check_date)) }}</td>
                    <td>{{ $check->fullname }}</td>
                    <td style="text-align: right;">{{ $check->account_no }}</td>
                    <td>{{ $check->account_name }}</td>
                    <td style="text-align: right;">{{ $check->check_no }}</td>                    
                    <td>{{ $check->bankbranchname }}</td>
                    <td class="aright">{{ number_format($check->check_amount,2) }}</td>
                    <td>{{ $check->check_status }}</td>
                    <td>
                        <div class='action-check' data-id='{{$check->checks_id}}' data-check='{{$check->check_no}}'>
                            <span class='glyphicon glyphicon-list gly-icons mright8' id='viewcheck'></span>
                            @if($check->check_status=='CLEARED')
                                <span class='glyphicon glyphicon-tag gly-icons mleft8' id='tagasbounced' title="Tag as Bounced"></span>
                            @endif
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
