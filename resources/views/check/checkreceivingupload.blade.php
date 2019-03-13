@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()->all()) }} --}}
<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<input type="hidden" value="{{ route('createcheckdialog') }}" id="createcheckroute">
						
						<div class="col-xs-3">
							<h5><span class="glyphicon glyphicon-list-alt"></span> Check Receiving (Textfile Upload)</h5>
						</div>
						<div class="col-xs-offset-5 col-xs-2">
                            <form id="uploadfiles">
                                <div class="form-group margin-bot0">
                                    
                                    <div class="file-upload">
                                        <label for="upload" class="file-upload__label"> <span class="glyphicon glyphicon-cloud-upload" aria-hidden="true"></span> Upload File</label>
                                        <input id="upload" class="file-upload__input" type="file" accept=".txt, Text Document" name="files[]" multiple>
                                    </div>
                                </div>    
                            </form> 
						</div>
						<div class="col-xs-2">
							<button type="button" class="btn btn-primary btn-sm btn-block" id="savecheck" disabled="true">
								<span class="glyphicon glyphicon-floppy-save"></span> Save
							</button>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
                 <form class="form-horizontal" id="_saveCheck" action="{{ route('savecheck') }}">
						<div class="form-group">
							<input type="hidden" value="{{ route('searchSalesman') }}" id="ssearchroute">
							<label class="col-md-2 control-label" for="textinput">Salesman Search</label>  
							<div class="col-md-4">
				 				<div class="input-group">
		                            <input type="text" id="ssearch" class="form-control input-sm inp-b up" autocomplete="off" required="required">
		                            <span class="input-group-btn">
		                                <button class="btn btn-info input-sm btn-find" type="button" id="_addnewSalesman">
		                                    <i class="fa fa-user-plus" aria-hidden="true"></i>
		                                </button>
		                            </span>
		                        </div>
								<span class="smanlist">
								</span>		         
							</div>
							<label class="col-md-offset-2 col-md-2 control-label" for="textinput">Control #</label>  
							<div class="col-md-2">
                            <input id="textinput" value="{{ $controlno }}" readonly="readonly" name="textinput" type="text" class="form-control input-sm">
							</div>
						</div>
						<div class="form-group">
							<input type="hidden" name="smanid" id="smanid" value="">
							<label class="col-md-2 control-label" for="textinput">Salesman Code & Name</label>  
							<div class="col-md-5">
								<input id="scodename" readonly="readonly" name="scodename" type="text" class="form-control input-sm inp-b">
							</div>
							<label class="col-md-offset-1 col-md-2 control-label" for="textinput">Date Received</label> 
							<div class="col-md-2">
                            <input id="textinput" readonly="readonly" name="textinput" type="text" class="form-control input-sm" value="{{ $ldate }}">
							</div> 
						</div>
						<div class="table-responsive">
							<table class="table" id="check-info">
								<thead>
									<tr>
										<th>Cust Code</th>
										<th>Check Type</th>
										<th>Check Class</th>
										<th>Acct. No.</th>
										<th>Account Name</th>
										<th>Check No.</th>
										<th>Check Date</th>
										<th>Branch Name</th>
										<th>Amount</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>

								</tbody>
							</table>
						</div>
			 		</form>
			 	</div>
				<div class="panel-footer"><h5 class="h5totamt">Total Amount: <span class="_totAmt">0.00</span></h5></div>  				
			</div>
		</div>
	</div>
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