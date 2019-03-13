@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}
<div class="container bot-margin20" id="container-main">
    <form method="POST" action="{{route('pdctagging') }}" id="_pdctagging">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <div class="row">					
                            <div class="col-xs-2 mtop6">
                                <span class="glyphicon glyphicon-list-alt"></span> Due Checks (PDC)
                            </div>
                            <!-- <div class="col-xs-offset-6 col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="duepdcexport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
                                </button>
                            </div>
                            <div class="col-xs-2">
                                <button type="submit" class="btn btn-primary btn-sm btn-block" id="clearcheck2">
                                    <span class="glyphicon glyphicon-export"></span> Save
                                </button>
                            </div> -->
                            <div class="col-xs-offset-8 col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="duepdcexport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">	
                        <div class="col-sm-10">
                                <i class="fa fa-hidden"></i>
                        </div>
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