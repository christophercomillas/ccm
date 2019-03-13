@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6">
                            <span class="glyphicon glyphicon-list-alt"></span> PDC - Total Amount: {{ number_format($sum,2) }}
                            
                        </div>
						<div class="col-xs-6">                            
                            <span class="glyphicon glyphicon-search isearch pull-right"></span> 
                            <a href="{{ url('checksPDCExport') }}">
                                <span class="glyphicon glyphicon-export iexport margin-8 pull-right"></span> 
                            </a> 
                        </div>
                    </div>
                    <div class="row" id="_checkssearch" style="display:none;">
                        <form class="form-horizontal" method="GET" id="formCheckSearch" action="{{ route('checklistpdc') }}">
                            <div class="form-group">
                                <div class="col-sm-12">      
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="fullname" checked>Customer Name
                                    </label>     
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="account_no">Account Number
                                    </label>                                  
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="account_name">Account Name
                                    </label>      
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="check_no">Check Number
                                    </label>     
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="bankbranchname">Branch Name
                                    </label>
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="check_date">Check Date(YYYY-MM-DD)
                                    </label>
                                    <label class="radio-inline mright8">
                                        <input type="radio" name="searchcol" value="check_received">Check Received(YYYY-MM-DD)
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">                                
                                <label class="control-label input-sm col-sm-1">Search: </label>
                                <div class="col-sm-2">
                                    <input type="text" value="" name="searchvalue" class="form-control input-sm" autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group">     
                                    <div class="col-sm-2">
                                        <button type="submit" id="btn" class="btn btn-primary btn-block">Submit</button>
                                    </div>
                                <div class="col-sm-2">
                                    <button type="reset" class="btn btn-default btn-block">Reset</button>
                                </div>                           

                            </div>
                        </form>
                        
                    </div>
			 	</div>
			 	<div class="panel-body">
                    @include('check.loadcheckspdc')
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection