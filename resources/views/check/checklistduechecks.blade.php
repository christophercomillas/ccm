@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-2">
                            <span class="glyphicon glyphicon-list-alt"></span> Due Checks (PDC)
                            
                        </div>
                        <div class="col-xs-offset-8 col-xs-2">
                                <button type="button" class="btn btn-primary btn-sm btn-block" id="duepdcexport">
                                    <span class="glyphicon glyphicon-export"></span> Export Excel
                                </button>
                        <br>
                        </div>
						<div class="col-xs-2 pull-right">
                            <label>Search:&nbsp;</label>
                            <span class="glyphicon glyphicon-search isearch pull-right"></span>  
                        </div>
                    </div>
                    <div class="row" id="_checkssearch" style="display:none;">
                        <form class="form-horizontal" method="GET" id="formCheckSearch" action="{{ route('duechecks') }}">
                            <div class="form-group">
                                <div class="col-sm-2">                                    
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="cus_code" checked>Customer Code
                                    </label>
                                </div>
                                <div class="col-sm-2">      
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="fullname">Customer Name
                                    </label>
                                </div>
                                <div class="col-sm-2">      
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="account_no">Account Number
                                    </label>
                                </div>
                                <div class="col-sm-2">      
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="check_no">Check Number
                                    </label>
                                </div>
                                <div class="col-sm-2">      
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="bankbranchname">Branch Name
                                    </label>
                                </div>
                                <div class="col-sm-2">      
                                    <label class="radio-inline">
                                        <input type="radio" name="searchcol" value="check_type">Check Date
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
                    @include('check.loadduechecks')
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection