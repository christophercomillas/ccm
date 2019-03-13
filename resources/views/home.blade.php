@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()) }} --}}

<div class="container bot-margin20" id="container-main">
    <div class="row">
        <div class="col-md-12">
            <h3> Dashboard</h3>
            <div class="tabbable-panel">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#tab_default_1" data-toggle="tab">
                            <span class="glyphicon glyphicon-list-alt"></span> Checks Received </a>
                        </li>
                        <!-- <li>
                            <a href="#tab_default_2" data-toggle="tab">
                            <span class="glyphicon glyphicon-list-alt"></span> Receiv </a>
                        </li> -->
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_default_1">
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Checks</span>
                                            <span class="info-box-number">{{ $checks }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Post Dated Checks</span>
                                            <span class="info-box-number">{{ $checkpdc }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Dated Checks</span>
                                            <span class="info-box-number">{{ $datedChecks }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cleared</span>
                                            <span class="info-box-number">{{ $cleared }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bounced</span>
                                            <span class="info-box-number">{{ $bounced }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Converted to Cash</span>
                                            <span class="info-box-number">{{ $cash }}</span>
                                        </div>
                                    </div>    
                                </div>
                            </div>     
                        </div>
                        <div class="tab-pane" id="tab_default_2">
                        <div class="row">
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Checks Received</span>
                                            <span class="info-box-number">{{ $checks }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Post Dated Checks</span>
                                            <span class="info-box-number">{{ $checkpdc }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Dated Checks</span>
                                            <span class="info-box-number">{{ $datedChecks }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Cleared</span>
                                            <span class="info-box-number">{{ $cleared }}</span>
                                        </div>
                                    </div>    
                                </div>
                                <div class="col-sm-3">
                                    <div class="info-box">
                                        <div class="info-box-content">
                                            <span class="info-box-text">Bounced</span>
                                            <span class="info-box-number">{{ $bounced }}</span>
                                        </div>
                                    </div>    
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

