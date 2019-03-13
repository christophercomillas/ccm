@extends('layouts.main')

@section('content')
{{--{{ dd( Illuminate\Support\Facades\Auth::user()->all()) }}--}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-12">
							<h5><span class="glyphicon glyphicon-list-alt"></span> Upload Bank</h5>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
			 		<form class="form-horizontal" action="{{ route('importbanks') }}" method="POST" enctype="multipart/form-data" id="_saveBanks">
			 			<label>Upload file:</label><br />
			 			<input type="file" name="files[]" id="file" multiple><br />			 			
			 			<input type="submit" value="Upload" id="btnUpBank">
			 		</form>
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection