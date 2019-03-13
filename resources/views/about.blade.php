@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()) }} --}}

<div class="container bot-margin20" id="container-main">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
			 	<div class="panel-heading">
					<div class="row">
						<div class="col-xs-2">
							<span class="glyphicon glyphicon-list-alt"></span> About CCMS
                            <br>
                            <br>
                            &nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-user"></span><b> The Developers</b>
						</div>
					</div>
			 	</div>
			 	<div class="panel-body">
                    {{-- <h4>Welcome {{ Auth::user()->usertype->usertype_name }}</h4> --}}
                    <div class="row">   
                        <div class="col-sm-2" style="margin-left: 24%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-success" style="color: white; margin-left: 35%;"><strong>ACTIVE</strong></span>
                                </div>
                            </div>   
                        </div>
                        <div class="col-sm-2" style="margin-left: 1%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-danger" style="color: white; margin-left: 35%;"><strong>FIRED</strong></span>
                                </div>
                            </div>   
                        </div>
                        <div class="col-sm-2" style="margin-left: 1%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-warning" style="color: white; margin-left: 35%;"><strong>OJT</strong></span>
                                </div>
                            </div>   
                        </div> 
                    </div> 
                    <hr>
                    <div class="row">
                        <div class="col-sm-2" style="margin-left: 24%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-success" style="color: white; margin-left: 35%;"><strong>ACTIVE</strong></span>
                                </div>
                            </div>   
                        </div>
                        <div class="col-sm-2" style="margin-left: 1%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-danger" style="color: white; margin-left: 35%;"><strong>FIRED</strong></span>
                                </div>
                            </div>   
                        </div>
                        <div class="col-sm-2" style="margin-left: 1%; background-color: #777777; border: 0px #ccc solid;">
                            <div class="">
                                <div class="info-box-content">
                                    <span class="info-box-text"><img src="{{ asset('images/dev.jpg') }}" width="150px" height="150px" style="border-radius: 50%; margin-top: 5%;"></span>
                                    <br><br>
                                    <h5 style="color: white; margin-left: 10%;">Harold G. Talatagod</h5>
                                    <h5 style="color: white; margin-left: 40%;">BSIT</h5>
                                    <hr>
                                    <h5 style="margin-left: 10%;"><strong>Programmer Aide</strong></h5>
                                    <hr>
                                    <h6 style="color: white; margin-left: 7%;"><i>November 2018 - Present</i></h6>
                                    <br>
                                    <span class="label label-warning" style="color: white; margin-left: 35%;"><strong>OJT</strong></span>
                                </div>
                            </div>   
                        </div> 
                    </div>                    
			 	</div>				
			</div>
		</div>
	</div>
</div>

@endsection

