@extends('layouts.main')

@section('content')
{{-- {{ dd( Illuminate\Support\Facades\Auth::user()) }} --}}

<div class="container bot-margin20" id="container-main">
    <div class="row">
        <div class="col-md-4">
            <div class="tabbable-panel">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="">
                            <a href="#tab_default_1" data-toggle="tab">
                            <span class="glyphicon glyphicon-list-alt"></span> Check Report </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_default_1">
                            <div class="row">
                               <div class="col-sm-12">
                                <label class="label-dialog">Date</label>
                                    <div class="form-group">
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input type="text" class="form-control pull-right" id="datepicker" name="date" autocomplete="off">
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="response">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary btn-sm btn-block" id="report">
                                            <span class="glyphicon glyphicon-cloud-download"></span> Submit
                                        </button>
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

