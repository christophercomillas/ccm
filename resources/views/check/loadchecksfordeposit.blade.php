
<div class="_loadchecks">
    <table class="table paleBlueRows" id="deptbody">
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Bank Account No</th>
                <th>Bank Account Name</th>
                <th>Check No.</th>
                <th>Check Date</th>
                <th>Check Type</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody id="deptbody" style="text-align: left;">
            @foreach($checks as $check)
                <tr>
                    <td>{{ $check->fullname }}</td>
                    <td style="text-align: right;">{{ $check->account_no }}</td>
                    <td>{{ strtoupper($check->account_name) }}</td>
                    <td style="text-align: right;">{{ $check->check_no }}</td>
                    <td style="text-align: right;">{{ date('F j, Y', strtotime($check->check_date)) }}</td>
                    <td style="text-align: center;">{{ $check->check_type }}</td>
                    <td class="aright" style="text-align: right;">{{ number_format($check->check_amount,2) }}</td>
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
