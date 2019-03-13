@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}
<div class="container bot-margin20" id="container-main">
    <form method="POST" action="{{route('pdctagging') }}" id="_pdctagclearing">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">					
                            <div class="col-xs-2 mtop6">
                                <span class="glyphicon glyphicon-list-alt"></span> Check Tagging
                            </div>
                            <div class="col-xs-offset-6 col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="checksforDepositExport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
                                </button>
                            </div>
                            <div class="col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="clearcheck">
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
                            <div class="col-sm-2">
                                {{-- <div class="checkbox">
                                    <input type="checkbox" id="cboxdeposit" checked> <label>Check All (Cleared)</label>
                                </div> --}}
                                {{-- <div class="checkbox">
                                    <label>
                                        <input type="checkbox" value="" id="cboxdeposit" checked="checked">
                                        <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                        <span class="lblcheck">Check All (Cleared)</span>
                                    </label>
                                </div> --}}
                                <div class="pretty p-default p-head">
                                    <input type="checkbox" value="" id="cboxdeposit"/>
                                    <div class="state p-info">
                                        <label class="bold">Check All (Cleared)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                    <p class="totdeposit">Amount Bounced: <span class="totspanbounce">0.00</span></p>
                                </div>
                            <div class="col-sm-3">
                                <p class="totdeposit">Amount Cleared: <span class="totspan">0.00</span></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table paleBlueRows">
                                    <thead>
                                        <tr>
                                            <th>Customer Name</th>
                                            <th>Bank Account No</th>
                                            <th>Bank Account Name</th>
                                            <th>Check No</th>
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
                                                <!-- <td style="text-align: center;">
                                                    <div class='action-check containerx' data-id='{{$check->checks_id}}' data-amount="{{$check->check_amount}}">
                                                        <input type="hidden" class="taggeditems" id="tagbouncecid-{{$check->checks_id}}" value="false">
                                                        
                                                        <div class="pretty p-default">
                                                            <input type="checkbox" name="ck[]" class="checkboxItemsDep" value="{{$check->checks_id}}" />
                                                            <div class="state p-info">
                                                                <label> - </label>
                                                            </div>
                                                        </div>
                                                        <a href='#' title='VIEW'>
                                                            <span class='glyphicon glyphicon-list' id="viewcheck"></span>
                                                        </a>&emsp;
                                                        <a href='#' title='TAG AS'>
                                                            <span class='glyphicon glyphicon-tag' id='tagasbounce'></span>
                                                        </a>&emsp;         
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
                                                </td> -->
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-7 lblinp">Remarks</label>
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