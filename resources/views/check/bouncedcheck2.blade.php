@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}
<div class="container bot-margin20" id="container-main">
    <form method="POST" action="{{route('pdctagging2') }}" id="_pdctagclearing">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">					
                            <div class="col-xs-2 mtop6">
                                <span class="glyphicon glyphicon-list-alt"></span> Bounced Check Tagging
                            </div>
                            <div class="col-xs-offset-6 col-xs-2">
                                
                            </div>
                            <div class="col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="bouncedChecksExport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
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
                                            <th>Action</th>
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
                                                <td style="text-align: center;">
                                                    <div class='action-check containerx' data-id='{{$check->checks_id}}' data-amount="{{$check->check_amount}}">
                                                        <input type="hidden" class="taggeditems" id="tagbouncecid-{{$check->checks_id}}" value="false">
                                                        <span class='glyphicon glyphicon-list gly-icons' id="viewcheck"></span>&emsp;
                                                        <span class='glyphicon glyphicon-edit gly-icons' id='updatebounced2'></span>&emsp; 
                                                                                                               
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