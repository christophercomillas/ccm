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
							<span class="glyphicon glyphicon-list-alt"></span> Institutional Check Import
                        </div>
                        <div class="col-xs-offset-4 col-xs-2">
                            <button type="button" class="btn btn-primary btn-sm btn-block" id="insimport">
                                <span class="fa fa-refresh"></span> Import
                            </button>
                        </div>
					</div>
			 	</div>
			 	<div class="panel-body">
                    Click import button to Update Database.
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection