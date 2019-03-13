
<div class="_loadchecks">
    <table class="table paleBlueRows">
                            <thead>
                                <tr>
                                    <th>Customer Code</th>
                                    <th>Customer Name</th>
                                    <th>Bank Account No</th>
                                    <th>Bank Account Name</th>
                                    <th>Check No</th>
                                    <th>Check Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody style="text-align: left;">
                                @foreach($checks as $check)
                                    <tr>
                                        <td>{{ $check->cus_code }}</td>
                                        <td>{{ $check->fullname }}</td>
                                        <td style="text-align: right;">{{ $check->account_no }}</td>
                                        <td>{{ strtoupper($check->account_name) }}</td>
                                        <td style="text-align: right;">{{ $check->check_no }}</td>
                                        <td style="text-align: right;">{{ date('F j, Y', strtotime($check->check_date)) }}</td>
                                        <td class="aright" style="text-align: right;">{{ number_format($check->check_amount,2) }}</td>
                                        <!--<td style="text-align: center;">
                                            <div class='action-duecheck' data-id='{{$check->checks_id}}'>
                                                <input type="hidden" class="taggeditems" id="tagbouncecid-{{$check->checks_id}}" value="false">
                                                &emsp;
                                                <input type="checkbox" name="ck[]" class="checkboxItems" value="{{$check->checks_id}}" />
                                                &emsp;
                                                <a href='#' title='VIEW'>
                                                    <span class='glyphicon glyphicon-list' id="viewcheck2"></span>
                                                </a>&emsp;
                                                <a href='#' title='TAG AS'>
                                                    <span class='glyphicon glyphicon-tag' id='tagcheckas'></span>
                                                </a>&emsp;                                           
                                            </div>                                       
                                        </td>-->
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
