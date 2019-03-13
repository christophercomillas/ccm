<div class="_loadchecks">
    <table class="table paleBlueRows">
        <thead>
            <tr>
                <th>Date Cleared</th>
                <th>Cleared By</th>
                <th>View</th>
            </tr>
        </thead>
        <tbody style="position: relative; ">
            @foreach($checks as $check)
                <tr>
                    <td>{{ date('F j, Y',strtotime($check->checktagging->created_at)) }}</td>
                    <td>{{ strtoupper($check->checktagging->user->name) }}</td>
                    <td>
                        <div data-id='{{$check->checktagginghdr_id}}'>&emsp;
                            <a href="{{ url('viewclearedbytrid') }}/{{ $check->checktagginghdr_id}}"><span class='glyphicon glyphicon-list gly-icons'></span></a>
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
