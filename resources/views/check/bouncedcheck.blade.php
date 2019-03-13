@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}
<div class="container bot-margin20" id="container-main">
    <form method="POST" action="{{route('pdctagging') }}" id="_pdctagclearing">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">					
                            <div class="col-xs-2 mtop6">
                                <span class="glyphicon glyphicon-list-alt"></span> Bounced Check Tagging
                            </div>
                            <div class="col-xs-offset-6 col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="bouncedChecksExport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
                                </button>
                            </div>
                            <div class="col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="clearcheck3">
                                    <span class="glyphicon glyphicon-export"></span> Save
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body deposit">	
                        <div class="row">
                            <div class="col-sm-1 depsearchlbl">
                                <label>Search:</label> 
                            </div>
                            <div class="col-sm-3">
                                <div class="input-group depgroup">
                                    <input name="filter" type="text" id="depsearch" class="form-control input-sm" autocomplete="off">
                                    <span class="input-group-btn">
                                        <button class="btn input-sm" id="depbtn" type="button">
                                            <span class="glyphicon glyphicon-filter" id="filterclear"></span>
                                        </button>
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-2 pull-right" id="_taggcnt">
                                <label class="">Tagged Count: <span class="taggedcnt">0</span></label></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table paleBlueRows">
                                    <thead>
                                        <tr>
                                            <th>Check Date</th>
                                            <th>Customer Name</th>
                                            <th>Bank Account No</th>
                                            <th>Bank Account Name</th>
                                            <th>Check No</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="deptbody" style="text-align: left;">
                                        @foreach($checks as $check)
                                            <tr>
                                            <td style="text-align: right;">{{ date('F j, Y', strtotime($check->check_date)) }}</td>
                                                <td>{{ $check->fullname }}</td>
                                                <td style="text-align: right;">{{ $check->account_no }}</td>
                                                <td>{{ strtoupper($check->account_name) }}</td>
                                                <td style="text-align: right;">{{ $check->check_no }}</td>
                                                <td class="aright" style="text-align: right;">{{ number_format($check->check_amount,2) }}</td>
                                                <td style="text-align: center;">
                                                    <div class='action-check containerx' data-id='{{$check->checks_id}}' data-amount="{{$check->check_amount}}" data-type="bounced">
                                                        <input type="hidden" class="taggeditems" id="tagbouncecid-{{$check->checks_id}}" value="false">
                                                        <span class='glyphicon glyphicon-list gly-icons' id="viewcheck"></span>&emsp;
                                                        <span class='glyphicon glyphicon-edit gly-icons' id='updatebounced'></span>&emsp; 
                                                                                                               
                                                        {{-- <input type="hidden" class="taggeditems" id="tagbouncecid-{{$check->checks_id}}" value="false">
                                                        &emsp;
                                                        <input type="checkbox" name="ck[]" class="checkboxItemsDep" value="{{$check->checks_id}}" checked="checked"/>
                                                        &emsp;
                                                        <a href='#' title='VIEW'>
                                                            <span class='glyphicon glyphicon-list' id="viewcheck"></span>
                                                        </a>&emsp;
                                                        <a href='#' title='TAG AS'>
                                                            <span class='glyphicon glyphicon-tag' id='tagcheckas'></span>
                                                        </a>&emsp;                                            --}}
                                                    </div></td>                                        
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <label class="">Check Count: <span class="">{{ count($checks) }}</span></label></label>
                            </div>
                            <label class="col-sm-2 lblinp">Remarks</label>
                            <div class="col-sm-5"><input type="text" class="form-control input-sm blu" name="remarks" id="remarks" autocomplete="off"/></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal modal-static fade loadingstyle" id="processing-modal" role="dialog" aria-hidden="true" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog loadingstyle">
      <div class="text-center">
        <img src="{{ asset('img/index.coffee-cup-drink-loader.svg') }}" class="icon" height="200px" width="200px"/>
          <div id="loaderdiv"></div>
      </div>
    </div>
</div>


@endsection